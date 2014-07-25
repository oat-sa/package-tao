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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Service that retrieve information about Activty definition during runtime
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_models_classes_ServiceCacheInterface
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/models/classes/interface.ServiceCacheInterface.php');

/**
 * include wfEngine_models_classes_StepService
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('wfEngine/models/classes/class.StepService.php');

/* user defined includes */
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E82-includes begin
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E82-includes end

/* user defined constants */
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E82-constants begin
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E82-constants end

/**
 * Service that retrieve information about Activty definition during runtime
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ActivityService
    extends wfEngine_models_classes_StepService
        implements tao_models_classes_ServiceCacheInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method setCache
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
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
//				case __CLASS__.'::isInitial':
//				case __CLASS__.'::isHidden':
				case __CLASS__.'::getNextConnectors':{
					if(isset($args[0]) && $args[0] instanceof core_kernel_classes_Resource){
						$activity = $args[0];
						if(!isset($this->instancesCache[$activity->getUri()])){
							$this->instancesCache[$activity->getUri()] = array();
						}
						$this->instancesCache[$activity->getUri()][$methodName] = $value;
						$returnValue = true;
					}
					break;
				}	
				case __CLASS__.'::getAclModes':{
					if(is_array($value) && !empty($value)){
						$this->instancesCache[$methodName] = $value;
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
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @return mixed
     */
    public function getCache($methodName, $args = array())
    {
        $returnValue = null;

        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D0 begin
		if($this->cache){
			
			switch($methodName){
//				case __CLASS__.'::isInitial':
//				case __CLASS__.'::isHidden':
				case __CLASS__.'::getNextConnectors':{
					if(isset($args[0]) && $args[0] instanceof core_kernel_classes_Resource){
						$activity = $args[0];
						if(isset($this->instancesCache[$activity->getUri()])
						&& isset($this->instancesCache[$activity->getUri()][$methodName])){
							$returnValue = $this->instancesCache[$activity->getUri()][$methodName];
						}
					}
					break;
				}	
				case __CLASS__.'::getAclModes':{
					if(isset($this->instancesCache[$methodName])){
						$returnValue = $this->instancesCache[$methodName];
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
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * Short description of method __construct
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    protected function __construct()
    {
        // section 127-0-1-1--384c890a:132d352d389:-8000:00000000000030A8 begin
		
		$this->instancesCache = array();
		$this->cache = true;
		parent::__construct();
        // section 127-0-1-1--384c890a:132d352d389:-8000:00000000000030A8 end
    }

    /**
     * indicate if the activity need back and forth controls
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return array
     */
    public function getControls( core_kernel_classes_Resource $activity)
    {
        $returnValue = array();

        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E84 begin
		$possibleValues = array(INSTANCE_CONTROL_BACKWARD, INSTANCE_CONTROL_FORWARD); 
		$propValues = $activity->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_CONTROLS));
		foreach ($propValues as $value) {
			if(in_array($value, $possibleValues)){
				$returnValue[$value] = true;
			}
		}
		if($this->isInitial($activity)){
			$returnValue[INSTANCE_CONTROL_BACKWARD] = false ;
		}
        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E84 end

        return (array) $returnValue;
    }

    /**
     * retrieve the Interactive service associate to the Activity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return array
     */
    public function getInteractiveServices( core_kernel_classes_Resource $activity)
    {
        $returnValue = array();

        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E92 begin
        
		$services = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
		foreach($services->getIterator() as $service){
			if($service instanceof core_kernel_classes_Resource){
				$returnValue[$service->getUri()] = $service;
			}
		}

        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E92 end

        return (array) $returnValue;
    }

    /**
     * Check if the activity is initial
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return boolean
     */
    public function isInitial( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EA3 begin
		$cachedValue = $this->getCache(__METHOD__, array($activity));
		if(!is_null($cachedValue) && is_bool($cachedValue)){
			$returnValue = $cachedValue;
		}else{
			$isIntial = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL));
			if(!is_null($isIntial) && $isIntial instanceof core_kernel_classes_Resource){
				if($isIntial->getUri() == GENERIS_TRUE){
					$returnValue = true;
				}
			}
			$this->setCache(__METHOD__, array($activity), $returnValue);
		}
        
        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EA3 end

        return (bool) $returnValue;
    }

    /**
     * check if activity is final
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return boolean
     */
    public function isFinal( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EA7 begin
        $nextConnectors = $this->getNextConnectors($activity);
        if(count($nextConnectors) == 0){
            $returnValue = true;
        }
        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EA7 end

        return (bool) $returnValue;
    }

    /**
     * get activity's next connector
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return array
     */
    public function getNextConnectors( core_kernel_classes_Resource $activity)
    {
        $returnValue = array();

        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EAB begin
		
		$cachedValue = $this->getCache(__METHOD__, array($activity));
		if(!is_null($cachedValue) && is_array($cachedValue)){
			$returnValue = $cachedValue;
		}else{
			foreach ($this->getNextSteps($activity) as $next) {
				$returnValue[$next->getUri()] = $next;
			}
			// fixing the following error breaks the wfEngine
			$this->getCache(__METHOD__, array($activity), $returnValue);
		}
		
        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EAB end

        return (array) $returnValue;
    }

    /**
     * Short description of method isActivity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return boolean
     */
    public function isActivity( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EB8 begin
        if(!is_null($activity)){
            $returnValue = $activity->hasType( new core_kernel_classes_Class(CLASS_ACTIVITIES));
        }
        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EB8 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isHidden
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return boolean
     */
    public function isHidden( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-52a9110:13219ee179c:-8000:0000000000002EBE begin
		$cachedValue = $this->getCache(__METHOD__, array($activity));
		if(!is_null($cachedValue) && is_bool($cachedValue)){
			$returnValue = $cachedValue;
		}else{
			$propHidden = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN);
			$hidden = $activity->getOnePropertyValue($propHidden);
			if (!is_null($hidden) && $hidden instanceof core_kernel_classes_Resource) {
				if ($hidden->getUri() == GENERIS_TRUE) {
					$returnValue = true;
				}
			}
			$this->setCache(__METHOD__, array($activity), $returnValue);
		}
        
        // section 127-0-1-1-52a9110:13219ee179c:-8000:0000000000002EBE end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getUniqueNextConnector
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return core_kernel_classes_Resource
     */
    public function getUniqueNextConnector( core_kernel_classes_Resource $activity)
    {
        $returnValue = null;

        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F84 begin
		
		$connectors = $this->getNextConnectors($activity);
		$countConnectors = count($connectors);
		
		if($countConnectors > 1){
			//there might be a join connector among them or an issue
			$connectorsTmp = array();
			foreach ($connectors as $connector){
				$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
				//drop the connector join for now 
				//(a join connector is considered only when it is only one found, i.e. the "else" case below)
				if($connectorType->getUri() != INSTANCE_TYPEOFCONNECTORS_JOIN){
					$connectorsTmp[] = $connector;
				}else{
					//warning: join connector:
					$connectorsTmp = array($connector);
					break;
				}
			}
			
			if(count($connectorsTmp) == 1){
				//ok, the unique next connector has been found
				$returnValue = $connectorsTmp[0];
			} else {
				common_Logger::w('Found multiple nonjoin next connectors for activity '.$activity->getUri());
			}
		}else if($countConnectors == 1){
			$returnValue = reset($connectors);
		}else{
			//it is the final activity
		}
		
        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F84 end

        return $returnValue;
    }

    /**
     * Short description of method setAcl
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @param  Resource mode
     * @param  Resource target
     * @return boolean
     */
    public function setAcl( core_kernel_classes_Resource $activity,  core_kernel_classes_Resource $mode,  core_kernel_classes_Resource $target = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--1b682bf3:132cdc3fef4:-8000:000000000000309D begin
		
		//check the kind of resources
        if($this->getClass($activity)->getUri() != CLASS_ACTIVITIES){
        	throw new Exception("Activity must be an instance of the Activities Class");
        }
        if(!in_array($mode->getUri(), array_keys($this->getAclModes()))){
        	throw new Exception("Unknow ACL mode");
        }
        
        //set the ACL mode
        $properties = array(
        	PROPERTY_ACTIVITIES_ACL_MODE => $mode->getUri()
        );
        
        switch($mode->getUri()){
        	case INSTANCE_ACL_ROLE:
        	case INSTANCE_ACL_ROLE_RESTRICTED_USER:
        	case INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED:
			case INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY:{
        		if(is_null($target)){
        			throw new Exception("Target must reference a Role Resource");
        		}
        		$properties[PROPERTY_ACTIVITIES_RESTRICTED_ROLE] = $target->getUri();
        		break;
        	}	
        	case INSTANCE_ACL_USER:{
        		if(is_null($target)){
        			throw new Exception("Target must reference a user Resource");
        		}
        		$properties[PROPERTY_ACTIVITIES_RESTRICTED_USER] = $target->getUri();
        		break;
			}
        }
        
        //bind the mode and the target (user or role) to the activity
        $returnValue = $this->bindProperties($activity, $properties);
		
        // section 127-0-1-1--1b682bf3:132cdc3fef4:-8000:000000000000309D end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAclModes
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getAclModes()
    {
        $returnValue = array();

        // section 127-0-1-1--1b682bf3:132cdc3fef4:-8000:00000000000030A2 begin
		$returnValue = $this->getCache(__METHOD__);
		if(is_null($returnValue)){
			$aclModeClass = new core_kernel_classes_Class(CLASS_ACL_MODES);
			foreach($aclModeClass->getInstances() as $mode){
				$returnValue[$mode->getUri()] = $mode;
			}
			$this->setCache(__METHOD__, array(), $returnValue);
		}
        // section 127-0-1-1--1b682bf3:132cdc3fef4:-8000:00000000000030A2 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setHidden
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @param  boolean hidden
     * @return boolean
     */
    public function setHidden( core_kernel_classes_Resource $activity, $hidden = true)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--1e09aee3:133358e11e1:-8000:0000000000003233 begin
		
		$propHidden = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN);
		$hidden = (bool) $hidden;
		$returnValue = $activity->editPropertyValues($propHidden, ($hidden)?GENERIS_TRUE:GENERIS_FALSE);
		$this->setCache(__CLASS__.'::isHidden', array($activity), $hidden);
		
        // section 127-0-1-1--1e09aee3:133358e11e1:-8000:0000000000003233 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setControls
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @param  array controls
     * @return boolean
     */
    public function setControls( core_kernel_classes_Resource $activity, $controls)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--1e09aee3:133358e11e1:-8000:000000000000323B begin
		$possibleValues = $this->getAllControls();
		if(is_array($controls)){
			$values = array();
			foreach($controls as $control){
				if(in_array($control, $possibleValues)){
					$values[] = $control;
				}
			}
			$returnValue = $activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_CONTROLS), $values);
		}
		
        // section 127-0-1-1--1e09aee3:133358e11e1:-8000:000000000000323B end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAllControls
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getAllControls()
    {
        $returnValue = array();

        // section 127-0-1-1--1e09aee3:133358e11e1:-8000:000000000000324F begin
		$returnValue = array(INSTANCE_CONTROL_BACKWARD, INSTANCE_CONTROL_FORWARD); 
        // section 127-0-1-1--1e09aee3:133358e11e1:-8000:000000000000324F end

        return (array) $returnValue;
    }

    /**
     * Short description of method getProcess
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return core_kernel_classes_Resource
     */
    public function getProcess( core_kernel_classes_Resource $activity)
    {
        $returnValue = null;

        // section 10-30-1--78-4ca28256:13aace225cc:-8000:0000000000003BFF begin
		$processClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processes = $processClass->searchInstances(
			array(PROPERTY_PROCESS_ACTIVITIES => $activity),
			array('like'=>false, 'recursive' => false)
		);
		if (count($processes) != 1) {
			throw new common_exception_Error('ActivityDefinition('.$activity->getUri().') is associated to '.count($process).' processes');
		}
		$returnValue = current($processes); 
        // section 10-30-1--78-4ca28256:13aace225cc:-8000:0000000000003BFF end

        return $returnValue;
    }

} /* end of class wfEngine_models_classes_ActivityService */

?>