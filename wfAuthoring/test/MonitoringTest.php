<?php

include_once dirname(__FILE__) . '/../includes/raw_start.php';

require_once dirname(__FILE__) . '/../../wfEngine/test/wfEngineServiceTest.php';

/**
 * Test the execution of a complex translation process
 * 
 * @author Somsack Sipasseuth, <taosupport@tudor.lu>
 * @package wfEngine
 
 */
class MonitoringTest extends wfEngineServiceTest {
	
	public function testCreateProcessMonitoringGrid(){
		
		//wfEngine_helpers_Monitoring_ProcessMonitoringGrid
		//wfEngine_helpers_Monitoring_TranslationProcessMonitoringGrid
		$processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		$processExecutions = $processInstancesClass->getInstances();
		
		$processMonitoringGrid = new wfAuthoring_helpers_Monitoring_TranslationProcessMonitoringGrid(array_keys($processExecutions));
		/*var_dump($processMonitoringGrid->toArray());
		var_dump($processMonitoringGrid->getGrid()->getColumnsModel());/*/
		
		//wfEngine_helpers_Monitoring_ExecutionHistoryGrid
		//wfEngine_helpers_Monitoring_TranslationExecutionHistoryGrid
		if(!empty($processExecutions)){
			$executionHistoryGrid = new wfAuthoring_helpers_Monitoring_TranslationExecutionHistoryGrid(reset($processExecutions));
			//var_dump($executionHistoryGrid->toArray());
		}
	}
	
}
?>
