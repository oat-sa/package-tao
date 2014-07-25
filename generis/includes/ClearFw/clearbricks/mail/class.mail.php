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
* Email utilities
*
* @package Clearbricks
* @subpackage Mail
*/
class mail
{
	/**
	* Send email
	*
	* Sends email to destination. If a function called _mail() exists it will 
	* be used instead of PHP mail() function. _mail() function should have the
	* same signature. Headers could be provided as a string or an array.
	*
	* @param string		$to			Email destination
	* @param string		$subject		Email subject
	* @param string		$message		Email message
	* @param string|array	$headers		Email headers
	* @param string		$p			UNIX mail additionnal parameters
	* @return boolean					true on success
	*/
	public static function sendMail($to,$subject,$message,$headers=null,$p=null)
	{
		$f = function_exists('_mail') ? '_mail' : null;
		$eol = trim(ini_get('sendmail_path')) ? "\n" : "\r\n";
		
		if (is_array($headers)) {
			$headers = implode($eol,$headers);
		}
		
		if ($f == null)
		{
			if (!@mail($to,$subject,$message,$headers,$p)) {
				throw new Exception('Unable to send email');
			}
		}
		else
		{
			call_user_func($f,$to,$subject,$message,$headers,$p);
		}
		
		return true;
	}
	
	/**
	* Get Host MX
	*
	* Returns MX records sorted by weight for a given host.
	*
	* @param string	$host		Hostname
	* @return array
	*/
	public static function getMX($host)
	{
		if (!getmxrr($host,$mx_h,$mx_w) || count($mx_h) == 0) {
			return false;
		}
		
		$res = array();
		
		for ($i=0; $i<count($mx_h); $i++) {
			$res[$mx_h[$i]] = $mx_w[$i];
		}
		
		asort($res);
		
		return $res;
	}
	
	/**
	* Quoted printable header
	*
	* Encodes given string as a quoted printable mail header.
	*
	* @param string	$str			String to encode
	* @return string
	*/
	public static function QPHeader($str,$charset='UTF-8')
	{
		if (!preg_match('/[^\x00-\x3C\x3E-\x7E]/',$str)) {
			return $str;
		}
		
		return '=?'.$charset.'?Q?'.text::QPEncode($str).'?=';
	}
	
	/**
	* B64 header
	*
	* Encodes given string as a base64 mail header.
	*
	* @param string	$str			String to encode
	* @return string
	*/
	public static function B64Header($str,$charset='UTF-8')
	{
		if (!preg_match('/[^\x00-\x3C\x3E-\x7E]/',$str)) {
			return $str;
		}
		
		return '=?'.$charset.'?B?'.base64_encode($str).'?=';
	}
}
?>