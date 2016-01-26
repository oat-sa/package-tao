<?php

use qtism\data\expressions\BaseValue;
use qtism\common\enums\BaseType;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class BaseValueMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		$baseType = BaseType::FLOAT;
		$value = 27.11;
		
		$component = new BaseValue($baseType, $value);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('baseValue', $element->nodeName);
		$this->assertEquals('float', $element->getAttribute('baseType'));
		$this->assertEquals($value . '', $element->nodeValue);
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<baseValue xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" baseType="float">27.11</baseValue>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $component);
		$this->assertEquals($component->getBaseType(), BaseType::FLOAT);
		$this->assertInternalType('float', $component->getValue());
		$this->assertEquals($component->getValue(), 27.11);
	}
	
	public function testUnmarshallCDATA() {
	    $element = $this->createDOMElement('<baseValue baseType="string"><![CDATA[A string...]]></baseValue>');
	    $component = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $component);
	    $this->assertEquals($component->getBaseType(), BaseType::STRING);
	    $this->assertEquals('A string...', $component->getValue());
	}
}
