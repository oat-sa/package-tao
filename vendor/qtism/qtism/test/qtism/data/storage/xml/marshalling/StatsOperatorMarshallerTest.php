<?php

use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\operators\StatsOperator;
use qtism\data\expressions\operators\Statistics;
use qtism\data\expressions\BaseValue;
use qtism\common\enums\BaseType;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class StatsOperatorMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$subExpr = new ExpressionCollection(array(new BaseValue(BaseType::FLOAT, 12.5468)));
		$name = Statistics::POP_VARIANCE;
		$component = new StatsOperator($subExpr, $name);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('statsOperator', $element->nodeName);
		$this->assertEquals('popVariance', $element->getAttribute('name'));
		
		$subExprElts = $element->getElementsByTagName('baseValue');
		$this->assertEquals(1, $subExprElts->length);
		$this->assertEquals('float', $subExprElts->item(0)->getAttribute('baseType'));
		$this->assertEquals('12.5468', $subExprElts->item(0)->nodeValue);
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<statsOperator xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" name="popVariance">
				<baseValue baseType="float">12.5468</baseValue>
			</statsOperator>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\operators\\StatsOperator', $component);
		$this->assertEquals(Statistics::POP_VARIANCE, $component->getName());
		
		$subExpr = $component->getExpressions();
		$this->assertEquals(1, count($subExpr));
		$this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $subExpr[0]);
		$this->assertInternalType('float', $subExpr[0]->getValue());
		$this->assertEquals(12.5468, $subExpr[0]->getValue());
		$this->assertEquals(BaseType::FLOAT, $subExpr[0]->getBaseType());
	}
}
