<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\common\datatypes\Duration;
use qtism\common\enums\BaseType;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\common\State;
use qtism\runtime\expressions\DefaultProcessor;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\common\OutcomeVariable;

class DefaultProcessorTest extends QtiSmTestCase {
	
	public function testMultipleCardinality() {
		$variableDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="response1" baseType="duration" cardinality="ordered">
				<defaultValue>
					<value>P2D</value>
					<value>P3D</value>
					<value>P4D</value>
				</defaultValue>
			</responseDeclaration>
		');
		 
		$expr = $this->createComponentFromXml('<default identifier="response1"/>');
		$variable = ResponseVariable::createFromDataModel($variableDeclaration);
		$processor = new DefaultProcessor($expr);
		$processor->setState(new State(array($variable)));
		
		$comparable = new OrderedContainer(BaseType::DURATION);
		$comparable[] = new Duration('P2D');
		$comparable[] = new Duration('P3D');
		$comparable[] = new Duration('P4D');
		$this->assertTrue($comparable->equals($processor->process()));
	}
	
	public function testSingleCardinality() {
		$variableDeclaration = $this->createComponentFromXml('
			<outcomeDeclaration identifier="outcome1" baseType="boolean" cardinality="single">
				<defaultValue>
					<value>false</value>
				</defaultValue>
			</outcomeDeclaration>
		');
		$expr = $this->createComponentFromXml('<default identifier="outcome1"/>');
		$variable = OutcomeVariable::createFromDataModel($variableDeclaration);
		$processor = new DefaultProcessor($expr);
		$processor->setState(new State(array($variable)));
		$result = $processor->process();
		
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testNoVariable() {
		$expr = $this->createComponentFromXml('<default identifier="outcome1"/>');
		$processor = new DefaultProcessor($expr);
		$result = $processor->process();
		
		$this->assertTrue($result === null);
	}
	
	public function testNoDefaultValue() {
		$variableDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="response1" baseType="point" cardinality="multiple"/>
		');
		$expr = $this->createComponentFromXml('<default identifier="response1"/>');
		$processor = new DefaultProcessor($expr);
		$variable = ResponseVariable::createFromDataModel($variableDeclaration);
		$processor->setState(new State(array($variable)));
		$result = $processor->process();
		$this->assertTrue($result === null);
	}
}