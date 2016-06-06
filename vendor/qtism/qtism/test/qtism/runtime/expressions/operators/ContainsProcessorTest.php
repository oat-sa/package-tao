<?php

use qtism\common\datatypes\Identifier;

use qtism\common\datatypes\Float;

use qtism\common\datatypes\Integer;

use qtism\common\datatypes\String;

use qtism\runtime\common\RecordContainer;

use qtism\common\datatypes\Point;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\enums\BaseType;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\expressions\operators\ContainsProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;

class ContainsProcessorTest extends QtiSmTestCase {
	
	public function testPrimitiveOrderedTrailing() {
		$expression = $this->createFakeExpression();
		
		// For ordered containers [A,B,C] contains [B,C]
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C')));
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('B'), new String('C')));
		$processor = new ContainsProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		// [A,B,C] does not contain [C,B]
		$operands->reset();
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C')));
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('C'), new String('B')));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
		
		// [A,B,C] does not contain [E,F]
		$operands->reset();
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C')));
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('E'), new String('F')));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testPrimitiveOrderedLeading() {
		$expression = $this->createFakeExpression();
	
		// For ordered containers [A,B,C] contains [A,B]
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C')));
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('A'), new String('B')));
		$processor = new ContainsProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	
		// [A,B,C] does not contain [B,A]
		$operands->reset();
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C')));
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('B'), new String('A')));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testPrimitiveOrderedInBetween() {
		$expression = $this->createFakeExpression();
	
		// For ordered containers [A,B,C,D] contains [B]
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C'), new String('D')));
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('B')));
		$processor = new ContainsProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	
		// [A,B,C,D] does not contain [E]
		$operands->reset();
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C'), new String('D')));
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('E')));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
		
		// [A,B,C,D] contains [B,C]
		$operands->reset();
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C'), new String('D')));
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('B'), new String('C')));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	}
	
	public function testPrimitiveMultipleTrailing() {
		$expression = $this->createFakeExpression();
	
		// For multiple containers [A,B,C] contains [B,C]
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C')));
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('B'), new String('C')));
		$processor = new ContainsProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	
		// [A,B,C] contains [C,B]
		$operands->reset();
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C')));
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('C'), new String('B')));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	
		// [A,B,C] does not contain [E,F]
		$operands->reset();
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C')));
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('E'), new String('F')));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testPrimitiveMultipleLeading() {
		$expression = $this->createFakeExpression();
	
		// For ordered containers [A,B,C] contains [A,B]
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C')));
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('A'), new String('B')));
		$processor = new ContainsProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	
		// [A,B,C] contains [B,A]
		$operands->reset();
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C')));
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('B'), new String('A')));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	}
	
	public function testPrimitiveMultipleInBetween() {
		$expression = $this->createFakeExpression();
	
		// For ordered containers [A,B,C,D] contains [B]
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C'), new String('D')));
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('B')));
		$processor = new ContainsProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	
		// [A,B,C,D] does not contain [E]
		$operands->reset();
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C'), new String('D')));
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('E')));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	
		// [A,B,C,D] contains [A,D]
		$operands->reset();
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('A'), new String('B'), new String('C'), new String('D')));
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('A'), new String('D')));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	}

	public function testComplexOrderedTrailing() {
		$expression = $this->createFakeExpression();
	
		// For ordered containers [A,B,C] contains [B,C]
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6)));
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(3, 4), new Point(5, 6)));
		$processor = new ContainsProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	
		// [A,B,C] does not contain [C,B]
		$operands->reset();
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6)));
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(5, 6), new Point(3, 4)));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	
		// [A,B,C] does not contain [E,F]
		$operands->reset();
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6)));
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(7, 8), new Point(9, 10)));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testComplexOrderedLeading() {
		$expression = $this->createFakeExpression();
	
		// For ordered containers [A,B,C] contains [A,B]
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6)));
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4)));
		$processor = new ContainsProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	
		// [A,B,C] does not contain [B,A]
		$operands->reset();
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6)));
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(3, 4), new Point(1, 2)));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testComplexOrderedInBetween() {
		$expression = $this->createFakeExpression();
	
		// For ordered containers [A,B,C,D] contains [B]
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6), new Point(7, 8)));
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(3, 4)));
		$processor = new ContainsProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	
		// [A,B,C,D] does not contain [E]
		$operands->reset();
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6), new Point(7, 8)));
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(9, 10)));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	
		// [A,B,C,D] contains [B,C]
		$operands->reset();
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6), new Point(7, 8)));
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(3, 4), new Point(5, 6)));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	}
	
	public function testComplexMultipleTrailing() {
		$expression = $this->createFakeExpression();
	
		// For multiple containers [A,B,C] contains [B,C]
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6)));
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(3, 4), new Point(5, 6)));
		$processor = new ContainsProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	
		// [A,B,C] contains [C,B]
		$operands->reset();
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6)));
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(5, 6), new Point(3, 4)));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	
		// [A,B,C] does not contain [E,F]
		$operands->reset();
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6)));
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(9, 10), new Point(11, 12)));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testComplexMultipleLeading() {
		$expression = $this->createFakeExpression();
	
		// For ordered containers [A,B,C] contains [A,B]
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6)));
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4)));
		$processor = new ContainsProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	
		// [A,B,C] contains [B,A]
		$operands->reset();
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6)));
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(3, 4), new Point(1, 2)));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		// [A,B,C] does not contain [B,D]
		$operands->reset();
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6)));
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(3, 4), new Point(7, 8)));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testComplexMultipleInBetween() {
		$expression = $this->createFakeExpression();
	
		// For ordered containers [A,B,C,D] contains [B]
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6), new Point(7, 8)));
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(3, 4)));
		$processor = new ContainsProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	
		// [A,B,C,D] does not contain [E]
		$operands->reset();
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6), new Point(7, 8)));
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(9, 10)));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	
		// [A,B,C,D] contains [A,D]
		$operands->reset();
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(1, 2), new Point(3, 4), new Point(5, 6), new Point(7, 8)));
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(1, 2), new Point(7, 8)));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	}
	
	public function testNull() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(null, new MultipleContainer(BaseType::INTEGER, array(new Integer(25)))));
		$processor = new ContainsProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
		
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::INTEGER), new MultipleContainer(BaseType::INTEGER, array(new Integer(25)))));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testNotSameBaseTypeOne() {
	    $expression = $this->createFakeExpression();
	    $operands = new OperandsCollection();
	    $operands[] = new MultipleContainer(BaseType::STRING, array(new String('identifier3'), new String('identifier4'), null, new String('identifier2')));
	    $operands[] = new MultipleContainer(BaseType::IDENTIFIER, array(new Identifier('identifier3'), new Identifier('identifier2')));
	    $processor = new ContainsProcessor($expression, $operands);
	    $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
	    $processor->process();
	}
	
	public function testNotSameBaseTypeTwo() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(25)));
		$operands[] = new MultipleContainer(BaseType::FLOAT, array(new Float(25.0)));
		$processor = new ContainsProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotSameCardinality() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(25)));
		$operands[] = new OrderedContainer(BaseType::INTEGER, array(new Integer(25)));
		$processor = new ContainsProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongCardinality() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::INTEGER, array(new Integer(25)));
		$operands[] = new RecordContainer(array('1' => new Integer(1), '2' => new Integer(2)));
		$processor = new ContainsProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::POINT, array(new Point(1, 2)))));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new ContainsProcessor($expression, $operands);
	}
	
	public function testTooMuchOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::INTEGER, array(new Integer(25)));
		$operands[] = new OrderedContainer(BaseType::INTEGER, array(new Integer(25)));
		$operands[] = new OrderedContainer(BaseType::INTEGER, array(new Integer(25)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new ContainsProcessor($expression, $operands);
	}
	
	public function createFakeExpression() {
		return $this->createComponentFromXml('
			<contains>
				<multiple>
					<baseValue baseType="string">A</baseValue>
					<baseValue baseType="string">B</baseValue>
					<baseValue baseType="string">C</baseValue>
				</multiple>
				<multiple>
					<baseValue baseType="string">B</baseValue>
					<baseValue baseType="string">C</baseValue>
				</multiple>
			</contains>
		');
	}
}