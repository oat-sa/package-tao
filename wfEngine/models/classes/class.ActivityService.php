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
 * Service that retrieve information about Activty definition during runtime
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 
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
        
		
		$this->instancesCache = array();
		$this->cache = true;
		parent::__construct();
        
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

        
        
		$services = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
		foreach($services->getIterator() as $service){
			if($service instanceof core_kernel_classes_Resource){
				$returnValue[$service->getUri()] = $service;
			}
		}

        

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

        
        $nextConnectors = $this->getNextConnectors($activity);
        if(count($nextConnectors) == 0){
            $returnValue = true;
        }
        

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

        
        if(!is_null($activity)){
            $returnValue = $activity->hasType( new core_kernel_classes_Class(CLASS_ACTIVITIES));
        }
        

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

        
		$returnValue = $this->getCache(__METHOD__);
		if(is_null($returnValue)){
			$aclModeClass = new core_kernel_classes_Class(CLASS_ACL_MODES);
			foreach($aclModeClass->getInstances() as $mode){
				$returnValue[$mode->getUri()] = $mode;
			}
			$this->setCache(__METHOD__, array(), $returnValue);
		}
        

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

        
		
		$propHidden = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN);
		$hidden = (bool) $hidden;
		$returnValue = $activity->editPropertyValues($propHidden, ($hidden)?GENERIS_TRUE:GENERIS_FALSE);
		$this->setCache(__CLASS__.'::isHidden', array($activity), $hidden);
		
        

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

        
		$returnValue = array(INSTANCE_CONTROL_BACKWARD, INSTANCE_CONTROL_FORWARD); 
        

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

        
		$processClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processes = $processClass->searchInstances(
			array(PROPERTY_PROCESS_ACTIVITIES => $activity),
			array('like'=>false, 'recursive' => false)
		);
		if (count($processes) != 1) {
			throw new common_exception_Error('ActivityDefinition('.$activity->getUri().') is associated to '.count($process).' processes');
		}
		$returnValue = current($processes); 
        

        return $returnValue;
    }

} /* end of class wfEngine_models_classes_ActivityService */

?>