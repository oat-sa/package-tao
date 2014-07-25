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
 
 */
class taoWfTest_models_classes_WfTestCompiler extends taoTests_models_classes_TestCompiler
{
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_Compiler::compile()
     */
    public function compile() {
        
        $report = new common_report_Report(common_report_Report::TYPE_SUCCESS,__('Published test "%s"', $this->getResource()->getLabel()));
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoWfTest'); // loads the extension
        
        $test = $this->getResource();
        common_Logger::i('Compiling test ' . $test->getLabel().' items');
        $process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
        
        if (count(wfEngine_models_classes_ProcessDefinitionService::singleton()->getAllActivities($process)) == 0) {
            return new common_report_Report(common_report_Report::TYPE_ERROR,__('An empty test cannot be published.'));
        }
        
        $processCloner = new wfAuthoring_models_classes_ProcessCloner();
        try {
            $processClone = $processCloner->cloneProcess($process);
            $report->add(new common_report_Report(common_report_Report::TYPE_SUCCESS,__('Cloned the process %s', $process->getLabel())));
        
            $itemsServiceReport = $this->process($processClone);
            foreach ($itemsServiceReport as $subReport) {
                $report->add($subReport);
            }
            
            if ($itemsServiceReport->getType() == common_report_Report::TYPE_SUCCESS) {
                $serviceCall = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(INSTANCE_SERVICE_PROCESSRUNNER));
                $param = new tao_models_classes_service_ConstantParameter(new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_PROCESSDEFINITION), $processClone->getUri());
                $serviceCall->addInParameter($param);
                
                $report->setData($serviceCall);
            } else {
                $report->setType(common_report_Report::TYPE_ERROR);
            }
        } catch (common_Exception $e) {
            $report->add(new common_report_Report(common_report_Report::TYPE_ERROR,__('Failed to clone the process')));
            $report->setType(common_report_Report::TYPE_ERROR);
        }
        

        if ($report->getType() != common_report_Report::TYPE_SUCCESS) {
            $report->setMessage(__('Failed to publish test "%s".' , $this->getResource()->getLabel()));
        }
        
        return $report;
    }

    /**
     * Walk through the cloned process and replace the item runner placeholders
     *
     * @param core_kernel_classes_Resource $processDefinition            
     */
    protected function process(core_kernel_classes_Resource $processDefinition)
    {
        $report = new common_report_Report(common_report_Report::TYPE_SUCCESS);
        $activities = wfEngine_models_classes_ProcessDefinitionService::singleton()->getAllActivities($processDefinition);
        foreach ($activities as $activity) {
            $services = wfEngine_models_classes_ActivityService::singleton()->getInteractiveServices($activity);
            foreach ($services as $service) {
                $serviceDefinition = $service->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
                if ($serviceDefinition->getUri() == INSTANCE_ITEMCONTAINER_SERVICE) {
                    $item = taoWfTest_models_classes_WfTestService::singleton()->getItemByService($service);
                    if (is_null($item)) {
                        $report->add($this->fail(__('No valid item found for service "%s"', $service->getLabel())));
                    } else {
                        $itemReport = $this->subCompile($item);
                        if ($itemReport->getType() == common_report_Report::TYPE_SUCCESS) {
                            $serviceCall = $itemReport->getData();
                            $storedServiceCall = $serviceCall->toOntology();
                            
                            // remove old service
                            wfEngine_models_classes_InteractiveServiceService::singleton()->deleteInteractiveService($service);
                            $activity->removePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $service);
                            // add new service
                            $activity->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $storedServiceCall);
                        }
                        $report->add($itemReport);
                        if ($itemReport->getType() != common_report_Report::TYPE_SUCCESS) {
                            $report->setType($itemReport->getType());
                        }
                    }
                }
            }
        }
        return $report;
    }
}