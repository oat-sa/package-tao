<?php

use qtism\data\content\interactions\AssociableHotspot;
use qtism\data\ShowHide;
use qtism\common\datatypes\Coords;
use qtism\common\datatypes\Shape;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class AssociableHotspotMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
        $shape = Shape::RECT;
        $coords = new Coords($shape, array(92, 19, 261, 66));
	    $matchMax = 2;
	    $matchMin = 1;
	    $fixed = true;
	    $showHide = ShowHide::HIDE;
	    
	    $associableHotspot = new AssociableHotspot('hotspot1', $matchMax, $shape, $coords, 'my-hot');
	    $associableHotspot->setMatchMin($matchMin);
	    $associableHotspot->setFixed($fixed);
	    $associableHotspot->setShowHide($showHide);
        
	    $element = $this->getMarshallerFactory()->createMarshaller($associableHotspot)->marshall($associableHotspot);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<associableHotspot identifier="hotspot1" shape="rect" coords="92,19,261,66" fixed="true" showHide="hide" matchMax="2" matchMin="1" id="my-hot"/>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $element = $this->createDOMElement('
	        <associableHotspot identifier="hotspot1" shape="rect" coords="92,19,261,66" fixed="true" showHide="hide" matchMax="2" matchMin="1" id="my-hot"/>
	    ');
	    
	    $component = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\content\\interactions\\AssociableHotspot', $component);
	    $this->assertInstanceOf('qtism\\data\\content\\interactions\\Hotspot', $component);
	    $this->assertInstanceOf('qtism\\data\\content\\interactions\\Choice', $component);
	    
	    $this->assertEquals('hotspot1', $component->getIdentifier());
	    $this->assertEquals(Shape::RECT, $component->getShape());
	    $this->assertEquals('92,19,261,66', $component->getCoords()->__toString());
	    $this->assertTrue($component->isFixed());
	    $this->assertEquals(ShowHide::HIDE, $component->getShowHide());
	    $this->assertEquals(2, $component->getMatchMax());
	    $this->assertEquals(1, $component->getMatchMin());
	    $this->assertEquals('my-hot', $component->getId());
	    $this->assertFalse($component->hasHotspotLabel());
	}
}