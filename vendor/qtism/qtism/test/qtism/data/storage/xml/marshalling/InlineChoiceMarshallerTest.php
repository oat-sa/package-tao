<?php

use qtism\data\ShowHide;
use qtism\data\content\PrintedVariable;
use qtism\data\content\TextOrVariableCollection;
use qtism\data\content\interactions\InlineChoice;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class InlineChoiceMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    
	    $choice = new InlineChoice('choice1', 'my-choice1');
	    $choice->setContent(new TextOrVariableCollection(array(new PrintedVariable('pr1'))));
	    $choice->setFixed(true);
	    $choice->setTemplateIdentifier('tpl1');
	    $choice->setShowHide(ShowHide::HIDE);
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($choice)->marshall($choice);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<inlineChoice id="my-choice1" identifier="choice1" fixed="true" templateIdentifier="tpl1" showHide="hide"><printedVariable identifier="pr1" base="10" powerForm="false" delimiter=";" mappingIndicator="="/></inlineChoice>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $element = $this->createDOMElement('<inlineChoice id="my-choice1" identifier="choice1" fixed="true" templateIdentifier="tpl1" showHide="hide"><printedVariable identifier="pr1" base="10" powerForm="false" delimiter=";" mappingIndicator="="/></inlineChoice>');
	    $component = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    
	    $this->assertInstanceOf('qtism\\data\\content\\interactions\\InlineChoice', $component);
	    $this->assertEquals('my-choice1', $component->getId());
	    $this->assertEquals('choice1', $component->getIdentifier());
	    $this->assertTrue($component->isFixed());
	    $this->assertEquals('tpl1', $component->getTemplateIdentifier());
	    $this->assertEquals(ShowHide::HIDE, $component->getShowHide());
	    
	    $content = $component->getContent();
	    $this->assertEquals(1, count($content));
	    $this->assertInstanceOf('qtism\\data\\content\\PrintedVariable', $content[0]);
	}
}