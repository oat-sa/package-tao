<?php

use qtism\data\content\interactions\InlineChoiceInteraction;
use qtism\data\content\TextRun;
use qtism\data\content\TextOrVariableCollection;
use qtism\data\content\interactions\InlineChoice;
use qtism\data\content\interactions\InlineChoiceCollection;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class InlineChoiceInteractionMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		
	    $inlineChoices = new InlineChoiceCollection();
	    
	    $choice = new InlineChoice('inlineChoice1');
	    $choice->setFixed(true);
	    $choice->setContent(new TextOrVariableCollection(array(new TextRun('Option1'))));
	    $inlineChoices[] = $choice;
	    
	    $choice = new InlineChoice('inlineChoice2');
	    $choice->setContent(new TextOrVariableCollection(array(new TextRun('Option2'))));
	    $inlineChoices[] = $choice;
	    
	    $choice = new InlineChoice('inlineChoice3');
	    $choice->setContent(new TextOrVariableCollection(array(new TextRun('Option3'))));
	    $inlineChoices[] = $choice;
	    
	    $inlineChoiceInteraction = new InlineChoiceInteraction('RESPONSE', $inlineChoices);
	    $inlineChoiceInteraction->setShuffle(true);
	    $inlineChoiceInteraction->setRequired(true);
	    
        $element = $this->getMarshallerFactory()->createMarshaller($inlineChoiceInteraction)->marshall($inlineChoiceInteraction);
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->importNode($element, true);
        $this->assertEquals('<inlineChoiceInteraction responseIdentifier="RESPONSE" shuffle="true" required="true"><inlineChoice identifier="inlineChoice1" fixed="true">Option1</inlineChoice><inlineChoice identifier="inlineChoice2">Option2</inlineChoice><inlineChoice identifier="inlineChoice3">Option3</inlineChoice></inlineChoiceInteraction>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
        $element = $this->createDOMElement('
            <inlineChoiceInteraction responseIdentifier="RESPONSE" shuffle="true" required="true">
                <inlineChoice identifier="inlineChoice1" fixed="true">Option1</inlineChoice>
                <inlineChoice identifier="inlineChoice2">Option2</inlineChoice>
                <inlineChoice identifier="inlineChoice1">Option1</inlineChoice>
            </inlineChoiceInteraction>
        ');
        
        $inlineChoiceInteraction = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
        $this->assertInstanceOf('qtism\\data\\content\\interactions\\InlineChoiceInteraction', $inlineChoiceInteraction);
        $this->assertEquals('RESPONSE', $inlineChoiceInteraction->getResponseIdentifier());
        $this->assertTrue($inlineChoiceInteraction->mustShuffle());
        $this->assertTrue($inlineChoiceInteraction->isRequired());
        $this->assertEquals(3, count($inlineChoiceInteraction->getComponentsByClassName('inlineChoice')));
	}
}