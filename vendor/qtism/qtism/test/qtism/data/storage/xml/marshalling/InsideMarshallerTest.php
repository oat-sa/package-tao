<?php

use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\operators\Inside;
use qtism\common\datatypes\Shape;
use qtism\common\datatypes\Coords;
use qtism\data\expressions\Variable;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class InsideMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$subs = new ExpressionCollection();
		$subs[] = new Variable('pointVariable');
		
		$shape = Shape::RECT;
		$coords = new Coords($shape, array(0, 0, 100, 20));
		
		$component = new Inside($subs, $shape, $coords);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('inside', $element->nodeName);
		$this->assertEquals(implode(",", array(0, 0, 100, 20)), $element->getAttribute('coords'));
		$this->assertEquals('rect', $element->getAttribute('shape'));
		$this->assertEquals(1, $element->getElementsByTagName('variable')->length);
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<inside xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" shape="rect" coords="0,0,100,20">
				<variable identifier="pointVariable"/>
			</inside>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\operators\\Inside', $component);
		$this->assertInstanceOf('qtism\\common\\datatypes\\Coords', $component->getCoords());
		$this->assertInternalType('integer', $component->getShape());
		$this->assertEquals(Shape::RECT, $component->getShape());
		$this->assertEquals(1, count($component->getExpressions()));
	}
}
