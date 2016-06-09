<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\common\datatypes\Boolean;
use qtism\runtime\rules\RuleProcessingException;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\State;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\processing\OutcomeProcessingEngine;
use qtism\runtime\common\ProcessingException;

class OutcomeProcessingEngineTest extends QtiSmTestCase {
	
	public function testResponseProcessingMatchCorrect() {
		$outcomeProcessing = $this->createComponentFromXml('
		    <!-- I known that this outcomeProcessing is not well written but this is just
		         for a testing purpose. -->
		    <outcomeProcessing>
                <outcomeCondition>
		            <outcomeIf>
                        <match>
		                    <variable identifier="t"/>
		                    <baseValue baseType="boolean">true</baseValue>
		                </match>
		                <setOutcomeValue identifier="SCORE">
		                    <!-- 20/20 !!! -->
		                    <baseValue baseType="float">20</baseValue>
		                </setOutcomeValue>
		            </outcomeIf>
		        </outcomeCondition>
		        <outcomeCondition>
		            <outcomeIf>
                        <match>
		                    <variable identifier="t"/>
    		                <baseValue baseType="boolean">false</baseValue>
		                </match>
		                <setOutcomeValue identifier="SCORE">
		                    <!-- 0/20 !!! -->
		                    <baseValue baseType="float">0</baseValue>
		                </setOutcomeValue>
		            </outcomeIf>
		        </outcomeCondition>
            </outcomeProcessing>
		');
		
		$outcomeVariable = new OutcomeVariable('SCORE', Cardinality::SINGLE, BaseType::FLOAT);
		$context = new State(array($outcomeVariable));
		$engine = new OutcomeProcessingEngine($outcomeProcessing, $context);
		$engine->process();
		
		// SCORE is still NULL because the 't' variable was not provided to the context.
		$this->assertSame(null, $context['SCORE']);
		$context->setVariable(new OutcomeVariable('t', Cardinality::SINGLE, BaseType::BOOLEAN, new Boolean(true)));
		$this->assertTrue($context['t']->getValue());
		
		// After processing, the $context['SCORE'] value must be 20.0.
		$engine->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $context['SCORE']);
		$this->assertEquals(20.0, $context['SCORE']->getValue());
		
		$context['t'] = new Boolean(false);
		// After processing, the $context['SCORE'] value must switch to 0.0.
		$engine->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $context['SCORE']);
		$this->assertEquals(0.0, $context['SCORE']->getValue());
	}
	
	public function testResponseProcessingExitTest() {
	    $outcomeProcessing = $this->createComponentFromXml('
	        <outcomeProcessing>
                <exitTest/>
	        </outcomeProcessing>
	    ');
	    
	    $engine = new OutcomeProcessingEngine($outcomeProcessing);
	    
	    try {
	        $engine->process();
	        
	        // An exception must be raised because of the Test termination.
	        // In other words, the following code must be not reachable.
	        $this->assertTrue(false);
	    }
	    catch (ProcessingException $e) {
	        $this->assertInstanceOf('qtism\\runtime\\rules\\RuleProcessingException', $e);
	        $this->assertEquals(RuleProcessingException::EXIT_TEST, $e->getCode());
	    }
	}
}