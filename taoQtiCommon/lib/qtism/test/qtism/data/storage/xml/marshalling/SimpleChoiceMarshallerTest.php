<?php

use qtism\data\content\InlineCollection;
use qtism\data\content\xhtml\text\Strong;
use qtism\data\content\TextRun;
use qtism\data\content\FlowStaticCollection;
use qtism\data\content\interactions\SimpleChoice;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class SimpleChoiceMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		$simpleChoice = new SimpleChoice('choice_1');
		$simpleChoice->setClass('qti-simpleChoice');
		$strong = new Strong();
		$strong->setContent(new InlineCollection(array(new TextRun('strong'))));
	    $simpleChoice->setContent(new FlowStaticCollection(array(new TextRun('This is ... '), $strong, new TextRun('!'))));
	    
	    $marshaller = $this->getMarshallerFactory()->createMarshaller($simpleChoice);
	    $element = $marshaller->marshall($simpleChoice);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<simpleChoice class="qti-simpleChoice" identifier="choice_1">This is ... <strong>strong</strong>!</simpleChoice>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $element = $this->createDOMElement('
	        <simpleChoice class="qti-simpleChoice" identifier="choice_1">This is ... <strong>strong</strong>!</simpleChoice>
	    ');
	    
	    $marshaller = $this->getMarshallerFactory()->createMarshaller($element);
	    $component = $marshaller->unmarshall($element);
	    
	    $this->assertInstanceOf('qtism\\data\\content\\interactions\\SimpleChoice', $component);
	    $this->assertEquals('qti-simpleChoice', $component->getClass());
	    $this->assertEquals('choice_1', $component->getIdentifier());
	    
	    $content = $component->getContent();
	    $this->assertInstanceOf('qtism\\data\\content\\FlowStaticCollection', $content);
	    $this->assertEquals(3, count($content));
	}
}