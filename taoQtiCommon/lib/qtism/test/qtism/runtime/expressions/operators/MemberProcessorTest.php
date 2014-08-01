<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Identifier;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Integer;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\Point;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\expressions\operators\MemberProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;

class MemberProcessorTest extends QtiSmTestCase {
	
	public function testMultiple() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Float(10.1);
		$mult = new MultipleContainer(BaseType::FLOAT, array(new Float(1.1), new Float(2.1), new Float(3.1)));
		$operands[] = $mult;
		$processor = new MemberProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertEquals(false, $result->getValue());
		
		$mult[] = new Float(10.1);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertEquals(true, $result->getValue());
	}
	
	public function testOrdered() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Pair('A', 'B');
		$ordered = new OrderedContainer(BaseType::PAIR, array(new Pair('B', 'C')));
		$operands[] = $ordered;
		$processor = new MemberProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertEquals(false, $result->getValue());
		
		$ordered[] = new Pair('A', 'B');
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertEquals(true, $result->getValue());
	}
	
	public function testNull() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		
		// second operand is null.
		$operands[] = new Integer(10);
		$operands[] = new OrderedContainer(BaseType::INTEGER);
		$processor = new MemberProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
		
		// fist operand is null.
		$operands->reset();
		$operands[] = null;
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(10)));
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testDifferentBaseTypeOne() {
	    $expression = $this->createFakeExpression();
	    $operands = new OperandsCollection();
	    $operands[] = new String('String1');
	    $operands[] = new OrderedContainer(BaseType::IDENTIFIER, array(new Identifier('String2'), new Identifier('String1'), null));
	    $processor = new MemberProcessor($expression, $operands);
	    
	    $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
	    $processor->process();
	}
	
	public function testDifferentBaseTypeTwo() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Pair('A', 'B');
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(1, 2)));
		$processor = new MemberProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongCardinalityOne() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(13, 37)));
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(1, 2)));
		$processor = new MemberProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongCardinalityTwo() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Point(13, 37);
		$operands[] = new Point(13, 38);
		$processor = new MemberProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Point(13, 37);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new MemberProcessor($expression, $operands);
	}
	
	public function testTooMuchOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Point(13, 37);
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(1, 2)));
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(3, 4)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new MemberProcessor($expression, $operands);
	}
	
	public function createFakeExpression() {
		return $this->createComponentFromXml('
			<member>
				<baseValue baseType="boolean">true</baseValue>
				<ordered>
					<baseValue baseType="boolean">false</baseValue>
					<baseValue baseType="boolean">true</baseValue>
				</ordered>
			</member>
		');
	}
}