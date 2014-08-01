<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\String;
use qtism\common\datatypes\Integer;
use qtism\common\datatypes\Duration;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\expressions\operators\IntegerDivideProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;

class IntegerDivideProcessorTest extends QtiSmTestCase {
	
	public function testIntegerDivide() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(10), new Integer(5)));
		$processor = new IntegerDivideProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(2, $result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(49), new Integer(-5)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(-10, $result->getValue());
	}
	
	public function testNull() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(null, new Integer(5)));
		$processor = new IntegerDivideProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testDivisionByZero() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(50), new Integer(0)));
		$processor = new IntegerDivideProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testWrongCardinality() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::INTEGER, array(new Integer(10))), new Integer(5)));
		$processor = new IntegerDivideProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongBaseTypeOne() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new String('ping!'), new Integer(5)));
		$processor = new IntegerDivideProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongBaseTypeTwo() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(5), new Duration('P1D')));
		$processor = new IntegerDivideProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(5)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new IntegerDivideProcessor($expression, $operands);
	}
	
	public function testTooMuchOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(5), new Integer(5), new Integer(5)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new IntegerDivideProcessor($expression, $operands);
	}
	
	public function createFakeExpression() {
		return $this->createComponentFromXml('
			<integerDivide>
				<baseValue baseType="integer">10</baseValue>
				<baseValue baseType="integer">5</baseValue>
			</integerDivide>
		');
	}
}