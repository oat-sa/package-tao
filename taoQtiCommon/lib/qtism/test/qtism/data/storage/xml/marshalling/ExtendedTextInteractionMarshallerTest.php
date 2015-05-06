<?php

use qtism\data\content\interactions\TextFormat;
use qtism\data\content\interactions\ExtendedTextInteraction;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class ExtendedTextInteractionMarshallerTest extends QtiSmTestCase {

	public function testMarshallMinimal() {
	    $extendedTextInteraction = new ExtendedTextInteraction('RESPONSE');
        $element = $this->getMarshallerFactory()->createMarshaller($extendedTextInteraction)->marshall($extendedTextInteraction);
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->importNode($element, true);
        $this->assertEquals('<extendedTextInteraction responseIdentifier="RESPONSE"/>', $dom->saveXML($element));
	}
	
	public function testMarshallMaximal() {
	    $extendedTextInteraction = new ExtendedTextInteraction('RESPONSE');
	    $extendedTextInteraction->setBase(2);
	    $extendedTextInteraction->setStringIdentifier('mystring');
	    $extendedTextInteraction->setExpectedLength(35);
	    $extendedTextInteraction->setPatternMask('[0-9]+');
	    $extendedTextInteraction->setPlaceholderText('input here...');
	    $extendedTextInteraction->setMinStrings(2);
	    $extendedTextInteraction->setMaxStrings(10);
	    $extendedTextInteraction->setExpectedLines(1);
	    $extendedTextInteraction->setFormat(TextFormat::PRE_FORMATTED);
	    $element = $this->getMarshallerFactory()->createMarshaller($extendedTextInteraction)->marshall($extendedTextInteraction);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<extendedTextInteraction responseIdentifier="RESPONSE" base="2" stringIdentifier="mystring" expectedLength="35" patternMask="[0-9]+" placeholderText="input here..." maxStrings="10" minStrings="2" expectedLines="1" format="preFormatted"/>', $dom->saveXML($element));
	}
	
	public function testUnmarshallMinimal() {
        $element = $this->createDOMElement('<extendedTextInteraction responseIdentifier="RESPONSE"/>');
        $extendedTextInteraction = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
        
        $this->assertInstanceOf('qtism\\data\\content\\interactions\\ExtendedTextInteraction', $extendedTextInteraction);
        $this->assertEquals('RESPONSE', $extendedTextInteraction->getResponseIdentifier());
        $this->assertEquals(10, $extendedTextInteraction->getBase());
        $this->assertFalse($extendedTextInteraction->hasStringIdentifier());
        $this->assertFalse($extendedTextInteraction->hasExpectedLength());
        $this->assertFalse($extendedTextInteraction->hasPatternMask());
        $this->assertFalse($extendedTextInteraction->hasPlaceholderText());
	}
	
	public function testUnmarshallMaximal() {
	    $element = $this->createDOMElement('<extendedTextInteraction responseIdentifier="RESPONSE" base="2" stringIdentifier="mystring" expectedLength="35" patternMask="[0-9]+" placeholderText="input here..." maxStrings="10" minStrings="2" expectedLines="1" format="preFormatted"/>');
	    $extendedTextInteraction = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    
	    $this->assertInstanceOf('qtism\\data\\content\\interactions\\ExtendedTextInteraction', $extendedTextInteraction);
	    $this->assertEquals('RESPONSE', $extendedTextInteraction->getResponseIdentifier());
	    $this->assertEquals(2, $extendedTextInteraction->getBase());
	    $this->assertTrue($extendedTextInteraction->hasStringIdentifier());
	    $this->assertEquals('mystring', $extendedTextInteraction->getStringIdentifier());
	    $this->assertTrue($extendedTextInteraction->hasExpectedLength());
	    $this->assertEquals(35, $extendedTextInteraction->getExpectedLength());
	    $this->assertTrue($extendedTextInteraction->hasPatternMask());
	    $this->assertEquals('[0-9]+', $extendedTextInteraction->getPatternMask());
	    $this->assertTrue($extendedTextInteraction->hasPlaceholderText());
	    $this->assertEquals('input here...', $extendedTextInteraction->getPlaceholderText());
	    $this->assertTrue($extendedTextInteraction->hasMaxStrings());
	    $this->assertEquals(10, $extendedTextInteraction->getMaxStrings());
	    $this->assertEquals(2, $extendedTextInteraction->getMinStrings());
	    $this->assertEquals(1, $extendedTextInteraction->getExpectedLines());
	    $this->assertEquals(TextFormat::PRE_FORMATTED, $extendedTextInteraction->getFormat());
	}
}