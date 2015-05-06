<?php

use qtism\data\ShowHide;

use qtism\data\content\PrintedVariable;
use qtism\data\content\TextRun;
use qtism\data\content\TextOrVariableCollection;
use qtism\data\content\interactions\GapText;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class GapTextMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		$gapText = new GapText('gapText1', 1);
		$gapText->setContent(new TextOrVariableCollection(array(new TextRun('My var is '), new PrintedVariable('var1'))));
		
	    $marshaller = $this->getMarshallerFactory()->createMarshaller($gapText);
	    $element = $marshaller->marshall($gapText);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<gapText identifier="gapText1" matchMax="1">My var is <printedVariable identifier="var1" base="10" powerForm="false" delimiter=";" mappingIndicator="="/></gapText>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $element = $this->createDOMElement('
	        <gapText identifier="gapText1" matchMax="1">My var is <printedVariable identifier="var1" base="10" powerForm="false" delimiter=";" mappingIndicator="="/></gapText>
	    ');
	    
	    $marshaller = $this->getMarshallerFactory()->createMarshaller($element);
	    $gapText = $marshaller->unmarshall($element);
	    
	    $this->assertInstanceOf('qtism\\data\\content\\interactions\\GapText', $gapText);
	    $this->assertEquals('gapText1', $gapText->getIdentifier());
	    $this->assertEquals(1, $gapText->getMatchMax());
	    $this->assertEquals(0, $gapText->getMatchMin());
	    $this->assertFalse($gapText->isFixed());
	    $this->assertFalse($gapText->hasTemplateIdentifier());
	    $this->assertEquals(ShowHide::SHOW, $gapText->getShowHide());
	}
	
	public function testUnmarshallInvalid() {
	    $this->setExpectedException('qtism\\data\\storage\\xml\\marshalling\\UnmarshallingException');
	    $element = $element = $this->createDOMElement('
	        <gapText identifier="gapText1" matchMax="1">My var is <strong>invalid</strong>!</gapText>
	    ');
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	}
}