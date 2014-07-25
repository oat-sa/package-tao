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
 * TAO - wfEngine/models/classes/class.ProcessDefinitionService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 29.10.2012, 15:36:37 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EEA-includes begin
// section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EEA-includes end

/* user defined constants */
// section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EEA-constants begin
// section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EEA-constants end

/**
 * Short description of class wfEngine_models_classes_ProcessDefinitionService
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ProcessDefinitionService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute processActivitiesProp
     *
     * @access public
     * @var Property
     */
    public $processActivitiesProp = null;

    /**
     * Short description of attribute activitiesIsInitialProp
     *
     * @access public
     * @var Property
     */
    public $activitiesIsInitialProp = null;

    /**
     * Short description of attribute processVariablesProp
     *
     * @access public
     * @var Property
     */
    public $processVariablesProp = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getRootActivities
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource processDefinition
     * @return array
     */
    public function getRootActivities( core_kernel_classes_Resource $processDefinition)
    {
        $returnValue = array();

        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EEB begin
		
		//@TODO: use $this->processRootActivitiesProp property to optimize performance:
		//@TODO: remove all call of the constant PROPERTY_ACTIVITIES_ISINITIAL
		$activities = $processDefinition->getPropertyValuesCollection($this->processActivitiesProp);
		foreach ($activities->getIterator() as $activity)
		{
			$isInitialCollection = $activity->getOnePropertyValue($this->activitiesIsInitialProp);
		
			if ($isInitialCollection!= null && $isInitialCollection->getUri() == GENERIS_TRUE)
			{
				//new: return array of Resource insteand of Activity
				$returnValue[] = $activity;
			}
		}
		
        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EEB end

        return (array) $returnValue;
    }

    /**
     * Short description of method getAllActivities
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource processDefinition
     * @return array
     */
    public function getAllActivities( core_kernel_classes_Resource $processDefinition)
    {
        $returnValue = array();

        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EF0 begin
		foreach ($processDefinition->getPropertyValuesCollection($this->processActivitiesProp)->getIterator() as $activity){
			if($activity instanceof core_kernel_classes_Resource){
				$returnValue[$activity->getUri()] = $activity;
			}
		}
        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EF0 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getProcessVars
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource processDefinition
     * @return array
     */
    public function getProcessVars( core_kernel_classes_Resource $processDefinition)
    {
        $returnValue = array();

        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EF3 begin
		
		$rangeProp = new core_kernel_classes_Property(RDFS_RANGE);
		$widgetProp = new core_kernel_classes_Property(PROPERTY_WIDGET);
		
		$variables = $processDefinition->getPropertyValuesCollection($this->processVariablesProp);
		
		$returnValue[RDFS_LABEL] = array(
			'name' => "Name", 
			'widgets' => WIDGET_FTE,
			'range' => RDFS_LITERAL
		);

		foreach ($variables->getIterator() as $variable){
			
			$widgets = $variable->getPropertyValues($widgetProp);
			$label = $variable->getLabel();
			$range = $variable->getPropertyValues($rangeProp);

			$returnValue[$variable->getUri()] = array(
				'name' => trim(strip_tags($label)), 
				'widgets' => $widgets,
				'range' => $range
			);
			
		}

        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EF3 end

        return (array) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    protected function __construct()
    {
        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002F01 begin
		
		$this->processVariablesProp = new core_kernel_classes_Property(PROPERTY_PROCESS_VARIABLES);
		$this->processActivitiesProp = new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES);
		$this->activitiesIsInitialProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL);
		$this->processRootActivitiesProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL);
		
        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002F01 end
    }

    /**
     * Short description of method setProcessVariable
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource processDefinition
     * @param  string processVariable
     * @return boolean
     */
    public function setProcessVariable( core_kernel_classes_Resource $processDefinition, $processVariable)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002F06 begin
		if(is_string($processVariable) && !empty ($processVariable)){
			//is a code:
			$variableService = wfEngine_models_classes_VariableService::singleton();
			$processVariableResource = $variableService->getProcessVariable($processVariable);
			if(!is_null($processVariableResource) && $processVariableResource instanceof core_kernel_classes_Resource){
				$returnValue = $processDefinition->setPropertyValue($this->processVariablesProp, $processVariableResource->getUri());
			}
		}elseif($processVariable instanceof core_kernel_classes_Resource){
			$returnValue = $processDefinition->setPropertyValue($this->processVariablesProp, $processVariable->getUri());
		}
		
        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002F06 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setRootActivities
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource processDefinition
     * @param  array rootActivities
     * @return boolean
     */
    public function setRootActivities( core_kernel_classes_Resource $processDefinition, $rootActivities)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--db23604:133a151a3dc:-8000:00000000000033AC begin
		//@TODO: use this method to set initial process activities
		$processDefinition->editPropertyValues($this->processRootActivitiesProp, $rootActivities);
        // section 127-0-1-1--db23604:133a151a3dc:-8000:00000000000033AC end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setAcl
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource processDefinition
     * @param  Resource mode
     * @param  Resource target
     * @return core_kernel_classes_Resource
     */
    public function setAcl( core_kernel_classes_Resource $processDefinition,  core_kernel_classes_Resource $mode,  core_kernel_classes_Resource $target = null)
    {
        $returnValue = null;

        // section 127-0-1-1--db23604:133a151a3dc:-8000:00000000000033B0 begin
		
        if(!$processDefinition->hasType(new core_kernel_classes_Class(CLASS_PROCESS))){
        	throw new Exception("Process must be an instance of the class Process");
        }
        if(!in_array($mode->getUri(), array_keys($this->getAclModes()))){
        	throw new Exception("Unknow acl mode");
        }
        
        //set the ACL mode
        $properties = array(
        	PROPERTY_PROCESS_INIT_ACL_MODE	=> $mode->getUri()
        );
        
        switch($mode->getUri()){
        	case INSTANCE_ACL_ROLE:{
        		if(is_null($target)){
        			throw new Exception("Target must reference a role resource");
        		}
        		$properties[PROPERTY_PROCESS_INIT_RESTRICTED_ROLE] = $target->getUri();
        		break;
        	}	
        	case INSTANCE_ACL_USER:{
        		if(is_null($target)){
        			throw new Exception("Target must reference a user resource");
        		}
        		$properties[PROPERTY_PROCESS_INIT_RESTRICTED_USER] = $target->getUri();
        		break;
			}	
        }
        
        //bind the mode and the target (user or role) to the activity
        $returnValue = $this->bindProperties($processDefinition, $properties);
		
        // section 127-0-1-1--db23604:133a151a3dc:-8000:00000000000033B0 end

        return $returnValue;
    }

    /**
     * Short description of method checkAcl
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource processDefinition
     * @param  Resource currentUser
     * @return boolean
     */
    public function checkAcl( core_kernel_classes_Resource $processDefinition,  core_kernel_classes_Resource $currentUser)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--db23604:133a151a3dc:-8000:00000000000033B9 begin
		
		if(!is_null($processDefinition)){

            $processModeProp	= new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_ACL_MODE);
            $restrictedUserProp	= new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_RESTRICTED_USER);
            $restrictedRoleProp	= new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_RESTRICTED_ROLE);

            //process and current must be set to the activty execution otherwise a common Exception is thrown
             
            $modeUri = $processDefinition->getOnePropertyValue($processModeProp);
            if (is_null($modeUri) || $modeUri instanceof core_kernel_classes_Literal) {
                $returnValue = true;	//if no mode defined, the process is allowed
            }
            else{
                switch($modeUri->getUri()){
                     
                    //check if th current user is the restricted user
                    case INSTANCE_ACL_USER:
                        $processUser = $processDefinition->getOnePropertyValue($restrictedUserProp);
                        if(!is_null($processUser)){
                            if($processUser->getUri() == $currentUser->getUri()) {
                                $returnValue = true;
                            }
                        }
                        break;
                         
                        //check if the current user has the restricted role
                    case INSTANCE_ACL_ROLE:
                    	$processRole 		= $processDefinition->getOnePropertyValue($restrictedRoleProp);
                    	$userService 		= tao_models_classes_UserService::singleton();
                    	$returnValue		= $userService->userHasRoles($currentUser, $processRole);
                        break;
                    default:
                        $returnValue = true;
                }
            }
        }
        // section 127-0-1-1--db23604:133a151a3dc:-8000:00000000000033B9 end

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

        // section 127-0-1-1--db23604:133a151a3dc:-8000:00000000000033BE begin
		$returnValue = array(
			INSTANCE_ACL_ROLE => new core_kernel_classes_Resource(INSTANCE_ACL_ROLE),
			INSTANCE_ACL_USER => new core_kernel_classes_Resource(INSTANCE_ACL_USER)
		);
        // section 127-0-1-1--db23604:133a151a3dc:-8000:00000000000033BE end

        return (array) $returnValue;
    }

    /**
     * Short description of method getInitialSteps
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource process
     * @return array
     */
    public function getInitialSteps( core_kernel_classes_Resource $process)
    {
        $returnValue = array();

        // section 10-30-1--78-4ca28256:13aace225cc:-8000:0000000000003BF3 begin
		foreach($this->getActivitiesByProcess($process) as $activity){
			if(wfEngine_models_classes_ActivityService::singleton()->isInitial($activity)){
				$returnValue[] = $activity;
			}
		}	
        // section 10-30-1--78-4ca28256:13aace225cc:-8000:0000000000003BF3 end

        return (array) $returnValue;
    }

    /**
     * returns all the activities that are final
     * aka which have no following step
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource process
     * @return array
     */
    public function getFinalSteps( core_kernel_classes_Resource $process)
    {
        $returnValue = array();
        foreach($this->getAllActivities($process) as $activity) {
			$nexts = wfEngine_models_classes_StepService::singleton()->getNextSteps($activity);
			if(empty($nexts)){
				$returnValue[] = $activity;
			}
		}	
        return (array) $returnValue;
    }

}