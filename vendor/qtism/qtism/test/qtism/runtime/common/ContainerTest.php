<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\common\datatypes\Boolean;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\common\enums\BaseType;
use qtism\data\state\Value;
use qtism\data\state\ValueCollection;
use qtism\runtime\common\Container;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\DirectedPair;
use qtism\common\datatypes\Point;
use qtism\common\datatypes\Duration;

class ContainerTest extends QtiSmTestCase {

	/**
	 * A Container object reset at each test.
	 * 
	 * @var Container
	 */
	private $container;
	
	/**
	 * Get the Container object.
	 * 
	 * @return Container A Container object.
	 */
	protected function getContainer() {
		return $this->container;
	}
	
	public function setUp() {
		parent::setUp();
		$this->container = new Container();
	}
	
	public function tearDown() {
	    parent::tearDown();
	    unset($this->container);
	}
	
	/**
	 * @dataProvider validValueProvider
	 */
	public function testAddValid($value) {
		// Try to test any QTI runtime model compliant data
		// for addition in the container.
		$container = $this->getContainer();
		$container[] = $value;
		
		$this->assertTrue($container->contains($value));
	}
	
	/**
	 * @dataProvider invalidValueProvider
	 */
	public function testAddInvalid($value) {
		$container = $this->getContainer();
		
		$this->setExpectedException('\\InvalidArgumentException');
		$container[] = $value;
	}
	
	public function testIsNull() {
		$container = $this->getContainer();
		
		$this->assertTrue($container->isNull());
		
		$container[] = new Integer(1);
		$this->assertFalse($container->isNull());
	}
	
	/**
	 * @dataProvider validValueCollectionProvider
	 */
	public function testCreateFromDataModelValid(ValueCollection $valueCollection) {
		$container = Container::createFromDataModel($valueCollection);
		$this->assertInstanceOf('qtism\\runtime\\common\\Container', $container);
	}
	
	/**
	 * @dataProvider validEqualsPrimitiveProvider
	 */
	public function testEqualsPrimitiveValid($a, $b) {
		$this->assertTrue($a->equals($b));
	}
	
	/**
	 * @dataProvider invalidEqualsPrimitiveProvider
	 */
	public function testEqualsPrimitiveInvalid($a, $b) {
		$this->assertFalse($a->equals($b));
	}
	
	/**
	 * @dataProvider occurencesProvider
	 */
	public function testOccurences($container, $lookup, $expected) {
		$this->assertEquals($expected, $container->occurences($lookup));
	}
	
	public function validValueProvider() {
		return array(
			array(new Integer(25)),
			array(new Float(25.3)),
			array(new Integer(0)),
			array(new String('')),
			array(new String('super')),
			array(new Boolean(true)),
			array(new Boolean(false)),
			array(new Duration('P1D')),
			array(new Point(20, 20)),
			array(new Pair('A', 'B')),
			array(new DirectedPair('C', 'D')),
			array(null)
		);
	}

	public function invalidValueProvider() {
		return array(
			array(new \DateTime()),
			array(array())	
		);
	}
	
	public function validEqualsPrimitiveProvider() {
		return array(
			array(new Container(array(new Boolean(true), new Boolean(false))), new Container(array(new Boolean(false), new Boolean(true)))),
			array(new Container(array(new Integer(14), new Integer(13))), new Container(array(new Integer(13), new Integer(14)))),
			array(new Container(array(null)), new Container(array(null))),
			array(new Container(array(new Integer(0))), new Container(array(new Integer(0)))),
			array(new Container(array(new String('string'))), new Container(array(new String('string')))),
			array(new Container(array(new Float(14.5))), new Container(array(new Float(14.5)))),
			array(new Container(array(new String('string1'), new String('string2'))), new Container(array(new String('string1'), new String('string2')))),
			array(new Container(), new Container()),
		);
	}
	
	public function invalidEqualsPrimitiveProvider() {
		return array(
			array(new Container(array(new Integer(14))), new Container(array(new Integer(13)))),
			array(new Container(array(new Integer(14))), new Container(array(new String('string')))),
			array(new Container(array(null)), new Container(array(new Integer(0)))),
			array(new Container(), new Container(array(new Integer(13)))),
			array(new Container(array(new Boolean(true))), new Boolean(true)),
		);
	}
	
	public function occurencesProvider() {
		return array(
			array(new Container(array(new Integer(15))), new Integer(15), 1),
			array(new Container(array(new Float(14.3))), new Float(14.3), 1),
			array(new Container(array(new Boolean(true))), new Boolean(true), 1),
			array(new Container(array(new Boolean(false))), new Boolean(false), 1),
			array(new Container(array(new String('string'))), new String('string'), 1),
			array(new Container(array(new Integer(0))), new Integer(0), 1),
			array(new Container(array(null)), null, 1),
			array(new Container(array(new Integer(15), new String('string'), new Integer(15))), new Integer(15), 2),
			array(new Container(array(new Float(14.3), new Integer(143), new Float(14.3))), new Float(14.3),  2),
			array(new Container(array(new Boolean(true), new Boolean(false), new Boolean(false))), new Boolean(false), 2),
			array(new Container(array(new String('string'), new Integer(2), new String('str'), new String('string'), new String('string'))), new String('string'), 3),
			array(new Container(array(new String('null'), null)), null, 1),
			array(new Container(array(new Integer(14), new Integer(15), new Integer(16))), true, 0),
			array(new Container(array(new String('string'), new Integer(1), new Boolean(true), new Float(14.3), new Point(20, 20), new Point(20, 21))), new Point(20, 20), 1)
		);
	}
	
	public function validValueCollectionProvider() {
		$returnValue = array();
		
		$valueCollection = new ValueCollection();
		$returnValue[] = array($valueCollection);
		
		$valueCollection = new ValueCollection();
		$valueCollection[] = new Value(15, BaseType::INTEGER);
		$valueCollection[] = new Value('string', BaseType::STRING);
		$valueCollection[] = new Value(true, BaseType::BOOLEAN);
		$returnValue[] = array($valueCollection);
		
		return $returnValue;
	}
	
	public function testClone() {
		$container = $this->getContainer();
		$container[] = new Point(10, 20);
		$container[] = new Duration('P2D'); // 2 days.
		$container[] = new Pair('A', 'B');
		$container[] = new DirectedPair('C', 'D');
		$container[] = new Integer(20);
		$container[] = new Float(20.1);
		$container[] = new Boolean(true);
		$container[] = new String('String!');
		
		$clone = clone $container;
		$this->assertFalse($clone === $container);
		$this->assertFalse($clone[0] === $container[0]);
		$this->assertFalse($clone[1] === $container[1]);
		$this->assertFalse($clone[2] === $container[2]);
		$this->assertFalse($clone[3] === $container[3]);
		$this->assertFalse($clone[4] === $container[4]);
		$this->assertFalse($clone[5] === $container[5]);
		$this->assertFalse($clone[6] === $container[6]);
		$this->assertFalse($clone[7] === $container[7]);
	}
	
	public function testContains() {
		$pair = new Pair('A', 'B');
		$container = $this->getContainer();
		$container[] = $pair;
		$this->assertTrue($container->contains(new Pair('A', 'B')));
	}
	
	/**
	 * @dataProvider toStringProvider
	 * 
	 * @param Container $container
	 * @param string $expected The expected result of a __toString() call.
	 */
	public function testToString(Container $container, $expected) {
		$this->assertEquals($expected, $container->__toString());
	}
	
	public function toStringProvider() {
		$returnValue = array();
		
		$returnValue[] = array(new Container(), '[]');
		$returnValue[] = array(new Container(array(new Integer(10))), '[10]');
		$returnValue[] = array(new Container(array(new Boolean(true), new Boolean(false))), '[true; false]');
		$returnValue[] = array(new Container(array(new Duration('P2DT2S'), new Point(10, 15), new Pair('A', 'B'), new DirectedPair('C', 'D'), new String('String!'))), '[P2DT2S; 10 15; A B; C D; \'String!\']');
		
		return $returnValue;
	}
}