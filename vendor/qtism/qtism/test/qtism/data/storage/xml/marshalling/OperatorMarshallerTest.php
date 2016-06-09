<?php

use qtism\data\expressions\operators\Sum;
use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\BaseValue;
use qtism\common\enums\BaseType;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class OperatorMarshallerTest extends QtiSmTestCase {

	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<sum xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1">
				<sum>
					<baseValue baseType="integer">1</baseValue>
					<baseValue baseType="integer">2</baseValue>
				</sum>
				<sum>
					<baseValue baseType="integer">3</baseValue>
					<baseValue baseType="integer">4</baseValue>
				</sum>
			</sum>
			');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\operators\\Sum', $component);
		$this->assertEquals(2, count($component->getExpressions()));
		
		$subExpressions = $component->getExpressions();
		$sub1 = $subExpressions[0];
		$sub2 = $subExpressions[1];
		
		$this->assertEquals(2, count($sub1->getExpressions()));
		$this->assertEquals(2, count($sub2->getExpressions()));
		
		$sub1Expressions = $sub1->getExpressions();
		$sub11 = $sub1Expressions[0];
		$sub12 = $sub1Expressions[1];
		$this->assertInternalType('integer', $sub11->getValue());
		$this->assertInternalType('integer', $sub12->getValue());
		$this->assertEquals(1, $sub11->getValue());
		$this->assertEquals(2, $sub12->getValue());
		
		$sub2Expressions = $sub2->getExpressions();
		$sub21 = $sub2Expressions[0];
		$sub22 = $sub2Expressions[1];
		$this->assertInternalType('integer', $sub21->getValue());
		$this->assertInternalType('integer', $sub21->getValue());
		$this->assertEquals(3, $sub21->getValue());
		$this->assertEquals(4, $sub22->getValue());
	}
	
	public function testMarshall() {

		$sub1Operands = new ExpressionCollection(array(new BaseValue(BaseType::INTEGER, 1), new BaseValue(BaseType::INTEGER, 2)));
		$sub2Operands = new ExpressionCollection(array(new BaseValue(BaseType::INTEGER, 3), new BaseValue(BaseType::INTEGER, 4)));
		
		$sub1 = new Sum($sub1Operands);
		$sub2 = new Sum($sub2Operands);
		
		$sum = new Sum(new ExpressionCollection(array($sub1, $sub2)));
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($sum);
		$element = $marshaller->marshall($sum);
		
		$this->assertEquals('sum', $element->nodeName);
		
		$sums = $element->getElementsByTagName('sum');
		$this->assertEquals(2, $sums->length);
		
		$sum1 = $sums->item(0);
		$sum2 = $sums->item(1);
		
		$this->assertEquals('sum', $sum1->nodeName);
		$baseValues = $sum1->getElementsByTagName('baseValue');
		$this->assertEquals(2, $baseValues->length);
		$this->assertEquals('1', $baseValues->item(0)->nodeValue);
		$this->assertEquals('2', $baseValues->item(1)->nodeValue);
		
		$this->assertEquals('sum', $sum2->nodeName);
		$baseValues = $sum2->getElementsByTagName('baseValue');
		$this->assertEquals(2, $baseValues->length);
		$this->assertEquals('3', $baseValues->item(0)->nodeValue);
		$this->assertEquals('4', $baseValues->item(1)->nodeValue);
	}
}
