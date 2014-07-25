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
* PostgreSQL Database Driver
* 
* See the {@link dbLayer} documentation for common methods.
* 
* This class adds a method for PostgreSQL only: {@link callFunction()}.
*
* @package Clearbricks
* @subpackage DBLayer
*/
class pgsqlConnection extends dbLayer implements i_dbLayer
{
	/** @ignore */
	protected $__driver = 'pgsql';
	
	private function get_connection_string($host,$user,$password,$database)
	{
		$str = '';
		$port = false;
		
		if ($host)
		{
			if (strpos($host,':') !== false) {
				$bits = explode(':',$host);
				$host = array_shift($bits);
				$port = abs((integer) array_shift($bits));
			}
			$str .= "host = '".addslashes($host)."' ";
			
			if ($port) {
				$str .= 'port = '.$port.' ';
			}
		}
		if ($user) {
			$str .= "user = '".addslashes($user)."' ";
		}
		if ($password) {
			$str .= "password = '".addslashes($password)."' ";
		}
		if ($database) {
			$str .= "dbname = '".addslashes($database)."' ";
		}
		
		return $str;
	}
	
	/** @ignore */
	public function db_connect($host,$user,$password,$database)
	{
		if (!function_exists('pg_connect')) {
			throw new Exception('PHP PostgreSQL functions are not available');
		}
		
		$str = $this->get_connection_string($host,$user,$password,$database);
		
		if (($link = @pg_connect($str)) === false) {
			throw new Exception('Unable to connect to database');
		}
		
		return $link;
	}
	
	/** @ignore */
	public function db_pconnect($host,$user,$password,$database)
	{
		if (!function_exists('pg_pconnect')) {
			throw new Exception('PHP PostgreSQL functions are not available');
		}
		
		$str = $this->get_connection_string($host,$user,$password,$database);
		
		if (($link = @pg_pconnect($str)) === false) {
			throw new Exception('Unable to connect to database');
		}
		
		return $link;
	}
	
	/** @ignore */
	public function db_close($handle)
	{
		if (is_resource($handle)) {
			pg_close($handle);
		}
	}
	
	/** @ignore */
	public function db_version($handle)
	{
		if (is_resource($handle))
		{
			return pg_parameter_status($handle,'server_version');
		}
		return null;
	}
	
	/** @ignore */
	public function db_query($handle,$query)
	{
		if (is_resource($handle))
		{
			$res = @pg_query($handle,$query);
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
		if (is_resource($res)) {
			return pg_num_fields($res);
		}
		return 0;
	}
	
	/** @ignore */
	public function db_num_rows($res)
	{
		if (is_resource($res)) {
			return pg_num_rows($res);
		}
		return 0;
	}
	
	/** @ignore */
	public function db_field_name($res,$position)
	{
		if (is_resource($res)) {
			return pg_field_name($res,$position);
		}
	}
	
	/** @ignore */
	public function db_field_type($res,$position)
	{
		if (is_resource($res)) {
			return pg_field_type($res,$position);
		}
	}
	
	/** @ignore */
	public function db_fetch_assoc($res)
	{
		if (is_resource($res)) {
			return pg_fetch_assoc($res);
		}
	}
	
	/** @ignore */
	public function db_result_seek($res,$row)
	{
		if (is_resource($res)) {
			return pg_result_seek($res,(int) $row);
		}
		return false;
	}
	
	/** @ignore */
	public function db_changes($handle,$res)
	{
		if (is_resource($handle) && is_resource($res)) {
			return pg_affected_rows($res);
		}
	}
	
	/** @ignore */
	public function db_last_error($handle)
	{
		if (is_resource($handle)) {
			return pg_last_error($handle);
		}
		return false;
	}
	
	/** @ignore */
	public function db_escape_string($str,$handle=null)
	{
		return pg_escape_string($str);
	}
	
	/** @ignore */
	public function db_write_lock($table)
	{
		$this->execute('BEGIN');
		$this->execute('LOCK TABLE '.$this->escapeSystem($table).' IN EXCLUSIVE MODE');
	}
	
	/** @ignore */
	public function db_unlock()
	{
		$this->execute('END');
	}
	
	/** @ignore */
	public function vacuum($table)
	{
		$this->execute('VACUUM FULL '.$this->escapeSystem($table));
	}
	
	/** @ignore */
	public function dateFormat($field,$pattern)
	{
		$rep = array(
			'%d' => 'DD',
			'%H' => 'HH24',
			'%M' => 'MI',
			'%m' => 'MM',
			'%S' => 'SS',
			'%Y' => 'YYYY'
		);
		
		$pattern = str_replace(array_keys($rep),array_values($rep),$pattern);
		
		return 'TO_CHAR('.$field.','."'".$this->escape($pattern)."') ";
	}
	
	/**
	* Function call
	*
	* Calls a PostgreSQL function an returns the result as a {@link record}.
	* After <var>$name</var>, you can add any parameters you want to append
	* them to the PostgreSQL function. You don't need to escape string in
	* arguments.
	* 
	* @param string	$name	Function name
	* @return	record
	*/
	public function callFunction($name)
	{
		$data = func_get_args();
		array_shift($data);
		
		foreach ($data as $k => $v)
		{
			if (is_null($v)) {
				$data[$k] = 'NULL';
			} elseif (is_string($v)) {
				$data[$k] = "'".$this->escape($v)."'";
			} elseif (is_array($v)) {
				$data[$k] = $v[0];
			} else {
				$data[$k] = $v;
			}
		}
		
		$req =
		'SELECT '.$name."(\n".
		implode(",\n",array_values($data)).
		"\n) ";
		
		return $this->select($req);
	}
}
?>