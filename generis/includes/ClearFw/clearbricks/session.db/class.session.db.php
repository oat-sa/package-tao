<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of Clearbricks.
#
# Copyright (c) 2003-2009 Olivier Meunier and contributors
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------

/**
* Database Session Handler
*
* This class allows you to handle session data in database.
*
* @package Clearbricks
* @subpackage Session
*/
class sessionDB
{
	private $con;
	private $table;
	private $cookie_name;
	private $cookie_path;
	private $ttl = '-120 minutes';
	
	/**
	* Constructor
	*
	* This method creates an instance of sessionDB class.
	*
	* @param dbLayer	&$con		dbLayer inherited database instance
	* @param string	$table		Table name
	* @param string	$cookie_name	Session cookie name
	* @param string	$cookie_path	Session cookie path
	* @param string	$cookie_domain	Session cookie domaine
	* @param boolean	$cookie_secure	Session cookie is available only through SSL if true
	*/
	public function __construct(&$con,$table,$cookie_name,$cookie_path=null,$cookie_domain=null,$cookie_secure=false)
	{
		$this->con =& $con;
		$this->table = $table;
		$this->cookie_name = $cookie_name;
		$this->cookie_path = is_null($cookie_path) ? '/' : $cookie_path;
		$this->cookie_domain = $cookie_domain;
		$this->cookie_secure = $cookie_secure;
		
		if(function_exists('ini_set'))
		{
			@ini_set('session.use_cookies','1');
			@ini_set('session.use_only_cookies','1');
			@ini_set('url_rewriter.tags','');
			@ini_set('session.use_trans_sid','0');
			@ini_set('session.cookie_path',$this->cookie_path);
			@ini_set('session.cookie_domain',$this->cookie_domain);
			@ini_set('session.cookie_secure',$this->cookie_secure);
		}
	}
	
	/**
	* Destructor
	*
	* This method calls session_write_close PHP function.
	*/
	public function __destruct()
	{
		if (isset($_SESSION)) {
			session_write_close();
		}
	}
	
	/**
	* Session Start
	*/
	public function start()
	{
		session_set_save_handler(
			array(&$this, '_open'),
			array(&$this, '_close'),
			array(&$this, '_read'),
			array(&$this, '_write'),
			array(&$this, '_destroy'),
			array(&$this, '_gc')
		); 
		
		if (isset($_SESSION) && session_name() != $this->cookie_name) {
			$this->destroy();
		}
		
		if (!isset($_COOKIE[$this->cookie_name])) {
			session_id(sha1(uniqid(rand(),true)));
		}
		
		session_name($this->cookie_name);
		session_start();
	}
	
	/**
	* Session Destroy
	*
	* This method destroies all session data and removes cookie.
	*/
	public function destroy()
	{
		$_SESSION = array();
		session_unset();
		session_destroy();
		call_user_func_array('setcookie',$this->getCookieParameters(false,-600));
	}
	
	/**
	* Session Cookie
	*
	* This method returns an array of all session cookie parameters.
	*
	* @param mixed		$value		Cookie value
	* @param integer	$expire		Cookie expiration timestamp
	*/
	public function getCookieParameters($value=null,$expire=0)
	{
		return array(
			session_name(),
			$value,
			$expire,
			$this->cookie_path,
			$this->cookie_domain,
			$this->cookie_secure
		);
	}
	
	/** @ignore */
	public function _open($path,$name)
	{
		return true;
	}
	
	/** @ignore */
	public function _close()
	{
		$this->_gc();
		return true;
	}
	
	/** @ignore */
	public function _read($ses_id)
	{
		$strReq = 'SELECT ses_value FROM '.$this->table.' '.
				'WHERE ses_id = \''.$this->checkID($ses_id).'\' ';
		
		$rs = $this->con->select($strReq);
		
		if ($rs->isEmpty()) {
			return '';
		} else {
			return $rs->f('ses_value');
		}
	}
	
	/** @ignore */
	public function _write($ses_id, $data)
	{
		$strReq = 'SELECT ses_id '.
				'FROM '.$this->table.' '.
				"WHERE ses_id = '".$this->checkID($ses_id)."' ";
		
		$rs = $this->con->select($strReq);
		
		$cur = $this->con->openCursor($this->table);
		$cur->ses_time = (string) time();
		$cur->ses_value = (string) $data;
		
		if (!$rs->isEmpty())
		{
			$cur->update("WHERE ses_id = '".$this->checkID($ses_id)."' ");
		}
		else
		{
			$cur->ses_id = (string) $this->checkID($ses_id);
			$cur->ses_start = (string) time();
			
			$cur->insert();
		}
		
		return true;
	}
	
	/** @ignore */
	public function _destroy($ses_id)
	{
		$strReq = 'DELETE FROM '.$this->table.' '.
				'WHERE ses_id = \''.$this->checkID($ses_id).'\' ';
		
		$this->con->execute($strReq);
		
		$this->_optimize();
		return true;
	}
	
	/** @ignore */
	public function _gc()
	{
		$ses_life = strtotime($this->ttl);
		
		$strReq = 'DELETE FROM '.$this->table.' '.
				'WHERE ses_time < '.$ses_life.' ';
		
		
		$this->con->execute($strReq);
		
		if ($this->con->changes() > 0) {
			$this->_optimize();
		}
		return true;
	}
	
	private function _optimize()
	{
		$this->con->vacuum($this->table);
	}
	
	private function checkID($id)
	{
		if (!preg_match('/^([0-9a-f]{40})$/i',$id)) {
			return null;
		}
		return $id;
	}
}

?>