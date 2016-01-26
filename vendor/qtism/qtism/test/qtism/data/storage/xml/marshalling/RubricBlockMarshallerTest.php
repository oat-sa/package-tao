<?php

use qtism\data\content\StylesheetCollection;
use qtism\data\content\FlowStaticCollection;
use qtism\data\View;
use qtism\data\ViewCollection;
use qtism\data\content\RubricBlock;
use qtism\data\content\xhtml\text\P;
use qtism\data\content\TextRun;
use qtism\data\content\InlineCollection;
use qtism\data\content\xhtml\text\H3;
use qtism\data\content\Stylesheet;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class RubricBlockMarshallerTest extends QtiSmTestCase {

	public function testUnmarshall() {
        $rubricBlock = $this->createComponentFromXml('
            <rubricBlock class="warning" view="candidate tutor">
                <h3>Be carefull kiddo !</h3>
                <p>Read the instructions twice.</p>
                <stylesheet href="./stylesheet.css" type="text/css" media="screen"/>
            </rubricBlock>
        ');
        
        $this->assertInstanceOf('qtism\\data\\content\\RubricBlock', $rubricBlock);
        $this->assertEquals('warning', $rubricBlock->getClass());
        $this->assertEquals(2, count($rubricBlock->getViews()));
        
        $rubricBlockContent = $rubricBlock->getContent();
        $this->assertEquals(6, count($rubricBlockContent));
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\H3', $rubricBlockContent[1]);
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\P', $rubricBlockContent[3]);
        
        $stylesheets = $rubricBlock->getStylesheets();
        $this->assertEquals(1, count($stylesheets));
        $this->assertEquals('./stylesheet.css', $stylesheets[0]->getHref());
        $this->assertEquals('text/css', $stylesheets[0]->getType());
        $this->assertEquals('screen', $stylesheets[0]->getMedia());
        
	}
	
	
	public function testMarshall() {

	    $stylesheet = new Stylesheet('./stylesheet.css');
	    
	    $h3 = new H3();
	    $h3->setContent(new InlineCollection(array(new TextRun('Be carefull kiddo!'))));
	    
	    $p = new P();
	    $p->setContent(new InlineCollection(array(new TextRun('Read the instructions twice.'))));
	    
	    $rubricBlock = new RubricBlock(new ViewCollection(array(View::CANDIDATE, View::TUTOR)));
	    $rubricBlock->setClass('warning');
	    $rubricBlock->setContent(new FlowStaticCollection((array($h3, $p))));
	    $rubricBlock->setStylesheets(new StylesheetCollection(array($stylesheet)));
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($rubricBlock)->marshall($rubricBlock);
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    
	    $this->assertEquals('<rubricBlock view="candidate tutor" class="warning"><h3>Be carefull kiddo!</h3><p>Read the instructions twice.</p><stylesheet href="./stylesheet.css" media="screen" type="text/css"/></rubricBlock>', $dom->saveXML($element));
	}
}
