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
require_once dirname(__FILE__) . '/../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Test the service wfEngine_models_classes_ConnectorService
 *
 * @author Lionel Lecaque, <taosupport@tudor.lu>
 * @package wfEngine
 
 */

class ConnectorServiceTest extends TaoPhpUnitTestRunner {
    /**
     * @var wfEngine_models_classes_ActivityService
     */
    protected $service;
    protected $authoringService;
    protected $processDefinition;
    protected $activity;

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
     * tests initialization
     */
    public function setUp(){
        TaoPhpUnitTestRunner::initTest();

        $this->authoringService = wfAuthoring_models_classes_ProcessService::singleton();
		$this->variableService = wfEngine_models_classes_VariableService::singleton();
		
        $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
        $this->processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');
         
        //define activities and connectors
        $activity = $this->authoringService->createActivity($this->processDefinition, 'activity for interactive service unit test');
        if($activity instanceof core_kernel_classes_Resource){
            $this->activity = $activity;
        }else{
            $this->fail('fail to create a process definition resource');
        }
        
        $this->service = wfEngine_models_classes_ConnectorService::singleton();
    }

    public function tearDown() {
        $this->assertTrue($this->authoringService->deleteProcess($this->processDefinition));
    }

    /**
     * Test the service implementation
     */
    public function testService(){
        $this->assertIsA($this->service, 'tao_models_classes_Service');
        $this->assertIsA($this->service, 'wfEngine_models_classes_ConnectorService');


    }

    public function testIsConnector(){
        $connector1 = $this->authoringService->createConnector($this->activity);
        $this->authoringService->setConnectorType($connector1, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
        $activity2 = $this->authoringService->createSequenceActivity($connector1, null, 'activity2');
        $this->assertTrue($this->service->isConnector($connector1));
        $this->assertFalse($this->service->isConnector($activity2));

        $connector1->delete(true);
        $activity2->delete(true);
    }

    public function testGetTransitionRule(){
        $connector1 = $this->authoringService->createConnector($this->activity);

        $then = $this->authoringService->createConditionalActivity($connector1, 'then');//create "Activity_2"
        $else = $this->authoringService->createConditionalActivity($connector1, 'else', null, '', true);//create another connector
        $activity3 = $this->authoringService->createSequenceActivity($else, null, 'Act3');

        $myProcessVar1 = $this->variableService->getProcessVariable('myProcessVarCode1', true);
        $transitionRule = $this->authoringService->createTransitionRule($connector1, '^myProcessVarCode1 == 1');

        $transitionRuleBis = $this->service->getTransitionRule($connector1);
        $this->assertEquals($transitionRule->getUri(),$transitionRuleBis->getUri());

        $then->delete(true);
        $else->delete(true);
        $activity3->delete(true);
        $transitionRule->delete(true);
        $connector1->delete(true);

    }

    public function testGetType(){


        /*
         *  activity > connector1(COND)
         *  -> THEN  > thenConnector(SQ)
         *  -> ELSE > elseConnector (SQ)
         *  -> Act3 > connector2(PARA)
         *  -> Act4 > connector3(JOIN)
         *  -> Act5 > connector4(JOIN)
         * 	-> Acto6
         *
         */
        $connector1 = $this->authoringService->createConnector($this->activity);

        $then = $this->authoringService->createConditionalActivity($connector1, 'then');//create "Activity_2"
        $thenConnector = $this->authoringService->createConnector($then, 'then Connector');//create "Activity_2"

        $else = $this->authoringService->createConditionalActivity($connector1, 'else', null, '', true);//create another connector
        $elseConnector = $this->authoringService->createConnector($else, 'else Connector');//create "Activity_2"

        $activity3 = $this->authoringService->createSequenceActivity($thenConnector, null, 'Act3');
        $this->authoringService->createSequenceActivity($elseConnector, $activity3);

        $this->assertIsA($this->service->getType($thenConnector),'core_kernel_classes_Resource');
        $this->assertIsA($this->service->getType($elseConnector),'core_kernel_classes_Resource');
        $this->assertEquals($this->service->getType($thenConnector)->getUri(), INSTANCE_TYPEOFCONNECTORS_SEQUENCE);
        $this->assertEquals($this->service->getType($elseConnector)->getUri(), INSTANCE_TYPEOFCONNECTORS_SEQUENCE);

        $myProcessVar1 = $this->variableService->getProcessVariable('myProcessVarCode1', true);
        $transitionRule = $this->authoringService->createTransitionRule($connector1, '^myProcessVarCode1 == 1');
        
        $connectorType = $this->service->getType($connector1);
        $this->assertEquals($connectorType->getUri(),INSTANCE_TYPEOFCONNECTORS_CONDITIONAL);

        $connector2 = $this->authoringService->createConnector($activity3);
        $activity4 = $this->authoringService->createActivity($this->processDefinition, 'activity4 for interactive service unit test');
        $connector3 = $this->authoringService->createConnector($activity4);

        $activity5 = $this->authoringService->createActivity($this->processDefinition, 'activity5 for interactive service unit test');
        $connector4 = $this->authoringService->createConnector($activity5);

        $newActivitiesArray = array(
            $activity4->getUri() => 2,
            $activity5->getUri() => 3
        );

        $this->authoringService->setParallelActivities($connector2, $newActivitiesArray);
        $activity6 = $this->authoringService->createActivity($this->processDefinition);
		$connector3 = wfAuthoring_models_classes_ConnectorService::singleton()->createJoin(array($activity4, $activity5), $activity6);
        /*
        $activity6 = $this->authoringService->createJoinActivity($connector3, null, '', $activity4);
		$activity7 = $this->authoringService->createJoinActivity($connector4, $activity6, '', $activity5);
		*/
		
		//check if the connector merging has been effective:
		$this->assertFalse($connector4->exists());
		//$this->assertEquals($activity6->getUri(), $activity7->getUri());
		
        $this->assertEquals($this->service->getType($connector2)->getUri(), INSTANCE_TYPEOFCONNECTORS_PARALLEL);
        $this->assertEquals($this->service->getType($connector3)->getUri(), INSTANCE_TYPEOFCONNECTORS_JOIN);

        $then->delete(true);
        $else->delete(true);
        $activity3->delete(true);
        $activity4->delete(true);
        $activity5->delete(true);
        $activity6->delete(true);

        $transitionRule->delete(true);
        $connector1->delete(true);
        $connector2->delete(true);
        $connector3->delete(true);
//        $connector4->delete(true);
		

    }
    
  public function testGetNextActivities(){


        /*
         *  activity > connector1(COND)
         *  -> THEN  > thenConnector(SQ)
         *  -> ELSE > elseConnector (SQ)
         *  -> Act3 > connector2(PARA)
         *  -> Act4 > connector3(JOIN)
         *  -> Act5 > connector4(JOIN)
         * 	-> Acto6
         *
         */
        $connector1 = $this->authoringService->createConnector($this->activity);

        $then = $this->authoringService->createConditionalActivity($connector1, 'then');//create "then Activity"
        $thenConnector = $this->authoringService->createConnector($then, 'then Connector');//create connector for "then Activity"
        
        $else = $this->authoringService->createConditionalActivity($connector1, 'else', null, '', true);//create "else Activity"
        $elseConnector = $this->authoringService->createConnector($else, 'else Connector');//create connector for "else Activity"

        $activity3 = $this->authoringService->createSequenceActivity($thenConnector, null, 'Act3');
        $this->authoringService->createSequenceActivity($elseConnector, $activity3);

       //  $this->assertIsA($this->service->getNextActivities($thenConnector),'core_kernel_classes_ContainerCollection');
        // $this->assertTrue($this->service->getNextActivities($thenConnector)->count() == 3 );

        $connector1NextAct = $this->service->getNextActivities($connector1);
        $connector1RealNextAct = array($then->getUri(),$else->getUri());
        
        
        $this->assertInternalType('array',$connector1NextAct);
        $this->assertEquals(sizeof($connector1NextAct), 2);
        foreach ($connector1NextAct as $nextAct){
           $this->assertTrue(in_array($nextAct->getUri(), $connector1RealNextAct));
        }
    
        $elseNextAct = $this->service->getNextActivities($elseConnector);

        $this->assertInternalType('array',$elseNextAct);
        $this->assertTrue(sizeof($elseNextAct) == 1);
        if(isset($elseNextAct[0]) && $elseNextAct[0] instanceof core_kernel_classes_Resource){
            $this->assertTrue($elseNextAct[0]->getUri() == $activity3->getUri());
        }

        $thenNextAct = $this->service->getNextActivities($thenConnector);

        $this->assertInternalType('array',$thenNextAct);
        $this->assertTrue(sizeof($thenNextAct) == 1);
         if(isset($thenNextAct[0]) && $thenNextAct[0] instanceof core_kernel_classes_Resource){
            $this->assertTrue($thenNextAct[0]->getUri() == $activity3->getUri());
        }

        
        $myProcessVar1 = $this->variableService->getProcessVariable('myProcessVarCode1', true);
        $transitionRule = $this->authoringService->createTransitionRule($connector1, '^myProcessVarCode1 == 1');
        

        $connector2 = $this->authoringService->createConnector($activity3);
        $activity4 = $this->authoringService->createActivity($this->processDefinition, 'activity4 for interactive service unit test');
        $connector3 = $this->authoringService->createConnector($activity4);

        $activity5 = $this->authoringService->createActivity($this->processDefinition, 'activity5 for interactive service unit test');
        $connector4 = $this->authoringService->createConnector($activity5);

        $newActivitiesArray = array(
            $activity4->getUri() => 2,
            $activity5->getUri() => 3
        );

        $this->authoringService->setParallelActivities($connector2, $newActivitiesArray);
        $activity6 = $this->authoringService->createActivity($this->processDefinition);
        $connector3 = wfAuthoring_models_classes_ConnectorService::singleton()->createJoin(array($activity4, $activity5), $activity6);
        /*
        $activity6 = $this->authoringService->createJoinActivity($connector3, null, '', $activity4);
		$activity7 = $this->authoringService->createJoinActivity($connector4, $activity6, '', $activity5);
		*/
        
		//check if the connector merging has been effective:
		$this->assertFalse($connector4->exists());
		//$this->assertEquals($activity6->getUri(), $activity7->getUri());
		
		$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
        $activity3NextActi = $this->service->getNextActivities($connector2);
        $this->assertInternalType('array',$activity3NextActi);
        $this->assertEquals(sizeof($activity3NextActi), 2);
        $newActivitiesarrayCount = array();
        foreach ($activity3NextActi as $cardinality){
			$this->assertTrue($cardinalityService->isCardinality($cardinality));
			$activity = $cardinalityService->getDestination($cardinality);
			$keyExists = array_key_exists($activity->getUri(), $newActivitiesArray);
            $this->assertTrue($keyExists);
            if($keyExists){
				$newActivitiesarrayCount[$activity->getUri()] = $cardinalityService->getCardinality($cardinality);
            }
        }
        $this->assertEquals($newActivitiesarrayCount, $newActivitiesArray);
        
        $activity4NextActi = $this->service->getNextActivities($connector3);
        $this->assertTrue(sizeof($activity4NextActi) == 1);
         if(isset($activity4NextActi[0]) && $activity4NextActi[0] instanceof core_kernel_classes_Resource){
                        $activity4NextActi[0]->getLabel();
             $this->assertTrue($activity4NextActi[0]->getUri() == $activity6->getUri());
        }
        
        $then->delete(true);
        $else->delete(true);
        $activity3->delete(true);
        $activity4->delete(true);
        $activity5->delete(true);
        $activity6->delete(true);

        $transitionRule->delete(true);
        $connector1->delete(true);
        $connector2->delete(true);
        $connector3->delete(true);
        $connector4->delete(true);

    }
    
 public function testGetPreviousActivities(){


        /*
         *  activity > connector1(COND)
         *  -> THEN  > thenConnector(SQ)
         *  -> ELSE > elseConnector (SQ)
         *  -> Act3 > connector2(PARA)
         *  -> Act4 > connector3(JOIN)
         *  -> Act5 > connector4(JOIN)
         * 	-> Acto6
         *
         */
        $connector1 = $this->authoringService->createConnector($this->activity);

        $then = $this->authoringService->createConditionalActivity($connector1, 'then');//create "Activity_2"
        $thenConnector = $this->authoringService->createConnector($then, 'then Connector');//create "Activity_2"

        $else = $this->authoringService->createConditionalActivity($connector1, 'else', null, '', true);//create another connector
        $elseConnector = $this->authoringService->createConnector($else, 'else Connector');//create "Activity_2"

        $activity3 = $this->authoringService->createSequenceActivity($thenConnector, null, 'Act3');
        $this->authoringService->createSequenceActivity($elseConnector, $activity3);

    
        $connector1PrevAct = $this->service->getPreviousActivities($connector1);
        
        $this->assertInternalType('array',$connector1PrevAct);
        $this->assertTrue(sizeof($connector1PrevAct) == 1);
        if(isset($connector1PrevAct[0]) && $connector1PrevAct[0] instanceof core_kernel_classes_Resource){
            $this->assertTrue($connector1PrevAct[0]->getUri() == $this->activity->getUri());
        }
        
        $elsePrevAct = $this->service->getPreviousActivities($elseConnector);
       

        $this->assertInternalType('array',$elsePrevAct);
        $this->assertTrue(sizeof($elsePrevAct) == 1);
        if(isset($elsePrevAct[0]) && $elsePrevAct[0] instanceof core_kernel_classes_Resource){
            $this->assertTrue($elsePrevAct[0]->getUri() == $else->getUri());
        }

        $thenPrevAct = $this->service->getPreviousActivities($thenConnector);
        $this->assertInternalType('array',$thenPrevAct);
        $this->assertTrue(sizeof($thenPrevAct) == 1);
        if(isset($thenPrevAct[0]) && $thenPrevAct[0] instanceof core_kernel_classes_Resource){
            $this->assertTrue($thenPrevAct[0]->getUri() == $then->getUri());
        }

        
        $myProcessVar1 = $this->variableService->getProcessVariable('myProcessVarCode1', true);
        $transitionRule = $this->authoringService->createTransitionRule($connector1, '^myProcessVarCode1 == 1');
        

        $connector2 = $this->authoringService->createConnector($activity3);
        $activity4 = $this->authoringService->createActivity($this->processDefinition, 'activity4 for interactive service unit test');
        $connector3 = $this->authoringService->createConnector($activity4);

        $activity5 = $this->authoringService->createActivity($this->processDefinition, 'activity5 for interactive service unit test');
        $connector4 = $this->authoringService->createConnector($activity5);

        $newActivitiesArray = array(
            $activity4->getUri() => 2,
            $activity5->getUri() => 3
        );

        $this->authoringService->setParallelActivities($connector2, $newActivitiesArray);
        $activity6 = $this->authoringService->createActivity($this->processDefinition);
        $connector3 = wfAuthoring_models_classes_ConnectorService::singleton()->createJoin(array($activity4, $activity5), $activity6);
        /*
        $activity6 = $this->authoringService->createJoinActivity($connector3, null, '', $activity4);
		$activity7 = $this->authoringService->createJoinActivity($connector4, $activity6, '', $activity5);
		*/
        $this->assertEquals(count($this->service->getNextActivities($connector2)), 2);
		
        $activity3PrevActi = $this->service->getPreviousActivities($connector2);

         $this->assertInternalType('array', $activity3PrevActi);
        $this->assertTrue(sizeof($activity3PrevActi) == 1);
        if(isset($activity3PrevActi[0]) && $activity3PrevActi[0] instanceof core_kernel_classes_Resource){
            $this->assertTrue($activity3PrevActi[0]->getUri() == $activity3->getUri());
        }
        
        $activity4PrevActi = $this->service->getPreviousSteps($connector3);
        $this->assertInternalType('array',$activity4PrevActi);
        $this->assertEquals(sizeof($activity4PrevActi), 2);
        $cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
		$prevActivitiesarrayCount = array();
        foreach ($activity4PrevActi as $cardinality){
			$this->assertTrue($cardinalityService->isCardinality($cardinality));
			$activities = $cardinalityService->getPreviousSteps($cardinality);
			$this->assertEquals(count($activities), 1);
			$activity = current($activities);
			$keyExists = array_key_exists($activity->getUri(), $newActivitiesArray);
            $this->assertTrue($keyExists);
            if($keyExists){
				$prevActivitiesarrayCount[$activity->getUri()] = $cardinalityService->getCardinality($cardinality);
            }
        }
        $this->assertEquals($prevActivitiesarrayCount, $newActivitiesArray);
        
        $then->delete(true);
        $else->delete(true);
        $activity3->delete(true);
        $activity4->delete(true);
        $activity5->delete(true);
        $activity6->delete(true);

        $transitionRule->delete(true);
        $connector1->delete(true);
        $connector2->delete(true);
        $connector3->delete(true);
        $connector4->delete(true);

    }

}