<?php

use qtism\data\AssessmentSectionRef;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class AssessmentSectionRefMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		$identifier = 'mySectionRef';
		$href = 'http://www.rdfabout.com';
		
		$component = new AssessmentSectionRef($identifier, $href);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('assessmentSectionRef', $element->nodeName);
		$this->assertEquals($identifier, $element->getAttribute('identifier'));
		$this->assertEquals($href, $element->getAttribute('href'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<assessmentSectionRef xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="mySectionRef" href="http://www.rdfabout.com"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\AssessmentSectionRef', $component);
		$this->assertEquals($component->getIdentifier(), 'mySectionRef');
		$this->assertEquals($component->getHref(), 'http://www.rdfabout.com');
	}
}
