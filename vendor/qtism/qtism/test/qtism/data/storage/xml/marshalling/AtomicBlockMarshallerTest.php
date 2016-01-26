<?php

use qtism\data\content\xhtml\text\Em;
use qtism\data\content\TextRun;
use qtism\data\content\InlineCollection;
use qtism\data\content\xhtml\text\P;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class AtomicBlockMarshallerTest extends QtiSmTestCase {

	public function testMarshallP() {
	    $p = new P('my-p');
	    $em = new Em();
	    $em->setContent(new InlineCollection(array(new TextRun('simple'))));
	    $p->setContent(new InlineCollection(array(new TextRun('This text is a '), $em , new TextRun(' test.'))));

	    $marshaller = $this->getMarshallerFactory()->createMarshaller($p);
	    $element = $marshaller->marshall($p);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<p id="my-p">This text is a <em>simple</em> test.</p>', $dom->saveXML($element));
	}
	
	public function testUnmarshallP() {
	    $p = $this->createComponentFromXml('
	        <p id="my-p">
                This text is
                a <em>simple</em> test.
            </p>
	    ');

	    $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\P', $p);
	    $this->assertEquals('my-p', $p->getId());
	    $this->assertEquals(3, count($p->getContent()));
	    
	    $content = $p->getContent();
	    $this->assertEquals("\n                This text is\n                a ", $content[0]->getContent());
	    $em = $content[1];
	    $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\Em', $em);
	    $emContent = $em->getContent();
	    $this->assertEquals('simple', $emContent[0]->getContent());
	    $this->assertEquals(" test.\n            ", $content[2]->getContent());
	}
}