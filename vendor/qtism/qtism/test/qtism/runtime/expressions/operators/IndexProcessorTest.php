<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Integer;
use qtism\common\datatypes\Point;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\common\State;
use qtism\runtime\expressions\operators\IndexProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;

class IndexProcessorTest extends QtiSmTestCase {
	
	public function testIndexNumeric() {
		// first trial at the trail of the collection.
		$expression = $this->createFakeExpression(1);
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::INTEGER, array(new Integer(1), new Integer(2), new Integer(3), new Integer(4), new Integer(5)));
		$processor = new IndexProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(1, $result->getValue());
		
		// in the middle...
		$expression = $this->createFakeExpression(3);
		$processor->setExpression($expression);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(3, $result->getValue());
		
		// in the end...
		$expression = $this->createFakeExpression(5);
		$processor->setExpression($expression);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(5, $result->getValue());
	}
	
	public function testIndexVariableReference() {
		$expression = $this->createFakeExpression('variable1');
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::INTEGER, array(new Integer(1), new Integer(2), new Integer(3), new Integer(4), new Integer(5)));
		$processor = new IndexProcessor($expression, $operands);
		
		$state = new State();
		$state->setVariable(new OutcomeVariable('variable1', Cardinality::SINGLE, BaseType::INTEGER, new Integer(3)));
		$processor->setState($state);
		
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(3, $result->getValue());
	}
	
	public function testIndexVariableReferenceNotFound() {
		$expression = $this->createFakeExpression('variable1');
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::INTEGER, array(new Integer(1), new Integer(2), new Integer(3), new Integer(4), new Integer(5)));
		$processor = new IndexProcessor($expression, $operands);
		
		$state = new State();
		$state->setVariable(new OutcomeVariable('variableXXX', Cardinality::SINGLE, BaseType::INTEGER, new Integer(3)));
		$processor->setState($state);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testVariableReferenceNotInteger() {
		$expression = $this->createFakeExpression('variable1');
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::INTEGER, array(new Integer(1), new Integer(2), new Integer(3), new Integer(4), new Integer(5)));
		$processor = new IndexProcessor($expression, $operands);
		
		$state = new State();
		$state->setVariable(new OutcomeVariable('variable1', Cardinality::SINGLE, BaseType::POINT, new Point(1, 2)));
		$processor->setState($state);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testOutOfRangeOne() {
		// 1. non-zero integer
		$expression = $this->createFakeExpression(-3);
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::INTEGER, array(new Integer(1), new Integer(2), new Integer(3), new Integer(4), new Integer(5)));
		$processor = new IndexProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testOutOfRangeTwo() {
		// 2. out of range
		$expression = $this->createFakeExpression(1000);
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::INTEGER, array(new Integer(1), new Integer(2), new Integer(3), new Integer(4), new Integer(5)));
		$processor = new IndexProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testWrongCardinality() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(1), new Integer(2), new Integer(3), new Integer(4), new Integer(5)));
		$processor = new IndexProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNull() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::FLOAT);
		$processor = new IndexProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new IndexProcessor($expression, $operands);
	}
	
	public function testTooMuchOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::INTEGER, array(new Integer(1), new Integer(2), new Integer(3), new Integer(4), new Integer(5)));
		$operands[] = new OrderedContainer(BaseType::INTEGER, array(new Integer(1), new Integer(2), new Integer(3), new Integer(4), new Integer(5)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new IndexProcessor($expression, $operands);
	}
	
	public function createFakeExpression($n = -1) {
		
		if ($n === -1) {
			$n = 3;
		}
		 
		return $this->createComponentFromXml('
			<index n="' . $n . '">
				<ordered>
					<baseValue baseType="integer">1</baseValue>
					<baseValue baseType="integer">2</baseValue>
					<baseValue baseType="integer">3</baseValue>
					<baseValue baseType="integer">4</baseValue>
					<baseValue baseType="integer">5</baseValue>
				</ordered>
			</index>
		');
	}
}