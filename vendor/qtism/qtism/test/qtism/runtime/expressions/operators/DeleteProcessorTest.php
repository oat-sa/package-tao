<?php
use qtism\common\datatypes\Float;

use qtism\common\datatypes\Integer;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Point;
use qtism\runtime\expressions\operators\DeleteProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\common\RecordContainer;

class DeleteProcessorTest extends QtiSmTestCase {
	
	public function testMultiple() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Integer(10);
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(0), new Integer(10), new Integer(20), new Integer(30)));
		$processor = new DeleteProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\runtime\\common\\MultipleContainer', $result);
		$this->assertEquals(3, count($result));
		$this->assertTrue($result->contains(new Integer(0)));
		$this->assertTrue($result->contains(new Integer(20)));
		$this->assertTrue($result->contains(new Integer(30)));
		$this->assertFalse($result->contains(new Integer(10)));
		
		// Check that ALL the occurences of the first sub-expression are removed.
		$operands->reset();
		$operands[] = new Integer(10);
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(0), new Integer(10), new Integer(20), new Integer(10), new Integer(10), new Integer(30)));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\runtime\\common\\MultipleContainer', $result);
		$this->assertEquals(3, count($result));
		$this->assertTrue($result->contains(new Integer(0)));
		$this->assertTrue($result->contains(new Integer(20)));
		$this->assertTrue($result->contains(new Integer(30)));
		$this->assertFalse($result->contains(new Integer(10)));
	}
	
	public function testMultipleNotMatch() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Integer(60);
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(0), new Integer(10), new Integer(20), new Integer(30)));
		$processor = new DeleteProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertTrue($operands[1]->equals($result));
	}
	
	public function testEverythingRemoved() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Integer(60);
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(60), new Integer(60), new Integer(60), new Integer(60)));
		$processor = new DeleteProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\runtime\\common\\MultipleContainer', $result);
		$this->assertTrue($result->isNull());
	}
	
	public function testOrdered() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Point(2, 4);
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(1, 2), new Point(2, 4), new Point(3, 4)));
		$processor = new DeleteProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\runtime\\common\\OrderedContainer', $result);
		$this->assertEquals(2, count($result));
		$this->assertTrue($result->contains(new Point(1, 2)));
		$this->assertTrue($result->contains(new Point(3, 4)));
		$this->assertFalse($result->contains(new Point(2, 4)));
	
		// Check that ALL the occurences of the first sub-expression are removed.
		$operands->reset();
		$operands[] = new Point(2, 4);
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(1, 2), new Point(2, 4), new Point(2, 4), new Point(3, 4)));
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\runtime\\common\\OrderedContainer', $result);
		$this->assertEquals(2, count($result));
		$this->assertTrue($result->contains(new Point(1, 2)));
		$this->assertTrue($result->contains(new Point(3, 4)));
		$this->assertFalse($result->contains(new Point(2, 4)));
	}
	
	public function testNull() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = null;
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(0), new Integer(10), new Integer(20), new Integer(30)));
		$processor = new DeleteProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
		
		$operands->reset();
		$operands[] = new Integer(10);
		$operands[] = new MultipleContainer(BaseType::INTEGER);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testDifferentBaseType() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Float(10.1);
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(0), new Integer(10), new Integer(20), new Integer(30)));
		$processor = new DeleteProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongCardinalityOne() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(0), new Integer(10), new Integer(20), new Integer(30)));
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(0), new Integer(10), new Integer(20), new Integer(30)));
		$processor = new DeleteProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongCardinalityTwo() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Integer(10);
		$operands[] = new Integer(10);
		$processor = new DeleteProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new DeleteProcessor($expression, $operands);
	}
	
	public function testTooMuchOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Integer(10);
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(10)));
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(10)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new DeleteProcessor($expression, $operands);
	}
	
	public function createFakeExpression() {
		return $this->createComponentFromXml('
			<delete>
				<baseValue baseType="integer">10</baseValue>
				<multiple>
					<baseValue baseType="integer">0</baseValue>
					<baseValue baseType="integer">10</baseValue>
					<baseValue baseType="integer">20</baseValue>
					<baseValue baseType="integer">30</baseValue>
				</multiple>
			</delete>
		');
	}
}