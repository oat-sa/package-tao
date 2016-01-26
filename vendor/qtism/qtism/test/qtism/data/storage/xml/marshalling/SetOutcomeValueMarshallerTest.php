<?php

use qtism\data\rules\SetOutcomeValue;
use qtism\data\expressions\BaseValue;
use qtism\common\enums\BaseType;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class SetOutcomeValueMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$identifier = 'variable1';
		
		$component = new SetOutcomeValue($identifier, new BaseValue(BaseType::BOOLEAN, true));
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('setOutcomeValue', $element->nodeName);
		$this->assertEquals('baseValue', $element->getElementsByTagName('baseValue')->item(0)->nodeName);
		$this->assertEquals($identifier, $element->getAttribute('identifier'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<setOutcomeValue xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="variable1">
				<baseValue baseType="boolean">true</baseValue>
			</setOutcomeValue>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\rules\\SetOutcomeValue', $component);
		$this->assertEquals('variable1', $component->getIdentifier());
		$this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $component->getExpression());
	}
}
