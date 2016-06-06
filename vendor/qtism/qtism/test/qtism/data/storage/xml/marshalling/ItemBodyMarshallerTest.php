<?php

use qtism\data\content\TextRun;
use qtism\data\content\BlockCollection;
use qtism\data\content\ItemBody;
use qtism\data\content\FlowCollection;
use qtism\data\content\xhtml\text\Div;
use qtism\data\content\InlineCollection;
use qtism\data\content\xhtml\text\H1;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class ItemBodyMarshallerTest extends QtiSmTestCase {

	public function testUnmarshall() {
        $itemBody = $this->createComponentFromXml('
            <itemBody id="my-body">
                <h1>Super Item</h1>
                <div>This is some stimulus.</div>   
            </itemBody>
        ');
        
        $this->assertInstanceOf('qtism\\data\\content\\ItemBody', $itemBody);
        $this->assertEquals('my-body', $itemBody->getId());
        $itemBodyContent = $itemBody->getContent();
        $this->assertEquals(2, count($itemBodyContent));
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\H1', $itemBodyContent[0]);
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\Div', $itemBodyContent[1]);
        
        $h1 = $itemBodyContent[0];
        $h1Content = $h1->getContent();
        $this->assertEquals(1, count($h1Content));
        $this->assertEquals('Super Item', $h1Content[0]->getContent());
        
        $div = $itemBodyContent[1];
        $divContent = $div->getContent();
        $this->assertEquals(1, count($divContent));
        $this->assertEquals('This is some stimulus.', $divContent[0]->getContent());
	}
	
	
	public function testMarshall() {
       
	    $h1 = new H1();
	    $h1->setContent(new InlineCollection(array(new TextRun('Super Item'))));
	    
	    $div = new Div();
	    $div->setContent(new FlowCollection(array(new TextRun('This is some stimulus.'))));
	    
	    $itemBody = new ItemBody('my-body');
	    $itemBody->setContent(new BlockCollection(array($h1, $div)));
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($itemBody)->marshall($itemBody);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<itemBody id="my-body"><h1>Super Item</h1><div>This is some stimulus.</div></itemBody>', $dom->saveXML($element));
	}
}
