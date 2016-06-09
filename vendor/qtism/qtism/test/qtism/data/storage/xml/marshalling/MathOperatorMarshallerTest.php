<?php

use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\operators\MathOperator;
use qtism\data\expressions\operators\MathFunctions;
use qtism\data\expressions\BaseValue;
use qtism\common\enums\BaseType;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class MathOperatorMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$subExpr = new ExpressionCollection(array(new BaseValue(BaseType::FLOAT, 1.57))); // 90°
		$name = MathFunctions::SIN;
		$component = new MathOperator($subExpr, $name);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('mathOperator', $element->nodeName);
		$this->assertEquals('sin', $element->getAttribute('name'));
		
		$subExprElts = $element->getElementsByTagName('baseValue');
		$this->assertEquals(1, $subExprElts->length);
		$this->assertEquals('float', $subExprElts->item(0)->getAttribute('baseType'));
		$this->assertEquals('1.57', $subExprElts->item(0)->nodeValue);
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<mathOperator xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" name="sin">
				<baseValue baseType="float">1.57</baseValue>
			</mathOperator>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\operators\\MathOperator', $component);
		$this->assertEquals(MathFunctions::SIN, $component->getName());
		
		$subExpr = $component->getExpressions();
		$this->assertEquals(1, count($subExpr));
		$this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $subExpr[0]);
		$this->assertInternalType('float', $subExpr[0]->getValue());
		$this->assertEquals(1.57, $subExpr[0]->getValue());
		$this->assertEquals(BaseType::FLOAT, $subExpr[0]->getBaseType());
	}
}
