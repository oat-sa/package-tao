<?php
use qtism\common\datatypes\String;

use qtism\common\datatypes\Float;

use qtism\common\datatypes\Boolean;

use qtism\common\datatypes\Integer;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Point;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\common\RecordContainer;
use qtism\common\datatypes\Duration;
use qtism\runtime\expressions\operators\OperandsCollection;

class OperandsCollectionProcessorTest extends QtiSmTestCase {
	
	private $operands = null;
	
	public function setUp() {
		parent::setUp();
		$this->operands = new OperandsCollection();
	}
	
	public function tearDown() {
	    parent::tearDown();
	    unset($this->operands);
	}
	
	/**
	 * 
	 * @return OperandsCollection
	 */
	protected function getOperands() {
		return $this->operands;
	}
	
	public function testContainsNullEmpty() {
		$operands = $this->getOperands();
		$this->assertFalse($operands->containsNull());
	}
	
	public function testContainsNullFullSingleCardinality() {
		$operands = $this->getOperands();
		$operands[] = new Integer(15);
		
		$this->assertFalse($operands->containsNull());
		
		$operands[] = new Boolean(true);
		$operands[] = new Float(0.4);
		$operands[] = new String('string');
		$operands[] = new Duration('P1D');
		$this->assertFalse($operands->containsNull());
		
		$operands[] = null;
		$this->assertTrue($operands->containsNull());
	}
	
	public function testContainsNullMixed() {
		$operands = $this->getOperands();
		$operands[] = new MultipleContainer(BaseType::FLOAT);
		
		$this->assertTrue($operands->containsNull());
		
		$operands[0][] = new Float(15.3);
		$this->assertFalse($operands->containsNull());
		
		$operands[] = new String('');
		$this->assertTrue($operands->containsNull());
		
		$operands[1] = new String('string!');
		$this->assertFalse($operands->containsNull());
		
		$operands[] = new RecordContainer();
		$this->assertTrue($operands->containsNull());
		
		$operands[2]['date'] = new Duration('P2D');
		$this->assertFalse($operands->containsNull());
	}
	
	public function testExclusivelyNumeric() {
		$operands = $this->getOperands();
		$this->assertFalse($operands->exclusivelyNumeric());
		
		$operands[] = new Integer(14);
		$operands[] = new Float(15.3);
		$this->assertTrue($operands->exclusivelyNumeric());
		
		$operands[] = new String('');
		$this->assertFalse($operands->exclusivelyNumeric());
		unset($operands[2]);
		$this->assertTrue($operands->exclusivelyNumeric());
		
		$operands[] = new Point(1, 10);
		$this->assertFalse($operands->exclusivelyNumeric());
		unset($operands[3]);
		$this->assertTrue($operands->exclusivelyNumeric());
		
		$mult = new MultipleContainer(BaseType::INTEGER);
		$operands[] = $mult;
		$this->assertFalse($operands->exclusivelyNumeric());
		$mult[] = new Integer(15);
		$this->assertTrue($operands->exclusivelyNumeric());
		
		$ord = new OrderedContainer(BaseType::FLOAT);
		$operands[] = $ord;
		$this->assertFalse($operands->exclusivelyNumeric());
		$ord[] = new Float(15.5);
		$this->assertTrue($operands->exclusivelyNumeric());
		
		$operands[] = new RecordContainer();
		$this->assertFalse($operands->exclusivelyNumeric());
		unset($operands[6]);
		$this->assertTrue($operands->exclusivelyNumeric());
		
		$operands[] = new MultipleContainer(BaseType::DURATION);
		$this->assertFalse($operands->exclusivelyNumeric());
	}
	
	public function testExclusivelyInteger() {
		$operands = $this->getOperands();
		$this->assertFalse($operands->exclusivelyInteger());
		
		$operands[] = new Integer(14);
		$operands[] = new Integer(15);
		$this->assertTrue($operands->exclusivelyInteger());
		
		$operands[] = new String('');
		$this->assertFalse($operands->exclusivelyInteger());
		unset($operands[2]);
		$this->assertTrue($operands->exclusivelyInteger());
		
		$operands[] = new Point(1, 10);
		$this->assertFalse($operands->exclusivelyInteger());
		unset($operands[3]);
		$this->assertTrue($operands->exclusivelyInteger());
		
		$mult = new MultipleContainer(BaseType::INTEGER);
		$operands[] = $mult;
		$this->assertFalse($operands->exclusivelyInteger());
		$mult[] = new Integer(15);
		$this->assertTrue($operands->exclusivelyInteger());
		
		$operands[] = new RecordContainer();
		$this->assertFalse($operands->exclusivelyInteger());
		unset($operands[5]);
		$this->assertTrue($operands->exclusivelyInteger());
		
		$operands[] = new MultipleContainer(BaseType::DURATION);
		$this->assertFalse($operands->exclusivelyInteger());
	}
	
	public function testExclusivelyPoint() {
		$operands = $this->getOperands();
		$this->assertFalse($operands->exclusivelyPoint());
	
		$operands[] = new Point(1, 2);
		$operands[] = new Point(3, 4);
		$this->assertTrue($operands->exclusivelyPoint());
	
		$operands[] = new String('');
		$this->assertFalse($operands->exclusivelyPoint());
		unset($operands[2]);
		$this->assertTrue($operands->exclusivelyPoint());
	
		$operands[] = new Duration('P1D');
		$this->assertFalse($operands->exclusivelyPoint());
		unset($operands[3]);
		$this->assertTrue($operands->exclusivelyPoint());
	
		$mult = new MultipleContainer(BaseType::POINT);
		$operands[] = $mult;
		$this->assertFalse($operands->exclusivelyPoint());
		$mult[] = new Point(1, 3);
		$this->assertTrue($operands->exclusivelyPoint());
	
		$operands[] = new RecordContainer();
		$this->assertFalse($operands->exclusivelyPoint());
		unset($operands[5]);
		$this->assertTrue($operands->exclusivelyPoint());
	
		$operands[] = new MultipleContainer(BaseType::DURATION);
		$this->assertFalse($operands->exclusivelyPoint());
	}
	
	public function testExclusivelyDuration() {
		$operands = $this->getOperands();
		$this->assertFalse($operands->exclusivelyDuration());
	
		$operands[] = new Duration('P1D');
		$operands[] = new Duration('P2D');
		$this->assertTrue($operands->exclusivelyDuration());
	
		$operands[] = new Integer(10);
		$this->assertFalse($operands->exclusivelyDuration());
		unset($operands[2]);
		$this->assertTrue($operands->exclusivelyDuration());
	
		$operands[] = new Point(1, 2);
		$this->assertFalse($operands->exclusivelyDuration());
		unset($operands[3]);
		$this->assertTrue($operands->exclusivelyDuration());
	
		$mult = new MultipleContainer(BaseType::DURATION);
		$operands[] = $mult;
		$this->assertFalse($operands->exclusivelyDuration());
		$mult[] = new Duration('P1DT2S');
		$this->assertTrue($operands->exclusivelyDuration());
	
		$operands[] = new RecordContainer();
		$this->assertFalse($operands->exclusivelyDuration());
		unset($operands[5]);
		$this->assertTrue($operands->exclusivelyDuration());
	
		$operands[] = new MultipleContainer(BaseType::POINT);
		$this->assertFalse($operands->exclusivelyDuration());
	}
	
	public function testAnythingButRecord() {
		$operands = $this->getOperands();
		$this->assertTrue($operands->anythingButRecord());
		
		$operands[] = null;
		$this->assertTrue($operands->anythingButRecord());
		
		$operands[] = new Integer(10);
		$this->assertTrue($operands->anythingButRecord());
		
		$operands[] = new Float(10.11);
		$this->assertTrue($operands->anythingButRecord());
		
		$operands[] = new Point(1, 1);
		$this->assertTrue($operands->anythingButRecord());
		
		$operands[] = new String('');
		$this->assertTrue($operands->anythingButRecord());
		
		$operands[] = new String('string');
		$this->assertTrue($operands->anythingButRecord());
		
		$operands[] = new Boolean(true);
		$this->assertTrue($operands->anythingButRecord());
		
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(10), new Integer(20)));
		$this->assertTrue($operands->anythingButRecord());
		
		$operands[] = new OrderedContainer(BaseType::BOOLEAN, array(new Boolean(true), new Boolean(false), new Boolean(true)));
		$this->assertTrue($operands->anythingButRecord());
		
		$operands[] = new RecordContainer();
		$this->assertFalse($operands->anythingButRecord());
	}
	
	public function testExclusivelyMultipleOrOrdered() {
		$operands = $this->getOperands();
		$this->assertFalse($operands->exclusivelyMultipleOrOrdered());
		
		$operands[] = new MultipleContainer(BaseType::BOOLEAN);
		$this->assertTrue($operands->exclusivelyMultipleOrOrdered());
		
		$operands[] = new OrderedContainer(BaseType::POINT);
		$this->assertTrue($operands->exclusivelyMultipleOrOrdered());
		
		$operands[] = new RecordContainer();
		$this->assertFalse($operands->exclusivelyMultipleOrOrdered());
		unset($operands[2]);
		$this->assertTrue($operands->exclusivelyMultipleOrOrdered());
		
		$operands[] = new Integer(15);
		$this->assertFalse($operands->exclusivelyMultipleOrOrdered());
		unset($operands[3]);
		$this->assertTrue($operands->exclusivelyMultipleOrOrdered());
		
		$operands[] = null;
		$this->assertFalse($operands->exclusivelyMultipleOrOrdered());
		unset($operands[4]);
		
		$this->assertTrue($operands->exclusivelyMultipleOrOrdered());
		
		$operands[] = new String('');
		$this->assertFalse($operands->exclusivelyMultipleOrOrdered());
		unset($operands[5]);
		$this->assertTrue($operands->exclusivelyMultipleOrOrdered());
		
		$operands[] = new Boolean(false);
		$this->assertFalse($operands->exclusivelyMultipleOrOrdered());
		unset($operands[6]);
		$this->assertTrue($operands->exclusivelyMultipleOrOrdered());
	}
	
	public function testExclusivelySingleOrOrdered() {
		$operands = $this->getOperands();
		$operands[] = null;
		$this->assertTrue($operands->exclusivelySingleOrOrdered());
		
		$operands[] = new OrderedContainer(BaseType::INTEGER);
		$this->assertTrue($operands->exclusivelySingleOrOrdered());
		
		$operands[] = new Integer(10);
		$this->assertTrue($operands->exclusivelySingleOrOrdered());
		
		$operands[] = new Boolean(false);
		$this->assertTrue($operands->exclusivelySingleOrOrdered());
		
		$operands[] = new Point(10, 20);
		$this->assertTrue($operands->exclusivelySingleOrOrdered());
		
		$operands[] = new MultipleContainer(BaseType::INTEGER);
		$this->assertFalse($operands->exclusivelySingleOrOrdered());
	}
	
	public function testExclusivelySingleOrMultiple() {
		$operands = $this->getOperands();
		$operands[] = null;
		$this->assertTrue($operands->exclusivelySingleOrMultiple());
	
		$operands[] = new MultipleContainer(BaseType::INTEGER);
		$this->assertTrue($operands->exclusivelySingleOrMultiple());
	
		$operands[] = new Integer(10);
		$this->assertTrue($operands->exclusivelySingleOrMultiple());
	
		$operands[] = new Boolean(false);
		$this->assertTrue($operands->exclusivelySingleOrMultiple());
	
		$operands[] = new Point(10, 20);
		$this->assertTrue($operands->exclusivelySingleOrMultiple());
	
		$operands[] = new OrderedContainer(BaseType::INTEGER);
		$this->assertFalse($operands->exclusivelySingleOrMultiple());
	}
	
	public function testSameBaseType() {
		// If any of the values is null, false.
		$operands = new OperandsCollection(array(null, null, null));
		$this->assertFalse($operands->sameBaseType());
		
		// If any of the values is null, false.
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::INTEGER), null, null));
		$this->assertFalse($operands->sameBaseType());
		
		// If any of the values is null, false.
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::INTEGER), null, new Integer(15)));
		$this->assertFalse($operands->sameBaseType());
		
		// If any of the values is null (an empty container is considered null), false.
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::INTEGER), new Integer(1), new Integer(15)));
		$this->assertFalse($operands->sameBaseType());
		
		// Non-null values, all integers.
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::INTEGER, array(new Integer(15))), new Integer(1), new Integer(15)));
		$this->assertTrue($operands->sameBaseType());
		
		// Non-null, exclusively records.
		$operands = new OperandsCollection(array(new RecordContainer(array('a' => new Integer(15))), new RecordContainer(array('b' => new Integer(22)))));
		$this->assertTrue($operands->sameBaseType());
		
		// Exclusively records but considered to be null because they are empty.
		$operands = new OperandsCollection(array(new RecordContainer(), new RecordContainer()));
		$this->assertFalse($operands->sameBaseType());
		
		// Test Exclusively boolean
		$operands = new OperandsCollection(array(new Boolean(true), new Boolean(false)));
		$this->assertTrue($operands->sameBaseType());
		
		$operands = new Operandscollection(array(new Boolean(false)));
		$this->assertTrue($operands->sameBaseType());
		
		// Test Exclusively int
		$operands = new OperandsCollection(array(new Integer(10), new Integer(0)));
		$this->assertTrue($operands->sameBaseType());
		
		$operands = new OperandsCollection(array(new Integer(0)));
		$this->assertTrue($operands->sameBaseType());
		
		$operands = new OperandsCollection(array(new Integer(10), new OrderedContainer(BaseType::INTEGER, array(new Integer(10), new Integer(-1), new Integer(20))), new Integer(5)));
		$this->assertTrue($operands->sameBaseType());
		
		// - Misc
		$operands = new Operandscollection(array(new Integer(0), new Integer(10), new Float(10.0)));
		$this->assertFalse($operands->sameBaseType());
	}
	
	public function testSameCardinality() {
		$operands = new OperandsCollection();
		$this->assertFalse($operands->sameCardinality());
		
		$operands = new OperandsCollection(array(null));
		$this->assertFalse($operands->sameCardinality());
		
		$operands = new OperandsCollection(array(null, new Integer(10), new Integer(10)));
		$this->assertFalse($operands->sameCardinality());
		
		$operands = new OperandsCollection(array(new Integer(0), new Boolean(false), new Integer(16), new Boolean(true), new Point(1, 1)));
		$this->assertTrue($operands->sameCardinality());
		
		$operands = new OperandsCollection(array(new Integer(10), new Integer(20), new OrderedContainer(BaseType::INTEGER)));
		$this->assertFalse($operands->sameCardinality());
	}
	
	public function testExclusivelyBoolean() {
		$operands = new OperandsCollection();
		$this->assertFalse($operands->exclusivelyBoolean());

		$operands[] = new Boolean(true);
		$this->assertTrue($operands->exclusivelyBoolean());
		
		$operands[] = new Boolean(false);
		$this->assertTrue($operands->exclusivelyBoolean());
		
		$container = new MultipleContainer(BaseType::BOOLEAN);
		$operands[] = $container;
		$this->assertFalse($operands->exclusivelyBoolean());
		
		$container[] = new Boolean(false);
		$this->assertTrue($operands->exclusivelyBoolean());
		
		$operands = new OperandsCollection();
		$operands[] = new Boolean(true);
		$this->assertTrue($operands->exclusivelyBoolean());
		$operands[] = null;
		$this->assertFalse($operands->exclusivelyBoolean());
		
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::BOOLEAN, array(new Boolean(true), new Boolean(false), new Boolean(true)));
		$this->assertTrue($operands->exclusivelyBoolean());
		$operands[] = new MultipleContainer(BaseType::BOOLEAN);
		$this->assertFalse($operands->exclusivelyBoolean());
		
		$operands = new OperandsCollection();
		$operands[] = new Boolean(true);
		$operands[] = new Boolean(false);
		$this->assertTrue($operands->exclusivelyBoolean());
		$operands[] = new RecordContainer(array('b1' => new Boolean(true), 'b2' => new Boolean(false)));
		
		$operands = new OperandsCollection();
		$operands[] = new Boolean(true);
		$operands[] = new MultipleContainer(BaseType::BOOLEAN, array(new Boolean(true)));
		$this->assertTrue($operands->exclusivelyBoolean());
		$operands[] = new RecordContainer();
		$this->assertFalse($operands->exclusivelyBoolean());
	}
	
	public function testExclusivelyRecord() {
		$operands = $this->getOperands();
		$this->assertFalse($operands->exclusivelyRecord());
		
		$rec = new RecordContainer();
		$operands[] = $rec;
		$this->assertTrue($operands->exclusivelyRecord());
		
		$rec['A'] = new Integer(1);
		$this->assertTrue($operands->exclusivelyRecord());
		
		$operands[] = null;
		$this->assertFalse($operands->exclusivelyRecord());
		
		$operands->reset();
		$operands[] = $rec;
		$this->assertTrue($operands->exclusivelyRecord());
		
		$operands[] = new Integer(10);
		$this->assertFalse($operands->exclusivelyRecord());
		
		$operands->reset();
		$operands[] = $rec;
		$operands[] = new String('String!');
		$this->assertFalse($operands->exclusivelyRecord());
		
	}
	
	public function testExclusivelyOrdered() {
		$operands = $this->getOperands();
		$this->assertFalse($operands->exclusivelyOrdered());
	
		$mult = new OrderedContainer(BaseType::INTEGER);
		$operands[] = $mult;
		$this->assertTrue($operands->exclusivelyOrdered());
	
		$mult[] = new Integer(-10);
		$this->assertTrue($operands->exclusivelyOrdered());
	
		$operands[] = null;
		$this->assertFalse($operands->exclusivelyOrdered());
	
		$operands->reset();
		$operands[] = $mult;
		$this->assertTrue($operands->exclusivelyOrdered());
	
		$operands[] = new Integer(10);
		$this->assertFalse($operands->exclusivelyOrdered());
	
		$operands->reset();
		$operands[] = $mult;
		$operands[] = new String('String!');
		$this->assertFalse($operands->exclusivelyOrdered());
		
		$operands->reset();
		$operands[] = $mult;
		$operands[] = new MultipleContainer(BaseType::URI);
		$this->assertFalse($operands->exclusivelyOrdered());
	}
}