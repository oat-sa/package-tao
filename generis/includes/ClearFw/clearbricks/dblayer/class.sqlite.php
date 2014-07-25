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
* SQLite Database Driver
* 
* See the {@link dbLayer} documentation for common methods.
* 
* @package Clearbricks
* @subpackage DBLayer
*/
class sqliteConnection extends dbLayer implements i_dbLayer
{
	/** @ignore */
	protected $__driver = 'sqlite';
	
	/** @ignore */
	public function db_connect($host,$user,$password,$database)
	{
		if (!class_exists('PDO') || !in_array('sqlite',PDO::getAvailableDrivers())) {
			throw new Exception('PDO SQLite class is not available');
		}
		
		$link = new PDO('sqlite:'.$database);
		$this->db_post_connect($link,$database);
		
		return $link;
	}
	
	/** @ignore */
	public function db_pconnect($host,$user,$password,$database)
	{
		if (!class_exists('PDO') || !in_array('sqlite',PDO::getAvailableDrivers())) {
			throw new Exception('PDO SQLite class is not available');
		}
		
		$link = new PDO('sqlite:'.$database,null,null,array(PDO::ATTR_PERSISTENT => true));
		$this->db_post_connect($link,$database);
		
		return $link;
	}
	
	/** @ignore */
	private function db_post_connect($handle,$database)
	{
		if ($handle instanceof PDO) {
			$this->db_exec($handle,'PRAGMA short_column_names = 1');
			$this->db_exec($handle,'PRAGMA encoding = "UTF-8"');
			$handle->sqliteCreateFunction('now',array($this,'now'),0);
		}
	}
	
	/** @ignore */
	public function db_close($handle)
	{
		if ($handle instanceof PDO) {
			$handle = null;
			$this->__link = null;
		}
	}
	
	/** @ignore */
	public function db_version($handle)
	{
		if ($handle instanceof PDO) {
			return $handle->getAttribute(PDO::ATTR_SERVER_VERSION);
		}
	}
	
	# There is no other way than get all selected data in a staticRecord
	/** @ignore */
	public function select($sql)
	{
		$result = $this->db_query($this->__link,$sql);
		$this->__last_result =& $result;
		
		$info = array();
		$info['con'] =& $this;
		$info['cols'] = $this->db_num_fields($result);
		$info['info'] = array();
		
		for ($i=0; $i<$info['cols']; $i++) {
			$info['info']['name'][] = $this->db_field_name($result,$i);
			$info['info']['type'][] = $this->db_field_type($result,$i);
		}
		
		$data = array();
		while ($r = $result->fetch(PDO::FETCH_ASSOC))
		{
			$R = array();
			foreach ($r as $k => $v) {
				$k = preg_replace('/^(.*)\./','',$k);
				$R[$k] = $v;
				$R[] =& $R[$k];
			}
			$data[] = $R;
		}
		
		$info['rows'] = count($data);
		$result->closeCursor();
		
		return new staticRecord($data,$info);
	}
	
	/** @ignore */
	public function db_query($handle,$query)
	{
		if ($handle instanceof PDO)
		{
			$res = $handle->query($query);
			if ($res === false) {
				$e = new Exception($this->db_last_error($handle));
				$e->sql = $query;
				throw $e;
			}
			
			return $res;
		}
	}
	
	/** @ignore */
	public function db_exec($handle,$query)
	{
		return $this->db_query($handle,$query);
	}
	
	/** @ignore */
	public function db_num_fields($res)
	{
		if ($res instanceof PDOStatement) {
			return $res->columnCount();
		}
		return 0;
	}
	
	/** @ignore */
	public function db_num_rows($res)
	{
	}
	
	/** @ignore */
	public function db_field_name($res,$position)
	{
		if ($res instanceof PDOStatement) {
			$m = $res->getColumnMeta($position);
			return preg_replace('/^.+\./','',$m['name']); # we said short_column_names = 1
		}
	}
	
	/** @ignore */
	public function db_field_type($res,$position)
	{
		if ($res instanceof PDOStatement) {
			$m = $res->getColumnMeta($position);
			switch ($m['pdo_type']) {
				case PDO::PARAM_BOOL:
					return 'boolean';
				case PDO::PARAM_NULL:
					return 'null';
				case PDO::PARAM_INT:
					return 'integer';
				default:
					return 'varchar';
			}
		}
	}
	
	/** @ignore */
	public function db_fetch_assoc($res)
	{
	}
	
	/** @ignore */
	public function db_result_seek($res,$row)
	{
	}
	
	/** @ignore */
	public function db_changes($handle,$res)
	{
		if ($res instanceof PDOStatement) {
			return $res->rowCount();
		}
	}
	
	/** @ignore */
	public function db_last_error($handle)
	{
		if ($handle instanceof PDO) {
			$err = $handle->errorInfo();
			return $err[2].' ('.$err[1].')';
		}
		return false;
	}
	
	/** @ignore */
	public function db_escape_string($str,$handle=null)
	{
		if ($handle instanceof PDO) {
			return trim($handle->quote($str),"'");
		}
		return $str;
	}
	
	/** @ignore */
	public function escapeSystem($str)
	{
		return "'".$this->escape($str)."'";
	}
	
	/** @ignore */
	public function begin()
	{
		if ($this->__link instanceof PDO) {
			$this->__link->beginTransaction();
		}
	}
	
	/** @ignore */
	public function commit()
	{
		if ($this->__link instanceof PDO) {
			$this->__link->commit();
		}
	}
	
	/** @ignore */
	public function rollback()
	{
		if ($this->__link instanceof PDO) {
			$this->__link->rollBack();
		}
	}
	
	/** @ignore */
	public function db_write_lock($table)
	{
		$this->execute('BEGIN EXCLUSIVE TRANSACTION');
	}
	
	/** @ignore */
	public function db_unlock()
	{
		$this->execute('END');
	}
	
	/** @ignore */
	public function vacuum($table)
	{
		$this->execute('VACUUM '.$this->escapeSystem($table));
	}
	
	/** @ignore */
	public function dateFormat($field,$pattern)
	{
		return "strftime('".$this->escape($pattern)."',".$field.') ';
	}
	
	# Internal SQLite function that adds NOW() SQL function.
	/** @ignore */
	public function now()
	{
		return date('Y-m-d H:i:s');
	}
}
?>