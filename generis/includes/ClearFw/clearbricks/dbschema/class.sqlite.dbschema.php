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

class sqliteSchema extends dbSchema implements i_dbSchema
{
	private $table_hist = array();
	
	private $table_stack = array();	// Stack for tables creation
	private $x_stack = array();		// Execution stack
	
	public function dbt2udt($type,&$len,&$default)
	{
		$type = parent::dbt2udt($type,$len,$default);
		
		switch ($type)
		{
			case 'float':
				return 'real';
			case 'double':
				return 'float';
			case 'timestamp':
				# DATETIME real type is TIMESTAMP
				if ($default == "'1970-01-01 00:00:00'") {
					# Bad hack
					$default = 'now()';
				}
				return 'timestamp';
			case 'integer':
			case 'mediumint':
			case 'bigint':
			case 'tinyint':
			case 'smallint':
			case 'numeric':
				return 'integer';
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
			case 'integer':
			case 'smallint':
			case 'bigint':
				return 'integer';
			case 'real':
			case 'float:':
				return 'real';
			case 'date':
			case 'time':
				return 'timestamp';
			case 'timestamp':
				if ($default == 'now()') {
					# SQLite does not support now() default value...
					$default = "'1970-01-01 00:00:00'";
				}
				return $type;
		}
		
		return $type;
	}
	
	public function flushStack()
	{
		foreach ($this->table_stack as $table => $def)
		{
			$sql = 'CREATE TABLE '.$table." (\n".implode(",\n",$def)."\n)\n ";
			$this->con->execute($sql);
		}
		
		foreach ($this->x_stack as $x)
		{
			$this->con->execute($x);
		}
		
	}
	
	public function db_get_tables()
	{
		$res = array();
		$sql = "SELECT * FROM sqlite_master WHERE type = 'table'";
		$rs = $this->con->select($sql);
		
		$res = array();
		while ($rs->fetch()) {
			$res[] = $rs->tbl_name;
		}
		
		return $res;
	}
	
	public function db_get_columns($table)
	{
		$sql = 'PRAGMA table_info('.$this->con->escapeSystem($table).')';
		$rs = $this->con->select($sql);
		
		$res = array();
		while ($rs->fetch())
		{
			$field = trim($rs->name);
			$type = trim($rs->type);
			$null = trim($rs->notnull) == 0;
			$default = trim($rs->dflt_value);
			
			$len = null;
			if (preg_match('/^(.+?)\(([\d,]+)\)$/si',$type,$m)) {
				$type = $m[1];
				$len = (integer) $m[2];
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
		$t = array();
		$res = array();
		
		# Get primary keys first
		$sql = "SELECT sql FROM sqlite_master WHERE type='table' AND name='".$this->con->escape($table)."'";
		$rs = $this->con->select($sql);
		
		if ($rs->isEmpty()) {
			return array();
		}
		
		# Get primary keys
		$n = preg_match_all('/^\s*CONSTRAINT\s+([^,]+?)\s+PRIMARY\s+KEY\s+\((.+?)\)/msi',$rs->sql,$match);
		if ($n > 0)
		{
			foreach ($match[1] as $i => $name)
			{
				$cols = preg_split('/\s*,\s*/',$match[2][$i]);
				$res[] = array(
					'name' => $name,
					'primary' => true,
					'unique' => false,
					'cols' => $cols
				);
			}
		}
		
		# Get unique keys
		$n = preg_match_all('/^\s*CONSTRAINT\s+([^,]+?)\s+UNIQUE\s+\((.+?)\)/msi',$rs->sql,$match);
		if ($n > 0)
		{
			foreach ($match[1] as $i => $name)
			{
				$cols = preg_split('/\s*,\s*/',$match[2][$i]);
				$res[] = array(
					'name' => $name,
					'primary' => false,
					'unique' => true,
					'cols' => $cols
				);
			}
		}
		
		return $res;
	}
	
	public function db_get_indexes($table)
	{
		$sql = 'PRAGMA index_list('.$this->con->escapeSystem($table).')';
		$rs = $this->con->select($sql);
		
		$res = array();
		while ($rs->fetch())
		{
			if (preg_match('/^sqlite_/',$rs->name)) {
				continue;
			}
			
			$idx = $this->con->select('PRAGMA index_info('.$this->con->escapeSystem($rs->name).')');
			$cols = array();
			while ($idx->fetch()) {
				$cols[] = $idx->name;
			}
			
			$res[] = array(
				'name' => $rs->name,
				'type' => 'btree',
				'cols' => $cols
			);
		}
		
		return $res;
	}
	
	public function db_get_references($table)
	{
		$sql = 'SELECT * FROM sqlite_master WHERE type=\'trigger\' AND tbl_name = \'%1$s\' AND name LIKE \'%2$s_%%\' ';
		$res = array();
		
		# Find constraints on table
		$bir = $this->con->select(sprintf($sql,$this->con->escape($table),'bir'));
		$bur = $this->con->select(sprintf($sql,$this->con->escape($table),'bur'));
		
		if ($bir->isEmpty() || $bur->isempty()) {
			return $res;
		}
		
		while ($bir->fetch())
		{
			# Find child column and parent table and column
			if (!preg_match('/FROM\s+(.+?)\s+WHERE\s+(.+?)\s+=\s+NEW\.(.+?)\s*?\) IS\s+NULL/msi',$bir->sql,$m)) {
				continue;
			}
			
			$c_col = $m[3];
			$p_table = $m[1];
			$p_col = $m[2];
			
			# Find on update
			$on_update = 'restrict';
			$aur = $this->con->select(sprintf($sql,$this->con->escape($p_table),'aur'));
			while ($aur->fetch())
			{
				if (!preg_match('/AFTER\s+UPDATE/msi',$aur->sql)) {
					continue;
				}
				
				if (preg_match('/UPDATE\s+'.$table.'\s+SET\s+'.$c_col.'\s*=\s*NEW.'.$p_col.
					'\s+WHERE\s+'.$c_col.'\s*=\s*OLD\.'.$p_col.'/msi',$aur->sql)) {
					$on_update = 'cascade';
					break;
				}
				
				if (preg_match('/UPDATE\s+'.$table.'\s+SET\s+'.$c_col.'\s*=\s*NULL'.
					'\s+WHERE\s+'.$c_col.'\s*=\s*OLD\.'.$p_col.'/msi',$aur->sql)) {
					$on_update = 'set null';
					break;
				}
			}
			
			# Find on delete
			$on_delete = 'restrict';
			$bdr = $this->con->select(sprintf($sql,$this->con->escape($p_table),'bdr'));
			while ($bdr->fetch())
			{
				if (!preg_match('/BEFORE\s+DELETE/msi',$bdr->sql)) {
					continue;
				}
				
				if (preg_match('/DELETE\s+FROM\s+'.$table.'\s+WHERE\s+'.$c_col.'\s*=\s*OLD\.'.$p_col.'/msi',$bdr->sql)) {
					$on_delete = 'cascade';
					break;
				}
				
				if (preg_match('/UPDATE\s+'.$table.'\s+SET\s+'.$c_col.'\s*=\s*NULL'.
					'\s+WHERE\s+'.$c_col.'\s*=\s*OLD\.'.$p_col.'/msi',$bdr->sql)) {
					$on_update = 'set null';
					break;
				}
			}
			
			$res[] = array(
				'name' => substr($bir->name,4),
				'c_cols' => array($c_col),
				'p_table' => $p_table,
				'p_cols' => array($p_col),
				'update' => $on_update,
				'delete' => $on_delete
			);
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
			
			$a[] = $n.' '.$type.$len.' '.$null.' '.$default;
		}
		
		$this->table_stack[$name][] = implode(",\n",$a);
		$this->table_hist[$name] = $fields;
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
		'ALTER TABLE '.$this->con->escapeSystem($table).' '.
		'ADD COLUMN '.$this->con->escapeSystem($name).' '.
		$type.$len.' '.$null.' '.$default;
		
		$this->con->execute($sql);
	}
	
	public function db_create_primary($table,$name,$cols)
	{
		$this->table_stack[$table][] = 'CONSTRAINT '.$name.' PRIMARY KEY ('.implode(',',$cols).') ';
	}
	
	public function db_create_unique($table,$name,$cols)
	{
		$this->table_stack[$table][] = 'CONSTRAINT '.$name.' UNIQUE ('.implode(',',$cols).') ';
	}
	
	public function db_create_index($table,$name,$type,$cols)
	{
		$this->x_stack[] = 'CREATE INDEX '.$name.' ON '.$table.' ('.implode(',',$cols).') ';
	}
	
	public function db_create_reference($name,$c_table,$c_cols,$p_table,$p_cols,$update,$delete)
	{
		if (!isset($this->table_hist[$c_table])) {
			return;
		}
		
		if (count($c_cols) > 1 || count($p_cols) > 1) {
			throw new Exception('SQLite UDBS does not support multiple columns foreign keys');
		}
		
		$c_col = $c_cols[0];
		$p_col = $p_cols[0];
		
		$update = strtolower($update);
		$delete = strtolower($delete);
		
		$cnull = $this->table_hist[$c_table][$c_col]['null'];
		
		# Create constraint
		$this->x_stack[] =
		'CREATE TRIGGER bir_'.$name."\n".
		'BEFORE INSERT ON '.$c_table."\n".
		"FOR EACH ROW BEGIN\n".
		'  SELECT RAISE(ROLLBACK,\'insert on table "'.$c_table.'" violates foreign key constraint "'.$name.'"\')'."\n".
		'  WHERE '.
		($cnull ? 'NEW.'.$c_col." IS NOT NULL\n  AND " : '').
		'(SELECT '.$p_col.' FROM '.$p_table.' WHERE '.$p_col.' = NEW.'.$c_col.") IS NULL;\n".
		"END;\n";
		
		# Update constraint
		$this->x_stack[] =
		'CREATE TRIGGER bur_'.$name."\n".
		'BEFORE UPDATE ON '.$c_table."\n".
		"FOR EACH ROW BEGIN\n".
		'  SELECT RAISE(ROLLBACK,\'update on table "'.$c_table.'" violates foreign key constraint "'.$name.'"\')'."\n".
		'  WHERE '.
		($cnull ? 'NEW.'.$c_col." IS NOT NULL\n  AND " : '').
		'(SELECT '.$p_col.' FROM '.$p_table.' WHERE '.$p_col.' = NEW.'.$c_col.") IS NULL;\n".
		"END;\n";
		
		# ON UPDATE
		if ($update == 'cascade')
		{
			$this->x_stack[] =
			'CREATE TRIGGER aur_'.$name."\n".
			'AFTER UPDATE ON '.$p_table."\n".
			"FOR EACH ROW BEGIN\n".
			'  UPDATE '.$c_table.' SET '.$c_col.' = NEW.'.$p_col.' WHERE '.$c_col.' = OLD.'.$p_col.";\n".
			"END;\n";
		}
		elseif ($update == 'set null')
		{
			$this->x_stack[] =
			'CREATE TRIGGER aur_'.$name."\n".
			'AFTER UPDATE ON '.$p_table."\n".
			"FOR EACH ROW BEGIN\n".
			'  UPDATE '.$c_table.' SET '.$c_col.' = NULL WHERE '.$c_col.' = OLD.'.$p_col.";\n".
			"END;\n";
		}
		else # default on restrict
		{
			$this->x_stack[] =
			'CREATE TRIGGER burp_'.$name."\n".
			'BEFORE UPDATE ON '.$p_table."\n".
			"FOR EACH ROW BEGIN\n".
			'  SELECT RAISE (ROLLBACK,\'update on table "'.$p_table.'" violates foreign key constraint "'.$name.'"\')'."\n".
			'  WHERE (SELECT '.$c_col.' FROM '.$c_table.' WHERE '.$c_col.' = OLD.'.$p_col.") IS NOT NULL;\n".
			"END;\n";
		}
		
		# ON DELETE
		if ($delete == 'cascade')
		{
			$this->x_stack[] =
			'CREATE TRIGGER bdr_'.$name."\n".
			'BEFORE DELETE ON '.$p_table."\n".
			"FOR EACH ROW BEGIN\n".
			'  DELETE FROM '.$c_table.' WHERE '.$c_col.' = OLD.'.$p_col.";\n".
			"END;\n";
		}
		elseif ($delete == 'set null')
		{
			$this->x_stack[] =
			'CREATE TRIGGER bdr_'.$name."\n".
			'BEFORE DELETE ON '.$p_table."\n".
			"FOR EACH ROW BEGIN\n".
			'  UPDATE '.$c_table.' SET '.$c_col.' = NULL WHERE '.$c_col.' = OLD.'.$p_col.";\n".
			"END;\n";
		}
		else
		{
			$this->x_stack[] =
			'CREATE TRIGGER bdr_'.$name."\n".
			'BEFORE DELETE ON '.$p_table."\n".
			"FOR EACH ROW BEGIN\n".
			'  SELECT RAISE (ROLLBACK,\'delete on table "'.$p_table.'" violates foreign key constraint "'.$name.'"\')'."\n".
			'  WHERE (SELECT '.$c_col.' FROM '.$c_table.' WHERE '.$c_col.' = OLD.'.$p_col.") IS NOT NULL;\n".
			"END;\n";
		}
	}
	
	public function db_alter_field($table,$name,$type,$len,$null,$default)
	{
		$type = $this->udt2dbt($type,$len,$default);
		if ($type != 'integer' && $type != 'text' && $type != 'timestamp') {
			throw new Exception('SQLite fields cannot be changed.');
		}
	}
	
	public function db_alter_primary($table,$name,$newname,$cols)
	{
		throw new Exception('SQLite primary key cannot be changed.');
	}
	
	public function db_alter_unique($table,$name,$newname,$cols)
	{
		throw new Exception('SQLite unique index cannot be changed.');
	}
	
	public function db_alter_index($table,$name,$newname,$type,$cols)
	{
		$this->con->execute('DROP INDEX IF EXISTS '.$name);
		$this->con->execute('CREATE INDEX '.$newname.' ON '.$table.' ('.implode(',',$cols).') ');
	}
	
	public function db_alter_reference($name,$newname,$c_table,$c_cols,$p_table,$p_cols,$update,$delete)
	{
		$this->con->execute('DROP TRIGGER IF EXISTS bur_'.$name);
		$this->con->execute('DROP TRIGGER IF EXISTS burp_'.$name);
		$this->con->execute('DROP TRIGGER IF EXISTS bir_'.$name);
		$this->con->execute('DROP TRIGGER IF EXISTS aur_'.$name);
		$this->con->execute('DROP TRIGGER IF EXISTS bdr_'.$name);
		
		$this->table_hist[$c_table] = $this->db_get_columns($c_table);
		$this->db_create_reference($newname,$c_table,$c_cols,$p_table,$p_cols,$update,$delete);
	}
	
	public function db_drop_unique($table,$name)
	{
		throw new Exception('SQLite unique index cannot be removed.');
	}
}
?>