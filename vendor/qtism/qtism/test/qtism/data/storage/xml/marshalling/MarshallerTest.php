<?php

use qtism\data\storage\xml\marshalling\Marshaller;
use qtism\data\ItemSessionControl;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class MarshallerTest extends QtiSmTestCase {

	public function testCradle() {
		
		// Set cradle method accessible
		$class = new ReflectionClass('qtism\\data\\storage\\xml\\marshalling\\Marshaller');
		$method = $class->getMethod('getDOMCradle');
		$method->setAccessible(true);
		
		$this->assertInstanceOf('\\DOMDocument', $method->invoke(null));
	}
	
	public function testGetMarshaller() {
		$component = new ItemSessionControl();
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$this->assertInstanceOf('qtism\\data\\storage\\xml\\marshalling\\ItemSessionControlMarshaller', $marshaller);
	}
	
	public function testGetUnmarshaller() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<itemSessionControl xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" maxAttempts="1" validateResponses="true"/>');
		$marshaller = $this->getMarshallerFactory()->createMarshaller($dom->documentElement);
		$this->assertInstanceOf('qtism\\data\\storage\\xml\\marshalling\\ItemSessionControlMarshaller', $marshaller);
	}
	
	public function testGetFirstChildElement() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<parent>some text <child/> <![CDATA[function() { alert("go!"); }]]></parent>');
		$element = $dom->documentElement;
		
		$child = Marshaller::getFirstChildElement($element);
		$this->assertInstanceOf('\\DOMElement', $child);
		$this->assertEquals('child', $child->nodeName);
	}
	
	public function testGetFirstChildElementNotFound() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<parent>some text <![CDATA[function() { alert("stop!"); }]]></parent>');
		$element = $dom->documentElement;
		
		$this->assertFalse(Marshaller::getFirstChildElement($element));
	}
	
	public function testGetChildElements() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<parent>some text <child/><anotherChild/> <![CDATA[function() { alert("go!"); }]]></parent>');
		$element = $dom->documentElement;
		
		$childElements = Marshaller::getChildElements($element); 
		
		$this->assertInternalType('array', $childElements);
		$this->assertEquals(2, count($childElements));
		$this->assertEquals('child', $childElements[0]->nodeName);
		$this->assertEquals('anotherChild', $childElements[1]->nodeName);
	}
	
	public function testGetChildElementsByTagName() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		
		// There are 3 child elements. 2 at the first level, 1 at the second.
		// We should find only 2 direct child elements.
		$dom->loadXML('<parent><child/><child/><parent><child/></parent></parent>');
		$element = $dom->documentElement;
		
		$this->assertEquals(2, count(Marshaller::getChildElementsByTagName($element, 'child')));
	}
	
	public function testGetChildElementsByTagNameMultiple() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<parent><child/><child/><grandChild/><uncle/></parent>');
		$element = $dom->documentElement;
		
		$this->assertEquals(3, count(Marshaller::getChildElementsByTagName($element, array('child', 'grandChild'))));
	}
	
	public function testGetChildElementsByTagNameEmpty() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		
		// There is only 1 child but at the second level. Nothing
		// should be found.
		$dom->loadXML('<parent><parent><child/></parent></parent>');
		$element = $dom->documentElement;
		
		$this->assertEquals(0, count(Marshaller::getChildElementsByTagName($element, 'child')));
	}
	
	public function testGetXmlBase() {
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $dom->loadXML('<foo xml:base="http://forge.qtism.com"><bar>2000</bar><baz base="http://nowhere.com">fucked up beyond all recognition</baz></foo>');
	    
	    $foo = $dom->getElementsByTagName('foo')->item(0);
	    $bar = $dom->getElementsByTagName('bar')->item(0);
	    $baz = $dom->getElementsByTagName('baz')->item(0);
	    
	    $this->assertEquals('http://forge.qtism.com', Marshaller::getXmlBase($foo));
	    $this->assertFalse(Marshaller::getXmlBase($bar));
	    $this->assertFalse(Marshaller::getXmlBase($baz));
	}
	
	/**
	 * @depends testGetXmlBase
	 */
	public function testSetXmlBase() {
	    $dom = new DOMDocument('1.0');
	    $dom->loadXML('<foo><bar>2000</bar><baz>fucked up beyond all recognition</baz></foo>');
	    
	    $foo = $dom->getElementsByTagName('foo')->item(0);
	    $bar = $dom->getElementsByTagName('bar')->item(0);
	    $baz = $dom->getElementsByTagName('baz')->item(0);
	    
	    $this->assertFalse(Marshaller::getXmlBase($foo));
	    $this->assertFalse(Marshaller::getXmlBase($bar));
	    $this->assertFalse(Marshaller::getXmlBase($baz));
	    
	    Marshaller::setXmlBase($bar, 'http://my-new-base.com');
	    
	    $this->assertFalse(Marshaller::getXmlBase($foo));
	    $this->assertEquals('http://my-new-base.com', Marshaller::getXmlBase($bar));
	    $this->assertFalse(Marshaller::getXmlBase($baz));
	}
}
