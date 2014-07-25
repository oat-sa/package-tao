<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Clearbricks.
# Copyright (c) 2006 Olivier Meunier and contributors. All rights
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

class dbStruct
{
	protected $con;
	protected $prefix;
	protected $tables = array();
	protected $references = array();
	
	public function __construct(&$con,$prefix='')
	{
		$this->con =& $con;
		$this->prefix = $prefix;
	}
	
	public function driver()
	{
		return $this->con->driver();
	}
	
	public function table($name)
	{
		$this->tables[$name] = new dbStructTable($name);
		return $this->tables[$name];
	}
	
	public function __get($name)
	{
		if (!isset($this->tables[$name])) {
			return $this->table($name);
		}
		
		return $this->tables[$name];
	}
	
	public function reverse()
	{
		$schema = dbSchema::init($this->con);
		
		# Get tables
		$tables = $schema->getTables();
		
		foreach ($tables as $t_name)
		{
			if ($this->prefix && strpos($t_name,$this->prefix) !== 0) {
				continue;
			}
			
			$t = $this->table($t_name);
			
			# Get columns
			$cols = $schema->getColumns($t_name);
			
			foreach ($cols as $c_name => $col) {
				$type = $schema->dbt2udt($col['type'],$col['len'],$col['default']);
				$t->field($c_name,$type,$col['len'],$col['null'],$col['default'],true);
			}
			
			# Get keys
			$keys = $schema->getKeys($t_name);
			
			foreach ($keys as $k)
			{
				$args = $k['cols'];
				array_unshift($args,$k['name']);
				
				if ($k['primary']) {
					call_user_func_array(array($t,'primary'),$args);
				} elseif ($k['unique']) {
					call_user_func_array(array($t,'unique'),$args);
				}
			}
			
			# Get indexes
			$idx = $schema->getIndexes($t_name);
			foreach ($idx as $i)
			{
				$args = array($i['name'],$i['type']);
				$args = array_merge($args,$i['cols']);
					
				call_user_func_array(array($t,'index'),$args);
			}
			
			# Get foreign keys
			$ref = $schema->getReferences($t_name);
			foreach ($ref as $r) {
				$t->reference($r['name'],$r['c_cols'],$r['p_table'],$r['p_cols'],$r['update'],$r['delete']);
			}
		}
	}
	
	/**
	Synchronize this schema taken from database with $schema.
	
	@param	s		<b>dbStruct</b>		Structure to synchronize with
	*/
	public function synchronize($s)
	{
		$this->tables = array();
		$this->reverse();
		
		if (!($s instanceof self)) {
			throw new Exception('Invalid database schema');
		}
		
		$tables = $s->getTables();
		
		$table_create = array();
		$key_create = array();
		$index_create = array();
		$reference_create = array();
		
		$field_create = array();
		$field_update = array();
		$key_update = array();
		$index_update = array();
		$reference_update = array();
		
		$got_work = false;
		
		$schema = dbSchema::init($this->con);
		
		foreach ($tables as $tname => $t)
		{
			if (!$this->tableExists($tname))
			{
				# Table does not exist, create table
				$table_create[$tname] = $t->getFields();
				
				# Add keys, indexes and references
				$keys = $t->getKeys();
				$indexes = $t->getIndexes();
				$references = $t->getReferences();
				
				foreach ($keys as $k => $v) {
					$key_create[$tname][$this->prefix.$k] = $v;
				}
				foreach ($indexes as $k => $v) {
					$index_create[$tname][$this->prefix.$k] = $v;
				}
				foreach ($references as $k => $v) {
					$v['p_table'] = $this->prefix.$v['p_table'];
					$reference_create[$tname][$this->prefix.$k] = $v;
				}
				
				$got_work = true;
			}
			else # Table exists
			{
				# Check new fields to create
				$fields = $t->getFields();
				$db_fields = $this->tables[$tname]->getFields();
				foreach ($fields as $fname => $f)
				{
					if (!$this->tables[$tname]->fieldExists($fname))
					{
						# Field doest not exist, create it
						$field_create[$tname][$fname] = $f;
						$got_work = true;
					}
					elseif ($this->fieldsDiffer($db_fields[$fname],$f))
					{
						# Field exists and differs from db version
						$field_update[$tname][$fname] = $f;
						$got_work = true;
					}
				}
				
				# Check keys to add or upgrade
				$keys = $t->getKeys();
				$db_keys = $this->tables[$tname]->getKeys();
				
				foreach ($keys as $kname => $k)
				{
					if ($k['type'] == 'primary' && $this->con->driver() == 'mysql') {
						$kname = 'PRIMARY';
					} else {
						$kname = $this->prefix.$kname;
					}
					
					$db_kname = $this->tables[$tname]->keyExists($kname,$k['type'],$k['cols']);
					if (!$db_kname)
					{
						# Key does not exist, create it
						$key_create[$tname][$kname] = $k;
						$got_work = true;
					}
					elseif ($this->keysDiffer($db_kname,$db_keys[$db_kname]['cols'],$kname,$k['cols']))
					{
						# Key exists and differs from db version
						$key_update[$tname][$db_kname] = array_merge(array('name'=>$kname),$k);
						$got_work = true;
					}
				}
				
				# Check index to add or upgrade
				$idx = $t->getIndexes();
				$db_idx = $this->tables[$tname]->getIndexes();
				
				foreach ($idx as $iname => $i)
				{
					$iname = $this->prefix.$iname;
					$db_iname = $this->tables[$tname]->indexExists($iname,$i['type'],$i['cols']);
					
					if (!$db_iname)
					{
						# Index does not exist, create it
						$index_create[$tname][$iname] = $i;
						$got_work = true;
					}
					elseif ($this->indexesDiffer($db_iname,$db_idx[$db_iname],$iname,$i))
					{
						# Index exists and differs from db version
						$index_update[$tname][$db_iname] = array_merge(array('name'=>$iname),$i);
						$got_work = true;
					}
				}
				
				# Check references to add or upgrade
				$ref = $t->getReferences();
				$db_ref = $this->tables[$tname]->getReferences();
				
				foreach ($ref as $rname => $r)
				{
					$rname = $this->prefix.$rname;
					$r['p_table'] = $this->prefix.$r['p_table'];
					$db_rname = $this->tables[$tname]->referenceExists($rname,$r['c_cols'],$r['p_table'],$r['p_cols']);
					
					if (!$db_rname)
					{
						# Reference does not exist, create it
						$reference_create[$tname][$rname] = $r;
						$got_work = true;
					}
					elseif ($this->referencesDiffer($db_rname,$db_ref[$db_rname],$rname,$r))
					{
						$reference_update[$tname][$db_rname] = array_merge(array('name'=>$rname),$r);
						$got_work = true;
					}
				}
			}
		}
		
		if (!$got_work) {
			return;
		}
		
		# Create tables
		foreach ($table_create as $table => $fields)
		{
			$schema->createTable($table,$fields);
		}
		
		# Create new fields
		foreach ($field_create as $tname => $fields)
		{
			foreach ($fields as $fname => $f) {
				$schema->createField($tname,$fname,$f['type'],$f['len'],$f['null'],$f['default']);
			}
		}
		
		# Update fields
		foreach ($field_update as $tname => $fields)
		{
			foreach ($fields as $fname => $f) {
				$schema->alterField($tname,$fname,$f['type'],$f['len'],$f['null'],$f['default']);
			}
		}
		
		# Create new keys
		foreach ($key_create as $tname => $keys)
		{
			foreach ($keys as $kname => $k)
			{
				if ($k['type'] == 'primary') {
					$schema->createPrimary($tname,$kname,$k['cols']);
				} elseif ($k['type'] == 'unique') {
					$schema->createUnique($tname,$kname,$k['cols']);
				}
			}
		}
		
		# Update keys
		foreach ($key_update as $tname => $keys)
		{
			foreach ($keys as $kname => $k)
			{
				if ($k['type'] == 'primary') {
					$schema->alterPrimary($tname,$kname,$k['name'],$k['cols']);
				} elseif ($k['type'] == 'unique') {
					$schema->alterUnique($tname,$kname,$k['name'],$k['cols']);
				}
			}
		}
		
		# Create indexes
		foreach ($index_create as $tname => $index)
		{
			foreach ($index as $iname => $i) {
				$schema->createIndex($tname,$iname,$i['type'],$i['cols']);
			}
		}
		
		# Update indexes
		foreach ($index_update as $tname => $index)
		{
			foreach ($index as $iname => $i) {
				$schema->alterIndex($tname,$iname,$i['name'],$i['type'],$i['cols']);
			}
		}
		
		# Create references
		foreach ($reference_create as $tname => $ref)
		{
			foreach ($ref as $rname => $r)
			{
				$schema->createReference($rname,$tname,$r['c_cols'],$r['p_table'],$r['p_cols'],$r['update'],$r['delete']);
			}
		}
		
		# Update references
		foreach ($reference_update as $tname => $ref)
		{
			foreach ($ref as $rname => $r) {
				$schema->alterReference($rname,$r['name'],$tname,$r['c_cols'],$r['p_table'],$r['p_cols'],$r['update'],$r['delete']);
			}
		}
		
		# Flush execution stack
		$schema->flushStack();
		
		return
		count($table_create) + count($key_create) + count($index_create) +
		count($reference_create) + count($field_create) + count($field_update) +
		count($key_update) + count($index_update) + count($reference_update);
	}
	
	public function getTables()
	{
		$res = array();
		foreach ($this->tables as $t => $v)
		{
			$res[$this->prefix.$t] = $v;
		}
		
		return $res;
	}
	
	public function tableExists($name)
	{
		return isset($this->tables[$name]);
	}
	
	private function fieldsDiffer($db_field,$schema_field)
	{
		$d_type = $db_field['type'];
		$d_len = (integer) $db_field['len'];
		$d_default = $db_field['default'];
		$d_null = $db_field['null'];
		
		$s_type = $schema_field['type'];
		$s_len = (integer) $schema_field['len'];
		$s_default = $schema_field['default'];
		$s_null = $schema_field['null'];
		
		return $d_type != $s_type || $d_len != $s_len || $d_default != $s_default || $d_null != $s_null;
	}
	
	private function keysDiffer($d_name,$d_cols,$s_name,$s_cols)
	{
		return $d_name != $s_name || $d_cols != $s_cols;
	}
	
	private function indexesDiffer($d_name,$d_i,$s_name,$s_i)
	{
		return $d_name != $s_name || $d_i['cols'] != $s_i['cols'] || $d_i['type'] != $s_i['type']; 
	}
	
	private function referencesDiffer($d_name,$d_r,$s_name,$s_r)
	{
		return
		$d_name != $s_name || $d_r['c_cols'] != $s_r['c_cols']
		|| $d_r['p_table'] != $s_r['p_table'] || $d_r['p_cols'] != $s_r['p_cols']
		|| $d_r['update'] != $s_r['update'] || $d_r['delete'] != $s_r['delete'];
	}
}

class dbStructTable
{
	protected $name;
	protected $has_primary = false;
	
	protected $fields = array();
	protected $keys = array();
	protected $indexes = array();
	protected $references = array();
	
	/**
	Universal data types supported by dbSchema
	
	SMALLINT	: signed 2 bytes integer
	INTEGER	: signed 4 bytes integer
	BIGINT	: signed 8 bytes integer
	REAL		: signed 4 bytes floating point number
	FLOAT	: signed 8 bytes floating point number
	NUMERIC	: exact numeric type
	
	DATE		: Calendar date (day, month and year)
	TIME		: Time of day
	TIMESTAMP	: Date and time
	
	CHAR		: A fixed n-length character string
	VARCHAR	: A variable length character string
	TEXT		: A variable length of text
	*/
	protected $allowed_types = array(
		'smallint','integer','bigint','real','float','numeric',
		'date','time','timestamp',
		'char','varchar','text'
	);
	
	public function __construct($name)
	{
		$this->name = $name;
		return $this;
	}
	
	public function getFields()
	{
		return $this->fields;
	}
	
	public function getKeys($primary=null)
	{
		return $this->keys;
	}
	
	public function getIndexes()
	{
		return $this->indexes;
	}
	
	public function getReferences()
	{
		return $this->references;
	}
	
	public function fieldExists($name)
	{
		return isset($this->fields[$name]);
	}
	
	public function keyExists($name,$type,$cols)
	{
		# Look for key with the same name
		if (isset($this->keys[$name])) {
			return $name;
		}
		
		# Look for key with the same columns list and type
		foreach ($this->keys as $n => $k)
		{
			if ($k['cols'] == $cols && $k['type'] == $type) {
				# Same columns and type, return new name
				return $n;
			}
		}
		
		return false;
	}
	
	public function indexExists($name,$type,$cols)
	{
		# Look for key with the same name
		if (isset($this->indexes[$name])) {
			return $name;
		}
		
		# Look for index with the same columns list and type
		foreach ($this->indexes as $n => $i)
		{
			if ($i['cols'] == $cols && $i['type'] == $type) {
				# Same columns and type, return new name
				return $n;
			}
		}
		
		return false;
	}
	
	public function referenceExists($name,$c_cols,$p_table,$p_cols)
	{
		if (isset($this->references[$name])) {
			return $name;
		}
		
		# Look for reference with same chil columns, parent table and columns
		foreach ($this->references as $n => $r)
		{
			if ($c_cols == $r['c_cols'] && $p_table == $r['p_table'] && $p_cols == $r['p_cols']) {
				# Only name differs, return new name
				return $n;
			}
		}
		
		return false;
	}
	
	public function field($name,$type,$len,$null=true,$default=false,$to_null=false)
	{
		$type = strtolower($type);
		
		if (!in_array($type,$this->allowed_types))
		{
			if ($to_null) {
				$type = null;
			} else {
				throw new Exception('Invalid data type '.$type.' in schema');
			}
		}
		
		$this->fields[$name] = array(
			'type' => $type,
			'len' => (integer) $len,
			'default' => $default,
			'null' => (boolean) $null
		);
		
		return $this;
	}
	
	public function __call($name,$args)
	{
		array_unshift($args,$name);
		return call_user_func_array(array($this,'field'),$args);
	}
	
	public function primary($name,$col)
	{
		if ($this->has_primary) {
			throw new Exception(sprintf('Table %s already has a primary key',$this->name));
		}
		
		$cols = func_get_args();
		array_shift($cols);
		
		return $this->newKey('primary',$name,$cols);
	}
	
	public function unique($name,$col)
	{
		$cols = func_get_args();
		array_shift($cols);
		
		return $this->newKey('unique',$name,$cols);
	}
	
	public function index($name,$type,$col)
	{
		$cols = func_get_args();
		array_shift($cols);
		array_shift($cols);
		
		$this->checkCols($cols);
		
		$this->indexes[$name] = array(
			'type' => strtolower($type),
			'cols' => $cols
		);
		
		return $this;
	}
	
	public function reference($name,$c_cols,$p_table,$p_cols,$update=false,$delete=false)
	{
		if (!is_array($p_cols)) {
			$p_cols = array($p_cols);
		}
		if (!is_array($c_cols)) {
			$c_cols = array($c_cols);
		}
		
		$this->checkCols($c_cols);
		
		$this->references[$name] = array(
			'c_cols' => $c_cols,
			'p_table' => $p_table,
			'p_cols' => $p_cols,
			'update' => $update,
			'delete' => $delete
		);
	}
	
	protected function newKey($type,$name,$cols)
	{
		$this->checkCols($cols);
		
		$this->keys[$name] = array(
			'type' => $type,
			'cols' => $cols
		);
		
		if ($type == 'primary') {
			$this->has_primary = true;
		}
		
		return $this;
	}
	
	protected function checkCols($cols)
	{
		foreach ($cols as $v) {
			if (!preg_match('/^\(.*?\)$/',$v) && !isset($this->fields[$v])) {
				throw new Exception(sprintf('Field %s does not exist in table %s',$v,$this->name));
			}
		}
	}
}
?>