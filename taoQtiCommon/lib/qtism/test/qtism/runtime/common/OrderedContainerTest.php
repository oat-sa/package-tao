<?php

use qtism\common\datatypes\Uri;

use qtism\common\datatypes\Integer;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\common\enums\Cardinality;
use qtism\runtime\common\OrderedContainer;
use qtism\common\datatypes\Point;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\DirectedPair;
use qtism\runtime\common\MultipleContainer;
use qtism\common\enums\BaseType;

class OrderedContainerTest extends QtiSmTestCase {
	
	/**
	 * @dataProvider equalsValidProvider
	 */
	public function testEqualsValid($containerA, $containerB) {
		$this->assertTrue($containerA->equals($containerB));
		$this->assertTrue($containerB->equals($containerA));
	}
	
	/**
	 * @dataProvider equalsInvalidProvider
	 */
	public function testEqualsInvalid($containerA, $containerB) {
		$this->assertFalse($containerA->equals($containerB));
		$this->assertFalse($containerB->equals($containerA));
	}
	
	public function testCreationEmpty() {
		$container = new OrderedContainer(BaseType::INTEGER);
		$this->assertEquals(0, count($container));
		$this->assertEquals(BaseType::INTEGER, $container->getBaseType());
		$this->assertEquals(Cardinality::ORDERED, $container->getCardinality());
	}
	
	public function equalsValidProvider() {
		return array(
			array(new OrderedContainer(BaseType::INTEGER), new OrderedContainer(BaseType::INTEGER)),
			array(new OrderedContainer(BaseType::INTEGER, array(new Integer(20))), new OrderedContainer(BaseType::INTEGER, array(new Integer(20)))),
			array(new OrderedContainer(BaseType::URI, array(new Uri('http://www.taotesting.com'), new Uri('http://www.tao.lu'))), new OrderedContainer(BaseType::URI, array(new Uri('http://www.taotesting.com'), new Uri('http://www.tao.lu')))),
			array(new OrderedContainer(BaseType::PAIR, array(new Pair('abc', 'def'))), new OrderedContainer(BaseType::PAIR, array(new Pair('def', 'abc'))))
		);
	}
	
	public function equalsInvalidProvider() {
		return array(
			array(new OrderedContainer(BaseType::INTEGER, array(new Integer(20))), new OrderedContainer(BaseType::INTEGER, array(new Integer(30)))),
			array(new OrderedContainer(BaseType::URI, array(new Uri('http://www.taotesting.com'), new Uri('http://www.tao.lu'))), new OrderedContainer(BaseType::URI, array(new Uri('http://www.tao.lu'), new Uri('http://www.taotesting.com')))),
			array(new OrderedContainer(BaseType::DIRECTED_PAIR, array(new DirectedPair('abc', 'def'))), new OrderedContainer(BaseType::DIRECTED_PAIR, array(new DirectedPair('def', 'abc')))),
		);
	}
}