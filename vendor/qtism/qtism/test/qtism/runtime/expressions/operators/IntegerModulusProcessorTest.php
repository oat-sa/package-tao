<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Integer;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Duration;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\expressions\operators\IntegerModulusProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;

class IntegerModulusProcessorTest extends QtiSmTestCase {
	
	public function testIntegerModulus() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(10), new Integer(5)));
		$processor = new IntegerModulusProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(0, $result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(49), new Integer(-5)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(4, $result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(36), new Integer(7)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(1, $result->getValue());
	}
	
	public function testNull() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(null, new Integer(5)));
		$processor = new IntegerModulusProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testModulusByZero() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(50), new Integer(0)));
		$processor = new IntegerModulusProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testWrongCardinality() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::INTEGER, array(new Integer(10))), new Integer(5)));
		$processor = new IntegerModulusProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongBaseTypeOne() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new String('ping!'), new Integer(5)));
		$processor = new IntegerModulusProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongBaseTypeTwo() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(5), new Duration('P1D')));
		$processor = new IntegerModulusProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(5)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new IntegerModulusProcessor($expression, $operands);
	}
	
	public function testTooMuchOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(5), new Integer(5), new Integer(5)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new IntegerModulusProcessor($expression, $operands);
	}
	
	public function createFakeExpression() {
		return $this->createComponentFromXml('
			<integerModulus>
				<baseValue baseType="integer">36</baseValue>
				<baseValue baseType="integer">7</baseValue>
			</integerModulus>
		');
	}
}