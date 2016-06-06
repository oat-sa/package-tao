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
# ***** BEGIN LICENSE BLOCK *****
# This file is part of "ClearFw".
# Copyright (c) 2007 CRP Henri Tudor and contributors.
# All rights reserved.
#
# "ClearFw" is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License version 2 as published by
# the Free Software Foundation.
# 
# "ClearFw" is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with "ClearFw"; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****

/**
 *
 */
class HttpRequest {

	/**
	 * Url
	 */
	protected $url;

	/**
	 * Module = classe name
	 */
	protected $module;

	/**
	 * Action = method name
	 */
	protected $action;

	/**
	 * Arguments
	 */
	protected $args;

	/**
	 * Error class
	 */
	protected $error;

	/**
	 * Constructor
	 */
    function __construct() {
    	$this->url			= "";
    	$this->module		= null;
    	$this->action		= null;
    	$this->args			= array();

    	$this->error		= CfwError::getInstance();

    	$this->addGetParameters();
    	$this->addPostParameters();
    	$this->parseUrl();
    }

    /**
     * Return module name
     * @return	string		Module
     */
    function getModule() {
    	return $this->module;
    }

    /**
     * Return action name
     * @return	string		Action
     */
    function getAction() {
    	return $this->action;
    }
    
    function setModule($moduleName)
    {
    	$this->module = $moduleName;
    }
    
    function setAction($actionName)
    {
    	$this->action = $actionName;
    }

    /**
     * Return arguments
     * @return	array		Arguments
     */
    function getArgs() {
    	return $this->args;
    }

    /**
     * Return an argument
     * @param	string		$pKey		Argument name
     * @return	string		Argument value
     */
    function getArgument($pKey) {
    	return isset($this->args[$pKey])?$this->args[$pKey]:null;
    }

    /**
     * Returns the url path
     * ie if url = http://localhost/palette_cma/index.php/essai/test
     * then return palette_cma/
     * @return	string		url without index.php/.../... and without
     */
    public static function getPathUrl($removetest=0) {
    	$index = '';
		if (defined("INDEX_FILE")) {
			$index = INDEX_FILE;
		} else {
			$index = 'index.php';
		}
    	$urlCurrent		= $_SERVER["REQUEST_URI"];

    	// added for test environment on 07/06/08
    	if ($removetest==1) {
    		$urlCurrent = str_replace("/tests/","/",$urlCurrent);
    	}
    	// end of adding

    	// no /index.php
    	if (($pos = strpos($urlCurrent, "/".$index)) !== false) {
    		$urlCurrent 		= substr($urlCurrent, 0, $pos);
    	}
    	// no /testIndex.php
    	if (($pos = strpos($urlCurrent, "/testIndex.php")) !== false) {
    		$urlCurrent 		= substr($urlCurrent, 0, $pos);
    	}

    	return $urlCurrent;
    }

    /**
     *
     */
    protected function parseUrl() {
    	$index = '';
		if (defined("INDEX_FILE")) {
			$index = INDEX_FILE;
		} else {
			$index = 'index.php';
		}
    	$this->url	= $_SERVER["REQUEST_URI"];

    	// no /index.php
    	if (($pos = strpos($this->url, "/".$index)) === false) {
    		return;
    	}
    	$clean 		= substr($this->url, $pos + strlen($index) +2);

    	// no action; no module but arguments
    	if (isset($this->url[$pos+strlen($index)+1]) && $this->url[$pos+strlen($index)+1] == "?") {
    		return;
    	}

    	$cut		= explode("/", $clean);

    	if (isset($cut[0]) && $cut[0] != "") {
    		$this->module		= $cut[0];

    		if (isset($cut[1])) {
    			$actionCut		= explode("?", $cut[1]);
    			$this->action	= (isset($actionCut[0]) && ($actionCut[0] != ""))?$actionCut[0]:null;
    		}
    	}
    }

    /**
     * Add $_GET parameters
     */
    protected function addGetParameters() {
    	foreach($_GET as $cle => $value) {
    		$this->args[$cle]	= $this->error->secure($value, $cle);
    	}
    }

    /**
     * Add $_POST parameters
     */
    protected function addPostParameters() {
    	foreach($_POST as $cle => $value) {
    		$this->args[$cle]	= $this->error->secure($value, $cle);
    	}
    }
    
    public static function getBaseUrl() {
    	$index = '';
		if (defined("INDEX_FILE")) {
			$index = INDEX_FILE;
		} else {
			$index = $_SERVER['SCRIPT_NAME'];    	
    	}
    	return 'http://'.$_SERVER['HTTP_HOST'].'/'.$index;	
    
    }
    
    public function addParameter($key, $value) {
    		$this->args[$key]	= $value;
    }
}
?>