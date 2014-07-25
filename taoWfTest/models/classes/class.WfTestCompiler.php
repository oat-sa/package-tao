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
 * Compiles a test and item
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoWfTest_models_classes_WfTestCompiler extends tao_models_classes_Compiler
{
    public function __construct(core_kernel_classes_Resource $test) {
        parent::__construct($test);
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoWfTest'); // loads the extension
    }
	
    public function compile(core_kernel_file_File $destinationDirectory) {
        
        $test = $this->getResource();
        common_Logger::i('Compiling test ' . $test->getLabel().' items');
        $process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
        $processCloner = new wfAuthoring_models_classes_ProcessCloner();
        $processClone = $processCloner->cloneProcess($process);
        
        $this->process($processClone, $destinationDirectory);
        
        $serviceCall = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(INSTANCE_SERVICE_PROCESSRUNNER));
        $param = new tao_models_classes_service_ConstantParameter(
            new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_PROCESSDEFINITION),
            $processClone->getUri()
        );
        $serviceCall->addInParameter($param);
        return $serviceCall;
    }
    
    protected function process(core_kernel_classes_Resource $processDefinition, core_kernel_file_File $destinationDirectory)
    {
        $activities = wfEngine_models_classes_ProcessDefinitionService::singleton()->getAllActivities($processDefinition);
        foreach ($activities as $activity) {
            $services = wfEngine_models_classes_ActivityService::singleton()->getInteractiveServices($activity);
            foreach ($services as $service) {
                $serviceDefinition = $service->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
                if ($serviceDefinition->getUri() == INSTANCE_ITEMCONTAINER_SERVICE) {
                    $newService = $this->getItemRunnerService($service, $destinationDirectory);
                    // remove old service
                    wfEngine_models_classes_InteractiveServiceService::singleton()->deleteInteractiveService($service);
                    $activity->removePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $service);
                    // add new service
                    $activity->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $newService);
                }
                // todo if process called, flatten
            }
        }
    }
    
    protected function getItemRunnerService(core_kernel_classes_Resource $service, core_kernel_file_File $destinationDirectory)
    {
        $item = taoWfTest_models_classes_WfTestService::singleton()->getItemByService($service);
        if (is_null($item)) {
            throw new taoWfTest_models_classes_MalformedServiceCall($service, 'No valid item found for service '.$service->getUri());
        }
        $itemDirectory = $this->createSubDirectory($destinationDirectory, $item);
        
        $compiler = taoItems_models_classes_ItemsService::singleton()->getCompiler($item);
        $callService = $compiler->compile($itemDirectory);
        return $callService->toOntology();
    }
}