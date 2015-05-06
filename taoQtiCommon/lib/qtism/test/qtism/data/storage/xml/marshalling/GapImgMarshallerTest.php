<?php

use qtism\data\content\xhtml\Object;
use qtism\data\content\interactions\GapImg;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class GapImgMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    $object = new Object('http://imagine.us/myimg.png', "image/png");
	    $gapImg = new GapImg('gapImg1', 1, $object, 'my-gap', 'gaps');
	    
	    $marshaller = $this->getMarshallerFactory()->createMarshaller($gapImg);
	    $element = $marshaller->marshall($gapImg);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<gapImg id="my-gap" class="gaps" identifier="gapImg1" matchMax="1"><object data="http://imagine.us/myimg.png" type="image/png"/></gapImg>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $element = $this->createDOMElement('
	        <gapImg id="my-gap" class="gaps" identifier="gapImg1" matchMax="1"><object data="http://imagine.us/myimg.png" type="image/png"/></gapImg>
	    ');
	    
	    $marshaller = $this->getMarshallerFactory()->createMarshaller($element);
	    $gapImg = $marshaller->unmarshall($element);
	    
	    $this->assertInstanceOf('qtism\\data\\content\\interactions\\GapImg', $gapImg);
	    $this->assertEquals('my-gap', $gapImg->getId());
	    $this->assertEquals('gaps', $gapImg->getClass());
	    $this->assertEquals('gapImg1', $gapImg->getIdentifier());
	    $this->assertEquals(0, $gapImg->getMatchMin());
	    $this->assertEquals(1, $gapImg->getMatchMax());
	    
	    $object = $gapImg->getObject();
	    $this->assertEquals('http://imagine.us/myimg.png', $object->getData());
	    $this->assertEquals('image/png', $object->getType());
	}
}