<?php

use qtism\data\state\InterpolationTable;
use qtism\data\state\InterpolationTableEntry;
use qtism\data\state\InterpolationTableEntryCollection;
use qtism\common\enums\BaseType;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class InterpolationTableMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$baseType = BaseType::BOOLEAN;
		$entries = new InterpolationTableEntryCollection(); // Simulate that the variableDeclaration baseType is boolean.
		$entries[] = new InterpolationTableEntry(1.5, 'true');
		$entries[] = new InterpolationTableEntry(2.5, 'false', false);
		
		$component = new InterpolationTable($entries);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component, array($baseType));
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('interpolationTable', $element->nodeName);
		$entryElements = $element->getElementsByTagName('interpolationTableEntry');
		$this->assertEquals(2, $entryElements->length);
		
		$entry = $entryElements->item(0);
		$this->assertEquals('true', $entry->getAttribute('targetValue'));
		$this->assertEquals('1.5', $entry->getAttribute('sourceValue'));
		$this->assertEquals('true', $entry->getAttribute('includeBoundary'));
		
		$entry = $entryElements->item(1);
		$this->assertEquals('false', $entry->getAttribute('targetValue'));
		$this->assertEquals('2.5', $entry->getAttribute('sourceValue'));
		$this->assertEquals('false', $entry->getAttribute('includeBoundary'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<interpolationTable xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1">
				<interpolationTableEntry sourceValue="1.5" targetValue="true" includeBoundary="false"/>
				<interpolationTableEntry sourceValue="2.5" targetValue="false"/>
			</interpolationTable>
			'
		);
		$element = $dom->documentElement;
		
		$baseType = BaseType::BOOLEAN; // Theoretical variableDeclaration's baseType.
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element, array($baseType));
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\state\\InterpolationTable', $component);
		$entries = $component->getInterpolationTableEntries();
		$this->assertEquals(2, count($entries));
		
		$entry = $entries[0];
		$this->assertEquals(1.5, $entry->getSourceValue());
		$this->assertEquals(true, $entry->getTargetValue());
		$this->assertEquals(false, $entry->doesIncludeBoundary());
		
		$entry = $entries[1];
		$this->assertEquals(2.5, $entry->getSourceValue());
		$this->assertEquals(false, $entry->getTargetValue());
		$this->assertEquals(true, $entry->doesIncludeBoundary());
	}
}
