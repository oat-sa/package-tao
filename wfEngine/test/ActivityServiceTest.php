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

require_once dirname(__FILE__) . '/../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Test the service wfEngine_models_classes_ActivityService
 *
 * @author Lionel Lecaque, <taosupport@tudor.lu>
 * @package wfEngine
 
 */

class ActivityServiceTest extends TaoPhpUnitTestRunner {
    
    /**
	 * @var wfEngine_models_classes_ActivityService
     */
    protected $service;

    

    public function setUp(){
        $this->service = wfEngine_models_classes_ActivityService::singleton();
    }
    
    /**
     * output messages
     * @param string $message
     * @param boolean $ln
     * @return void
     */
    private function out($message, $ln = false){
        if(self::OUTPUT){
            if(PHP_SAPI == 'cli'){
                if($ln){
                    echo "\n";
                }
                echo "$message\n";
            }
            else{
                if($ln){
                    echo "<br />";
                }
                echo "$message<br />";
            }
        }
    }

    
    
    /**
     * Test the service implementation
     */
    public function testService(){

        $this->assertIsA($this->service, 'tao_models_classes_Service');
        $this->assertIsA($this->service, 'wfEngine_models_classes_ActivityService');


    }


    public function testIsFinal(){
		$processAuthoringService = wfAuthoring_models_classes_ProcessService::singleton();
        $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
        $processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');
        $authoringService = wfAuthoring_models_classes_ProcessService::singleton();
        
        //define activities and connectors
        $activity1 = $authoringService->createActivity($processDefinition, 'activity1');
       
        //check first activity
        $this->assertTrue($this->service->isFinal($activity1) );

        
        $connector1 = $authoringService->createConnector($activity1);
		$authoringService->setConnectorType($connector1, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
        $activity2 = $authoringService->createSequenceActivity($connector1, null, 'activity2');
		

		$this->assertFalse($this->service->isFinal($activity1) );
		$this->assertTrue($this->service->isFinal($activity2) );
		
		$activity1->delete(true);
        $connector1->delete(true);
        $activity2->delete(true);
        $processDefinition->delete(true);
       
    }
    
    public function testIsInitial(){
        $processAuthoringService = wfAuthoring_models_classes_ProcessService::singleton();
        $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
        $processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');
        $authoringService = wfAuthoring_models_classes_ProcessService::singleton();
        
        //define activities and connectors
        $activity1 = $authoringService->createActivity($processDefinition, 'activity1');
        $this->assertNotNull($activity1);
        $authoringService->setFirstActivity($processDefinition, $activity1);
                
        $this->assertTrue($this->service->isInitial($activity1) );
        
        $connector1 = $authoringService->createConnector($activity1);
		$authoringService->setConnectorType($connector1, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
        $activity2 = $authoringService->createSequenceActivity($connector1, null, 'activity2');
                
        $this->assertTrue($this->service->isInitial($activity1) );
        $this->assertFalse($this->service->isInitial($activity2) );
        
        $activity1->delete(true);
        $connector1->delete(true);
        $activity2->delete(true);
        $processDefinition->delete(true);
    }
    
    
    public function testGetNextConnectors(){
        $processAuthoringService = wfAuthoring_models_classes_ProcessService::singleton();
        $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
        $processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');
        $authoringService = wfAuthoring_models_classes_ProcessService::singleton();
        
        //define activities and connectors
        $activity1 = $authoringService->createActivity($processDefinition, 'activity1');
        $this->assertNotNull($activity1);
        $authoringService->setFirstActivity($processDefinition, $activity1);
        
        $this->assertTrue(count($this->service->getNextConnectors($activity1)) == 0);
                

		
        $connector1 = $authoringService->createConnector($activity1);
        $connectorList = $this->service->getNextConnectors($activity1);
        $this->assertTrue(count($connectorList) == 1);
        $this->assertTrue(array_key_exists($connector1->getUri(), $connectorList));
        
		$authoringService->setConnectorType($connector1, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));

		
		$activity2 = $authoringService->createSequenceActivity($connector1, null, 'activity2');
                
		$connectorList = $this->service->getNextConnectors($activity1);
        $this->assertTrue(count($connectorList) == 1);
        $this->assertTrue(array_key_exists($connector1->getUri(), $connectorList));
        
        $connector2 = $authoringService->createConnector($activity2);
        		
        $then = $authoringService->createConditionalActivity($connector2, 'then');//create "Activity_2"
		$else = $authoringService->createConditionalActivity($connector2, 'else', null, '', true);//create another connector
        
		
		$connector3 = $authoringService->createConnector($then);
		$activity3 = $authoringService->createSequenceActivity($connector3, null, 'activity3');
		
		$this->assertTrue(count($this->service->getNextConnectors($else)) == 0);
		$this->assertTrue(count($this->service->getNextConnectors($then)) == 1);
		$this->assertTrue(count($this->service->getNextConnectors($activity3)) == 0);
        
        $activity1->delete(true);
        $connector1->delete(true);
        $connector2->delete(true);
        $connector3->delete(true);
        $then->delete(true);
        $else->delete(true);
        $activity2->delete(true);
        $activity3->delete(true);
        $processDefinition->delete(true);
    }
    
    public function testIsActivity(){
        $processAuthoringService = wfAuthoring_models_classes_ProcessService::singleton();
        $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
        $processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');
        
        $authoringService = wfAuthoring_models_classes_ProcessService::singleton();
        
        //define activities and connectors
        $activity1 = $authoringService->createActivity($processDefinition, 'activity1');
        $this->assertTrue($this->service->isActivity($activity1) );
        $this->assertFalse($this->service->isActivity($processDefinition) );
        
        $activity1->delete(true);
       
        $processDefinition->delete(true);
    }
    
    public function testIsHidden(){
		$authoringService = wfAuthoring_models_classes_ProcessService::singleton();
        $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
        $processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');
        $this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
        
        //define activities and connectors
        $activity1 = $authoringService->createActivity($processDefinition, 'activity1');
        $this->assertFalse($this->service->isHidden($activity1));
        $this->service->setHidden($activity1, true);
        $this->assertTrue($this->service->isHidden($activity1));
        $this->service->setHidden($activity1, false);
        $this->assertFalse($this->service->isHidden($activity1));
        
        $activity1->delete(true);
       
        $processDefinition->delete(true);
        
    }
    
    public function testGetInteractiveServices(){
        $processAuthoringService = wfAuthoring_models_classes_ProcessService::singleton();
        $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
        $processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');
                    
        $authoringService = wfAuthoring_models_classes_ProcessService::singleton();
        $interactiveService = wfEngine_models_classes_InteractiveServiceService::singleton();
        
        
        
        //define activities and connectors
        $activity1 = $authoringService->createActivity($processDefinition, 'activity1');
        
        
        $service1 = $authoringService->createInteractiveService($activity1);
        $this->assertTrue( count($this->service->getInteractiveServices($activity1)) == 1 );
        $this->assertTrue( array_key_exists($service1->getUri(), $this->service->getInteractiveServices($activity1)) );
        
        $service1->delete(true);
        $activity1->delete(true);
        $processDefinition->delete(true);
    }
    
    public function testGetConstrols(){
        $processAuthoringService = wfAuthoring_models_classes_ProcessService::singleton();
        $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
        $processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');
        $this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
        
        $authoringService = wfAuthoring_models_classes_ProcessService::singleton();
        
        //define activities and connectors
        $activity1 = $authoringService->createActivity($processDefinition, 'activity1');
        $authoringService->setFirstActivity($processDefinition, $activity1);
    
         $connector1 = $authoringService->createConnector($activity1);
		$authoringService->setConnectorType($connector1, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
        $activity2 = $authoringService->createSequenceActivity($connector1, null, 'activity2');
		
        $activity1Controls = $this->service->getControls($activity1);
        $activity2Controls = $this->service->getControls($activity2);
        
        $this->assertFalse($activity1Controls[INSTANCE_CONTROL_BACKWARD]);
        $this->assertTrue($activity2Controls[INSTANCE_CONTROL_BACKWARD]);
        $this->assertTrue($activity1Controls[INSTANCE_CONTROL_FORWARD]);  
        $this->assertTrue($activity2Controls[INSTANCE_CONTROL_FORWARD]);  
                  
        $activity1->delete(true);
        $connector1->delete(true);
        $activity2->delete(true);

        $processDefinition->delete(true);
    }
    
    public function testVirtualProcess(){
        $processAuthoringService = wfAuthoring_models_classes_ProcessService::singleton();
        $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
        $processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');
        $this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
        
        $authoringService = wfAuthoring_models_classes_ProcessService::singleton();
        
        //define activities and connectors
        $activity1 = $authoringService->createActivity($processDefinition, 'activity1');
        $this->assertNotNull($activity1);
        $authoringService->setFirstActivity($processDefinition, $activity1);
    
        //check first activity
        $this->assertTrue($this->service->isActivity($activity1) );
        $this->assertTrue($this->service->isInitial($activity1) );

        
        $connector1 = $authoringService->createConnector($activity1);
		$authoringService->setConnectorType($connector1, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
        $activity2 = $authoringService->createSequenceActivity($connector1, null, 'activity2');
		
        $this->assertNotNull($activity2);		
		$this->assertTrue($this->service->isActivity($activity2) );
		$this->assertFalse($this->service->isFinal($activity1) );
		$this->assertFalse($this->service->isInitial($activity2) );
		$this->assertTrue($this->service->isFinal($activity2) );
        
		

		
		
        $activity1->delete(true);
        $connector1->delete(true);
        $activity2->delete(true);
        $processDefinition->delete(true);
        
         
    }

}