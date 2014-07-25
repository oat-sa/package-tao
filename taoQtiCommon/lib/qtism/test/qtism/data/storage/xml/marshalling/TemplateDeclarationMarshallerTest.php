<?php

use qtism\data\state\Value;
use qtism\data\state\ValueCollection;
use qtism\data\state\DefaultValue;
use qtism\common\enums\Cardinality;
use qtism\common\enums\BaseType;
use qtism\data\state\TemplateDeclaration;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class TemplateDeclarationMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    
	    $values = new ValueCollection(array(new Value('tplx', BaseType::IDENTIFIER)));
	    $defaultValue = new DefaultValue($values);
	    $templateDeclaration = new TemplateDeclaration('tpl1', BaseType::IDENTIFIER, Cardinality::SINGLE, $defaultValue);
	    $element = $this->getMarshallerFactory()->createMarshaller($templateDeclaration)->marshall($templateDeclaration);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<templateDeclaration identifier="tpl1" cardinality="single" baseType="identifier"><defaultValue><value>tplx</value></defaultValue></templateDeclaration>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $element = $this->createDOMElement('
	        <templateDeclaration identifier="tpl1" cardinality="single" baseType="identifier"><defaultValue><value>tplx</value></defaultValue></templateDeclaration>
	    ');
	    
	    $component = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\state\\TemplateDeclaration', $component);
	    $this->assertEquals('tpl1', $component->getIdentifier());
	    $this->assertEquals(Cardinality::SINGLE, $component->getCardinality());
	    $this->assertEquals(BaseType::IDENTIFIER, $component->getBaseType());
	    
	    $default = $component->getDefaultValue();
	    $this->assertInstanceOf('qtism\\data\\state\\DefaultValue', $default);
	    $values = $default->getValues();
	    $this->assertEquals(1, count($values));
	    $this->assertEquals('tplx', $values[0]->getValue());
	}
}