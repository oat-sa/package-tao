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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */


/**
 * Enable you to manage the process variables
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_VariableService
    extends tao_models_classes_GenerisService
        implements tao_models_classes_ServiceCacheInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method setCache
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @param  array value
     * @return boolean
     */
    public function setCache($methodName, $args = array(), $value = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065CB begin
		
		if($this->cache){
			switch($methodName){
				case __CLASS__.'::getProcessVariable':{
					if(isset($args[0]) && is_string($args[0]) && $value instanceof core_kernel_classes_Resource){
						$code = $args[0];
						if(!isset($this->instancesCache[$methodName])){
							$this->instancesCache[$methodName] = array();
						}
						$this->instancesCache[$methodName][$code] = $value;
						$returnValue = true;
					}
					break;
				}	
			}
		}
		
        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065CB end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getCache
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @return mixed
     */
    public function getCache($methodName, $args = array())
    {
        $returnValue = null;

        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D0 begin
		// 
		if($this->cache){
			
			switch($methodName){
				case __CLASS__.'::getProcessVariable':{
					if(isset($args[0]) && is_string($args[0])){
						$code = $args[0];
						if(isset($this->instancesCache[$methodName])
						&& isset($this->instancesCache[$methodName][$code])){
							$returnValue = $this->instancesCache[$methodName][$code];
						}
					}
					break;
				}	
			}
			
		}
        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D0 end

        return $returnValue;
    }

    /**
     * Short description of method clearCache
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @return boolean
     */
    public function clearCache($methodName = '', $args = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D4 begin
        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D4 end

        return (bool) $returnValue;
    }

    /**
     * save a list of process variable by key/value pair
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  array variable
     * @param  Resource activityExecution
     * @return boolean
     */
    public function save($variable,  core_kernel_classes_Resource $activityExecution = null)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C07 begin
    	$session = PHPSession::singleton();
        
		if(is_null($activityExecution) && $session->hasAttribute("activityExecutionUri")){
			$activityExecution = new core_kernel_classes_Resource($session->getAttribute("activityExecutionUri"));
		}
		
		if(!is_null($activityExecution)){
			
			$loader = new common_ext_ExtensionLoader(common_ext_ExtensionsManager::singleton()->getExtensionById('wfEngine'));
			$loader->load();	//because it could be called anywhere
			
			$variablesProp = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_VARIABLES);
			$newVar = unserialize($activityExecution->getOnePropertyValue($variablesProp));
			
			foreach($variable as $k => $v) {
				$processVariable = $this->getProcessVariable($k);
				if(!is_null($processVariable)){
					$property = new core_kernel_classes_Property($processVariable->getUri());

					$returnValue &= $activityExecution->editPropertyValues($property,$v);
					if(is_array($newVar)){
						$newVar = array_merge($newVar, array($k)); 
					}
					else{
						$newVar = array($k);
					}
				} else {
					common_Logger::w('No process variable found for key '.$k);
				}
				
			}
			$returnValue &= $activityExecution->editPropertyValues($variablesProp, serialize($newVar));
		}
		
        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C07 end

        return (bool) $returnValue;
    }

    /**
     * Remove the variables in parameter (list the keys)
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  mixed params
     * @param  Resource activityExecution
     * @return boolean
     */
    public function remove( mixed $params,  core_kernel_classes_Resource $activityExecution = null)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0B begin
        $session = PHPSession::singleton();
        
		if(is_null($activityExecution) && $session->hasAttribute("activityExecutionUri")){
			$activityExecution = new core_kernel_classes_Resource($session->getAttribute("activityExecutionUri"));
		}
		
		if(!is_null($activityExecution)){
			
			$loader = new common_ext_ExtensionLoader(common_ext_ExtensionsManager::singleton()->getExtensionById('wfEngine'));
			$loader->load();	//because it could be called anywhere
						
			$oldVar = unserialize($activityExecution->getOnePropertyValue($this->variablesProperty));
			if(is_string($params)){
				$params = array($params);
			}
			
			if(is_array($params)){
				foreach($params as $param) {
					if(in_array($param,$oldVar)){
						$processVariable = $this->getProcessVariable($param);
						if(!is_null($processVariable)){
							$property = new core_kernel_classes_Property($processVariable->getUri());
							$returnValue &= $activityExecution->removePropertyValues($property);
							$oldVar = array_diff($oldVar,array($param));
						}
					}
				}
				$returnValue &= $activityExecution->editPropertyValues($this->variablesProperty, serialize($oldVar));
			}
		}

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0B end

        return (bool) $returnValue;
    }

    /**
     * get the variable matching the key
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string key
     * @param  Resource activityExecution
     * @return mixed
     */
    public function get($key,  core_kernel_classes_Resource $activityExecution = null)
    {
        $returnValue = null;

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0E begin
        $session = PHPSession::singleton();
        
		if(is_null($activityExecution) && $session->hasAttribute("activityExecutionUri")){
			$activityExecution = new core_kernel_classes_Resource($session->getAttribute("activityExecutionUri"));
		}
		
		if(!is_null($activityExecution)){
			$vars = unserialize($activityExecution->getOnePropertyValue($this->variablesProperty));
			if(is_array($vars) && in_array($key,$vars)){
				$processVariable = $this->getProcessVariable($key);
				if(!is_null($processVariable)){
					$property = new core_kernel_classes_Property($processVariable->getUri());
					$values = $activityExecution->getPropertyValuesCollection($property);
					if($values->count() == 1){
						$returnValue = $values->get(0);
					}
					if($values->count() > 1){
						$returnValue = (array)$values->getIterator();
					}
				}
			}
		}
        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0E end

        return $returnValue;
    }

    /**
     * Get all the v ariables
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return array
     */
    public function getAll( core_kernel_classes_Resource $activityExecution = null)
    {
        $returnValue = array();

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C11 begin
        $session = PHPSession::singleton();
        
		if(is_null($activityExecution) && $session->hasAttribute("activityExecutionUri")){
			$activityExecution = new core_kernel_classes_Resource($session->getAttribute("activityExecutionUri"));
		}
		
		if(!is_null($activityExecution)){
			
			$vars = unserialize($activityExecution->getOnePropertyValue($this->variablesProperty));
			
            if(is_array($vars)){
				foreach($vars as $code){
					$processVariable = $this->getProcessVariable($code);
					if(!is_null($processVariable)){
						$property = new core_kernel_classes_Property($processVariable->getUri());
						$values = $activityExecution->getPropertyValuesCollection($property);
						if($values->count() == 1){
							$returnValue[$code] = $values->get(0);
						}
						if($values->count() > 1){
							$returnValue[$code] = (array)$values->getIterator();
						}
					}
				}
            }
		}
		
        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C11 end

        return (array) $returnValue;
    }

    /**
     * add a variable (different of save in case of multiple values)
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string key
     * @param  string value
     * @param  Resource activityExecution
     * @return boolean
     */
    public function push($key, $value,  core_kernel_classes_Resource $activityExecution = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--55065e1d:1294a729605:-8000:0000000000002006 begin
        $returnValue = $this->_set($key, $value, $activityExecution, false);
        // section 127-0-1-1--55065e1d:1294a729605:-8000:0000000000002006 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getProcessVariable
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string code
     * @param  boolean forceCreation
     * @return core_kernel_classes_Resource
     */
    public function getProcessVariable($code, $forceCreation = false)
    {
        $returnValue = null;

        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002F0C begin
		$cachedValue = $this->getCache(__METHOD__, array($code));
		if(!is_null($cachedValue) && $cachedValue instanceof core_kernel_classes_Resource){
			$returnValue = $cachedValue;
		}else{
			$variables = $this->processVariablesClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $code), array('like' => false, 'recursive' => 0));
			if (!empty($variables)) {
				$returnValue = reset($variables);
			} else if ($forceCreation) {
				$returnValue = $this->createProcessVariable($code, $code);
				if (is_null($returnValue)) {
					throw new Exception("the process variable ({$code}) cannot be created.");
				}
			}
			
			if(!is_null($cachedValue) && $cachedValue instanceof core_kernel_classes_Resource){
				$this->setCache(__METHOD__, array($code), $returnValue);
			}
		}
        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002F0C end

        return $returnValue;
    }

    /**
     * Short description of method createProcessVariable
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string label
     * @param  string code
     * @return core_kernel_classes_Resource
     */
    public function createProcessVariable($label = '', $code = '')
    {
        $returnValue = null;

        // section 127-0-1-1-8ae8e2e:132ba7fdd5a:-8000:0000000000003073 begin
		
		if(!empty($code) && $this->getProcessVariable($code)){
			throw new Exception("A process variable with the code '{$code}' already exists");
		}
		
		if(empty($label)){
			$label = "Process variable";
            if(!empty($code)){
				$label .= " ".$code;
			}
		}
		$returnValue = $this->createInstance($this->processVariablesClass, $label);
		
		if(!empty($code)){
			$returnValue->setPropertyValue($this->codeProperty, $code);
		}
		
		//set the new instance of process variable as a property of the class process instance:
		$ok = $returnValue->setType(new core_kernel_classes_Class(RDF_PROPERTY));
		if($ok){
			$newTokenProperty = new core_kernel_classes_Property($returnValue->getUri());
			$newTokenProperty->setDomain(new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION));
			$newTokenProperty->setRange(new core_kernel_classes_Class(RDFS_LITERAL));//literal only!
			$newTokenProperty->setPropertyValue(new core_kernel_classes_Property(PROPERTY_MULTIPLE), GENERIS_TRUE);
		}else{
			throw new Exception("the newly created process variable {$label} ({$returnValue->getUri()}) cannot be set as a property of the class Activity Execution");
		}
		
		
        // section 127-0-1-1-8ae8e2e:132ba7fdd5a:-8000:0000000000003073 end

        return $returnValue;
    }

    /**
     * Short description of method deleteProcessVariable
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string code
     * @return boolean
     */
    public function deleteProcessVariable($code)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8ae8e2e:132ba7fdd5a:-8000:0000000000003089 begin
		
		if(!is_null($code)){
		
			$processVariableToDelete = null;
			$processVariableToDelete = $this->getProcessVariable($code);
			if(!is_null($processVariableToDelete)){
				$returnValue = $processVariableToDelete->delete(true);
			}
			
		}
		
        // section 127-0-1-1-8ae8e2e:132ba7fdd5a:-8000:0000000000003089 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isProcessVariable
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource variable
     * @return boolean
     */
    public function isProcessVariable( core_kernel_classes_Resource $variable)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--1b682bf3:132cdc3fef4:-8000:0000000000003096 begin
		$returnValue = $variable->hasType(new core_kernel_classes_Class(CLASS_PROCESSVARIABLES));
        // section 127-0-1-1--1b682bf3:132cdc3fef4:-8000:0000000000003096 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1-ce05865:132dda78a59:-8000:00000000000030A8 begin
		
		$this->instancesCache = array();
		$this->cache = true;
		$this->processVariablesClass = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
		$this->variablesProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_VARIABLES);
		$this->codeProperty = new core_kernel_classes_Property(PROPERTY_PROCESSVARIABLES_CODE);
		
        // section 127-0-1-1-ce05865:132dda78a59:-8000:00000000000030A8 end
    }

    /**
     * Short description of method edit
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string key
     * @param  string value
     * @param  Resource activityExecution
     * @return boolean
     */
    public function edit($key, $value,  core_kernel_classes_Resource $activityExecution = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-1899355b:13312537157:-8000:00000000000030E0 begin
		$returnValue = $this->_set($key, $value, $activityExecution, true);
        // section 127-0-1-1-1899355b:13312537157:-8000:00000000000030E0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method _set
     *
     * @access private
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string key
     * @param  string value
     * @param  Resource activityExecution
     * @param  boolean edit
     * @return boolean
     */
    private function _set($key, $value,  core_kernel_classes_Resource $activityExecution = null, $edit = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-1899355b:13312537157:-8000:00000000000030E7 begin
        $session = PHPSession::singleton();
        
		if(is_null($activityExecution) && $session->hasAttribute("activityExecutionUri")){
			$activityExecution = new core_kernel_classes_Resource($session->getAttribute("activityExecutionUri"));
		}
		
		if(!is_null($activityExecution)){
			
			$allVars = unserialize($activityExecution->getOnePropertyValue($this->variablesProperty));
			$processVariable = $this->getProcessVariable($key);
			
			if(!is_null($processVariable)){
				$property = new core_kernel_classes_Property($processVariable->getUri());
				if($edit){
					$returnValue &= $activityExecution->editPropertyValues($property, $value);
				}else{
					$returnValue &= $activityExecution->setPropertyValue($property, $value);
				}
				
				if(is_array($allVars)){
					if(!array_search($key, $allVars)){
						$allVars[] = $key;
					}
				}
				else{
					$allVars = array($key);
				}
			}
				
			$returnValue = $activityExecution->editPropertyValues($this->variablesProperty, serialize($allVars));
		}
		
        // section 127-0-1-1-1899355b:13312537157:-8000:00000000000030E7 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getCode
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource variable
     * @return string
     */
    public function getCode( core_kernel_classes_Resource $variable)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--193aa0be:133cfb90ad2:-8000:0000000000003431 begin
		$returnValue = $variable->getOnePropertyValue($this->codeProperty);
        // section 127-0-1-1--193aa0be:133cfb90ad2:-8000:0000000000003431 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getAllVariables
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return array
     */
    public function getAllVariables()
    {
        $returnValue = array();

        // section 127-0-1-1--9be58b4:1341d29fb0d:-8000:0000000000003468 begin
		foreach($this->processVariablesClass->getInstances(false) as $variable){
			$returnValue[$variable->getUri()] = array(
				'code' => $this->getCode($variable),
				'label' => $variable->getLabel()
			);
		}
		
        // section 127-0-1-1--9be58b4:1341d29fb0d:-8000:0000000000003468 end

        return (array) $returnValue;
    }

} /* end of class wfEngine_models_classes_VariableService */

?>