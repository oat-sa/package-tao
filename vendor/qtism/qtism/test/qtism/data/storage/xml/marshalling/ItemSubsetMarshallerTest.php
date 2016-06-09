<?php

use qtism\data\expressions\ItemSubset;
use qtism\common\collections\IdentifierCollection;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class ItemSubsetMarshallerTest extends QtiSmTestCase {

	public function testMarshallNoCategories() {

		$sectionIdentifier = 'mySection1';
		
		$component = new ItemSubset();
		$component->setSectionIdentifier($sectionIdentifier);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('itemSubset', $element->nodeName);
		$this->assertEquals($sectionIdentifier, $element->getAttribute('sectionIdentifier'));
	}
	
	public function testMarshallIncludeCategories() {

		$sectionIdentifier = 'mySection1';
		$includeCategories = new IdentifierCollection(array('cat1', 'cat2'));
		
		$component = new ItemSubset();
		$component->setSectionIdentifier($sectionIdentifier);
		$component->setIncludeCategories($includeCategories);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('itemSubset', $element->nodeName);
		$this->assertEquals($sectionIdentifier, $element->getAttribute('sectionIdentifier'));
		$this->assertEquals(implode("\x20", $includeCategories->getArrayCopy()), $element->getAttribute('includeCategory'));
	}
	
	public function testMarshallIncludeExcludeCategories() {

		$sectionIdentifier = 'mySection1';
		$includeCategories = new IdentifierCollection(array('cat1', 'cat2'));
		$excludeCategories = new IdentifierCollection(array('cat3', 'cat4'));
	
		$component = new ItemSubset();
		$component->setSectionIdentifier($sectionIdentifier);
		$component->setIncludeCategories($includeCategories);
		$component->setExcludeCategories($excludeCategories);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
	
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('itemSubset', $element->nodeName);
		$this->assertEquals($sectionIdentifier, $element->getAttribute('sectionIdentifier'));
		$this->assertEquals(implode("\x20", $includeCategories->getArrayCopy()), $element->getAttribute('includeCategory'));
		$this->assertEquals(implode("\x20", $excludeCategories->getArrayCopy()), $element->getAttribute('excludeCategory'));
	}
	
	public function testUnmarshallNoCategories() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<itemSubset xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" sectionIdentifier="mySection1"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\ItemSubset', $component);
		$this->assertEquals($component->getSectionIdentifier(), 'mySection1');
	}
	
	public function testUnmarshallIncludeCategories() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<itemSubset xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" sectionIdentifier="mySection1" includeCategory="cat1 cat2"/>');
		$element = $dom->documentElement;
	
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
	
		$this->assertInstanceOf('qtism\\data\\expressions\\ItemSubset', $component);
		$this->assertEquals($component->getSectionIdentifier(), 'mySection1');
		$this->assertEquals(implode("\x20", $component->getIncludeCategories()->getArrayCopy()), 'cat1 cat2');
	}
	
	public function testUnmarshallIncludeExcludeCategories() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<itemSubset xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" sectionIdentifier="mySection1" includeCategory="cat1 cat2" excludeCategory="cat3"/>');
		$element = $dom->documentElement;
	
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
	
		$this->assertInstanceOf('qtism\\data\\expressions\\ItemSubset', $component);
		$this->assertEquals($component->getSectionIdentifier(), 'mySection1');
		$this->assertEquals(implode("\x20", $component->getIncludeCategories()->getArrayCopy()), 'cat1 cat2');
		$this->assertEquals(implode("\x20", $component->getExcludeCategories()->getArrayCopy()), 'cat3');
	}
}
