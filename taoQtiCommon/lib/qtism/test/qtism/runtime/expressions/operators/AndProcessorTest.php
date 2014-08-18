<?php

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Point;
use qtism\runtime\expressions\operators\AndProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\RecordContainer;
use qtism\common\datatypes\Boolean;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\String;

class AndProcessorTest extends QtiSmTestCase {
	
	public function testNotEnoughOperands	() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new AndProcessor($expression, $operands);
		$result = $processor->process();
	}
	
	public function testWrongBaseType() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Point(1, 2)));
		$processor = new AndProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongCardinalityOne() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new RecordContainer(array('a' => new String('string!')))));
		$processor = new AndProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongCardinalityTwo() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::FLOAT, array(new Float(25.0)))));
		$processor = new AndProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNullOperands() {
		$expression = $this->createFakeExpression();
		
		// Even if the cardinality is wrong, the MultipleContainer object will be first considered
		// to be NULL because it is empty.
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::FLOAT)));
		$processor = new AndProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
		
		// Two NULL values, 'null' && new RecordContainer().
		$operands = new OperandsCollection(array(new Boolean(true), new Boolean(false), new Boolean(true), null, new RecordContainer()));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testTrue() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Boolean(true)));
		$processor = new AndProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertSame(true, $result->getValue());
		
		$operands = new OperandsCollection(array(new Boolean(true), new Boolean(true), new Boolean(true)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertSame(true, $result->getValue());
	}
	
	public function testFalse() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Boolean(false)));
		$processor = new AndProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertSame(false, $result->getValue());
		
		$operands = new OperandsCollection(array(new Boolean(false), new Boolean(true), new Boolean(false)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertSame(false, $result->getValue());
	}
	
	public function createFakeExpression() {
		return $this->createComponentFromXml('
			<and>
				<baseValue baseType="boolean">false</baseValue>
			</and>
		');
	}
}