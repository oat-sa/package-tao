<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Integer;
use qtism\common\datatypes\Boolean;
use qtism\runtime\expressions\operators\NotProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\common\datatypes\Point;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;

class NotProcessorTest extends QtiSmTestCase {
	
	public function testNotEnoughOperands	() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new NotProcessor($expression, $operands);
	}
	
	public function testTooMuchOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Boolean(true), new Boolean(false)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new NotProcessor($expression, $operands);
	}
	
	public function testWrongCardinality() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::POINT, array(new Point(1, 2)))));
		$processor = new NotProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongBaseType() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(25)));
		$processor = new NotProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNull() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(null));
		$processor = new NotProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testTrue() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Boolean(false)));
		$processor = new NotProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(true, $result->getValue());
	}
	
	public function testFalse() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Boolean(true)));
		$processor = new NotProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertSame(false, $result->getValue());
	}
	
	public function createFakeExpression() {
		return $this->createComponentFromXml('
			<not>
				<baseValue baseType="boolean">false</baseValue>
			</not>
		');
	}
}