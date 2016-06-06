<?php

use qtism\data\content\xhtml\lists\LiCollection;
use qtism\data\content\xhtml\lists\Ol;
use qtism\data\content\xhtml\lists\Ul;
use qtism\data\content\xhtml\text\P;
use qtism\data\content\xhtml\text\Em;
use qtism\data\content\InlineCollection;
use qtism\data\content\xhtml\text\Strong;
use qtism\data\content\TextRun;
use qtism\data\content\FlowCollection;
use qtism\data\content\xhtml\lists\Li;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class ListMarshallerTest extends QtiSmTestCase {

	public function testUnmarshallUl() {
		$ul = $this->createComponentFromXml('
		    <ul class="my-qti-list">
		        <li>Simple <strong>text</strong>.</li>
		        <li> olé
                   <ol id="ordered-list">
                      <li>
                         Some super <em>text</em>:
                         <p>Some complex text.</p>
		              </li>
		            </ol>
		        </li>
		    </ul>
		');
		
		$this->assertInstanceOf('qtism\\data\\content\\xhtml\\lists\\ul', $ul);
		$this->assertEquals('my-qti-list', $ul->getClass());
		
		$listItems = $ul->getContent();
		$this->assertEquals(2, count($listItems));
		
		// Check the first li node.
		$li = $listItems[0];
		$this->assertInstanceOf('qtism\\data\\content\\xhtml\\lists\\li', $li);
		$liContent = $li->getContent();
		$this->assertEquals(3, count($liContent));
		
		$this->assertInstanceOf('qtism\\data\\content\\TextRun', $liContent[0]);
		$this->assertEquals('Simple ', $liContent[0]->getContent());
		
		$this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\Strong', $liContent[1]);
		$strongContent = $liContent[1]->getContent();
		$this->assertEquals(1, count($strongContent));
		$this->assertEquals('text', $strongContent[0]->getContent());
		
		$this->assertInstanceOf('qtism\\data\\content\\TextRun', $liContent[2]);
		$this->assertEquals('.', $liContent[2]->getContent());
		
	    // Check the second li node.
	    $li = $listItems[1];
	    $this->assertInstanceOf('qtism\\data\\content\\xhtml\\lists\\li', $li);
	    $liContent = $li->getContent();
	    $this->assertEquals(3, count($liContent));
	    $this->assertEquals(" olé\n                   ", $liContent[0]->getContent());
	    
	    $ol = $liContent[1];
	    $this->assertInstanceOf('qtism\\data\\content\\xhtml\\lists\\ol', $ol);
	    $this->assertEquals('ordered-list', $ol->getId());
	    
	    $listItems = $ol->getContent();
	    $this->assertEquals(1, count($listItems));
	    $li = $listItems[0];
	    $this->assertInstanceOf('qtism\\data\\content\\xhtml\\lists\\li', $li);
	    $liContent = $li->getContent();
	    $this->assertEquals(5, count($liContent));
	    $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\Em', $liContent[1]);
	    $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\P', $liContent[3]);
	}
	
	
	public function testMarshallUl() {
	    $strong = new Strong();
	    $strong->setContent(new InlineCollection(array(new TextRun('text'))));
	    $li1 = new Li();
	    $li1->setContent(new FlowCollection(array(new TextRun('Simple '), $strong, new TextRun('.'))));
	    
	    $em = new Em();
	    $em->setContent(new InlineCollection(array(new TextRun('text'))));
	    
	    $p = new P();
	    $p->setContent(new InlineCollection(array(new TextRun('Some complex text.'))));
	    
	    $li3 = new Li();
	    $li3->setContent(new FlowCollection(array(new TextRun('Some super '), $em, new TextRun(':'), $p)));
	    
	    $ol = new Ol();
	    $ol->setId('ordered-list');
	    $ol->setContent(new LiCollection(array($li3)));
	    
	    $li2 = new Li();
	    $li2->setContent(new FlowCollection(array(new TextRun('olé '), $ol)));
	    
	    $ul = new Ul();
	    $ul->setClass('my-qti-list');
	    $ul->setContent(new LiCollection(array($li1, $li2)));
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($ul)->marshall($ul);
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    
	    $this->assertEquals('<ul class="my-qti-list"><li>Simple <strong>text</strong>.</li><li>olé <ol id="ordered-list"><li>Some super <em>text</em>:<p>Some complex text.</p></li></ol></li></ul>', $dom->saveXML($element));
	}
}
