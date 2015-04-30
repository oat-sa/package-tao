<?php
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
        	
        	//determine the target namespace.
        	$targetNamespace = rtrim(common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri(), '#');
        	if (!empty($this->options['model'])){
        		$targetNamespace = $this->options['model'];
        	}
        	else{
        		// Look for XML Base.
        		$this->outVerbose("Looking up for the value of xml:base as the model URI.");
        		try{
        			$dom = new DOMDocument('1.0', TAO_DEFAULT_ENCODING);
        			if (false !== $dom->load($this->options['input'])){
        				$this->outVerbose("RDF-XML document loaded.");
        				// try to find the 'xml:base' attribute on the root node.
        				$roots = $dom->getElementsByTagNameNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'RDF');
        				
        				if ($roots->length > 0){
        					$this->outVerbose("Root RDF element found.");
        					$root = $roots->item(0);
	        				$node = $root->getAttributeNodeNS('http://www.w3.org/XML/1998/namespace', 'base');
	        				if (!empty($node)){
	        					$targetNamespace = $node->value;
	        				}
	        				else{
	        					$this->outVerbose("No xml:base attribute found. The local model will be used.");	
	        				}
        				}
        			}
        			else{
        				$this->err("RDF-XML could not be loaded.", true);
        			}
        		}
        		catch (DOMException $e){
        			$this->err("RDF-XML parsing error: " . $e->getMessage(), true);
        		}
        	}
			
        	//validate the file to import
			$parser = new tao_models_classes_Parser($this->options['input'],
													array('extension' => 'rdf'));
			$parser->validate();
			$this->outVerbose("Model URI is '${targetNamespace}'.");
			if(!$parser->isValid()){
				foreach ($parser->getErrors() as $error) {
					$this->outVerbose("RDF-XML parsing error in '" . $error['file'] . "' at line '" . $error['line'] . "': '" . $error['message']. "'.");
				}
				
				$userService->logout();
				$this->err("RDF-XML parsing error.", true);
			}
			else{
			
				//initialize the adapter (no target class but a target namespace)
				$adapter = new tao_helpers_data_GenerisAdapterRdf();
				if($adapter->import($this->options['input'], null, $targetNamespace)){
					$this->outVerbose("RDF 'input' file successfully imported.");
					$userService->logout();
				}		
				else{
					$userService->logout();
					$this->err("An error occured during RDF-XML import.", true);
				}	
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

?>