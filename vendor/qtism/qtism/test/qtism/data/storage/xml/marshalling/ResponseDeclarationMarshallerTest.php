<?php

use qtism\data\state\MapEntryCollection;
use qtism\data\state\MapEntry;
use qtism\data\state\Mapping;
use qtism\data\state\CorrectResponse;
use qtism\data\state\ResponseDeclaration;
use qtism\common\enums\Cardinality;
use qtism\common\enums\BaseType;
use qtism\data\state\Value;
use qtism\data\state\ValueCollection;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class ResponseDeclarationMarshallerTest extends QtiSmTestCase {

	public function testMarshallMinimal() {

		// Initialize a minimal responseDeclaration.
		$identifier = "response1";
		$cardinality = Cardinality::SINGLE;
		$baseType = BaseType::INTEGER;
		
		$component = new ResponseDeclaration($identifier, $baseType, $cardinality);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('responseDeclaration', $element->nodeName);
		$this->assertEquals('single', $element->getAttribute('cardinality'));
		$this->assertEquals('integer', $element->getAttribute('baseType'));
		$this->assertEquals('response1', $element->getAttribute('identifier'));
	}
	
	public function testMarshallCorrectResponse() {

		$identifier = "response2";
		$cardinality = Cardinality::MULTIPLE;
		$baseType = BaseType::DURATION;
		
		$component = new ResponseDeclaration($identifier, $baseType, $cardinality);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		
		$values = new ValueCollection();
		$values[] = new Value('P2D', $baseType); // 2 days
		$values[] = new Value('P2MT3H', $baseType); // 2 days, 3 hours
		$component->setCorrectResponse(new CorrectResponse($values));
		
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('responseDeclaration', $element->nodeName);
		$this->assertEquals('multiple', $element->getAttribute('cardinality'));
		$this->assertEquals('duration', $element->getAttribute('baseType'));
		
		$defaultValue = $element->getElementsByTagName('correctResponse');
		$this->assertEquals(1, $defaultValue->length);
		$defaultValue = $defaultValue->item(0);
		$this->assertEquals('correctResponse', $defaultValue->nodeName);
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
	
	public function testMarshallMapping() {

		$identifier = 'response3';
		$cardinality = Cardinality::SINGLE;
		$baseType = BaseType::FLOAT;
		
		$component = new ResponseDeclaration($identifier, $baseType, $cardinality);
		$entries = new MapEntryCollection();
		$entries[] = new MapEntry(1.0, 1.1, true);
		$entries[] = new MapEntry(1.1, 1.2, false);
		
		$mapping = new Mapping($entries, 0.0);
		$component->setMapping($mapping);
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('responseDeclaration', $element->nodeName);
		$this->assertEquals($identifier, $element->getAttribute('identifier'));
		$this->assertEquals('float', $element->getAttribute('baseType'));
		$this->assertEquals('single', $element->getAttribute('cardinality'));
		
		$correctResponses = $element->getElementsByTagName('defaultValue');
		$this->assertEquals(0, $correctResponses->length);
		
		$mapping = $element->getElementsByTagName('mapping');
		$this->assertEquals(1, $mapping->length);
		$entries = $mapping->item(0)->getElementsByTagName('mapEntry');
		$this->assertEquals(2, $entries->length);
		
		$entry = $entries->item(0);
		$this->assertEquals('mapEntry', $entry->nodeName);
		$this->assertEquals('1.0', $entry->getAttribute('mapKey'));
		$this->assertEquals('1.1', $entry->getAttribute('mappedValue'));
		
		$entry = $entries->item(1);
		$this->assertEquals('mapEntry', $entry->nodeName);
		$this->assertEquals('1.1', $entry->getAttribute('mapKey'));
		$this->assertEquals('1.2', $entry->getAttribute('mappedValue'));
	}
	
	public function testUnmarshallMinimal() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<responseDeclaration xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="responseDeclaration1" cardinality="single" baseType="integer"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\state\\ResponseDeclaration', $component);
		$this->assertEquals($component->getIdentifier(), 'responseDeclaration1');
		$this->assertEquals($component->getCardinality(), Cardinality::SINGLE);
		$this->assertEquals($component->getBaseType(), BaseType::INTEGER);
	}
	
	public function testUnmarshallCorrectResponse() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<responseDeclaration xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="responseDeclaration2" cardinality="multiple" baseType="duration">
				<correctResponse interpretation="Up to you!">
					<value>P2D</value>
					<value>P2MT3H</value>
				</correctResponse>
			</responseDeclaration>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\state\\ResponseDeclaration', $component);
		$this->assertEquals($component->getIdentifier(), 'responseDeclaration2');
		$this->assertEquals($component->getCardinality(), Cardinality::MULTIPLE);
		$this->assertEquals($component->getBaseType(), BaseType::DURATION);
		
		$correctResponse = $component->getCorrectResponse();
		$this->assertInstanceOf('qtism\\data\\state\\CorrectResponse', $correctResponse);
		$this->assertEquals('Up to you!', $correctResponse->getInterpretation());
		
		$values = $correctResponse->getValues();
		$this->assertEquals(2, count($values));
		
		$this->assertInstanceOf('qtism\\data\\state\\Value', $values[0]);
		$this->assertInstanceOf('qtism\\common\\datatypes\\Duration', $values[0]->getValue());
		
		$this->assertInstanceOf('qtism\\data\\state\\Value', $values[1]);
		$this->assertInstanceOf('qtism\\common\\datatypes\\Duration', $values[1]->getValue());
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
