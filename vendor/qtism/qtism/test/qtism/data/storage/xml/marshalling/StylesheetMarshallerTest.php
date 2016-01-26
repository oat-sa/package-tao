<?php

use qtism\data\content\Stylesheet;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class StylesheetMarshallerTest extends QtiSmTestCase {

	public function testMarshallOne() {

		$uri = 'http://myuri.com';
		$type = 'text/css';
		$media = 'screen';
		$title = 'A pure stylesheet';
		
		$component = new Stylesheet($uri);
		$component->setType($type);
		$component->setMedia($media);
		$component->setTitle($title);
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('stylesheet', $element->nodeName);
		$this->assertEquals($uri, $element->getAttribute('href'));
		$this->assertEquals($type, $element->getAttribute('type'));
		$this->assertEquals($media, $element->getAttribute('media'));
		$this->assertEquals($title, $element->getAttribute('title'));
	}
	
	public function testMarshallTwo() {

		$uri = 'http://myuri.com';
	
		$component = new Stylesheet($uri);
	
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
	
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('stylesheet', $element->nodeName);
		$this->assertEquals($uri, $element->getAttribute('href'));
		$this->assertEquals('text/css', $element->getAttribute('type')); // default
		$this->assertEquals('screen', $element->getAttribute('media')); // default
		$this->assertFalse($element->hasAttribute('title'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<stylesheet xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" media="screen" href="http://myuri.com" type="text/css" title="A pure stylesheet"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\content\\Stylesheet', $component);
		$this->assertEquals($component->getHref(), 'http://myuri.com');
		$this->assertEquals($component->getTitle(), 'A pure stylesheet');
		$this->assertEquals($component->getMedia(), 'screen');
		$this->assertEquals($component->getType(), 'text/css');
	}
}
