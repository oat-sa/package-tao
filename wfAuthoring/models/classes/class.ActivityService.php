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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *
 *
 */


/**
 * TAO - wfAuthoring/models/classes/class.ActivityService.php
 *
 * Service that retrieve information about Activty definition during runtime
 *
 * This file is part of TAO.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage models_classes
 */
class wfAuthoring_models_classes_ActivityService
    extends wfEngine_models_classes_ActivityService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method createActivity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource process
     * @param  string label
     * @return core_kernel_classes_Resource
     */
    public function createActivity( core_kernel_classes_Resource $process, $label = '')
    {
        $returnValue = null;


	    $activityLabel = "";
		$number = 0;

		if(empty($label)){
			$number = $process->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES))->count();
			$number += 1;
			$activityLabel = "Activity_$number";
		}else{
			$activityLabel = $label;
		}

		$activityClass = new core_kernel_classes_Class(CLASS_ACTIVITIES);
		$activity = $activityClass->createInstance($activityLabel, "created by ActivityService.Class");

		if(!empty($activity)){
			//associate the new instance to the process instance
			$process->setPropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES), $activity->getUri());

			//set if it is the first or not:
			if($number == 1){
				$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
			}else{
				$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_FALSE);
			}

			//by default, set the 'isHidden' property value to false:
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN), GENERIS_FALSE);

			//by default we add the back and forward controls to the activity
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_CONTROLS), array(INSTANCE_CONTROL_BACKWARD, INSTANCE_CONTROL_FORWARD));

			$returnValue = $activity;
		}else{
			throw new Exception("the activity cannot be created for the process {$process->getUri()}");
		}


        return $returnValue;
    }

    /**
     * Short description of method createFromServiceDefinition
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource process
     * @param  Resource serviceDefinition
     * @param  array inputParameters
     * @return core_kernel_classes_Resource
     */
    public function createFromServiceDefinition( core_kernel_classes_Resource $process,  core_kernel_classes_Resource $serviceDefinition, $inputParameters = array())
    {
        $returnValue = null;


        $returnValue = $this->createActivity($process);
        $service = $this->addService($returnValue, $serviceDefinition);

        return $returnValue;
    }

    /**
     * Short description of method addService
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return core_kernel_classes_Resource
     */
    protected function addService( core_kernel_classes_Resource $activity, $serviceDefinition)
    {
        $returnValue = null;

		//an interactive service of an activity is a call of service:
		$callOfServiceClass = new core_kernel_classes_Class(CLASS_CALLOFSERVICES);

		//create new resource for the property value of the current call of service PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN or PROPERTY_CALLOFSERVICES_ACTUALPARAMETEROUT
		$returnValue = $callOfServiceClass->createInstance($activity->getLabel()."_service");

		if(empty($returnValue)){
			throw new Exception("the interactive service cannot be created for the activity {$activity->getUri()}");
		}
		
		//associate the new instance to the activity instance
		$activity->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $returnValue->getUri());

		tao_models_classes_InteractiveServiceService::singleton()->setCallOfServiceDefinition($returnValue, $serviceDefinition);
		
		$returnValue->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_WIDTH), 100);
		$returnValue->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_HEIGHT), 100);
		$returnValue->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_TOP), 0);
		$returnValue->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_LEFT), 0);
		
		$defaultParams = tao_models_classes_InteractiveServiceService::singleton()->setDefaultParameters($returnValue);
		
        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return boolean
     */
    public function delete( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;


        $connectorService = wfAuthoring_models_classes_ConnectorService::singleton();
		$interactiveServiceService = wfEngine_models_classes_InteractiveServiceService::singleton();
		$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$connectors = $connectorClass->searchInstances(array(PROPERTY_CONNECTORS_ACTIVITYREFERENCE => $activity->getUri()), array('like' => false, 'recursive' => 0));
		foreach($connectors as $connector){
			$connectorService->delete($connector);
		}
		
		//deleting resource "acitivty" with its references should be enough normally to remove all references... to be tested
				
		//delete call of service!!
		foreach($this->getInteractiveServices($activity) as $service){
			$interactiveServiceService->deleteInteractiveService($service);
		}
		
		//delete referenced actiivty cardinality resources:
		$activityCardinalityClass = new core_kernel_classes_Class(CLASS_ACTIVITYCARDINALITY);
		$cardinalities = $activityCardinalityClass->searchInstances(array(PROPERTY_STEP_NEXT => $activity->getUri()), array('like'=>false));
		foreach($cardinalities as $cardinality) {
			$cardinality->delete(true);
		}
		
		//delete activity itself:
		$returnValue = $activity->delete(true);


        return (bool) $returnValue;
    }

    /**
     * Short description of method setACL
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @param  Resource mode
     * @param  Resource target
     * @return boolean
     */
    public function setACL( core_kernel_classes_Resource $activity,  core_kernel_classes_Resource $mode,  core_kernel_classes_Resource $target = null)
    {
        $returnValue = (bool) false;


		//check the kind of resources
        if($this->getClass($activity)->getUri() != CLASS_ACTIVITIES){
        	throw new Exception("Activity must be an instance of the class Activities");
        }
        if(!in_array($mode->getUri(), array_keys($this->getAclModes()))){
        	throw new Exception("Unknow acl mode");
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
        			throw new Exception("Target must reference a role resource");
        		}
        		$properties[PROPERTY_ACTIVITIES_RESTRICTED_ROLE] = $target->getUri();
        		break;
        	}	
        	case INSTANCE_ACL_USER:{
        		if(is_null($target)){
        			throw new Exception("Target must reference a user resource");
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

} /* end of class wfAuthoring_models_classes_ActivityService */

?>