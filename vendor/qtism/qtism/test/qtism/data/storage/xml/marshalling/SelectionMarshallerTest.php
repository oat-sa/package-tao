<?php

use qtism\data\rules\Selection;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class SelectionMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$select = 2;
		$withReplacement = true;
		
		$component = new Selection($select);
		$component->setWithReplacement($withReplacement);
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('selection', $element->nodeName);
		$this->assertSame($select . '', $element->getAttribute('select'));
		$this->assertEquals('true', $element->getAttribute('withReplacement'));
	}
	
	public function testUnmarshallValid() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<selection xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" select="2" withReplacement="true"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\Rules\\Selection', $component);
		$this->assertEquals($component->getSelect(), 2);
		$this->assertEquals($component->isWithReplacement(), true);
	}
	
	public function testUnmarshallInvalid() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		// the mandatory 'select' attribute is missing in the following test.
		$dom->loadXML('<selection xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" withReplacement="true"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		
		$this->setExpectedException('qtism\\data\\storage\\xml\\marshalling\\UnmarshallingException');
		$component = $marshaller->unmarshall($element);
	}
}
