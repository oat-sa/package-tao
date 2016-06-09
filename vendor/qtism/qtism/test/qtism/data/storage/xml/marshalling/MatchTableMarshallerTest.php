<?php

use qtism\data\state\MatchTable;
use qtism\data\state\MatchTableEntry;
use qtism\data\state\MatchTableEntryCollection;
use qtism\common\enums\BaseType;
use qtism\common\datatypes\Pair;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class MatchTableMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$matchTableEntryCollection = new MatchTableEntryCollection();
		$matchTableEntryCollection[] = new MatchTableEntry(1, new Pair('A', 'B'));
		$matchTableEntryCollection[] = new MatchTableEntry(2, new Pair('A', 'C'));
		
		$component = new MatchTable($matchTableEntryCollection);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component, array(BaseType::PAIR));
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('matchTable', $element->nodeName);
		
		$entryElements = $element->getElementsByTagName('matchTableEntry');
		$this->assertEquals(2, $entryElements->length);
		$entry = $entryElements->item(0);
		$this->assertEquals($entry->getAttribute('targetValue'), 'A B');
		$this->assertEquals($entry->nodeName, 'matchTableEntry');
		$this->assertEquals($entry->getAttribute('sourceValue'), '1');
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<matchTable xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1">
				<matchTableEntry sourceValue="1" targetValue="A B"/>
				<matchTableEntry sourceValue="2" targetValue="A C"/>
			</matchTable>
			'
		);
		$element = $dom->documentElement;

		$marshaller = $this->getMarshallerFactory()->createMarshaller($element, array(BaseType::DIRECTED_PAIR));
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\state\\MatchTable', $component);
		$matchTableEntries = $component->getMatchTableEntries();
		$this->assertEquals(2, count($matchTableEntries));
		$entry = $matchTableEntries[0];
		$this->assertInstanceOf('qtism\\data\\state\\MatchTableEntry', $entry);
		$this->assertEquals(1, $entry->getSourceValue());
		$this->assertInstanceOf('qtism\\common\\datatypes\\DirectedPair', $entry->getTargetValue());
	}
}
