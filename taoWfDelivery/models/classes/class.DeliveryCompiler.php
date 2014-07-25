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
 * Compiles a Workflow Delivery
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoWfDelivery
 * @subpackage models_classes
 */
class taoWfDelivery_models_classes_DeliveryCompiler extends tao_models_classes_Compiler
{
    /**
     * Compiles the provided content
     * 
     * @param core_kernel_classes_Resource $content
     */
    public function __construct(core_kernel_classes_Resource $content) {
        parent::__construct($content);
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoWfDelivery'); // loads the extension
    }
	
    public function compile(core_kernel_file_File $destinationDirectory) {
        
        // @todo test process first
        
        $content = $this->getResource();
        common_Logger::i('Compiling delivery content ' . $content->getLabel());
        $process = $content->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_DELIVERYCONTENT_PROCESS));
        $processCloner = new wfAuthoring_models_classes_ProcessCloner();
        $processClone = $processCloner->cloneProcess($process);
        
        // replace the test place holders
        $this->replaceTestPlaceholders($processClone, $destinationDirectory);
        
        // verify we don't have nested workflow calls
        $flattener = new wfAuthoring_models_classes_ProcessFlattener($processClone);
        $flattener->flatten();
        
        $serviceCall = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(INSTANCE_SERVICE_PROCESSRUNNER));
        $param = new tao_models_classes_service_ConstantParameter(
            new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_PROCESSDEFINITION),
            $processClone->getUri()
        );
        $serviceCall->addInParameter($param);
        return $serviceCall;
    }
    
    protected function replaceTestPlaceholders(core_kernel_classes_Resource $processDefinition, core_kernel_file_File $destinationDirectory)
    {
        $activities = wfEngine_models_classes_ProcessDefinitionService::singleton()->getAllActivities($processDefinition);
        foreach ($activities as $activity) {
            $services = wfEngine_models_classes_ActivityService::singleton()->getInteractiveServices($activity);
            foreach ($services as $service) {
                $serviceDefinition = $service->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
                if ($serviceDefinition->getUri() == INSTANCE_SERVICEDEFINITION_TESTCONTAINER) {
                    
                    $serviceCall = $this->getNewService($service, $destinationDirectory);
                    // remove old service
                    wfEngine_models_classes_InteractiveServiceService::singleton()->deleteInteractiveService($service);
                    $activity->removePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $service);

                    // set new service
                    $serviceCallResource = $serviceCall->toOntology();
                    $activity->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $serviceCallResource);
                }
            }
        }
    }
    
    /**
     * 
     * @param core_kernel_classes_Resource $serviceCall
     * @param core_kernel_file_File $destinationDirectory
     * @return tao_models_classes_service_ServiceCall
     */
    protected function getNewService(core_kernel_classes_Resource $serviceCall, core_kernel_file_File $destinationDirectory)
    {
        $test = taoWfDelivery_models_classes_WfDeliveryService::singleton()->getTestFromService($serviceCall);
        if (empty($test)) {
            throw new taoWfDelivery_models_classes_MalformedServiceCall($serviceCall, 'No valid test found for service '.$serviceCall->getUri());
        }
        $subDirectory = $this->createSubDirectory($destinationDirectory, $test);
        $compiler = taoTests_models_classes_TestsService::singleton()->getCompiler($test);
        $callService = $compiler->compile($subDirectory);
        return $callService;
    }
}