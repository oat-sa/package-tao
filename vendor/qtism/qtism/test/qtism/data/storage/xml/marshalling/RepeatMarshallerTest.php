<?php

use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\operators\Repeat;
use qtism\data\expressions\BaseValue;
use qtism\data\expressions\operators\MathOperator;
use qtism\data\expressions\operators\MathFunctions;
use qtism\common\enums\BaseType;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class RepeatMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$sub1 = new BaseValue(BaseType::FLOAT, 23.545);
		$sub21 = new BaseValue(BaseType::FLOAT, 1.68);
		$sub2 = new MathOperator(new ExpressionCollection(array($sub21)), MathFunctions::SIN);
		
		$component = new Repeat(new ExpressionCollection(array($sub1, $sub2)), 2);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('repeat', $element->nodeName);
		$this->assertEquals('2', $element->getAttribute('numberRepeats'));
		
		$sub1 = $element->getElementsByTagName('baseValue')->item(0);
		$this->assertEquals('23.545', $sub1->nodeValue);
		$this->assertEquals('float', $sub1->getAttribute('baseType'));
		
		$sub2 = $element->getElementsByTagName('mathOperator')->item(0);
		$this->assertEquals('sin', $sub2->getAttribute('name'));
		
		$sub22 = $sub2->getElementsByTagName('baseValue')->item(0);
		$this->assertEquals('float', $sub22->getAttribute('baseType'));
		$this->assertEquals('1.68', $sub22->nodeValue);
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<repeat xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" numberRepeats="2">
				<mathOperator name="sin">
					<baseValue baseType="float">23.545</baseValue>
				</mathOperator>
				<repeat numberRepeats="10">
					<baseValue baseType="float">1.68</baseValue>
				</repeat>
			</repeat>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\operators\\Repeat', $component);
		$this->assertEquals(2, $component->getNumberRepeats());
		
		$sub1 = $component->getExpressions();
		$sub1 = $sub1[0];
		$this->assertInstanceOf('qtism\\data\\expressions\\operators\\MathOperator', $sub1);
		$this->assertEquals(MathFunctions::SIN, $sub1->getName());
		
		$sub11 = $sub1->getExpressions();
		$sub11 = $sub11[0];
		$this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $sub11);
		$this->assertInternalType('float', $sub11->getValue());
		$this->assertEquals(23.545, $sub11->getValue());
		$this->assertEquals(BaseType::FLOAT, $sub11->getBaseType());
		
		$sub2 = $component->getExpressions();
		$sub2 = $sub2[1];
		$this->assertInstanceOf('qtism\\data\\expressions\\operators\\Repeat', $sub2);
		$this->assertEquals(10, $sub2->getNumberRepeats());
		
		$sub21 = $sub2->getExpressions();
		$sub21 = $sub21[0];
		$this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $sub21);
		$this->assertInternalType('float', $sub21->getValue());
		$this->assertEquals(1.68, $sub21->getValue());
		$this->assertEquals(BaseType::FLOAT, $sub21->getBaseType());
	}
}
