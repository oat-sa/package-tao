<?php

use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\operators\Index;
use qtism\data\expressions\Variable;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class IndexMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$component = new Index(new ExpressionCollection(array(new Variable('orderedVar'))), 3);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('index', $element->nodeName);
		$this->assertEquals('3', $element->getAttribute('n'));
		
		$sub1 = $element->getElementsByTagName('variable')->item(0);
		$this->assertEquals('orderedVar', $sub1->getAttribute('identifier'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<index xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" n="3">
				<variable identifier="orderedVar"/>
			</index>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\operators\\Index', $component);
		$this->assertEquals(3, $component->getN());
		
		$sub1 = $component->getExpressions();
		$sub1 = $sub1[0];
		$this->assertInstanceOf('qtism\\data\\expressions\\Variable', $sub1);
		$this->assertEquals('orderedVar', $sub1->getIdentifier());
	}
}
