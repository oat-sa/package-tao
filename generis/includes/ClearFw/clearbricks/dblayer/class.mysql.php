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
* MySQL Database Driver
* 
* See the {@link dbLayer} documentation for common methods.
* 
* @package Clearbricks
* @subpackage DBLayer
*/
class mysqlConnection extends dbLayer implements i_dbLayer
{
	/** @var boolean	Enables weak locks if true */
	public static $weak_locks = false;
	
	/** @ignore */
	protected $__driver = 'mysql';
	
	/** @ignore */
	public function db_connect($host,$user,$password,$database)
	{
		if (!function_exists('mysql_connect')) {
			throw new Exception('PHP MySQL functions are not available');
		}
		
		if (($link = @mysql_connect($host,$user,$password,true)) === false) {
			throw new Exception('Unable to connect to database');
		}
		
		$this->db_post_connect($link,$database);
		
		return $link;
	}
	
	/** @ignore */
	public function db_pconnect($host,$user,$password,$database)
	{
		if (!function_exists('mysql_pconnect')) {
			throw new Exception('PHP MySQL functions are not available');
		}
		
		if (($link = @mysql_pconnect($host,$user,$password)) === false) {
			throw new Exception('Unable to connect to database');
		}
		
		$this->db_post_connect($link,$database);
		
		return $link;
	}
	
	/** @ignore */
	private function db_post_connect($link,$database)
	{
		if (@mysql_select_db($database,$link) === false) {
			throw new Exception('Unable to use database '.$database);
		}
		
		if (version_compare($this->db_version($link),'4.1','>='))
		{
			$this->db_query($link,'SET NAMES utf8');
			$this->db_query($link,'SET CHARACTER SET utf8');
			$this->db_query($link,"SET COLLATION_CONNECTION = 'utf8_general_ci'");
			$this->db_query($link,"SET COLLATION_SERVER = 'utf8_general_ci'");
			$this->db_query($link,"SET CHARACTER_SET_SERVER = 'utf8'");
			$this->db_query($link,"SET CHARACTER_SET_DATABASE = 'utf8'");
		}
	}
	
	/** @ignore */
	public function db_close($handle)
	{
		if (is_resource($handle)) {
			mysql_close($handle);
		}
	}
	
	/** @ignore */
	public function db_version($handle)
	{
		if (is_resource($handle)) {
			return mysql_get_server_info();
		}
		return null;
	}
	
	/** @ignore */
	public function db_query($handle,$query)
	{
		if (is_resource($handle))
		{
			$res = @mysql_query($query,$handle);
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
			return mysql_num_fields($res);
		}
		return 0;
	}
	
	/** @ignore */
	public function db_num_rows($res)
	{
		if (is_resource($res)) {
			return mysql_num_rows($res);
		}
		return 0;
	}
	
	/** @ignore */
	public function db_field_name($res,$position)
	{
		if (is_resource($res)) {
			return mysql_field_name($res,$position);
		}
	}
	
	/** @ignore */
	public function db_field_type($res,$position)
	{
		if (is_resource($res)) {
			return mysql_field_type($res,$position);
		}
	}
	
	/** @ignore */
	public function db_fetch_assoc($res)
	{
		if (is_resource($res)) {
			return mysql_fetch_assoc($res);
		}
	}
	
	/** @ignore */
	public function db_result_seek($res,$row)
	{
		if (is_resource($res)) {
			return mysql_data_seek($res,$row);
		}
	}
	
	/** @ignore */
	public function db_changes($handle,$res)
	{
		if (is_resource($handle)) {
			return mysql_affected_rows($handle);
		}
	}
	
	/** @ignore */
	public function db_last_error($handle)
	{
		if (is_resource($handle))
		{
			$e = mysql_error($handle);
			if ($e) {
				return $e.' ('.mysql_errno($handle).')';
			}
		}		
		return false;
	}
	
	/** @ignore */
	public function db_escape_string($str,$handle=null)
	{
		if (is_resource($handle)) {
			return mysql_real_escape_string($str,$handle);
		} else {
			return mysql_escape_string($str);
		}
	}
	
	/** @ignore */
	public function db_write_lock($table)
	{
		try {
			$this->execute('LOCK TABLES '.$this->escapeSystem($table).' WRITE');
		} catch (Exception $e) {
			# As lock is a privilege in MySQL, we can avoid errors with weak_locks static var
			if (!self::$weak_locks) {
				throw $e;
			}
		}
	}
	
	/** @ignore */
	public function db_unlock()
	{
		try {
			$this->execute('UNLOCK TABLES');
		} catch (Exception $e) {
			if (!self::$weak_locks) {
				throw $e;
			}
		}
	}
	
	/** @ignore */
	public function vacuum($table)
	{
		$this->execute('OPTIMIZE TABLE '.$this->escapeSystem($table));
	}
	
	/** @ignore */
	public function dateFormat($field,$pattern)
	{
		$pattern = str_replace('%M','%i',$pattern);
		
		return 'DATE_FORMAT('.$field.','."'".$this->escape($pattern)."') ";
	}
	
	/** @ignore */
	public function concat()
	{
		$args = func_get_args();
		return 'CONCAT('.implode(',',$args).')';
	}
	
	/** @ignore */
	public function escapeSystem($str)
	{
		return '`'.$str.'`';
	}
}
?>