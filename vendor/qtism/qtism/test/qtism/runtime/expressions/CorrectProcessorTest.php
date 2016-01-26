<?php

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\common\State;
use qtism\runtime\common\ResponseVariable;
use qtism\common\datatypes\DirectedPair;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\expressions\CorrectProcessor;

class CorrectProcessorTest extends QtiSmTestCase {
	
	public function testMultipleCardinality() {
		$responseDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="response1" baseType="directedPair" cardinality="multiple">
				<correctResponse>
					<value>A B</value>
					<value>C D</value>
				</correctResponse>
			</responseDeclaration>		
		');
		$expr = $this->createComponentFromXml('<correct identifier="response1"/>');
		
		$processor = new CorrectProcessor($expr);
		$variable = ResponseVariable::createFromDataModel($responseDeclaration);
		$processor->setState(new State(array($variable)));
		
		$comparable = new MultipleContainer(BaseType::DIRECTED_PAIR);
		$comparable[] = new DirectedPair('A', 'B');
		$comparable[] = new DirectedPair('C', 'D');
		
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\runtime\\common\\MultipleContainer', $result);
		$this->assertTrue($result->equals($comparable));
		$this->assertTrue($comparable->equals($result));
	}
	
	public function testSingleCardinality() {
		$responseDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="response1" baseType="integer" cardinality="single">
				<correctResponse interpretation="A single value!">
					<value>20</value>
				</correctResponse>
			</responseDeclaration>
		');
		$expr = $this->createComponentFromXml('<correct identifier="response1"/>');
		$variable = ResponseVariable::createFromDataModel($responseDeclaration);
		
		$processor = new CorrectProcessor($expr);
		$processor->setState(new State(array($variable)));
		
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(20, $result->getValue());
	}
	
	public function testNull() {
		$responseDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="response1" baseType="integer" cardinality="single"/>
		');
		
		$expr = $this->createComponentFromXml('<correct identifier="response1"/>');
		$variable = ResponseVariable::createFromDataModel($responseDeclaration);
		
		$processor = new CorrectProcessor($expr);
		$result = $processor->process(); // No state set.
		$this->assertTrue($result === null);
		
		$processor->setState(new State(array($variable)));
		$result = $processor->process();
		$this->assertTrue($result === null);
	}
	
	public function testException() {
		$variableDeclaration = $this->createComponentFromXml('
			<outcomeDeclaration identifier="outcome1" baseType="integer" cardinality="single"/>
		');
		$variable = OutcomeVariable::createFromDataModel($variableDeclaration);
		$expr = $this->createComponentFromXml('<correct identifier="outcome1"/>');
		
		$processor = new CorrectProcessor($expr);
		$processor->setState(new State(array($variable)));
		$this->setExpectedException("qtism\\runtime\\expressions\\ExpressionProcessingException");
		$result = $processor->process();
	}
}