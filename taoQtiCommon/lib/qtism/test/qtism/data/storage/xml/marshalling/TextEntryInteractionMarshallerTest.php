<?php

use qtism\data\content\interactions\TextEntryInteraction;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class TextEntryInteractionMarshallerTest extends QtiSmTestCase {

	public function testMarshallMinimal() {
	    $textEntryInteraction = new TextEntryInteraction('RESPONSE');
        $element = $this->getMarshallerFactory()->createMarshaller($textEntryInteraction)->marshall($textEntryInteraction);
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->importNode($element, true);
        $this->assertEquals('<textEntryInteraction responseIdentifier="RESPONSE"/>', $dom->saveXML($element));
	}
	
	public function testMarshallMaximal() {
	    $textEntryInteraction = new TextEntryInteraction('RESPONSE');
	    $textEntryInteraction->setBase(2);
	    $textEntryInteraction->setStringIdentifier('mystring');
	    $textEntryInteraction->setExpectedLength(35);
	    $textEntryInteraction->setPatternMask('[0-9]+');
	    $textEntryInteraction->setPlaceholderText('input here...');
	    $element = $this->getMarshallerFactory()->createMarshaller($textEntryInteraction)->marshall($textEntryInteraction);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<textEntryInteraction responseIdentifier="RESPONSE" base="2" stringIdentifier="mystring" expectedLength="35" patternMask="[0-9]+" placeholderText="input here..."/>', $dom->saveXML($element));
	}
	
	public function testUnmarshallMinimal() {
        $element = $this->createDOMElement('<textEntryInteraction responseIdentifier="RESPONSE"/>');
        $textEntryInteraction = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
        
        $this->assertInstanceOf('qtism\\data\\content\\interactions\\TextEntryInteraction', $textEntryInteraction);
        $this->assertEquals('RESPONSE', $textEntryInteraction->getResponseIdentifier());
        $this->assertEquals(10, $textEntryInteraction->getBase());
        $this->assertFalse($textEntryInteraction->hasStringIdentifier());
        $this->assertFalse($textEntryInteraction->hasExpectedLength());
        $this->assertFalse($textEntryInteraction->hasPatternMask());
        $this->assertFalse($textEntryInteraction->hasPlaceholderText());
	}
	
	public function testUnmarshallMaximal() {
	    $element = $this->createDOMElement('<textEntryInteraction responseIdentifier="RESPONSE" base="2" stringIdentifier="mystring" expectedLength="35" patternMask="[0-9]+" placeholderText="input here..."/>');
	    $textEntryInteraction = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    
	    $this->assertInstanceOf('qtism\\data\\content\\interactions\\TextEntryInteraction', $textEntryInteraction);
	    $this->assertEquals('RESPONSE', $textEntryInteraction->getResponseIdentifier());
	    $this->assertEquals(2, $textEntryInteraction->getBase());
	    $this->assertTrue($textEntryInteraction->hasStringIdentifier());
	    $this->assertEquals('mystring', $textEntryInteraction->getStringIdentifier());
	    $this->assertTrue($textEntryInteraction->hasExpectedLength());
	    $this->assertEquals(35, $textEntryInteraction->getExpectedLength());
	    $this->assertTrue($textEntryInteraction->hasPatternMask());
	    $this->assertEquals('[0-9]+', $textEntryInteraction->getPatternMask());
	    $this->assertTrue($textEntryInteraction->hasPlaceholderText());
	    $this->assertEquals('input here...', $textEntryInteraction->getPlaceholderText());
	    
	}
}