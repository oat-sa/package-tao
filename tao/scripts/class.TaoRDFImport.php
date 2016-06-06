<?php
use oat\tao\model\import\ImportRdf;
/**  
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
 * Short description of class tao_scripts_TaoRDFImport
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class tao_scripts_TaoRDFImport
    extends tao_scripts_Runner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method preRun
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function preRun()
    {
        
        $this->options = array('verbose' => false,
        					   'user' => null,
        					   'password' => null,
        					   'model' => null,
        					   'input' => null);
        
        $this->options = array_merge($this->options, $this->parameters);
        
        // the 'file' param is checked by the parent implementation.
        
        if ($this->options['user'] == null){
        	$this->err("Please provide a TAO 'user'.", true);
        }
        else if ($this->options['password'] == null){
        	$this->err("Please provide a TAO 'password'.", true);
        }
        else if ($this->options['input'] == null){
        	$this->err("Please provide a RDF 'input' file.", true);
        }

        
    }

    /**
     * Short description of method run
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function run()
    {
        
        $userService = tao_models_classes_UserService::singleton();
        $this->outVerbose("Connecting to TAO as '" . $this->options['user'] . "' ...");
        if ($userService->loginUser($this->options['user'], $this->options['password'])){
        	$this->outVerbose("Connected to TAO as '" . $this->options['user'] . "'.");
        	
        	$filename = $this->options['input'];
        	$action = new ImportRdf();
        	$params = array($filename);
        	
        	if (!empty($this->options['model'])){
        	    $nameSpace = common_ext_NamespaceManager::singleton()->getNamespace($this->options['model']);
        	    $params[] = $nameSpace->getModelId();
        	}
        	 
        	$report = $action->__invoke($params);
        	
        	$string = tao_helpers_report_Rendering::renderToCommandline($report);
        	foreach (explode(PHP_EOL, $string) as $line) {
        	    $this->out($line);
        	}
        }
        else{
        	$this->err("Unable to connect to TAO as '" . $this->options['user'] . "'.", true);
        }
        
    }

    /**
     * Short description of method postRun
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function postRun()
    {
        
        
    }

}
