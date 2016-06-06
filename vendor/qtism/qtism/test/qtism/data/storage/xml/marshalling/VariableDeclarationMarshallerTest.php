<?php

use qtism\data\state\VariableDeclaration;
use qtism\common\enums\Cardinality;
use qtism\common\enums\BaseType;
use qtism\data\state\DefaultValue;
use qtism\data\state\Value;
use qtism\data\state\ValueCollection;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class VariableDeclarationMarshallerTest extends QtiSmTestCase {
	
	public function testMarshall() {
		
		$component = new VariableDeclaration('myVar', BaseType::INTEGER, Cardinality::SINGLE);
		
		$values = new ValueCollection();
		$values[] = new Value(10, BaseType::INTEGER);
		$component->setDefaultValue(new DefaultValue($values));
		
		$defaultValue = new DefaultValue($values);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\DOMElement', $element);
		$this->assertEquals('variableDeclaration', $element->nodeName);
		$this->assertEquals('myVar', $element->getAttribute('identifier'));
		$this->assertEquals('integer', $element->getAttribute('baseType'));
		
		$defaultValueElts = $element->getElementsByTagName('defaultValue');
		$this->assertEquals(1, $defaultValueElts->length);
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<variableDeclaration xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="myVar" baseType="integer" cardinality="single">
				<defaultValue>
					<value>10</value>
				</defaultValue>
			</variableDeclaration>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\state\\VariableDeclaration', $component);
		$this->assertEquals('myVar', $component->getIdentifier());
		$this->assertEquals(BaseType::INTEGER, $component->getBaseType());
		$this->assertEquals(Cardinality::SINGLE, $component->getCardinality());
		$this->assertInstanceOf('qtism\\data\\state\\DefaultValue', $component->getDefaultValue());
		
		$values = $component->getDefaultValue()->getValues();
		$this->assertEquals(1, count($values));
		$this->assertInternalType('integer', $values[0]->getValue());
	}
}
