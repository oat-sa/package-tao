<?php

use qtism\data\expressions\OutcomeMaximum;
use qtism\common\collections\IdentifierCollection;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class OutcomeMaximumMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$sectionIdentifier = 'mySection1';
		$outcomeIdentifier = 'myOutcome1';
		$includeCategory = 'cat1';
		$excludeCategory = 'cat2 cat3';
		$weightIdentifier = 'myWeight1';
		
		$component = new OutcomeMaximum($outcomeIdentifier, $weightIdentifier);
		$component->setSectionIdentifier($sectionIdentifier);
		$component->setIncludeCategories(new IdentifierCollection(explode("\x20", $includeCategory)));
		$component->setExcludeCategories(new IdentifierCollection(explode("\x20", $excludeCategory)));
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('outcomeMaximum', $element->nodeName);
		$this->assertEquals($sectionIdentifier, $element->getAttribute('sectionIdentifier'));
		$this->assertEquals($outcomeIdentifier, $element->getAttribute('outcomeIdentifier'));
		$this->assertEquals($weightIdentifier, $element->getAttribute('weightIdentifier'));
		$this->assertEquals($includeCategory, $element->getAttribute('includeCategory'));
		$this->assertEquals($excludeCategory, $element->getAttribute('excludeCategory'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<outcomeMaximum xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" sectionIdentifier="mySection1" outcomeIdentifier="myOutcome1" includeCategory="cat1" excludeCategory="cat2 cat3" weightIdentifier="myWeight1"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\OutcomeMaximum', $component);
		$this->assertEquals($component->getSectionIdentifier(), 'mySection1');
		$this->assertEquals($component->getOutcomeIdentifier(), 'myOutcome1');
		$this->assertEquals($component->getWeightIdentifier(), 'myWeight1');
		$this->assertEquals('cat1', implode("\x20", $component->getIncludeCategories()->getArrayCopy()));
		$this->assertEquals('cat2 cat3', implode("\x20", $component->getExcludeCategories()->getArrayCopy()));
	}
}
