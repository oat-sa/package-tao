<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\common\datatypes\Boolean;
use qtism\runtime\common\State;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\rules\SetOutcomeValueProcessor;

class setOutcomeValueProcessorTest extends QtiSmTestCase {
	
	public function testSetOutcomeValueSimple() {
		$rule = $this->createComponentFromXml('
			<setOutcomeValue identifier="SCORE">
				<baseValue baseType="float">4.3</baseValue>
			</setOutcomeValue>
		');
		
		$processor = new SetOutcomeValueProcessor($rule);
		$score = new OutcomeVariable('SCORE', Cardinality::SINGLE, BaseType::FLOAT);
		$state = new State(array($score));
		$processor->setState($state);
		$processor->process();
		
		// The state must be modified.
		// OutcomeVariable with identifier 'SCORE' must contain 4.3.
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $state['SCORE']);
		$this->assertEquals(4.3, $state['SCORE']->getValue());
	}
	
	public function testSetOutcomeValueJugglingFromIntToFloat() {
	    $rule = $this->createComponentFromXml('
	        <setOutcomeValue identifier="SCORE">
	            <baseValue baseType="integer">4</baseValue>
	        </setOutcomeValue>
	    ');
	    
	    $processor = new SetOutcomeValueProcessor($rule);
	    $score = new OutcomeVariable('SCORE', Cardinality::SINGLE, BaseType::FLOAT);
	    $state = new State(array($score));
	    $processor->setState($state);
	    $processor->process();
	    
	    $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $state['SCORE']);
	    $this->assertEquals(4.0, $state['SCORE']->getValue());
	}
	
	public function testSetOtucomeValueJugglingFromFloatToInt() {
	    $rule = $this->createComponentFromXml('
	        <setOutcomeValue identifier="SCORE">
	            <baseValue baseType="float">4.3</baseValue>
	        </setOutcomeValue>
	    ');
	     
	    $processor = new SetOutcomeValueProcessor($rule);
	    $score = new OutcomeVariable('SCORE', Cardinality::SINGLE, BaseType::INTEGER);
	    $state = new State(array($score));
	    $processor->setState($state);
	    $processor->process();
	     
	    $this->assertInstanceOf('qtism\\common\\datatypes\\INTEGER', $state['SCORE']);
	    $this->assertEquals(4, $state['SCORE']->getValue());
	}
	
	public function testSetOutcomeValueWrongJugglingScalar() {
	    $rule = $this->createComponentFromXml('
	        <setOutcomeValue identifier="SCORE">
	            <baseValue baseType="string">String!</baseValue>
	        </setOutcomeValue>
	    ');
	    
	    $processor = new SetOutcomeValueProcessor($rule);
	    $score = new OutcomeVariable('SCORE', Cardinality::SINGLE, BaseType::INTEGER);
	    $state = new State(array($score));
	    $processor->setState($state);
	    
	    $this->setExpectedException('qtism\\runtime\\rules\\RuleProcessingException');
	    $processor->process();
	}
	
	public function testSetOutcomeValueWrongJugglingMultipleOne() {
	    $rule = $this->createComponentFromXml('
	        <setOutcomeValue identifier="SCORE">
	            <baseValue baseType="integer">1337</baseValue>
	        </setOutcomeValue>
	    ');
	     
	    $processor = new SetOutcomeValueProcessor($rule);
	    $score = new OutcomeVariable('SCORE', Cardinality::MULTIPLE, BaseType::INTEGER);
	    $state = new State(array($score));
	    $processor->setState($state);
	     
	    $this->setExpectedException('qtism\\runtime\\rules\\RuleProcessingException');
	    $processor->process();
	}
	
	public function testSetOutcomeValueWrongJugglingMultipleTwo() {
	    $rule = $this->createComponentFromXml('
	        <setOutcomeValue identifier="SCORE">
	            <multiple>
	                <baseValue baseType="float">1337.1337</baseValue>
	            </multiple>
	        </setOutcomeValue>
	    ');
	
	    $processor = new SetOutcomeValueProcessor($rule);
	    $score = new OutcomeVariable('SCORE', Cardinality::SINGLE, BaseType::INTEGER);
	    $state = new State(array($score));
	    $processor->setState($state);
	
	    $this->setExpectedException('qtism\\runtime\\rules\\RuleProcessingException');
	    $processor->process();
	}
	
	public function testSetOutcomeValueModerate() {
		$rule = $this->createComponentFromXml('
			<setOutcomeValue identifier="myBool">
				<member>
					<baseValue baseType="string">Incredible!</baseValue>
					<multiple>
						<baseValue baseType="string">This...</baseValue>
						<baseValue baseType="string">Is...</baseValue>
						<baseValue baseType="string">Incredible!</baseValue>
					</multiple>
				</member>
			</setOutcomeValue>
		');
		
		$processor = new SetOutcomeValueProcessor($rule);
		$myBool = new OutcomeVariable('myBool', Cardinality::SINGLE, BaseType::BOOLEAN, new Boolean(false));
		$state = new State(array($myBool));
		$this->assertFalse($state['myBool']->getValue());
		
		$processor->setState($state);
		$processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $state['myBool']);
		$this->assertTrue($state['myBool']->getValue());
	}
}