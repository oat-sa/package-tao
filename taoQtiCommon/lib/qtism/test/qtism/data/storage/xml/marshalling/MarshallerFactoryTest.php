<?php

use qtism\common\datatypes\Coords;

use qtism\common\datatypes\Shape;

use qtism\data\state\AreaMapEntry;

use qtism\data\storage\xml\marshalling\Marshaller;
use qtism\data\storage\xml\marshalling\MarshallerFactory;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class MarshallerFactyoryTest extends QtiSmTestCase {

	public function testFromDomElement() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<areaMapEntry xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" shape="rect" coords="0, 20, 100, 0" mappedValue="1.337"/>');
		$element = $dom->documentElement;
		
		$factory = new MarshallerFactory();
		$marshaller = $factory->createMarshaller($element);
		$this->assertInstanceOf('qtism\\data\\storage\\xml\\marshalling\\AreaMapEntryMarshaller', $marshaller);
	}
	
	public function testFromQtiComponent() {
		$shape = Shape::RECT;
		$coords = new Coords($shape, array(0, 20, 100, 0));
		$component = new AreaMapEntry($shape, $coords, 1.337);
		
		$factory = new MarshallerFactory();
		$marshaller = $factory->createMarshaller($component);
		$this->assertInstanceOf('qtism\\data\\storage\\xml\\marshalling\\AreaMapEntryMarshaller', $marshaller);
	}
	
	public function testFromInvalidObject() {
		$this->setExpectedException('\\InvalidArgumentException');
		$component = new stdClass();
		$factory = new MarshallerFactory();
		$marshaller = $factory->createMarshaller($component);
	}
}