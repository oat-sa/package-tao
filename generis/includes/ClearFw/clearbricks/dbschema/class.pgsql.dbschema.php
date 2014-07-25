<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Clearbricks.
# Copyright (c) 2007 Olivier Meunier and contributors. All rights
# reserved.
#
# Clearbricks is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# Clearbricks is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Clearbricks; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****

class pgsqlSchema extends dbSchema implements i_dbSchema
{
	protected $ref_actions_map = array(
		'a' => 'no action',
		'r' => 'restrict',
		'c' => 'cascade',
		'n' => 'set null',
		'd' => 'set default'
	);
	
	public function dbt2udt($type,&$len,&$default)
	{
		$type = parent::dbt2udt($type,$len,$default);
		
		return $type;
	}
	
	public function udt2dbt($type,&$len,&$default)
	{
		$type = parent::udt2dbt($type,$len,$default);
		
		return $type;
	}
	
	public function db_get_tables()
	{
		$sql =
		'SELECT table_name '.
		'FROM information_schema.tables '.
		"WHERE table_schema = 'public' ";
		
		$rs = $this->con->select($sql);
		
		$res = array();
		while ($rs->fetch()) {
			$res[] = $rs->f(0);
		}
		return $res;
	}
	
	public function db_get_columns($table)
	{
		$sql = 
		'SELECT column_name, udt_name, character_maximum_length, '.
		'is_nullable, column_default '.
		'FROM information_schema.columns '.
		"WHERE table_name = '".$this->con->escape($table)."' ";
		
		$rs = $this->con->select($sql);
		
		$res = array();
		while ($rs->fetch())
		{
			$field = trim($rs->column_name);
			$type = trim($rs->udt_name);
			$null = strtolower($rs->is_nullable) == 'yes';
			$default = $rs->column_default;
			$len = $rs->character_maximum_length;
			
			if ($len == '') {
				$len = null;
			}
			
			$default = preg_replace('/::([\w\d\s]*)$/','',$default);
			
			$res[$field] = array(
				'type' => $type,
				'len' => $len,
				'null' => $null,
				'default' => $default
			);
		}
		
		return $res;
	}
	
	public function db_get_keys($table)
	{
		$sql =
		'SELECT DISTINCT ON(cls.relname) cls.oid, cls.relname as idxname, indisunique::integer, indisprimary::integer, '.
		'indnatts, tab.relname as tabname, contype, amname '.
		'FROM pg_index idx '.
		'JOIN pg_class cls ON cls.oid=indexrelid '.
		'JOIN pg_class tab ON tab.oid=indrelid '.
		'LEFT OUTER JOIN pg_tablespace ta on ta.oid=cls.reltablespace '.
		'JOIN pg_namespace n ON n.oid=tab.relnamespace '.
		'JOIN pg_am am ON am.oid=cls.relam '.
		"LEFT JOIN pg_depend dep ON (dep.classid = cls.tableoid AND dep.objid = cls.oid AND dep.refobjsubid = '0') ".
		'LEFT OUTER JOIN pg_constraint con ON (con.tableoid = dep.refclassid AND con.oid = dep.refobjid) '.
		'LEFT OUTER JOIN pg_description des ON des.objoid=con.oid '.
		'LEFT OUTER JOIN pg_description desp ON (desp.objoid=con.oid AND desp.objsubid = 0) '.
		"WHERE tab.relname = '".$this->con->escape($table)."' ".
		"AND contype IN ('p','u') ".
		'ORDER BY cls.relname ';
		
		$rs = $this->con->select($sql);
		
		$res = array();
		while ($rs->fetch())
		{
			$k = array(
				'name' => $rs->idxname,
				'primary' => (boolean) $rs->indisprimary,
				'unique' => (boolean) $rs->indisunique,
				'cols' => array()
			);
			
			for ($i=1; $i<=$rs->indnatts; $i++) {
				$cols = $this->con->select('SELECT pg_get_indexdef('.$rs->oid.'::oid, '.$i.', true);');
				$k['cols'][] = $cols->f(0);
			}
			
			$res[] = $k;
		}
		
		return $res;
	}
	
	public function db_get_indexes($table)
	{
		$sql =
		'SELECT DISTINCT ON(cls.relname) cls.oid, cls.relname as idxname, n.nspname, '.
		'indnatts, tab.relname as tabname, contype, amname '.
		'FROM pg_index idx '.
		'JOIN pg_class cls ON cls.oid=indexrelid '.
		'JOIN pg_class tab ON tab.oid=indrelid '.
		'LEFT OUTER JOIN pg_tablespace ta on ta.oid=cls.reltablespace '.
		'JOIN pg_namespace n ON n.oid=tab.relnamespace '.
		'JOIN pg_am am ON am.oid=cls.relam '.
		"LEFT JOIN pg_depend dep ON (dep.classid = cls.tableoid AND dep.objid = cls.oid AND dep.refobjsubid = '0') ".
		'LEFT OUTER JOIN pg_constraint con ON (con.tableoid = dep.refclassid AND con.oid = dep.refobjid) '.
		'LEFT OUTER JOIN pg_description des ON des.objoid=con.oid '.
		'LEFT OUTER JOIN pg_description desp ON (desp.objoid=con.oid AND desp.objsubid = 0) '.
		"WHERE tab.relname = '".$this->con->escape($table)."' ".
		'AND conname IS NULL '.
		'ORDER BY cls.relname ';
		
		$rs = $this->con->select($sql);
		
		$res = array();
		while ($rs->fetch())
		{
			$k = array(
				'name' => $rs->idxname,
				'type' => $rs->amname,
				'cols' => array()
			);
			
			for ($i=1; $i<=$rs->indnatts; $i++) {
				$cols = $this->con->select('SELECT pg_get_indexdef('.$rs->oid.'::oid, '.$i.', true);');
				$k['cols'][] = $cols->f(0);
			}
			
			$res[] = $k;
		}
		
		return $res;
	}
	
	public function db_get_references($table)
	{
		$sql =
		'SELECT ct.oid, conname, condeferrable, condeferred, confupdtype, '.
		'confdeltype, confmatchtype, conkey, confkey, conrelid, confrelid, cl.relname as fktab, '.
		'cr.relname as reftab '.
		'FROM pg_constraint ct '.
		'JOIN pg_class cl ON cl.oid=conrelid '.
		'JOIN pg_namespace nl ON nl.oid=cl.relnamespace '.
		'JOIN pg_class cr ON cr.oid=confrelid '.
		'JOIN pg_namespace nr ON nr.oid=cr.relnamespace '.
		"WHERE contype='f' ".
		"AND cl.relname = '".$this->con->escape($table)."' ".
		'ORDER BY conname ';
		
		$rs = $this->con->select($sql);
		
		$cols_sql =
		'SELECT a1.attname as conattname, a2.attname as confattname '.
		'FROM pg_attribute a1, pg_attribute a2 '.
		'WHERE a1.attrelid=%1$s::oid AND a1.attnum=%2$s '.
		'AND a2.attrelid=%3$s::oid AND a2.attnum=%4$s ';
		
		$res = array();
		while ($rs->fetch())
		{
			$conkey = preg_replace('/[^\d]/','',$rs->conkey);
			$confkey = preg_replace('/[^\d]/','',$rs->confkey);
			
			$k = array(
				'name' => $rs->conname,
				'c_cols' => array(),
				'p_table' => $rs->reftab,
				'p_cols' => array(),
				'update' => $this->ref_actions_map[$rs->confupdtype],
				'delete' => $this->ref_actions_map[$rs->confdeltype]
			);
			
			$cols = $this->con->select(sprintf($cols_sql,$rs->conrelid,$conkey,$rs->confrelid,$confkey));
			while ($cols->fetch()) {
				$k['c_cols'][] = $cols->conattname;
				$k['p_cols'][] = $cols->confattname;
			}
			
			$res[] = $k;
		}
		
		return $res;
	}
	
	public function db_create_table($name,$fields)
	{
		$a = array();
		
		foreach ($fields as $n => $f)
		{
			$type = $f['type'];
			$len = (integer) $f['len'];
			$default = $f['default'];
			$null = $f['null'];
			
			$type = $this->udt2dbt($type,$len,$default);
			$len = $len > 0 ? '('.$len.')' : '';
			$null = $null ? 'NULL' : 'NOT NULL';
			
			if ($default === null) {
				$default = 'DEFAULT NULL';
			} elseif ($default !== false) {
				$default = 'DEFAULT '.$default.' ';
			} else {
				$default = '';
			}
			
			$a[] =
			$n.' '.
			$type.$len.' '.$null.' '.$default;
		}
		
		$sql =
		'CREATE TABLE '.$name." (\n".
			implode(",\n",$a).
		"\n)";
		
		$this->con->execute($sql);
	}
	
	public function db_create_field($table,$name,$type,$len,$null,$default)
	{
		$type = $this->udt2dbt($type,$len,$default);
		$len = $len > 0 ? '('.$len.')' : '';
		$null = $null ? 'NULL' : 'NOT NULL';
		
		if ($default === null) {
			$default = 'DEFAULT NULL';
		} elseif ($default !== false) {
			$default = 'DEFAULT '.$default.' ';
		} else {
			$default = '';
		}
		
		$sql =
		'ALTER TABLE '.$table.' ADD COLUMN '.$name.' '.
		$type.$len.' '.$null.' '.$default;
		
		$this->con->execute($sql);
	}
	
	public function db_create_primary($table,$name,$cols)
	{
		$sql =
		'ALTER TABLE '.$table.' '.
		'ADD CONSTRAINT '.$name.' PRIMARY KEY ('.implode(",",$cols).') ';
		
		$this->con->execute($sql);
	}
	
	public function db_create_unique($table,$name,$cols)
	{
		$sql =
		'ALTER TABLE '.$table.' '.
		'ADD CONSTRAINT '.$name.' UNIQUE ('.implode(',',$cols).') ';
		
		$this->con->execute($sql);
	}
	
	public function db_create_index($table,$name,$type,$cols)
	{
		$sql =
		'CREATE INDEX '.$name.' ON '.$table.' USING '.$type.
		'('.implode(',',$cols).') ';
		
		$this->con->execute($sql);
	}
	
	public function db_create_reference($name,$c_table,$c_cols,$p_table,$p_cols,$update,$delete)
	{
		$sql =
		'ALTER TABLE '.$c_table.' '.
		'ADD CONSTRAINT '.$name.' FOREIGN KEY '.
		'('.implode(',',$c_cols).') '.
		'REFERENCES '.$p_table.' '.
		'('.implode(',',$p_cols).') ';
		
		if ($update) {
			$sql .= 'ON UPDATE '.$update.' ';
		}
		if ($delete) {
			$sql .= 'ON DELETE '.$delete.' ';
		}
		
		$this->con->execute($sql);
	}
	
	public function db_alter_field($table,$name,$type,$len,$null,$default)
	{
		$type = $this->udt2dbt($type,$len,$default);
		$len = (integer) $len > 0 ? '('.(integer) $len.')' : '';
		
		$sql = 'ALTER TABLE '.$table.' ALTER COLUMN '.$name.' TYPE '.$type.$len;
		$this->con->execute($sql);
		
		if ($default === null) {
			$default = 'SET DEFAULT NULL';
		} elseif ($default !== false) {
			$default = 'SET DEFAULT '.$default;
		} else {
			$default = 'DROP DEFAULT';
		}
		
		$sql = 'ALTER TABLE '.$table.' ALTER COLUMN '.$name.' '.$default;
		$this->con->execute($sql);
		
		$null = $null ? 'DROP NOT NULL' : 'SET NOT NULL';
		$sql = 'ALTER TABLE '.$table.' ALTER COLUMN '.$name.' '.$null;
		$this->con->execute($sql);
	}
	
	public function db_alter_primary($table,$name,$newname,$cols)
	{
		$sql = 'ALTER TABLE '.$table.' DROP CONSTRAINT '.$name;
		$this->con->execute($sql);
		
		$this->createPrimary($table,$newname,$cols);
	}
	
	public function db_alter_unique($table,$name,$newname,$cols)
	{
		$sql = 'ALTER TABLE '.$table.' DROP CONSTRAINT '.$name;
		$this->con->execute($sql);
		
		$this->createUnique($table,$newname,$cols);
	}
	
	public function db_alter_index($table,$name,$newname,$type,$cols)
	{
		$sql = 'DROP INDEX '.$name;
		$this->con->execute($sql);
		
		$this->createIndex($table,$newname,$type,$cols);
	}
	
	public function db_alter_reference($name,$newname,$c_table,$c_cols,$p_table,$p_cols,$update,$delete)
	{
		$sql = 'ALTER TABLE '.$c_table.' DROP CONSTRAINT '.$name;
		$this->con->execute($sql);
		
		$this->createReference($newname,$c_table,$c_cols,$p_table,$p_cols,$update,$delete);
	}
	
	public function db_drop_unique($table,$name)
	{
		$sql = 'ALTER TABLE '.$table.' DROP CONSTRAINT '.$name;
		$this->con->execute($sql);
	}
}
?>