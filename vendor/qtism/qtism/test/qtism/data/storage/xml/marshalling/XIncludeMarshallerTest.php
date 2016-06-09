<?php
namespace qtismtest\data\storage\xml\marshalling;

use qtism\data\XInclude;
use \DOMDocument;

class XIncludeMarshallerTest extends \QtiSmTestCase {

	public function testMarshall() {
	    $xinclude = new XInclude('<xi:include xmlns:xi="http://www.w3.org/2001/XInclude" href="path/to/file"/>');
	    $element = $this->getMarshallerFactory('2.1.0')->createMarshaller($xinclude)->marshall($xinclude);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<xi:include xmlns:xi="http://www.w3.org/2001/XInclude" href="path/to/file"/>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $element = $this->createDOMElement('<xi:include xmlns:xi="http://www.w3.org/2001/XInclude" href="path/to/file"/>');
	    
	    $xinclude = $this->getMarshallerFactory('2.1.0')->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\XInclude', $xinclude);
	    $this->assertEquals('path/to/file', $xinclude->getHref());
	    $xml = $xinclude->getXml();
	    $this->assertInstanceOf('\\DOMDocument', $xml);
	    
	    $includeElement = $xml->documentElement;
	    $this->assertEquals('xi', $includeElement->prefix);
	    $this->assertEquals('http://www.w3.org/2001/XInclude', $includeElement->namespaceURI);
	}
	
	public function testGetXmlWrongNamespace() {
	    $element = $this->createDOMElement('<xi:include xmlns:xi="http://www.fruits.org/1998/Include/IncludeYoghourt" href="path/to/file"/>');
	     
	    $xinclude = $this->getMarshallerFactory('2.1.0')->createMarshaller($element)->unmarshall($element);
	    $this->setExpectedException('\\RuntimeException');
	    $xml = $xinclude->getXml();
	}
}
