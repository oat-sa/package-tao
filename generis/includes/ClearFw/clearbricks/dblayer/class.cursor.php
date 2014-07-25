<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of Clearbricks.
#
# Copyright (c) 2003-2008 Olivier Meunier and contributors
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------

/**
* DBLayer Cursor
*
* This class implements facilities to insert or update in a table.
*
* @package Clearbricks
* @subpackage DBLayer
*/
class cursor
{
	private $__con;
	private $__data = array();
	private $__table;
	
	/**
	* Constructor
	*
	* Init cursor object on a given table. Note that you can init it with 
	* {@link dbLayer::openCursor() openCursor()} method of your connection object.
	*
	* Example:
	* <code>
	* <?php
	*	$cur = $con->openCursor('table');
	*	$cur->field1 = 1;
	*	$cur->field2 = 'foo';
	*	$cur->insert(); // Insert field ...
	*
	*	$cur->update('WHERE field3 = 4'); // ... or update field
	* ?>
	* </code>
	*
	* @see dbLayer::openCursor()
	* @param dbLayer	&$con		Connection object
	* @param string	$table	Table name
	*/
	public function __construct(&$con,$table)
	{
		$this->__con =& $con;
		$this->setTable($table);
	}
	
	/**
	* Set table
	*
	* Changes working table and resets data
	*
	* @param string	$table	Table name
	*/
	public function setTable($table)
	{
		$this->__table = $table;
		$this->__data = array();
	}
	
	/**
	* Set field
	*
	* Set value <var>$v</var> to a field named <var>$n</var>. Value could be
	* an string, an integer, a float, a null value or an array.
	*
	* If value is an array, its first value will be interpreted as a SQL
	* command. String values will be automatically escaped.
	*
	* @see __set()
	* @param string	$n		Field name
	* @param mixed		$v		Field value
	*/
	public function setField($n,$v)
	{
		$this->__data[$n] = $v;
	}
	
	/**
	* Unset field
	*
	* Remove a field from data set.
	*
	* @param string	$n		Field name
	*/
	public function unsetField($n)
	{
		unset($this->__data[$n]);
	}
	
	/**
	* Field exists
	*
	* @return boolean	true if field named <var>$n</var> exists
	*/
	public function isField($n)
	{
		return isset($this->__data[$n]);
	}
	
	/**
	* Field value
	*
	* @see __get()
	* @return mixed	value for a field named <var>$n</var>
	*/
	public function getField($n)
	{
		if (isset($this->__data[$n])) {
			return $this->__data[$n];
		}
		
		return null;
	}
	
	/**
	* Set Field
	*
	* Magic alias for {@link setField()}
	*/
	public function __set($n,$v)
	{
		$this->setField($n,$v);
	}
	
	/**
	* Field value
	*
	* Magic alias for {@link getField()}
	*
	* @return mixed	value for a field named <var>$n</var>
	*/
	public function __get($n)
	{
		return $this->getField($n);
	}
	
	/**
	* Empty data set
	*
	* Removes all data from data set
	*/
	public function clean()
	{
		$this->__data = array();
	}
	
	private function formatFields()
	{
		$data = array();
		
		foreach ($this->__data as $k => $v)
		{
			$k = $this->__con->escapeSystem($k);
			
			if (is_null($v)) {
				$data[$k] = 'NULL';
			} elseif (is_string($v)) {
				$data[$k] = "'".$this->__con->escape($v)."'";
			} elseif (is_array($v)) {
				$data[$k] = $v[0];
			} else {
				$data[$k] = $v;
			}
		}
		
		return $data;
	}
	
	/**
	* Get insert query
	*
	* Returns the generated INSERT query
	*
	* @return string
	*/
	public function getInsert()
	{
		$data = $this->formatFields();
		
		$insReq = 'INSERT INTO '.$this->__con->escapeSystem($this->__table)." (\n".
				implode(",\n",array_keys($data))."\n) VALUES (\n".
				implode(",\n",array_values($data))."\n) ";
		
		return $insReq;
	}
	
	/**
	* Get update query
	*
	* Returns the generated UPDATE query
	*
	* @param string	$where		WHERE condition
	* @return string
	*/
	public function getUpdate($where)
	{
		$data = $this->formatFields();
		$fields = array();
		
		$updReq = 'UPDATE '.$this->__con->escapeSystem($this->__table)." SET \n";
		
		foreach ($data as $k => $v) {
			$fields[] = $k.' = '.$v."";
		}
		
		$updReq .= implode(",\n",$fields);
		$updReq .= "\n".$where;
		
		return $updReq;
	}
	
	/**
	* Execute insert query
	*
	* Executes the generated INSERT query
	*/
	public function insert()
	{
		if (!$this->__table) {
			throw new Exception('No table name.');
		}
		
		$insReq = $this->getInsert();
		
		$this->__con->execute($insReq);
		
		return true;
	}
	
	/**
	* Execute update query
	*
	* Executes the generated UPDATE query
	*
	* @param string	$where		WHERE condition
	*/
	public function update($where)
	{
		if (!$this->__table) {
			throw new Exception('No table name.');
		}
		
		$updReq = $this->getUpdate($where);
		
		$this->__con->execute($updReq);
		
		return true;
	}
}
?>