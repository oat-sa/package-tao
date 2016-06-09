<?php

use qtism\data\content\InlineCollection;
use qtism\runtime\rendering\markup\xhtml\TextRunRenderer;
use qtism\data\content\TextRun;
use qtism\runtime\rendering\markup\xhtml\BodyElementRenderer;
use qtism\data\content\xhtml\text\Br;
use qtism\data\content\xhtml\text\Abbr;
use qtism\runtime\rendering\markup\xhtml\XhtmlRenderingEngine;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class BodyElementRendererTest extends QtiSmTestCase {
	
	public function testRenderNoChildren() {
	    $ctx = new XhtmlRenderingEngine();
	    $br = new Br('my-br', 'break down', 'en-US', 'QTISM generated.');
	    
	    $renderer = new BodyElementRenderer();
	    $renderer->setRenderingEngine($ctx);
	    
	    $element = $renderer->render($br)->firstChild;
	    
	    $this->assertEquals('br', $element->nodeName);
	    $this->assertEquals('my-br', $element->getAttribute('id'));
	    $this->assertEquals('break down qti-bodyElement qti-br', $element->getAttribute('class'));
	    $this->assertEquals('en-US', $element->getAttribute('lang'));
	    $this->assertEquals('', $element->getAttribute('label'));
	}
	
	public function testRenderChildren() {
	    $ctx = new XhtmlRenderingEngine();
	    
	    $abbr = new Abbr('my-abbr', 'qti qti-abbr');
	    $abbrRenderer = new BodyElementRenderer();
	    $abbrRenderer->setRenderingEngine($ctx);
	    
	    $textRun = new TextRun('abbreviation...');
	    $textRunRenderer = new TextRunRenderer();
	    $textRunRenderer->setRenderingEngine($ctx);
	    $renderedTextRun = $textRunRenderer->render($textRun);
	    $ctx->storeRendering($textRun, $renderedTextRun);
	    
	    $abbr->setContent(new InlineCollection(array($textRun)));
	    
	    $element = $abbrRenderer->render($abbr)->firstChild;
	    
	    $this->assertEquals('abbr', $element->nodeName);
	    $this->assertEquals('my-abbr', $element->getAttribute('id'));
	    $this->assertEquals('qti qti-abbr qti-bodyElement qti-abbr', $element->getAttribute('class'));
	    $this->assertEquals('abbreviation...', $element->firstChild->wholeText);
	}
}