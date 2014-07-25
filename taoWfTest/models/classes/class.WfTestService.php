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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Service methods to manage the Tests business models using the RDF API.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoTests
 
 */
class taoWfTest_models_classes_WfTestService extends taoTests_models_classes_TestsService
{

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    protected function __construct()
    {
        parent::__construct();
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoWfTest'); // loads the extension
    }

    /**
     * Generates a new process for a test
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param
     *            Resource instance
     * @return core_kernel_classes_Resource
     */
    public function createTestProcess(core_kernel_classes_Resource $test)
    {
        $processInstance = wfEngine_models_classes_ProcessDefinitionService::singleton()->createInstance(new core_kernel_classes_Class(CLASS_PROCESS), 'process generated with testsService');
        
        // set ACL right to delivery process initialization:
        $processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_ACL_MODE), INSTANCE_ACL_ROLE);
        $processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_RESTRICTED_ROLE), INSTANCE_ROLE_DELIVERY);
        
        $test->setPropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP), $processInstance->getUri());
        $processInstance->setLabel("Process " . $test->getLabel());
        return $processInstance;
    }

    /**
     * Short description of method cloneInstance
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param
     *            Resource instance
     * @param
     *            Class clazz
     * @return core_kernel_classes_Resource
     */
    public function cloneInstance(core_kernel_classes_Resource $instance, core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;
        
        // call the parent create instance to prevent useless process test to be created:
        $label = $instance->getLabel();
        $cloneLabel = "$label bis";
        $clone = parent::createInstance($clazz, $cloneLabel);
        
        if (! is_null($clone)) {
            $noCloningProperties = array(
                TEST_TESTCONTENT_PROP,
                RDF_TYPE
            );
            
            foreach ($clazz->getProperties(true) as $property) {
                
                if (! in_array($property->getUri(), $noCloningProperties)) {
                    // allow clone of every property value but the deliverycontent, which is a process:
                    foreach ($instance->getPropertyValues($property) as $propertyValue) {
                        $clone->setPropertyValue($property, $propertyValue);
                    }
                }
            }
            // Fix label
            if (preg_match("/bis/", $label)) {
                $cloneNumber = (int) preg_replace("/^(.?)*bis/", "", $label);
                $cloneNumber ++;
                $cloneLabel = preg_replace("/bis(.?)*$/", "", $label) . "bis $cloneNumber";
            }
            $clone->setLabel($cloneLabel);
            
            // clone the process:
            $propInstanceContent = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
            try {
                $process = $instance->getUniquePropertyValue($propInstanceContent);
            } catch (Exception $e) {}
            if (! is_null($process)) {
                $processCloner = new wfAuthoring_models_classes_ProcessCloner();
                $processClone = $processCloner->cloneProcess($process);
                $clone->editPropertyValues($propInstanceContent, $processClone->getUri());
            } else {
                throw new Exception("the test process cannot be found");
            }
            
            $this->onChangeTestLabel($clone);
            $returnValue = $clone;
        }
        
        return $returnValue;
    }

    /**
     * Short description of method getTestItems
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param
     *            Resource test
     * @return array
     */
    public function getTestItems(core_kernel_classes_Resource $test)
    {
        $items = array();
        $processService = wfEngine_models_classes_ProcessDefinitionService::singleton();
        
        // get the associated process, set in the test content property
        $process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
        
        // get list of all activities:
        $activities = $processService->getAllActivities($process);
        $totalNumber = count($activities);
        
        // find the first one: property isinitial == true (must be only one, if not error) and set as the currentActivity:
        $currentActivity = null;
        foreach ($activities as $activity) {
            
            $isIntial = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL));
            if (! is_null($isIntial) && $isIntial instanceof core_kernel_classes_Resource) {
                if ($isIntial->getUri() == GENERIS_TRUE) {
                    $currentActivity = $activity;
                    break;
                }
            }
        }
        
        if (is_null($currentActivity)) {
            return $items;
        }
        
        // start the loop:
        for ($i = 0; $i < $totalNumber; $i ++) {
            $item = $this->getItemByActivity($currentActivity);
            if (! is_null($item)) {
                $items[$i] = $item;
            }
            
            // get its connector (check the type is "sequential) if ok, get the next activity
            $connector = $currentActivity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT));
            $nextActivity = null;
            if (! is_null($connector)) {
                $connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
                if ($connectorType->getUri() == INSTANCE_TYPEOFCONNECTORS_SEQUENCE) {
                    $nextActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT));
                }
            }
            
            if (! is_null($nextActivity)) {
                $currentActivity = $nextActivity;
            } else {
                if ($i == $totalNumber - 1) {
                    // it is normal, since it is the last activity and item
                } else {
                    throw new common_Exception('the next activity of the connector is not found');
                }
            }
        }
        
        return $items;
    }

    /**
     * Short description of method setTestItems
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param
     *            Resource test
     * @param
     *            array items
     * @return boolean
     */
    public function setTestItems(core_kernel_classes_Resource $test, $items)
    {
        $returnValue = (bool) false;
        
        $authoringService = wfAuthoring_models_classes_ProcessService::singleton();
        
        // get the current process:
        $process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
        
        // get formal param associated to the 3 required service input parameters:
        $itemUriParam = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMURI);
        
        // delete all related activities:
        $activities = $authoringService->getAllActivities($process);
        foreach ($activities as $activity) {
            if (! $authoringService->deleteActivity($activity)) {
                throw new common_exception_Error('Unable to delete Activity ' . $activity->getUri());
            }
        }
        
        // create the list of activities and interactive services and items plus their appropriate property values:
        $previousActivity = null;
        $connectorService = wfAuthoring_models_classes_ConnectorService::singleton();
        
        foreach ($items as $item) {
            if (! ($item instanceof core_kernel_classes_Resource)) {
                throw new common_Exception("An item provided to " . __FUNCTION__ . " is not a resource but " . gettype($item));
            }
            
            // create an activity
            $activity = null;
            $activity = $authoringService->createActivity($process, "item: {$item->getLabel()}");
            
            // set property value visible to true
            $activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN), GENERIS_FALSE);
            
            // set ACL mode to role user restricted with role=subject
            $extManager = common_ext_ExtensionsManager::singleton();
            $activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ACL_MODE), INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY);
            $activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_ROLE), INSTANCE_ROLE_DELIVERY);
            
            // get the item runner service definition: must exists!
            $itemRunnerServiceDefinition = new core_kernel_classes_Resource(INSTANCE_ITEMCONTAINER_SERVICE);
            if (! $itemRunnerServiceDefinition->exists()) {
                throw new common_exception_InconsistentData('required service definition item runner does not exists');
            }
            
            // create a call of service and associate the service definition to it:
            $interactiveService = $authoringService->createInteractiveService($activity);
            $interactiveService->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), $itemRunnerServiceDefinition->getUri());
            
            $authoringService->setActualParameter($interactiveService, $itemUriParam, $item->getUri(), PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN); // constant: we know it!
            
            if (! is_null($previousActivity)) {
                $connectorService->createSequential($previousActivity, $activity);
            } else {
                // set the property value as initial
                $activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
            }
            $previousActivity = $activity;
        }
        $returnValue = true;
        
        
        return (bool) $returnValue;
    }

    /**
     * Helper for get test items
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param
     *            Resource activity
     * @return core_kernel_classes_Resource
     */
    protected function getItemByActivity(core_kernel_classes_Resource $activity)
    {
        $returnValue = null;
        
        $services = wfEngine_models_classes_ActivityService::singleton()->getInteractiveServices($activity);
        foreach ($services as $iService) {
            if (! $iService instanceof core_kernel_classes_Resource) {
                throw new common_exception_InconsistentData('Non resource service call found for activity ' . $activity->getUri());
            }
            
            $serviceDefinition = $iService->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
            if ($serviceDefinition->getUri() == INSTANCE_ITEMCONTAINER_SERVICE) {
                $returnValue = $this->getItemByService($iService);
                break;
            }
        }
        return $returnValue;
    }

    public function getItemByService(core_kernel_classes_Resource $service)
    {
        $returnValue = null;
        $propertyIterator = $service->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN))->getIterator();
        foreach ($propertyIterator as $actualParam) {
            $formalParam = $actualParam->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_FORMALPARAMETER));
            if ($formalParam->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_NAME)) == 'itemUri') {
                $returnValue = $actualParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_CONSTANTVALUE));
                break;
            }
        }
        return $returnValue;
    }
}