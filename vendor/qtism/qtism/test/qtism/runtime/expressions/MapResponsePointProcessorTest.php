<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\common\datatypes\Point;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\common\State;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\expressions\MapResponsePointProcessor;

class MapResponsePointProcessorTest extends QtiSmTestCase {
	
	public function testSingleCardinality() {
		$expr = $this->createComponentFromXml('<mapResponsePoint identifier="response1"/>');
		$variableDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="response1" baseType="point" cardinality="single">
				<areaMapping defaultValue="666.666">
					<areaMapEntry shape="rect" coords="0,0,20,10" mappedValue="1"/>
					<areaMapEntry shape="poly" coords="0,8,7,4,2,2,8,-4,-2,1" mappedValue="2"/>
					<areaMapEntry shape="circle" coords="5,5,5" mappedValue="3"/>
				</areaMapping>
			</responseDeclaration>
		');
		$variable = ResponseVariable::createFromDataModel($variableDeclaration);
		$variable->setValue(new Point(1, 1)); // in rect, poly
		
		$processor = new MapResponsePointProcessor($expr);
		$state = new State(array($variable));
		
		$processor->setState($state);
		
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(3.0, $result->getValue());
		
		$state['response1'] = new Point(3, 3); // in rect, circle, poly
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(6, $result->getValue());
		
		$state['response1'] = new Point(19, 9); // in rect
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(1, $result->getValue());
		
		$state['response1'] = new Point(25, 25); // outside everything.
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(666.666, $result->getValue());
	}
	
	public function testMultipleCardinality() {
		$expr = $this->createComponentFromXml('<mapResponsePoint identifier="response1"/>');
		$variableDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="response1" baseType="point" cardinality="multiple">
				<areaMapping defaultValue="666.666">
					<areaMapEntry shape="rect" coords="0,0,20,10" mappedValue="1"/>
					<areaMapEntry shape="poly" coords="0,8,7,4,2,2,8,-4,-2,1" mappedValue="2"/>
					<areaMapEntry shape="circle" coords="5,5,5" mappedValue="3"/>
				</areaMapping>
			</responseDeclaration>
		');
		$variable = ResponseVariable::createFromDataModel($variableDeclaration);
		$points = new MultipleContainer(BaseType::POINT);
		$points[] = new Point(1, 1); // in rect, poly
		$points[] = new Point(3, 3); // in rect, circle, poly
		$variable->setValue($points);
		
		// because 1, 1 falls in 2 times in rect and poly, it is added to the total
		// just once only as per QTI 2.1 specification.
		// result = 1 + 2 + 3 = 6
		$processor = new MapResponsePointProcessor($expr);
		$state = new State(array($variable));
		$processor->setState($state);
		
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(6, $result->getValue());
		
		// Nothing matches... defaultValue returned.
		$points = new MultipleContainer(BaseType::POINT);
		$points[] = new Point(-1, -1);
		$points[] = new Point(21, 20);
		$state['response1'] = $points;
		
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(666.666, $result->getValue());
	}
	
	public function testNoVariable() {
		$expr = $this->createComponentFromXml('<mapResponsePoint identifier="response1"/>');
		$processor = new MapResponsePointProcessor($expr);
		$this->setExpectedException("qtism\\runtime\\expressions\\ExpressionProcessingException");
		$result = $processor->process();
	}
	
	public function testNoVariableValue() {
		$expr = $this->createComponentFromXml('<mapResponsePoint identifier="response1"/>');
		$variableDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="response1" baseType="point" cardinality="single">
				<areaMapping>
					<areaMapEntry shape="rect" coords="0 , 0 , 20 , 10" mappedValue="1"/>
				</areaMapping>
			</responseDeclaration>
		');
		$variable = ResponseVariable::createFromDataModel($variableDeclaration);
		$processor = new MapResponsePointProcessor($expr);
		$processor->setState(new State(array($variable)));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(0.0, $result->getValue());
	}
	
	public function testDefaultValue() {
		$expr = $this->createComponentFromXml('<mapResponsePoint identifier="response1"/>');
		$variableDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="response1" baseType="point" cardinality="single">
				<areaMapping defaultValue="2">
					<areaMapEntry shape="rect" coords="0 , 0 , 20 , 10" mappedValue="1"/>
				</areaMapping>
			</responseDeclaration>
		');
		$variable = ResponseVariable::createFromDataModel($variableDeclaration);
		$processor = new MapResponsePointProcessor($expr);
		$processor->setState(new State(array($variable)));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(2.0, $result->getValue());
	}
	
	public function testWrongBaseType() {
		$expr = $this->createComponentFromXml('<mapResponsePoint identifier="response1"/>');
		$variableDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="response1" baseType="integer" cardinality="single">
				<areaMapping>
					<areaMapEntry shape="rect" coords="0 , 0 , 20 , 10" mappedValue="1"/>
				</areaMapping>
			</responseDeclaration>
		');
		$variable = ResponseVariable::createFromDataModel($variableDeclaration);
		$processor = new MapResponsePointProcessor($expr);
		$processor->setState(new State(array($variable)));
		
		$this->setExpectedException("qtism\\runtime\\expressions\\ExpressionProcessingException");
		$result = $processor->process();
	}
	
	public function testNoAreaMapping() {
		$expr = $this->createComponentFromXml('<mapResponsePoint identifier="response1"/>');
		$variableDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="response1" baseType="integer" cardinality="single"/>
		');
		$variable = ResponseVariable::createFromDataModel($variableDeclaration);
		$processor = new MapResponsePointProcessor($expr);
		$processor->setState(new State(array($variable)));
        
		$result = $processor->process();
        $this->assertEquals(0.0, $result->getValue());
	}
	
	public function testWrongVariableType() {
		$expr = $this->createComponentFromXml('<mapResponsePoint identifier="response1"/>');
		$variableDeclaration = $this->createComponentFromXml('
			<outcomeDeclaration identifier="response1" baseType="point" cardinality="single"/>
		');
		$variable = OutcomeVariable::createFromDataModel($variableDeclaration);
		$processor = new MapResponsePointProcessor($expr);
		$processor->setState(new State(array($variable)));
		
		$this->setExpectedException("qtism\\runtime\\expressions\\ExpressionProcessingException");
		$result = $processor->process();
	}
	
	public function testLowerBoundOverflow() {
		$expr = $this->createComponentFromXml('<mapResponsePoint identifier="response1"/>');
		$variableDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="response1" baseType="point" cardinality="single">
				<areaMapping lowerBound="1">
					<areaMapEntry shape="rect" coords="0,0,20,10" mappedValue="-3"/>
					<areaMapEntry shape="circle" coords="5,5,5" mappedValue="1"/>
				</areaMapping>
			</responseDeclaration>
		');
		$variable = ResponseVariable::createFromDataModel($variableDeclaration);
		$processor = new MapResponsePointProcessor($expr);
		$variable->setValue(new Point(3, 3)); // inside everything.
		$processor->setState(new State(array($variable)));
		$result = $processor->process();
		
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(1, $result->getValue());
	}
	
	public function testUpperBoundOverflow() {
		$expr = $this->createComponentFromXml('<mapResponsePoint identifier="response1"/>');
		$variableDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="response1" baseType="point" cardinality="single">
				<areaMapping lowerBound="1" upperBound="5">
					<areaMapEntry shape="rect" coords="0,0,20,10" mappedValue="4"/>
					<areaMapEntry shape="circle" coords="5,5,5" mappedValue="2"/>
				</areaMapping>
			</responseDeclaration>
		');
		$variable = ResponseVariable::createFromDataModel($variableDeclaration);
		$processor = new MapResponsePointProcessor($expr);
		$variable->setValue(new Point(3, 3)); // inside everything.
		$processor->setState(new State(array($variable)));
		$result = $processor->process();
		
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(5, $result->getValue());
	}
}
