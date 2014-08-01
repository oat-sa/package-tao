<?php
use qtism\data\ShowHide;
use qtism\data\content\interactions\Gap;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class GapMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		$gap = new Gap('gap1', true, 'my-gap', 'gaps');
		$gap->setFixed(false);
		$gap->setTemplateIdentifier('tpl-gap');
	    
	    $marshaller = $this->getMarshallerFactory()->createMarshaller($gap);
	    $element = $marshaller->marshall($gap);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<gap identifier="gap1" templateIdentifier="tpl-gap" required="true" id="my-gap" class="gaps"/>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $element = $this->createDOMElement('
	        <gap identifier="gap1" templateIdentifier="tpl-gap" required="true" id="my-gap" class="gaps" showHide="hide"/>
	    ');
	    
	    $marshaller = $this->getMarshallerFactory()->createMarshaller($element);
	    $gap = $marshaller->unmarshall($element);
	    
	    $this->assertInstanceOf('qtism\\data\\content\\interactions\\Gap', $gap);
	    $this->assertEquals('gap1', $gap->getIdentifier());
	    $this->assertEquals('tpl-gap', $gap->getTemplateIdentifier());
	    $this->assertTrue($gap->hasTemplateIdentifier());
	    $this->assertTrue($gap->isRequired());
	    $this->assertEquals('gaps', $gap->getClass());
	    $this->assertEquals(ShowHide::HIDE, $gap->getShowHide());
	}
}