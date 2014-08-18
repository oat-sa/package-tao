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
 
 */
class taoWfDelivery_models_classes_DeliveryCompiler extends taoDelivery_models_classes_DeliveryCompiler
{
    /**
     * Compiles the provided content
     * 
     * @param core_kernel_classes_Resource $content
     */
    public function __construct(core_kernel_classes_Resource $resource, tao_models_classes_service_FileStorage $storage) {
        parent::__construct($resource, $storage);
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoWfDelivery'); // loads the extension
    }
	
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_Compiler::compile()
     */
    public function compile() {
        
        // @todo test process first
        
        $content = $this->getResource();
        common_Logger::i('Compiling delivery content ' . $content->getLabel());
        $process = $content->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_DELIVERYCONTENT_PROCESS));
        $processCloner = new wfAuthoring_models_classes_ProcessCloner();
        $processClone = $processCloner->cloneProcess($process);
        
        // replace the test place holders
        $report = $this->replaceTestPlaceholders($processClone);
        
        if ($report->getType() == common_report_Report::TYPE_SUCCESS) {
            // verify we don't have nested workflow calls
            $flattener = new wfAuthoring_models_classes_ProcessFlattener($processClone);
            $flattener->flatten();
            
            $serviceCall = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(INSTANCE_SERVICE_PROCESSRUNNER));
            $param = new tao_models_classes_service_ConstantParameter(
                new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_PROCESSDEFINITION),
                $processClone->getUri()
            );
            $serviceCall->addInParameter($param);
            $report->setData($serviceCall);
        }
        return $report;
    }
    
    /**
     * Replace the test place holders
     * This keeps the item placeholders, so don't add items into deliveries
     * 
     * @param core_kernel_classes_Resource $processDefinition
     * @return common_report_Report
     */
    protected function replaceTestPlaceholders(core_kernel_classes_Resource $processDefinition)
    {
        $report = new common_report_Report(common_report_Report::TYPE_SUCCESS, __('Delivery has been successfully published'));
        
        $activities = wfEngine_models_classes_ProcessDefinitionService::singleton()->getAllActivities($processDefinition);
        foreach ($activities as $activity) {
            $services = wfEngine_models_classes_ActivityService::singleton()->getInteractiveServices($activity);
            foreach ($services as $service) {
                $serviceDefinition = $service->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
                if ($serviceDefinition->getUri() == INSTANCE_SERVICEDEFINITION_TESTCONTAINER) {
                    
                    $subReport = $this->getNewService($service);
                    $report->add($subReport);
                    if ($subReport->getType() == common_report_Report::TYPE_SUCCESS) {
                        $serviceCall = $subReport->getData(); 
                        // remove old service
                        wfEngine_models_classes_InteractiveServiceService::singleton()->deleteInteractiveService($service);
                        $activity->removePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $service);
    
                        // set new service
                        $serviceCallResource = $serviceCall->toOntology();
                        $activity->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $serviceCallResource);
                    } else {
                        $report->setType(common_report_Report::TYPE_ERROR);
                        $report->setMessage(__('An error occured during compilation'));
                    }
                }
            }
        }
        return $report;
    }
    
    /**
     * 
     * @param core_kernel_classes_Resource $serviceCall
     * @return common_report_Report
     */
    protected function getNewService(core_kernel_classes_Resource $serviceCall)
    {
        $test = taoWfDelivery_models_classes_WfDeliveryService::singleton()->getTestFromService($serviceCall);
        if (empty($test)) {
            throw new taoWfDelivery_models_classes_MalformedServiceCall($serviceCall, 'No valid test found for service '.$serviceCall->getUri());
        }
        return $this->subCompile($test);
    }
}