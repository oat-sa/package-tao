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
 * Default Front Controller
 * @author Luc Dehand
 */
class DefaultFC implements FrontController {

	/**
	 *
	 */
	 protected $httpRequest;

	 /**
	  * All plugin paths
	  */
	 private $allPaths;

	/**
	 * Constructor
	 * @param	HttpRequest		$pHttpRequest
	 */
    function __construct( HttpRequest $pHttpRequest ) {
    	$this->httpRequest	= $pHttpRequest;
    	$this->getAllPaths();
    }

    /**
     * Load module
     */
    function loadModule() {
    	
    	
      	$action	= $this->httpRequest->getAction();
    	$module	= $this->httpRequest->getModule();

    	# load default module
    	if ($module === null && $action === null) {
    		$defaut	= new Defaut();
    		$defaut->index();
    		exit();
    	}
    	
		$action = Camelizer::firstToUpper($action);
		$module = Camelizer::firstToUpper($module);

    	// if module exist include the class
    	if ($module !== null) {
    		if (($path = $this->getPath($module)) !== null) {
    			require_once $path. $module . ".class.php";
    		} else {
    			throw new Exception(__("No file found"));
    		}
    		$moduleController	= new $module();
    	} else {
    		throw new Exception(__("No module"));
    	}

    	// if method exist call it
    	if (method_exists($moduleController, $action)) {
    		// search parameters method
    		$reflect	= new ReflectionMethod($module, $action);
    		$parameters	= $reflect->getParameters();

    		$tabParam 	= array();
    		foreach($parameters as $param) {
    			$tabParam[$param->getName()] 	= $this->httpRequest->getArgument($param->getName());
    		}

    		call_user_func_array(array(new $module, $action), $tabParam);
    	} else {
    		throw new Exception(__("No action"));
    	}
    }

    /**
     * Search all plugins path
     */
    private function getAllPaths() {
    	$this->allPaths		= array();
    	$this->allPaths[]	= DIR_ACTIONS;

    	// search all plugins
    	if(defined('DIR_PLUGINS')){
	    	if (is_dir(DIR_PLUGINS)) {
	    		$dir	= opendir(DIR_PLUGINS);
	    		while (($f = readdir($dir)) !== false) {
		    		if ($f != "." && $f != ".." && $f != ".svn") {
	    				$this->allPaths[]			= DIR_PLUGINS. $f."/";
						$GLOBALS['classesPath'][] 	=  DIR_PLUGINS. $f."/";
	    			}
	    		}
	    		closedir($dir);
	    	}
    	}
    }

    /**
     * Return module path
     * @param	string	$pModule		Module name
     * @return	string	module path
     */
    protected function getPath($pModule) {
    	$pathModule		= null;

    	foreach($this->allPaths as $path) {
    		if (file_exists($path. $pModule. ".class.php")) {
    			$pathModule	= $path;
    			break;
    		}
    	}

    	return $pathModule;
    }

    /**
     * Return tpl in the current view. If not exists, return default view
     * @param	string		$pView				View - tpl file
     * @return	string							Tpl file path
     */
    static function getView($pView) {
    	if (is_file(DIR_VIEWS . 'templates' .$pView)) {
    		return DIR_VIEWS . 'templates' .$pView;
    	} else if (is_file(DIR_VIEWS . "default/" .$pView)) {
    		return DIR_VIEWS . "default/" .$pView;
    	} else {
    		throw new Exception(__("Error load view : ". $pView));
    	}
    }

    /**
     *
     */
    static function redirection($pRedirection = "", $pSauvegarde = true) {
		$index = '';
		if (defined("INDEX_FILE")) {
			$index = INDEX_FILE;
		} else {
			$index = 'index.php';
		}

    	$url	= HttpRequest::getPathUrl();
    	$nb		= strlen($url);

    	# save actual path
    	if ($pSauvegarde) {
    		$_SESSION['originUrl']	= "http://". $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    	}

    	# FIXME surement � am�liorer
    	if ($pRedirection != "" && substr($pRedirection, 0, 7) == "http://") {
    		header("Location: ". $pRedirection);
    	} else if ($nb == 0 || $url[$nb-1] != "/") {
    		header("Location: http://".$_SERVER['HTTP_HOST'].$url. "/".$index."/". $pRedirection);
    	} else {
    		header("Location: http://".$_SERVER['HTTP_HOST'].$url. $index."/". $pRedirection);
    	}
    	exit(0);
    }
}
?>
