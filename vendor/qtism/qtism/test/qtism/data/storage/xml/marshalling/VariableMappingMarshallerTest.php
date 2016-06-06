<?php

use qtism\data\state\VariableMapping;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class VariableMappingMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		
		$source = 'myIdentifier1';
		$target = 'myIdentifier2';
		
		$component = new VariableMapping($source, $target);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('variableMapping', $element->nodeName);
		$this->assertEquals($source, $element->getAttribute('sourceIdentifier'));
		$this->assertEquals($target, $element->getAttribute('targetIdentifier'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<variableMapping xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" sourceIdentifier="myIdentifier1" targetIdentifier="myIdentifier2"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\state\\VariableMapping', $component);
		$this->assertEquals($component->getSource(), 'myIdentifier1');
		$this->assertEquals($component->getTarget(), 'myIdentifier2');
	}
}
