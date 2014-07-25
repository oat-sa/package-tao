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
* Send email through socket
*
* @package Clearbricks
* @subpackage Mail
*/
class socketMail
{
	/** @ignore */
	public static $fp;
	
	/** @var integer	Socket timeout */
	public static $timeout = 10;
	
	/** @var string	SMTP Relay to user */
	public static $smtp_relay = null;
	
	/**
	* Send email through socket
	*
	* This static method sends an email through a simple socket connection.
	* If {@link $smtp_relay} is set, it will be used as a relay to send the
	* email. Instead, email is sent directly to MX host of domain.
	*
	* @param string		$to			Email destination
	* @param string		$subject		Email subject
	* @param string		$message		Email message
	* @param string|array	$headers		Email headers
	* @throws Exception
	*/
	public static function mail($to,$subject,$message,$headers=null)
	{
		$from = self::getFrom($headers);
		
		$H = 'Return-Path: <'.$from.">\r\n";
		
		$from_host = explode('@',$from);
		$from_host = $from_host[1];
		
		$to_host = explode('@',$to);
		$to_host = $to_host[1];
		
		if (self::$smtp_relay != null) {
			$mx = array(gethostbyname(self::$smtp_relay) => 1);
		} else {
			$mx = mail::getMX($to_host);
		}
		
		foreach ($mx as $h => $w)
		{
			self::$fp = @fsockopen($h,25,$errno,$errstr,self::$timeout);
			
			if (self::$fp !== false) {
				break;
			}
		}
		
		if (!is_resource(self::$fp)) {
			self::$fp = null;
			throw new Exception('Unable to open socket');
		}
		
		# We need to read the first line
		fgets(self::$fp);
		
		$data = '';
		# HELO cmd
		if (!self::cmd('HELO '.$from_host,$data)) {
			self::quit();
			throw new Exception($data);
		}
		
		# MAIL FROM: <...>
		if (!self::cmd('MAIL FROM: <'.$from.'>',$data)) {
			self::quit();
			throw new Exception($data);
		}
		
		# RCPT TO: <...>
		if (!self::cmd('RCPT TO: <'.$to.'>',$data)) {
			self::quit();
			throw new Exception($data);
		}
		
		# Compose mail and send it with DATA
		$H = 'Return-Path: <'.$from.">\r\n";
		$H .= 'To: <'.$to.">\r\n";
		$H .= 'Subject: '.$subject."\r\n";
		$H .= $headers."\r\n";
		
		$message = $H."\r\n\r\n".$message;
		
		if (!self::sendMessage($message,$data)) {
			self::quit();
			throw new Exception($data);
		}
		
		
		self::quit();
	}
	
	private static function getFrom($headers)
	{
		$f = '';
		
		if (preg_match('/^from: (.+?)$/msi',$headers,$m)) {
			$f = trim($m[1]);
		}
		
		if (preg_match('/(?:<)(.+?)(?:$|>)/si',$f,$m)) {
			$f = trim($m[1]);
		} elseif (preg_match('/^(.+?)\(/si',$f,$m)) {
			$f = trim($m[1]);
		} elseif (!text::isEmail($f)) {
			$f = trim(ini_get('sendmail_from'));
		}
		
		if (!$f) {
			throw new Exception('No valid from e-mail address');
		}
		
		return $f;
	}
	
	private static function cmd($out,&$data='')
	{
		fwrite(self::$fp,$out."\r\n");
		$data = self::data();
		
		if (substr($data,0,3) != '250') {
			return false;
		}
		
		return true;
	}
	
	private static function data()
	{
		$s='';
		stream_set_timeout(self::$fp, 2);
		
		for($i=0;$i<2;$i++) {
			$s .= fgets(self::$fp, 1024);
		}
		
		return $s;
	}
	
	private static function sendMessage($msg,&$data)
	{
		$msg .= "\r\n.";
		
		self::cmd('DATA',$data);
		
		if (substr($data,0,3) != '354') {
			return false;
		}
		
		return self::cmd($msg,$data);
	}
	
	private static function quit()
	{
		self::cmd('QUIT');
		fclose(self::$fp);
		self::$fp = null;
	}
}
?>