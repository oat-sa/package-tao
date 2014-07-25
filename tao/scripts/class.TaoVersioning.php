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
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - tao/scripts/class.TaoVersioning.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 13.10.2011, 07:52:44 with ArgoUML PHP module
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package tao
 * @subpackage scripts
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_scripts_Runner
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('tao/scripts/class.Runner.php');

/* user defined includes */
// section 127-0-1-1--33cecc33:132fbb6bd64:-8000:0000000000003F57-includes begin
// section 127-0-1-1--33cecc33:132fbb6bd64:-8000:0000000000003F57-includes end

/* user defined constants */
// section 127-0-1-1--33cecc33:132fbb6bd64:-8000:0000000000003F57-constants begin
// section 127-0-1-1--33cecc33:132fbb6bd64:-8000:0000000000003F57-constants end

/**
 * Short description of class tao_scripts_TaoVersioning
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package tao
 * @subpackage scripts
 */
class tao_scripts_TaoVersioning
    extends tao_scripts_Runner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method preRun
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    public function preRun()
    {
        // section 127-0-1-1--33cecc33:132fbb6bd64:-8000:0000000000003F58 begin
        $this->options = array (
    		'enable'	=> false,
    		'disable'	=> false,
        	'login'		=> null,
        	'password'	=> null,
        	'type'		=> null,
        	'url'		=> null,
        	'path'		=> null
    	);
    	$this->options = array_merge($this->options, $this->parameters);
        // section 127-0-1-1--33cecc33:132fbb6bd64:-8000:0000000000003F58 end
    }

    /**
     * Short description of method run
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    public function run()
    {
        // section 127-0-1-1--33cecc33:132fbb6bd64:-8000:0000000000003F5A begin
        if($this->options['enable']){

        	$typeUri	= !is_null($this->options['type']) 		? $this->options['type'] 	: PROPERTY_GENERIS_VCS_TYPE_SUBVERSION;
        	if (!is_null($this->options['type'])) {
        		//Regarding to the versioning sytem type
				switch($this->options['type']){
					case 'svn':
						$typeUri = PROPERTY_GENERIS_VCS_TYPE_SUBVERSION;
						break;
					default:
						throw new common_Exception("Unable to recognize the given type ".$this->options['type']);
				}
        	} else {
        		$typeUri = PROPERTY_GENERIS_VCS_TYPE_SUBVERSION;
        	}
        	$type		= new core_kernel_classes_Resource($typeUri);
        	
        	//following parameters required
        	$url		= $this->options['url'];
        	$login		= $this->options['login'];
        	$password	= $this->options['password'];
        	$path		= $this->options['path'];

			$repo = core_kernel_fileSystem_FileSystemFactory::createFileSystem($type, $url, $login, $password, $path, '');
        	try {
				//Initialize
        		$success = $repo->enable();
        		
        		if ($success) {
					$this->out(__('repository added & enabled'), array('color' => 'light_blue'));
					/* no more default repository
					tao_models_classes_FileSourceService::singleton()->setDefaultFileSource($repo);
					$this->out(__('repository set as new default'), array('color' => 'light_blue'));
					*/
        		} else {
					$this->out(__('repository could not be enabled'), array('color' => 'red'));
				}
			} catch (Exception $e) {
				$this->out($e->getMessage(), array('color' => 'red'));
			}
        }

        // section 127-0-1-1--33cecc33:132fbb6bd64:-8000:0000000000003F5A end
    }

    /**
     * Short description of method postRun
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    public function postRun()
    {
        // section 127-0-1-1--33cecc33:132fbb6bd64:-8000:0000000000003F5C begin
        // section 127-0-1-1--33cecc33:132fbb6bd64:-8000:0000000000003F5C end
    }

} /* end of class tao_scripts_TaoVersioning */

?>