<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Float;
use qtism\common\datatypes\Boolean;
use qtism\common\datatypes\Integer;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Point;
use qtism\runtime\common\RecordContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\runtime\expressions\operators\IsNullProcessor;

class IsNullProcessorTest extends QtiSmTestCase {
	
	public function testWithEmptyString() {
		$operands = new OperandsCollection();
		$operands[] = new String('');
		
		$expression = $this->getFakeExpression();
		$processor = new IsNullProcessor($expression, $operands);
		$this->assertTrue($processor->process()->getValue());
	}
	
	public function testWithNull() {
		$operands = new OperandsCollection();
		$operands[] = null;
		
		$expression = $this->getFakeExpression();
		$processor = new IsNullProcessor($expression, $operands);
		$this->assertTrue($processor->process()->getValue());
	}
	
	public function testEmptyContainers() {
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::POINT);
		
		$expression = $this->getFakeExpression();
		$processor = new IsNullProcessor($expression, $operands);
		$this->assertTrue($processor->process()->getValue());
		
		$operands->reset();
		$operands[] = new OrderedContainer(BaseType::BOOLEAN);
		$this->assertTrue($processor->process()->getValue());
		
		$operands->reset();
		$operands[] = new RecordContainer();
		$this->assertTrue($processor->process()->getValue());
	}
	
	public function testNotEmpty() {
		$expression = $this->getFakeExpression();
		$operands = new OperandsCollection(array(new Integer(0)));
		
		$processor = new IsNullProcessor($expression, $operands);
		$this->assertFalse($processor->process()->getValue());
		
		$operands->reset();
		$operands[] = new Boolean(false);
		$this->assertFalse($processor->process()->getValue());
		
		$operands->reset();
		$operands[] = new Integer(-1);
		$this->assertFalse($processor->process()->getValue());
		
		$operands->reset();
		$operands[] = new Point(1, 2);
		$this->assertFalse($processor->process()->getValue());
		
		$operands->reset();
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(25)));
		$this->assertFalse($processor->process()->getValue());
		
		$operands->reset();
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(3, 4), new Point(5, 6)));
		$this->assertFalse($processor->process()->getValue());
		
		$operands->reset();
		$operands[] = new RecordContainer(array('a' => new Boolean(true),  'b' => null,  'c' => new Point(1, 2), 'd' => new Integer(24), 'e' => new Float(23.3)));
		$this->assertFalse($processor->process()->getValue());
	}
	
	public function testLessThanNeededOperands() {
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		
		$operands = new OperandsCollection();
		$expression = $this->getFakeExpression();
		$processor = new IsNullProcessor($expression, $operands);
		$result = $processor->process();
	}
	
	public function testMoreThanNeededOperands() {
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		
		$operands = new OperandsCollection(array(new Integer(25), null));
		$expression = $this->getFakeExpression();
		$processor = new IsNullProcessor($expression, $operands);
		$result = $processor->process();
	}
	
	private function getFakeExpression() {
		$expression = $this->createComponentFromXml('
			<isNull>
				<baseValue baseType="string"></baseValue>
			</isNull>
		');
		
		return $expression;
	}
}