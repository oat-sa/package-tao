<?php

use qtism\data\ShowHide;
use qtism\data\content\interactions\HotspotChoice;
use qtism\common\datatypes\Coords;
use qtism\common\datatypes\Shape;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class HotspotChoiceMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    
	    $shape = Shape::CIRCLE;
	    $coords = new Coords($shape, array(0, 0, 5));
	    $hotspotLabel = "This is a circle.";
	    $hotspotChoice = new HotspotChoice('hotspotchoice1', $shape, $coords, 'my-hotspotchoice');
	    $hotspotChoice->setFixed(true);
	    $hotspotChoice->setTemplateIdentifier('mytpl1');
	    $hotspotChoice->setShowHide(ShowHide::HIDE);
	    $hotspotChoice->setHotspotLabel($hotspotLabel);
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($hotspotChoice)->marshall($hotspotChoice);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<hotspotChoice identifier="hotspotchoice1" shape="circle" coords="0,0,5" fixed="true" templateIdentifier="mytpl1" showHide="hide" hotspotLabel="This is a circle." id="my-hotspotchoice"/>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $element = $this->createDOMElement('
	        <hotspotChoice identifier="hotspotchoice1" shape="circle" coords="0,0,5" fixed="true" templateIdentifier="mytpl1" showHide="hide" hotspotLabel="This is a circle." id="my-hotspotchoice"/>
	    ');
	    
	    $component = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\content\\interactions\\HotspotChoice', $component);
	    $this->assertInstanceOf('qtism\\data\\content\\interactions\\Hotspot', $component);
	    $this->assertInstanceOf('qtism\\data\\content\\interactions\\Choice', $component);
	    
	    $this->assertEquals('hotspotchoice1', $component->getIdentifier());
	    $this->assertEquals(Shape::CIRCLE, $component->getShape());
	    $this->assertEquals('0,0,5', $component->getCoords()->__toString());
	    $this->assertTrue($component->isFixed());
	    $this->assertEquals('mytpl1', $component->getTemplateIdentifier());
	    $this->assertTrue($component->hasTemplateIdentifier());
	    $this->assertEquals(ShowHide::HIDE, $component->getShowHide());
	    $this->assertEquals('my-hotspotchoice', $component->getId());
	    $this->assertEquals('This is a circle.', $component->getHotspotLabel());
	    $this->assertTrue($component->hasHotspotLabel());
	}
	
	public function testUnmarshallFloatCoords() {
	    // Example taken from a TAO migration issue. Coordinates contain "string-float" values.
	    $element = $this->createDOMElement('
	        <hotspotChoice identifier="r_50" fixed="false" shape="circle" coords="128, 222  , 18.36"/>
	    ');
	    
	    $component = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\content\\interactions\\HotspotChoice', $component);
	    $this->assertEquals('r_50', $component->getIdentifier());
	    $this->assertFalse($component->isFixed());
	    $this->assertEquals(Shape::CIRCLE, $component->getShape());
	    $this->assertTrue($component->getCoords()->equals(new Coords(Shape::CIRCLE, array(128, 222, 18))));
	}
}