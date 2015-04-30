<?php

use qtism\runtime\rendering\markup\AbstractMarkupRenderingEngine;
use qtism\data\storage\xml\XmlDocument;
use qtism\runtime\rendering\markup\xhtml\XhtmlRenderingContext;
use qtism\runtime\rendering\markup\xhtml\XhtmlRenderingEngine;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class XhtmlRenderingEngineTest extends QtiSmTestCase {
	
	public function testVerySimple() {
	    $div = $this->createComponentFromXml('
	        <div id="my-div" class="container">bla bla</div>
	    ');
	    
	    $renderingEngine = new XhtmlRenderingEngine();
	    $rendering = $renderingEngine->render($div);
	    
	    $this->assertInstanceOf('\\DOMDocument', $rendering);
	    
	    $divElt = $rendering->documentElement;
	    $this->assertEquals('div', $divElt->nodeName);
	    $this->assertEquals('my-div', $divElt->getAttribute('id'));
	    $this->assertEquals('container qti-bodyElement qti-div', $divElt->getAttribute('class'));
	    
	    $text = $divElt->firstChild;
	    $this->assertInstanceOf('\\DOMText', $text);
	    $this->assertEquals('bla bla', $text->wholeText);
	}
	
	public function testIgnoreClassesOne() {
	   
	   $renderingEngine = new XhtmlRenderingEngine();
	   $renderingEngine->ignoreQtiClasses('h1');
	    
	   $div = $this->createComponentFromXml('
	       <div>
              <h1>I will be ignored...</h1>
	          <p>I am alive!</p>
	       </div>
	   ');
	   
	   $divElt = $renderingEngine->render($div)->documentElement;
	   $this->assertEquals('div', $divElt->nodeName);
	   
	   $h1s = $divElt->getElementsByTagName('h1');
	   $this->assertEquals(0, $h1s->length);
	   
	   $ps = $divElt->getElementsByTagName('p');
	   $this->assertEquals(1, $ps->length);
	}
	
	public function testSeparateStylesheetOne() {

	    // The loaded component is a rubricBlock component
	    // with a single stylesheet component within.
	    $doc = new XmlDocument();
	    $doc->load(self::samplesDir() . 'rendering/rubricblock_2.xml');
	    $this->assertEquals(1, count($doc->getDocumentComponent()->getStylesheets()));
	    
	    $renderingEngine = new XhtmlRenderingEngine();
	    $renderingEngine->setStylesheetPolicy(XhtmlRenderingEngine::STYLESHEET_SEPARATE);
	    $rendering = $renderingEngine->render($doc->getDocumentComponent());
	    
	    // The main rendering must not contain <link> XHTML elements at all.
	    $linkElts = $rendering->getElementsByTagName('link');
	    $this->assertEquals(0, $linkElts->length);
	    
	    // The separate rendering must contain a single <link> element.
	    $linksFragment = $renderingEngine->getStylesheets();
	    $this->assertInstanceOf('\\DOMDocumentFragment', $linksFragment);
	    $this->assertEquals(1, $linksFragment->childNodes->length);
	    $linkElt = $linksFragment->firstChild;
	    $this->assertEquals('link', $linkElt->localName);
	    $this->assertEquals('style.css', $linkElt->getAttribute('href'));
	    $this->assertEquals('text/css', $linkElt->getAttribute('type'));
	    $this->assertEquals('screen', $linkElt->getAttribute('media'));
	    $this->assertEquals('My Very First Stylesheet I am Proud of', $linkElt->getAttribute('title'));
	}
	
	public function testSeparateStylesheetTwo() {
	    
	    // The loaded component is still a rubricBlock but this
	    // time with two (YES, TWO!) stylesheets.
	    
	    $doc = new XmlDocument('2.1');
	    $doc->load(self::samplesDir() . 'rendering/rubricblock_3.xml');
	    $this->assertEquals(2, count($doc->getDocumentComponent()->getStylesheets()));
	    
	    $renderingEngine = new XhtmlRenderingEngine();
	    $renderingEngine->setStylesheetPolicy(XhtmlRenderingEngine::STYLESHEET_SEPARATE);
	    $rendering = $renderingEngine->render($doc->getDocumentComponent());
	    
	    $linkElts = $rendering->getElementsByTagName('link');
	    $this->assertEquals(0, $linkElts->length);
	    
	    $linksFragment = $renderingEngine->getStylesheets();
	    $this->assertInstanceOf('\\DOMDocumentFragment', $linksFragment);
	    $this->assertEquals(2, $linksFragment->childNodes->length);
	    
	    // Test first <link> element.
	    $linkElt = $linksFragment->childNodes->item(0);
	    $this->assertEquals('link', $linkElt->localName);
	    $this->assertEquals('style1.css', $linkElt->getAttribute('href'));
	    $this->assertEquals('text/css', $linkElt->getAttribute('type'));
	    $this->assertEquals('screen', $linkElt->getAttribute('media'));
	    $this->assertEquals('\0_ !HOURRAY! _0/', $linkElt->getAttribute('title'));
	    
	    // Test second <link> element.
	    $linkElt = $linksFragment->childNodes->item(1);
	    $this->assertEquals('link', $linkElt->localName);
	    $this->assertEquals('style2.css', $linkElt->getAttribute('href'));
	    $this->assertEquals('text/css', $linkElt->getAttribute('type'));
	    $this->assertEquals('screen', $linkElt->getAttribute('media'));
	    $this->assertEquals('0/*\0 (Jedi duel)', $linkElt->getAttribute('title'));
	}
}