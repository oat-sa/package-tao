<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2006-2009 (original work) Public Research Centre Henri Tudor (under the project FP6-IST-PALETTE);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
/**
 * PHPSession class
 * 
 * manages access to the php user session
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class PHPSession
{
	private static $instance = null;
	
	/**
	 * Singleton implementation
	 * @return PHPSession
	 */
	public static function singleton() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * private constructor called by PHPSession::singleton()
	 */
	private function __construct()
	{
		if (!isset($_SESSION[SESSION_NAMESPACE])) {
			$_SESSION[SESSION_NAMESPACE] = array();
		}
	}
	
	/**
	 * returns whenever or not an attribute is set in the current session
	 * 
	 * @param string $name identifier of the attribute
	 * @return boolean
	 */
	public function hasAttribute($name)
	{
		return isset($_SESSION[SESSION_NAMESPACE][$name]);
	}
	
	/**
	 * returns the value of the attribute
	 * 
	 * @param string $name identifier of the attribute
	 * @return mixed
	 */
	public function getAttribute($name)
	{
		return $_SESSION[SESSION_NAMESPACE][$name];
	}
	
	/**
	 * stores an attribute in the php user session
	 * if the attribute has already been set it will be replaced
	 * 
	 * @param string $name identifier of the attribute
	 * @param mixed $value value to be stored
	 */
	public function setAttribute($name, $value)
	{
		$_SESSION[SESSION_NAMESPACE][$name] = $value;
	}
	
	/**
	 * removes an attribute
	 * 
	 * @param string $name identifier of the attribute
	 */
	public function removeAttribute($name)
	{
		if (isset($_SESSION[SESSION_NAMESPACE][$name])){
			unset($_SESSION[SESSION_NAMESPACE][$name]);
		}
	}
	
	/**
	 * returns the names of all set attributes
	 * 
	 * @return array array of strings identifing the names of the attributes currently set
	 */
	public function getAttributeNames()
	{
		return array_keys($_SESSION[SESSION_NAMESPACE]);
	}
	
	/**
	 * Clears either the current global user session or the TAO user session
	 * 
	 * @param boolean $global whenever or not to clear the entire php user session
	 */
	public function clear($global)
	{
		if ($global)
			session_unset();
		else
			$_SESSION[SESSION_NAMESPACE] = array();
	}
}
?>