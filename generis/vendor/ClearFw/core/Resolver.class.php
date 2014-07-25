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
 * This class resolve data containing into a specific URL
 *
 * @author Eric Montecalvo <eric.montecalvo@tudor.lu> <eric.mtc@gmail.com>
 */
class Resolver {

	/**
	 * @var String The Url requested
	 */
	protected $url;

	/**
	 * @var Sring The extension (extension name) requested
	 */
	protected $extension;

	/**
	 * @var Sring The module (classe name) requested
	 */
	protected $module;

	/**
	 * @var String The action (method name) requested
	 */
	protected $action;

	/**
	 * The constructor
	 */
    public function __construct($url = null) {
    	$this->url = is_null($url) ? $_SERVER['REQUEST_URI'] : $url;

    	$this->module		= null;
    	$this->action		= null;
    	
		# Now resolve the Url
    	$this->resolveRequest($this->url);
    }

    /**
     * @return	String The module name
     */
    public function getExtensionFromURL() {
    	return $this->extension;
    }

	/**
     * @return	String The module name
     */
    public function getModule() {
    	$defaultModuleName = 'Main';
    	
    	if (defined('DEFAULT_MODULE_NAME')){
    		$defaultModuleName = DEFAULT_MODULE_NAME;
    	}
    	
    	return is_null($this->module) ? $defaultModuleName : $this->module;
    }

    /**
     * Return action name
     * @return String The action name
     */
    public function getAction() {
    	$defaultActionName = 'index';
    	
    	if (defined('DEFAULT_ACTION_NAME')){
    		$defaultActionName = DEFAULT_ACTION_NAME;
    	}
    	
    	return is_null($this->action) ? $defaultActionName : $this->action;
    }
    
    /**
     * Returns the current relative call url
     *
     * @throws ResolverException
     * @return string
     */
    public function getRelativeUrl() {
        $request = $this->url;
        $rootUrlPath	= parse_url(ROOT_URL, PHP_URL_PATH);
        $absPath		= parse_url($request, PHP_URL_PATH);
        if (substr($absPath, 0, strlen($rootUrlPath)) != $rootUrlPath ) {
            throw new ResolverException('Request Uri '.$request.' outside of TAO path '.ROOT_URL);
        }
        return substr($absPath, strlen($rootUrlPath));
    }

	/**
	 * Parse the framework-object requested into the URL
	 *
	 * @param String $request A sub part of the requested URL
	 */
	protected function resolveRequest($request){
		$relPath		= ltrim($this->getRelativeUrl(), '/');
		$tab = explode('/', $relPath);
		
		if (count($tab) > 0) {
			$this->extension	= $tab[0];
			$this->module		= isset($tab[1]) && !empty($tab[1]) ? $tab[1] : null;
			$this->action		= isset($tab[2]) && !empty($tab[2]) ? $tab[2] : null;
		} else {
			throw new ResolverException('Empty request Uri '.$request.' reached resolver');
		}
	}
}