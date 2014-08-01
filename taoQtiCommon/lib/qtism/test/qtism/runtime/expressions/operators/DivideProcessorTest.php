<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Boolean;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\runtime\common\RecordContainer;
use qtism\common\datatypes\Point;
use qtism\runtime\expressions\operators\DivideProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;

class DivideProcessorTest extends QtiSmTestCase {
	
	public function testDivide() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(1), new Integer(1)));
		$processor = new DivideProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(1, $result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(0), new Integer(2)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(0, $result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(-30), new Integer(5)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(-6, $result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(30), new Integer(5)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(6, $result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(1), new Float(0.5)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(2, $result->getValue());
	}
	
	public function testDivisionByZero() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(1), new Integer(0)));
		$processor = new DivideProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testDivisionByInfinite() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(10), new Float(INF)));
		$processor = new DivideProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(0, $result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(-1), new Float(INF)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(-0, $result->getValue());
	}
	
	public function testInfiniteDividedByInfinite() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Float(INF), new Float(INF)));
		$processor = new DivideProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testWrongBaseTypeOne() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new String('string!'), new Boolean(true)));
		$processor = new DivideProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongBaseTypeTwo() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Point(1, 2), new Boolean(true)));
		$processor = new DivideProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongCardinality() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new RecordContainer(array('A' => new Integer(1))), new Integer(10)));
		$processor = new DivideProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new DivideProcessor($expression, $operands);
	}
	
	public function testTooMuchOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(10), new Integer(11), new Integer(12)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new DivideProcessor($expression, $operands);
	}
	
	public function createFakeExpression() {
		return $this->createComponentFromXml('
			<divide>
				<baseValue baseType="integer">10</baseValue>
				<baseValue baseType="integer">2</baseValue>
			</divide>
		');
	}
}