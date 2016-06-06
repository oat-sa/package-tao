<?php

use qtism\data\content\BlockCollection;
use qtism\data\content\xhtml\text\Blockquote;
use qtism\data\content\InlineCollection;
use qtism\data\content\xhtml\text\H4;
use qtism\data\content\TextRun;
use qtism\data\content\FlowCollection;
use qtism\data\content\xhtml\text\Div;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class BlockquoteMarshallerTest extends QtiSmTestCase {

	public function testUnmarshall() {
	    $blockquote = $this->createComponentFromXml('
	        <blockquote class="physics">
                <h4>Albert Einstein</h4>
	            <div class="description">An old Physicist.</div>
	        </blockquote>
	    ');
	    
	    $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\Blockquote', $blockquote);
	    $this->assertEquals('physics', $blockquote->getClass());
	    
	    $blockquoteContent = $blockquote->getContent();
	    $this->assertEquals(2, count($blockquoteContent));
	    
	    $h4 = $blockquoteContent[0];
	    $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\H4', $h4);
	    $h4Content = $h4->getContent();
	    $this->assertEquals(1, count($h4Content));
	    $this->assertEquals('Albert Einstein', $h4Content[0]->getContent());
	    
	    $div = $blockquoteContent[1];
	    $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\Div', $div);
	    $this->assertEquals('description', $div->getClass());
	    $divContent = $div->getContent();
	    $this->assertEquals(1, count($divContent));
	    $this->assertEquals('An old Physicist.', $divContent[0]->getContent());
	}
	
	
	public function testMarshall() {
	    $div = new Div();
	    $div->setClass('description');
	    $div->setContent(new FlowCollection(array(new TextRun('An old Physicist.'))));
	    
	    $h4 = new H4();
	    $h4->setContent(new InlineCollection(array(new TextRun('Albert Einstein'))));
	    
	    $blockquote = new Blockquote();
	    $blockquote->setClass('physics');
	    $blockquote->setContent(new BlockCollection(array($h4, $div)));
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($blockquote)->marshall($blockquote);
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    
	    $this->assertEquals('<blockquote class="physics"><h4>Albert Einstein</h4><div class="description">An old Physicist.</div></blockquote>', $dom->saveXML($element));
	}
}
