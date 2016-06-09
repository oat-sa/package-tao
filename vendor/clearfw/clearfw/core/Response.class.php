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
 * Response class
 * TODO Response class documentation.
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class Response
{
	private $contentType;
	private $charset;
	
	public function __construct($contentType = 'text/html', $charset = 'UTF-8')
	{
		$this->changeHeader($contentType, $charset);
		
		$this->contentType = $contentType;
		$this->charset = $charset;
	}
	
	public function setCookie($name, $value = null, $expire = null, 
							  $domainPath = null, $https = null, $httpOnly = null)
	{
		setcookie($name, $value, $expire, $domainPath, $https, $httpOnly);
	}
	
	public function setContentHeader($contentType, $charset = 'UTF-8')
	{
		$this->changeHeader($contentType, $charset);
		$this->contentType = $contentType;
		$this->charset = $charset;
	}
	
	public function getContentType()
	{
		return $this->contentType;
	}
	
	public function getCharset()
	{
		return $this->charset;
	}
	
	private function changeHeader($contentType, $charset)
	{
		header('Content-Type: ' . $contentType . '; charset=' . $charset, true);
	}
}
?>