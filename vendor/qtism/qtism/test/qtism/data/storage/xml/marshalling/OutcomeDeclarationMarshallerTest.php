<?php

use qtism\common\datatypes\Duration;
use qtism\data\state\OutcomeDeclaration;
use qtism\common\enums\Cardinality;
use qtism\common\enums\BaseType;
use qtism\data\state\DefaultValue;
use qtism\data\state\Value;
use qtism\data\state\ValueCollection;
use qtism\data\state\MatchTable;
use qtism\data\state\MatchTableEntry;
use qtism\data\state\MatchTableEntryCollection;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class OutcomeDeclarationMarshallerTest extends QtiSmTestCase {

	public function testMarshallMinimal() {

		// Initialize a minimal outcomeDeclaration.
		$identifier = "outcome1";
		$cardinality = Cardinality::SINGLE;
		$baseType = BaseType::INTEGER;
		
		$component = new OutcomeDeclaration($identifier, $baseType, $cardinality);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('outcomeDeclaration', $element->nodeName);
		$this->assertEquals('single', $element->getAttribute('cardinality'));
		$this->assertEquals('integer', $element->getAttribute('baseType'));
		$this->assertEquals('outcome1', $element->getAttribute('identifier'));
	}
	
	public function testMarshallDefaultValue() {
		
		$identifier = "outcome2";
		$cardinality = Cardinality::MULTIPLE;
		$baseType = BaseType::DURATION;
		
		$component = new OutcomeDeclaration($identifier, $baseType, $cardinality);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		
		$values = new ValueCollection();
		$values[] = new Value('P2D', $baseType); // 2 days
		$values[] = new Value('P2MT3H', $baseType); // 2 days, 3 hours
		$component->setDefaultValue(new DefaultValue($values));
		
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('outcomeDeclaration', $element->nodeName);
		$this->assertEquals('multiple', $element->getAttribute('cardinality'));
		$this->assertEquals('duration', $element->getAttribute('baseType'));
		
		$defaultValue = $element->getElementsByTagName('defaultValue');
		$this->assertEquals(1, $defaultValue->length);
		$defaultValue = $defaultValue->item(0);
		$this->assertEquals('defaultValue', $defaultValue->nodeName);
		$this->assertEquals('', $defaultValue->getAttribute('interpretation'));
		
		$values = $defaultValue->getElementsByTagName('value');
		$this->assertEquals(2, $values->length);
		
		$value = $values->item(0);
		$this->assertEquals('value', $value->nodeName);
		$this->assertEquals('P2D', $value->nodeValue);
		$this->assertEquals('', $value->getAttribute('baseType')); // No baseType because not in a record.
		
		$value = $values->item(1);
		$this->assertEquals('value', $value->nodeName);
		$this->assertEquals('P2MT3H', $value->nodeValue); // No baseType because not in a record.
		$this->assertEquals('', $value->getAttribute('baseType'));
	}
	
	public function testMarshallMatchTable() {

		$identifier = 'outcome3';
		$cardinality = Cardinality::SINGLE;
		$baseType = BaseType::FLOAT;
		
		$component = new OutcomeDeclaration($identifier, $baseType, $cardinality);
		$entries = new MatchTableEntryCollection();
		$entries[] = new MatchTableEntry(1, 1.5);
		$entries[] = new MatchTableEntry(2, 2.5);
		
		$matchTable = new MatchTable($entries);
		$component->setLookupTable($matchTable);
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('outcomeDeclaration', $element->nodeName);
		$this->assertEquals($identifier, $element->getAttribute('identifier'));
		$this->assertEquals('float', $element->getAttribute('baseType'));
		$this->assertEquals('single', $element->getAttribute('cardinality'));
		
		$defaultValues = $element->getElementsByTagName('defaultValue');
		$this->assertEquals(0, $defaultValues->length);
		
		$lookupTable = $element->getElementsByTagName('matchTable');
		$this->assertEquals(1, $lookupTable->length);
		$entries = $lookupTable->item(0)->getElementsByTagName('matchTableEntry');
		$this->assertEquals(2, $entries->length);
		
		$entry = $entries->item(0);
		$this->assertEquals('matchTableEntry', $entry->nodeName);
		$this->assertEquals('1', $entry->getAttribute('sourceValue'));
		$this->assertEquals('1.5', $entry->getAttribute('targetValue'));
		
		$entry = $entries->item(1);
		$this->assertEquals('matchTableEntry', $entry->nodeName);
		$this->assertEquals('2', $entry->getAttribute('sourceValue'));
		$this->assertEquals('2.5', $entry->getAttribute('targetValue'));
	}
	
	public function testUnmarshallMinimal() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<outcomeDeclaration xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="outcomeDeclaration1" cardinality="single" baseType="integer"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\state\\OutcomeDeclaration', $component);
		$this->assertEquals($component->getIdentifier(), 'outcomeDeclaration1');
		$this->assertEquals($component->getCardinality(), Cardinality::SINGLE);
		$this->assertEquals($component->getBaseType(), BaseType::INTEGER);
	}
	
	public function testUnmarshallDefaultValue() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<outcomeDeclaration xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="outcomeDeclaration2" cardinality="multiple" baseType="duration">
				<defaultValue interpretation="Up to you!">
					<value>P2D</value>
					<value>P2MT3H</value>
				</defaultValue>
			</outcomeDeclaration>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\state\\OutcomeDeclaration', $component);
		$this->assertEquals($component->getIdentifier(), 'outcomeDeclaration2');
		$this->assertEquals($component->getCardinality(), Cardinality::MULTIPLE);
		$this->assertEquals($component->getBaseType(), BaseType::DURATION);
		
		$defaultValue = $component->getDefaultValue();
		$this->assertInstanceOf('qtism\\data\\state\\DefaultValue', $defaultValue);
		$this->assertEquals('Up to you!', $defaultValue->getInterpretation());
		
		$values = $defaultValue->getValues();
		$this->assertEquals(2, count($values));
		
		$this->assertInstanceOf('qtism\\data\\state\\Value', $values[0]);
		$this->assertInstanceOf('qtism\\common\\datatypes\\Duration', $values[0]->getValue());
		
		$this->assertInstanceOf('qtism\\data\\state\\Value', $values[1]);
		$this->assertInstanceOf('qtism\\common\\datatypes\\Duration', $values[1]->getValue());
	}
	
	public function testUnmarshallRecord() {
	    $dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<outcomeDeclaration xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="outcomeDeclarationRec" cardinality="record">
				<defaultValue interpretation="Just to test records...">
					<value fieldIdentifier="A" baseType="duration">P2D</value>
					<value fieldIdentifier="B" baseType="identifier">identifier1</value>
		            <value fieldIdentifier="C" baseType="float">1.11</value>
				</defaultValue>
			</outcomeDeclaration>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
	    $this->assertInstanceOf('qtism\\data\\state\\OutcomeDeclaration', $component);
		$this->assertEquals($component->getIdentifier(), 'outcomeDeclarationRec');
		$this->assertEquals($component->getCardinality(), Cardinality::RECORD);
		$this->assertEquals($component->getBaseType(), -1);
		$this->assertFalse($component->hasBaseType());
		
		$this->assertTrue($component->hasDefaultValue());
		$defaultValue = $component->getDefaultValue();
		$this->assertEquals('Just to test records...', $defaultValue->getInterpretation());
		
		$values = $defaultValue->getValues();
		$this->assertEquals(3, count($values));
		
		$this->assertInstanceOf('qtism\\data\\state\\Value', $values[0]);
		$this->assertTrue($values[0]->hasFieldIdentifier());
		$this->assertTrue($values[0]->hasBaseType());
		$this->assertEquals('A', $values[0]->getFieldIdentifier());
		$this->assertEquals(BaseType::DURATION, $values[0]->getBaseType());
		$this->assertTrue($values[0]->getValue()->equals(new Duration('P2D')));
		
		$this->assertInstanceOf('qtism\\data\\state\\Value', $values[1]);
		$this->assertTrue($values[1]->hasFieldIdentifier());
		$this->assertTrue($values[1]->hasBaseType());
		$this->assertEquals('B', $values[1]->getFieldIdentifier());
		$this->assertEquals(BaseType::IDENTIFIER, $values[1]->getBaseType());
		$this->assertEquals('identifier1', $values[1]->getValue());
		
		$this->assertInstanceOf('qtism\\data\\state\\Value', $values[2]);
		$this->assertTrue($values[2]->hasFieldIdentifier());
		$this->assertTrue($values[2]->hasBaseType());
		$this->assertEquals('C', $values[2]->getFieldIdentifier());
		$this->assertEquals(BaseType::FLOAT, $values[2]->getBaseType());
		$this->assertEquals(1.11, $values[2]->getValue());
	}
	
	public function testUnmarshallMatchTable() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<outcomeDeclaration xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="outcomeDeclaration3" cardinality="single" baseType="float">
				<matchTable>
					<matchTableEntry sourceValue="1" targetValue="1.5"/>
					<matchTableEntry sourceValue="2" targetValue="2.5"/>
				</matchTable>
			</outcomeDeclaration>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\state\\OutcomeDeclaration', $component);
		$matchTable = $component->getLookupTable();
		$this->assertInstanceOf('qtism\\data\\state\\MatchTable', $matchTable);
		$entries = $matchTable->getMatchTableEntries();
		$this->assertEquals(2, count($entries));
		
		$this->assertInternalType('integer', $entries[0]->getSourceValue());
		$this->assertEquals(1, $entries[0]->getSourceValue());
		$this->assertInternalType('float', $entries[0]->getTargetValue());
		$this->assertEquals(1.5, $entries[0]->getTargetValue());
		
		$this->assertInternalType('integer', $entries[0]->getSourceValue());
		$this->assertEquals(2, $entries[1]->getSourceValue());
		$this->assertInternalType('float', $entries[0]->getTargetValue());
		$this->assertEquals(2.5, $entries[1]->getTargetValue());
	}
}
