<?php

use qtism\data\state\MapEntry;
use qtism\common\enums\BaseType;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class MapEntryMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$component = new MapEntry(1337, 1.377, true);
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component, array(BaseType::INTEGER));
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('mapEntry', $element->nodeName);
		$this->assertEquals('1337', $element->getAttribute('mapKey'));
		$this->assertEquals('1.377', $element->getAttribute('mappedValue'));
		$this->assertEquals('true', $element->getAttribute('caseSensitive'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<mapEntry xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" mapKey="1337" mappedValue="1.377" caseSensitive="true"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element, array(BaseType::INTEGER));
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\state\\MapEntry', $component);
		$this->assertInternalType('integer', $component->getMapKey());
		$this->assertEquals(1337, $component->getMapKey());
		$this->assertInternalType('float', $component->getMappedValue());
		$this->assertEquals(1.377, $component->getMappedValue());
		$this->assertInternalType('boolean', $component->isCaseSensitive());
		$this->assertEquals(true, $component->isCaseSensitive());
	}
}
