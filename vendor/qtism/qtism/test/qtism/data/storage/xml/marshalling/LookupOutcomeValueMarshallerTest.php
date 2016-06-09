<?php

use qtism\data\rules\LookupOutcomeValue;

use qtism\data\expressions\BaseValue;
use qtism\common\enums\BaseType;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class LookupOutcomeValueMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$component = new LookupOutcomeValue('myVariable1', new BaseValue(BaseType::STRING, 'a value'));
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('lookupOutcomeValue', $element->nodeName);
		$this->assertEquals('myVariable1', $element->getAttribute('identifier'));
		$this->assertEquals(1, $element->getElementsByTagName('baseValue')->length);
		$this->assertEquals('a value', $element->getElementsByTagName('baseValue')->item(0)->nodeValue);
		$this->assertEquals('string', $element->getElementsByTagName('baseValue')->item(0)->getAttribute('baseType'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<lookupOutcomeValue xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="myVariable1">
				<baseValue baseType="string">a value</baseValue>
			</lookupOutcomeValue>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\rules\\LookupOutcomeValue', $component);
		$this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $component->getExpression());
		$this->assertInternalType('string', $component->getExpression()->getValue());
		$this->assertEquals('a value', $component->getExpression()->getValue());
		$this->assertEquals(BaseType::STRING, $component->getExpression()->getBaseType());
	}
}
