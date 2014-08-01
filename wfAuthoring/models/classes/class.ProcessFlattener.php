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
 * Removes recursiv calls to the workflow engine, by resolving them
 * into a single workflow.
 * 
 * Assumes that the called workflows have a single entry and exit
 *
 * @access public
 * @package wfEngine
 
 */
class wfAuthoring_models_classes_ProcessFlattener extends wfAuthoring_models_classes_ProcessCloner
{

    private $processDefinition;

    public function __construct(core_kernel_classes_Resource $processDefinition){
        $this->processDefinition = $processDefinition;
        parent::__construct();
    }

    public function flatten(){

        //clone activities and connectors
        $processDefinitionService = wfEngine_models_classes_ProcessDefinitionService::singleton();
        $activities = $processDefinitionService->getAllActivities($this->processDefinition);

        foreach($activities as $activity){
            $this->flattenProcessActivity($activity);
        }
    }

    protected function flattenProcessActivity(core_kernel_classes_Resource $activity){

        $this->initCloningVariables();
        $services = wfEngine_models_classes_ActivityService::singleton()->getInteractiveServices($activity);
        // only replace single-service activities, with the service process runner
        if(count($services) == 1){
            $serviceCall = current($services);
            $serviceDefinition = $serviceCall->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
            if($serviceDefinition->getUri() == INSTANCE_SERVICE_PROCESSRUNNER){

                // found a wfEngine call, extract processDefnition
                $subProcess = $this->getSubProcess($serviceCall);
                if(empty($subProcess)){
                    throw new common_exception_InconsistentData('Missing process uri in service call '.$serviceCall->getUri());
                }

                // @todo test the process first
                // @todo clone sub process
                common_Logger::w('Should have cloned subprocess '.$subProcess);

                $segment = $this->cloneProcessSegment($subProcess);
                $inActivity = $segment['in'];
                $firstout = current($segment['out']);
                
                //allow first acitvity only if the parent is
                if(!wfAuthoring_models_classes_ActivityService::singleton()->isInitial($activity)){
                    $inActivity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_FALSE);
                }
                
                $this->addClonedActivity($inActivity, $activity, $firstout);

                $propProcessActivities = new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES);
                foreach($this->getClonedActivities() as $activityClone){
                    $this->processDefinition->setPropertyValue($propProcessActivities, $activityClone->getUri());
                }

                //get the previous connector if exists and clone it
                $allConnectors = wfAuthoring_models_classes_ProcessService::singleton()->getConnectorsByActivity($activity);
                $connectors = array_merge($allConnectors['next'], $allConnectors['prev']);
                foreach($connectors as $connector){
                    //trick to reference previous and following connector: connector_prev -> activity_subprocess[activity1, activity2, etc.] -> connector_follow
                    $this->addClonedConnector($connector, $connector); 
                }

                //glue segment:
                $glue = array_merge(array($activity), $allConnectors['prev']);
                foreach($glue as $fragment){
                    $this->linkClonedStep($fragment);
                }

                //delete all activity:
                $activity->delete(true);

                //recursive call:
                foreach($this->getClonedActivities() as $activityClone){
                    $this->flattenProcessActivity($activityClone);
                }
            }
        }
    }

    protected function getSubProcess($service){
        $returnValue = null;
        $propertyIterator = $service->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN))->getIterator();
        foreach($propertyIterator as $actualParam){
            $formalParam = $actualParam->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_FORMALPARAMETER));
            if($formalParam->getUri() == INSTANCE_FORMALPARAM_PROCESSDEFINITION){
                $returnValue = $actualParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_CONSTANTVALUE));
                break;
            }
        }
        return $returnValue;
    }

}