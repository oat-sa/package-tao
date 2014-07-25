<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Float;
use qtism\common\datatypes\Uri;
use qtism\common\datatypes\Identifier;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Integer;
use qtism\runtime\common\MultipleContainer;
use qtism\common\datatypes\Point;
use qtism\common\enums\BaseType;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\expressions\operators\RepeatProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;

class RepeatProcessorTest extends QtiSmTestCase {
	
	public function testRepeatScalarOnly() {
		$initialVal = array(new Integer(1), new Integer(2), new Integer(3));
		$expression = $this->createFakeExpression(1);
		$operands = new OperandsCollection($initialVal);
		$processor = new RepeatProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertTrue($result->equals(new OrderedContainer(BaseType::INTEGER, $initialVal)));
		
		$expression = $this->createFakeExpression(2);
		$processor->setExpression($expression);
		$result = $processor->process();
		$this->assertTrue($result->equals(new OrderedContainer(BaseType::INTEGER, array_merge($initialVal, $initialVal))));
	}
	
	public function testOrderedOnly() {
		$expression = $this->createFakeExpression(2);
		$ordered1 = new OrderedContainer(BaseType::INTEGER, array(new Integer(1), new Integer(2), new Integer(3)));
		$ordered2 = new OrderedContainer(BaseType::INTEGER, array(new Integer(4)));
		$operands = new OperandsCollection(array($ordered1, $ordered2));
		$processor = new RepeatProcessor($expression, $operands);
		$result = $processor->process();
		
		$comparison = new OrderedContainer(BaseType::INTEGER, array(new Integer(1), new Integer(2), new Integer(3), new Integer(4), new Integer(1), new Integer(2), new Integer(3), new Integer(4)));
		$this->assertTrue($comparison->equals($result));
	}
	
	public function testMixed() {
		$expression = $this->createFakeExpression(2);
		$operands = new OperandsCollection();
		$operands[] = new Point(0, 0);
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(1, 2), new Point(2, 3), new Point(3, 4)));
		$operands[] = new Point(10, 10);
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(4, 5)));
		
		$processor = new RepeatProcessor($expression, $operands);
		$result = $processor->process();
		
		$comparison = new OrderedContainer(BaseType::POINT, array(new Point(0, 0), new Point(1, 2), new Point(2, 3), new Point(3, 4), new Point(10, 10), new Point(4, 5), new Point(0, 0), new Point(1, 2), new Point(2, 3), new Point(3, 4), new Point(10, 10), new Point(4, 5)));
		$this->assertTrue($comparison->equals($result));
	}
	
	public function testNull() {
		// If all sub-expressions are NULL, the result is NULL.
		$expression = $this->createFakeExpression(1);
		$operands = new OperandsCollection(array(null, new OrderedContainer(BaseType::INTEGER)));
		$processor = new RepeatProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
		
		// Any sub-expressions evaluating to NULL are ignored.
		$operands = new OperandsCollection(array(null, new String('String1'), new OrderedContainer(BaseType::STRING, array(new String('String2'), null)), new String('String3')));
		$processor->setOperands($operands);
		$result = $processor->process();
		
		$comparison = new OrderedContainer(BaseType::STRING, array(new String('String1'), new String('String2'), null, new String('String3')));
		$this->assertTrue($result->equals($comparison));
	}
	
	public function testWrongBaseTypeOne() {
	    $expression = $this->createFakeExpression(1);
	    $operands = new OperandsCollection();
	    $operands[] = null;
	    $operands[] = new OrderedContainer(BaseType::IDENTIFIER, array(new Identifier('id1'), new Identifier('id2')));
	    $operands[] = new OrderedContainer(BaseType::URI, array(new Uri('id3'), new Uri('id4')));
	    $operands[] = new Uri('http://www.taotesting.com');
	    $operands[] = new OrderedContainer(BaseType::STRING);
	    
	    $processor = new RepeatProcessor($expression, $operands);
	    $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
	    $result = $processor->process();
	}
	
	public function testWrongCardinality() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::INTEGER, array(new Integer(10)))));
		$processor = new RepeatProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongBaseTypeTwo() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new OrderedContainer(BaseType::INTEGER, array(new Integer(10))), new Float(10.3)));
		$processor = new RepeatProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new RepeatProcessor($expression, $operands);
	}
	
	public function createFakeExpression($numberRepeats = 1) {
		return $this->createComponentFromXml('
			<repeat numberRepeats="' . $numberRepeats . '">
				<baseValue baseType="integer">120</baseValue>
			</repeat>
		');
	}
}