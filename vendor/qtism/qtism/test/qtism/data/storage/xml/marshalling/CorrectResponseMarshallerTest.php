<?php

use qtism\data\state\CorrectResponse;
use qtism\data\state\Value;
use qtism\data\state\ValueCollection;
use qtism\common\enums\BaseType;
use qtism\common\datatypes\Pair;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class CorrectResponseMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		$interpretation = 'It is up to you to interpret...';
		$pair = new Pair('id1', 'id2');
		$values = new ValueCollection();
		$values[] = new Value($pair);
		$component = new CorrectResponse($values, $interpretation);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('correctResponse', $element->nodeName);
		$this->assertEquals($interpretation, $element->getAttribute('interpretation'));
		$valueElements = $element->getElementsByTagName('value');
		$this->assertEquals(1, $valueElements->length);
		$valueElement = $valueElements->item(0);
		
		$this->assertEquals('value', $valueElement->nodeName);
		$this->assertEquals('id1 id2', $valueElement->nodeValue);
		$this->assertEquals('', $valueElement->getAttribute('baseType')); // no baseType attribute because not part of a record.
	}
	
	public function testUnmarshallOne() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<correctResponse xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" interpretation="My Interpretation">
				<value>25</value>
			</correctResponse>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element, array(BaseType::INTEGER));
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\state\\CorrectResponse', $component);
		$this->assertEquals('My Interpretation', $component->getInterpretation());
		$this->assertEquals(1, count($component->getValues()));
		
		$values = $component->getValues();
		$this->assertInstanceOf('qtism\\data\\state\\Value', $values[0]);
		$this->assertEquals(BaseType::INTEGER, $values[0]->getBaseType());
		$this->assertFalse($values[0]->isPartOfRecord());
	}
	
	public function testUnmarshallTwo() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
				'
			<correctResponse xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1">
				<value>A B</value>
				<value>C D</value>
			</correctResponse>
			'
		);
		$element = $dom->documentElement;
	
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element, array(BaseType::DIRECTED_PAIR));
		$component = $marshaller->unmarshall($element);
	
		$this->assertInstanceOf('qtism\\data\\state\\CorrectResponse', $component);
		$this->assertEquals('', $component->getInterpretation());
		$this->assertEquals(2, count($component->getValues()));
	
		foreach ($component->getValues() as $value) {
			$this->assertInstanceOf('qtism\\data\\state\\Value', $value);
			$this->assertEquals(BaseType::DIRECTED_PAIR, $value->getBaseType());
			$this->assertInstanceOf('qtism\\common\\datatypes\\DirectedPair', $value->getValue());
			$this->assertFalse($value->isPartOfRecord());
		}
	}
}
