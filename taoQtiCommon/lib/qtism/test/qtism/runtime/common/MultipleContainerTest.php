<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\data\state\ValueCollection;
use qtism\data\state\Value;
use qtism\common\datatypes\Point;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\MultipleContainer;
use qtism\common\enums\BaseType;

class MultipleContainerTest extends QtiSmTestCase {
	
	public function testCreationEmpty() {
		$container = new MultipleContainer(BaseType::BOOLEAN);
		$this->assertEquals(BaseType::BOOLEAN, $container->getBaseType());
		$this->assertEquals(0, count($container));
		$this->assertEquals(Cardinality::MULTIPLE, $container->getCardinality());
	}
	
	public function testCreationWithValues() {
		$data = array(new Integer(10), new Integer(20), new Integer(20), new Integer(30), new Integer(40), new Integer(50));
		$container = new MultipleContainer(BaseType::INTEGER, $data);
		$this->assertEquals(6, count($container));
		$this->assertEquals(BaseType::INTEGER, $container->getBaseType());
		$this->assertEquals($data, $container->getArrayCopy());
		$this->assertEquals($container[1]->getValue(), 20);
	}
	
	public function testCreationEmptyWrongBaseType1() {
		$this->setExpectedException('\\InvalidArgumentException');
		$container = new MultipleContainer('invalid');
	}
	
	public function testCreationEmptyWrongBaseType2() {
		$this->setExpectedException('\\InvalidArgumentException');
		$container = new MultipleContainer(14);
	}
	
	public function testCreationWithWrongValues() {
		$this->setExpectedException('\\InvalidArgumentException');
		$data = array(new Point(20, 20));
		$container = new MultipleContainer(BaseType::DURATION, $data);
	}
	
	public function testCreateFromDataModel() {
		$valueCollection = new ValueCollection();
		$valueCollection[] = new Value(new Point(10, 30), BaseType::POINT);
		$valueCollection[] = new Value(new Point(20, 40), BaseType::POINT);
		
		$container = MultipleContainer::createFromDataModel($valueCollection, BaseType::POINT);
		$this->assertInstanceOf('qtism\\runtime\\common\\MultipleContainer', $container);
		$this->assertEquals(2, count($container));
		$this->assertEquals(BaseType::POINT, $container->getBaseType());
		$this->assertEquals(Cardinality::MULTIPLE, $container->getCardinality());
		$this->assertTrue($container->contains($valueCollection[0]->getValue()));
		$this->assertTrue($container->contains($valueCollection[1]->getValue()));
	}
	
	/**
	 * @dataProvider validCreateFromDataModelProvider
	 */
	public function testCreateFromDataModelValid($baseType, ValueCollection $valueCollection) {
		$container = MultipleContainer::createFromDataModel($valueCollection, $baseType);
		$this->assertInstanceOf('qtism\\runtime\\common\\MultipleContainer', $container);
	}
	
	/**
	 * @dataProvider invalidCreateFromDataModelProvider
	 */
	public function testCreateFromDataModelInvalid($baseType, ValueCollection $valueCollection) {
		$this->setExpectedException('\\InvalidArgumentException');
		$container = MultipleContainer::createFromDataModel($valueCollection, $baseType);
	}
	
	public function invalidCreateFromDataModelProvider() {
		$returnValue = array();
		
		$valueCollection = new ValueCollection();
		$valueCollection[] = new Value(new Point(20, 30), BaseType::POINT);
		$valueCollection[] = new Value(10, BaseType::INTEGER);
		$returnValue[] = array(BaseType::INTEGER, $valueCollection);
		
		return $returnValue;
	}
	
	public function validCreateFromDataModelProvider() {
		$returnValue = array();
		
		$valueCollection = new ValueCollection();
		$returnValue[] = array(BaseType::DURATION, $valueCollection);
		
		$valueCollection = new ValueCollection();
		$valueCollection[] = new Value(10, BaseType::INTEGER);
		$valueCollection[] = new Value(-20, BaseType::INTEGER);
		$returnValue[] = array(BaseType::INTEGER, $valueCollection);
		
		return $returnValue;
	}
	
	public function testEquals() {
		$c1 = new MultipleContainer(BaseType::INTEGER, array(new Integer(5), new Integer(4), new Integer(3), new Integer(2), new Integer(1)));
		$c2 = new MultipleContainer(BaseType::INTEGER, array(new Integer(1), new Integer(6), new Integer(7), new Integer(8), new Integer(5)));
		$this->assertFalse($c1->equals($c2));
	}
	
	public function testEqualsTwo() {
	    $c1 = new MultipleContainer(BaseType::FLOAT, array(new Float(2.75), new Float(1.65)));
	    $c2 = new MultipleContainer(BaseType::FLOAT, array(new Float(2.75), new Float(1.65)));
	    $this->assertTrue($c1->equals($c2));
	}
}