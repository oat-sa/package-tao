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

class mysqlSchema extends dbSchema implements i_dbSchema
{
	public function dbt2udt($type,&$len,&$default)
	{
		$type = parent::dbt2udt($type,$len,$default);
		
		switch ($type)
		{
			case 'float':
				return 'real';
			case 'double':
				return 'float';
			case 'datetime':
				# DATETIME real type is TIMESTAMP
				if ($default == "'1970-01-01 00:00:00'") {
					# Bad hack
					$default = 'now()';
				}
				return 'timestamp';
			case 'integer':
			case 'mediumint':
				if ($len == 11) { $len = 0; }
				return 'integer';
			case 'bigint':
				if ($len == 20) { $len = 0; }
				break;
			case 'tinyint':
			case 'smallint':
				if ($len == 6) { $len = 0; }
				return 'smallint';
			case 'numeric':
				$len = 0;
				break;
			case 'tinytext':
			case 'longtext':
				return 'text';
		}
		
		return $type;
	}
	
	public function udt2dbt($type,&$len,&$default)
	{
		$type = parent::udt2dbt($type,$len,$default);
		
		switch ($type)
		{
			case 'real':
				return 'float';
			case 'float':
				return 'double';
			case 'timestamp':
				if ($default == 'now()') {
					# MySQL does not support now() default value...
					$default = "'1970-01-01 00:00:00'";
				}
				return 'datetime';
			case 'text':
				$len = 0;
				return 'longtext';
		}
		
		return $type;
	}
	
	public function db_get_tables()
	{
		$sql = 'SHOW TABLES';
		$rs = $this->con->select($sql);
		
		$res = array();
		while ($rs->fetch()) {
			$res[] = $rs->f(0);
		}
		return $res;
	}
	
	public function db_get_columns($table)
	{
		$sql = 'SHOW COLUMNS FROM '.$this->con->escapeSystem($table);
		$rs = $this->con->select($sql);
		
		$res = array();
		while ($rs->fetch())
		{
			$field = trim($rs->f('Field'));
			$type = trim($rs->f('Type'));
			$null = strtolower($rs->f('Null')) == 'yes';
			$default = $rs->f('Default');
			
			$len = null;
			if (preg_match('/^(.+?)\(([\d,]+)\)$/si',$type,$m)) {
				$type = $m[1];
				$len = (integer) $m[2];
			}
			
			if ($default != '' && !is_numeric($default)) {
				$default = "'".$default."'";
			}
			
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
		$sql = 'SHOW INDEX FROM '.$this->con->escapeSystem($table);
		$rs = $this->con->select($sql);
		
		$t = array();
		$res = array();
		while ($rs->fetch())
		{
			$key_name = $rs->f('Key_name');
			$unique = $rs->f('Non_unique') == 0;
			$seq = $rs->f('Seq_in_index');
			$col_name = $rs->f('Column_name');
			
			if ($key_name == 'PRIMARY' || $unique) {
				$t[$key_name]['cols'][$seq] = $col_name;
				$t[$key_name]['unique'] = $unique;
			}
		}
		
		foreach ($t as $name => $idx)
		{
			ksort($idx['cols']);
			
			$res[] = array(
				'name' => $name,
				'primary' => $name == 'PRIMARY',
				'unique' => $idx['unique'],
				'cols' => array_values($idx['cols'])
			);
		}
		
		return $res;
	}
	
	public function db_get_indexes($table)
	{
		$sql = 'SHOW INDEX FROM '.$this->con->escapeSystem($table);
		$rs = $this->con->select($sql);
		
		$t = array();
		$res = array();
		while ($rs->fetch())
		{
			$key_name = $rs->f('Key_name');
			$unique = $rs->f('Non_unique') == 0;
			$seq = $rs->f('Seq_in_index');
			$col_name = $rs->f('Column_name');
			$type = $rs->f('Index_type');
			
			if ($key_name != 'PRIMARY' && !$unique) {
				$t[$key_name]['cols'][$seq] = $col_name;
				$t[$key_name]['type'] = $type;
			}
		}
		
		foreach ($t as $name => $idx)
		{
			ksort($idx['cols']);
			
			$res[] = array(
				'name' => $name,
				'type' => $idx['type'],
				'cols' => $idx['cols']
			);
		}
		
		return $res;
	}
	
	public function db_get_references($table)
	{
		$sql = 'SHOW CREATE TABLE '.$this->con->escapeSystem($table);
		$rs = $this->con->select($sql);
		
		$s = $rs->f(1);
		
		$res = array();
		
		$n = preg_match_all('/^\s*CONSTRAINT\s+`(.+?)`\s+FOREIGN\s+KEY\s+\((.+?)\)\s+REFERENCES\s+`(.+?)`\s+\((.+?)\)(.*?)$/msi',$s,$match);
		if ($n > 0)
		{
			foreach ($match[1] as $i => $name)
			{
				# Columns transformation
				$t_cols = str_replace('`','',$match[2][$i]);
				$t_cols = explode(',',$t_cols);
				$r_cols = str_replace('`','',$match[4][$i]);
				$r_cols = explode(',',$r_cols);
				
				# ON UPDATE|DELETE
				$on = trim($match[5][$i],', ');
				$on_delete = null;
				$on_update = null;
				if ($on != '') {
					if (preg_match('/ON DELETE (.+?)(?:\s+ON|$)/msi',$on,$m)) {
						$on_delete = strtolower(trim($m[1]));
					}
					if (preg_match('/ON UPDATE (.+?)(?:\s+ON|$)/msi',$on,$m)) {
						$on_update = strtolower(trim($m[1]));
					}
				}
				
				$res[] = array (
					'name' => $name,
					'c_cols' => $t_cols,
					'p_table' => $match[3][$i],
					'p_cols' => $r_cols,
					'update' => $on_update,
					'delete' => $on_delete
				);
			}
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
			$this->con->escapeSystem($n).' '.
			$type.$len.' '.$null.' '.$default;
		}
		
		$sql =
		'CREATE TABLE '.$this->con->escapeSystem($name)." (\n".
			implode(",\n",$a).
		"\n) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_bin ";
		
		$this->con->execute($sql);
	}
	
	public function db_create_field($table,$name,$type,$len,$null,$default)
	{
		$type = $this->udt2dbt($type,$len,$default);
		$len = (integer) $len > 0 ? '('.(integer) $len.')' : '';
		$null = $null ? 'NULL' : 'NOT NULL';
		
		if ($default === null) {
			$default = 'DEFAULT NULL';
		} elseif ($default !== false) {
			$default = 'DEFAULT '.$default;
		} else {
			$default = '';
		}
		
		$sql =
		'ALTER TABLE '.$this->con->escapeSystem($table).' '.
		'ADD COLUMN '.$this->con->escapeSystem($name).' '.
		$type.$len.' '.$null.' '.$default;
		
		$this->con->execute($sql);
	}
	
	public function db_create_primary($table,$name,$cols)
	{
		$c = array();
		foreach ($cols as $v) {
			$c[] = $this->con->escapeSystem($v);
		}
		
		$sql =
		'ALTER TABLE '.$this->con->escapeSystem($table).' '.
		'ADD CONSTRAINT PRIMARY KEY ('.implode(',',$c).') ';
		
		$this->con->execute($sql);
	}
	
	public function db_create_unique($table,$name,$cols)
	{
		$c = array();
		foreach ($cols as $v) {
			$c[] = $this->con->escapeSystem($v);
		}
		
		$sql =
		'ALTER TABLE '.$this->con->escapeSystem($table).' '.
		'ADD CONSTRAINT UNIQUE KEY '.$this->con->escapeSystem($name).' '.
		'('.implode(',',$c).') ';
		
		$this->con->execute($sql);
	}
	
	public function db_create_index($table,$name,$type,$cols)
	{
		$c = array();
		foreach ($cols as $v) {
			$c[] = $this->con->escapeSystem($v);
		}
		
		$sql =
		'ALTER TABLE '.$this->con->escapeSystem($table).' '.
		'ADD INDEX '.$this->con->escapeSystem($name).' USING '.$type.' '.
		'('.implode(',',$c).') ';
		
		$this->con->execute($sql);
	}
	
	public function db_create_reference($name,$c_table,$c_cols,$p_table,$p_cols,$update,$delete)
	{
		$c = array();
		$p = array();
		foreach ($c_cols as $v) {
			$c[] = $this->con->escapeSystem($v);
		}
		foreach ($p_cols as $v) {
			$p[] = $this->con->escapeSystem($v);
		}
		
		$sql =
		'ALTER TABLE '.$this->con->escapeSystem($c_table).' '.
		'ADD CONSTRAINT '.$name.' FOREIGN KEY '.
		'('.implode(',',$c).') '.
		'REFERENCES '.$this->con->escapeSystem($p_table).' '.
		'('.implode(',',$p).') ';
		
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
		$null = $null ? 'NULL' : 'NOT NULL';
		
		if ($default === null) {
			$default = 'DEFAULT NULL';
		} elseif ($default !== false) {
			$default = 'DEFAULT '.$default;
		} else {
			$default = '';
		}
		
		$sql =
		'ALTER TABLE '.$this->con->escapeSystem($table).' '.
		'CHANGE COLUMN '.$this->con->escapeSystem($name).' '.$this->con->escapeSystem($name).' '.
		$type.$len.' '.$null.' '.$default;
		
		$this->con->execute($sql);
	}
	
	public function db_alter_primary($table,$name,$newname,$cols)
	{
		$c = array();
		foreach ($cols as $v) {
			$c[] = $this->con->escapeSystem($v);
		}
		
		$sql =
		'ALTER TABLE '.$this->con->escapeSystem($table).' '.
		'DROP PRIMARY KEY, ADD PRIMARY KEY '.
		'('.implode(',',$c).') ';
		
		$this->con->execute($sql);
	}
	
	public function db_alter_unique($table,$name,$newname,$cols)
	{
		$c = array();
		foreach ($cols as $v) {
			$c[] = $this->con->escapeSystem($v);
		}
		
		$sql =
		'ALTER TABLE '.$this->con->escapeSystem($table).' '.
		'DROP INDEX '.$this->con->escapeSystem($name).', '.
		'ADD UNIQUE '.$this->con->escapeSystem($newname).' '.
		'('.implode(',',$c).') ';
		
		$this->con->execute($sql);
	}
	
	public function db_alter_index($table,$name,$newname,$type,$cols)
	{
		$c = array();
		foreach ($cols as $v) {
			$c[] = $this->con->escapeSystem($v);
		}
		
		$sql =
		'ALTER TABLE '.$this->con->escapeSystem($table).' '.
		'DROP INDEX '.$this->con->escapeSystem($name).', '.
		'ADD INDEX '.$this->con->escapeSystem($newname).' '.
		'USING '.$type.' '.
		'('.implode(',',$c).') ';
		
		$this->con->execute($sql);
	}
	
	public function db_alter_reference($name,$newname,$c_table,$c_cols,$p_table,$p_cols,$update,$delete)
	{
		$sql =
		'ALTER TABLE '.$this->con->escapeSystem($c_table).' '.
		'DROP FOREIGN KEY '.$this->con->escapeSystem($name);
		
		$this->con->execute($sql);
		$this->createReference($newname,$c_table,$c_cols,$p_table,$p_cols,$update,$delete);
	}
	
	public function db_drop_unique($table,$name)
	{
		$sql =
		'ALTER TABLE '.$this->con->escapeSystem($table).' '.
		'DROP INDEX '.$this->con->escapeSystem($name);
		$this->con->execute($sql);
	}
}
?>