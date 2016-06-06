<?php

use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\operators\AnyN;
use qtism\data\expressions\BaseValue;
use qtism\common\enums\BaseType;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class AnyNMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		
		$subs = new ExpressionCollection();
		$subs[] = new BaseValue(BaseType::BOOLEAN, true);
		$subs[] = new BaseValue(BaseType::BOOLEAN, true);
		$subs[] = new BaseValue(BaseType::BOOLEAN, false);
	
		$min = 1;
		$max = 2;
		
		$component = new AnyN($subs, 1, 2);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('anyN', $element->nodeName);
		$this->assertEquals('' . $min, $element->getAttribute('min'));
		$this->assertEquals('' . $max, $element->getAttribute('max'));
		$this->assertEquals(3, $element->getElementsByTagName('baseValue')->length);
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<anyN xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" min="1" max="2">
				<baseValue baseType="boolean">true</baseValue>
				<baseValue baseType="boolean">true</baseValue>
				<baseValue baseType="boolean">false</baseValue>
			</anyN>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\operators\\AnyN', $component);
		$this->assertEquals(1, $component->getMin());
		$this->assertEquals(2, $component->getMax());
		$this->assertEquals(3, count($component->getExpressions()));
	}
}
