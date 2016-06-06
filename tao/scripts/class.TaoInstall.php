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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class tao_scripts_TaoInstall
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package tao
 
 */
class tao_scripts_TaoInstall
    extends tao_scripts_Runner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method preRun
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function preRun()
    {
        
        $root_path = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR;
        
    	$this->options = array (
			"db_driver"	=>			"mysql"
			, "db_host"	=>			"localhost"
			, "db_name"	=>			null
			, "db_pass"	=>			""
			, "db_user"	=>			"tao"
			, "install_sent"	=>	"1"
			, "module_host"	=>		"tao.local"
			, "module_lang"	=>		"en-US"
			, "module_mode"	=>		"debug"
			, "module_name"	=>		"mytao"
			, "module_namespace" =>	""
			, "module_url"	=>		""
			, "submit"	=>			"Install"
			, "user_email"	=>		""
			, "user_firstname"	=>	""	
			, "user_lastname"	=>	""
			, "user_login"	=>		""
			, "user_pass"	=>		""
			, "import_local" => 	true
			, "instance_name" =>	null
			, "extensions" =>		null
    	    , 'timezone'   =>      date_default_timezone_get()
    	    , "file_path" =>        $root_path.'data'.DIRECTORY_SEPARATOR
		);
    	
    	$this->options = array_merge($this->options, $this->parameters);
    	
    	// Feature #1789: default db_name is module_name if not specified.
        $this->options['db_name'] = (empty($this->options['db_name']) ? $this->options['module_name'] : $this->options['db_name']);
        
        // If no instance_name given, it takes the value of module_name.
        $this->options['instance_name'] = (empty($this->options['instance_name']) ? $this->options['module_name'] : $this->options['instance_name']);
        
    	// user password treatment
    	$this->options["user_pass1"] = $this->options['user_pass'];
    	// module namespace generation
    	if (empty ($this->options["module_namespace"])){
    		$this->options['module_namespace'] = 'http://'.$this->options['module_host'].'/'.$this->options['module_name'].'.rdf';
    	}
    	
    	if (empty ($this->options['module_url'])){
    		$this->options['module_url'] = 'http://' . $this->options['module_host'];
    	}
    	
        
    }

    /**
     * Short description of method run
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function run()
    {
        
        
    	$this->outVerbose("TAO is being installed. Please wait...");
    	try{
	        $rootDir = dir(dirname(__FILE__) . '/../../');
			$root = isset($this->parameters["root_path"])
                ? $this->parameters["root_path"]
                : realpath($rootDir->path) . DIRECTORY_SEPARATOR;
			
	        $installator = new tao_install_Installator (array(
				'root_path' 	=> $root,
				'install_path'	=> $root.'tao/install/'
			));
			
			// mod rewrite cannot be detected in CLI Mode.
			$installator->escapeCheck('custom_tao_ModRewrite');
			$installator->install($this->options);
    	}
    	catch (Exception $e){
    		$this->err("A fatal error occured during installation: " . $e->getMessage(), true);
    	}
		
        
    }

    /**
     * Short description of method postRun
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function postRun()
    {
        
        $this->outVerbose("Installation successful.");
        
    }

}

?>
