<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Integer;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Float;
use qtism\runtime\common\RecordContainer;
use qtism\common\datatypes\Point;
use qtism\common\datatypes\Duration;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\expressions\operators\RandomProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;

class RandomProcessorTest extends QtiSmTestCase {
	
	public function testPrimitiveMultiple() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::FLOAT, array(new Float(1.0), new Float(2.0), new Float(3.0)));
		$processor = new RandomProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertGreaterThanOrEqual(1.0, $result->getValue());
		$this->assertLessThanOrEqual(3.0, $result->getValue());
	}
	
	public function testPrimitiveOrdered() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('s1'), new String('s2'), new String('s3')));
		$processor = new RandomProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\String', $result);
		$this->assertTrue($result->equals(new String('s1')) || $result->equals(new String('s2')) || $result->equals(new String('s3')));
	}
	
	public function testComplexMultiple() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::DURATION, array(new Duration('P1D'), new Duration('P2D'), new Duration('P3D')));
		$processor = new RandomProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Duration', $result);
		$this->assertGreaterThanOrEqual(1, $result->getDays());
		$this->assertLessThanOrEqual(3, $result->getDays());
	}
	
	public function testComplexOrdered() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(1, 1), new Point(2, 2), new Point(3, 3)));
		$processor = new RandomProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Point', $result);
		$this->assertGreaterThanOrEqual(1, $result->getX());
		$this->assertLessThanOrEqual(3, $result->getY());
	}
	
	public function testOnlyOneInContainer() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::POINT, array(new Point(22, 33)));
		$processor = new RandomProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Point', $result);
		$this->assertEquals(22, $result->getX());
		$this->assertEquals(33, $result->getY());
	}
	
	public function testNull() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::POINT);
		$processor = new RandomProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testWrongCardinalityOne() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Integer(10);
		$processor = new RandomProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongCardinalityTwo() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new RecordContainer(array('A' => new Integer(1)));
		$processor = new RandomProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new RandomProcessor($expression, $operands);
		$result = $processor->process();
	}
	
	public function testTooMuchOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::PAIR);
		$operands[] = new MultipleContainer(BaseType::PAIR);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new RandomProcessor($expression, $operands);
		$result = $processor->process();
	}
	
	public function createFakeExpression() {
		return $this->createComponentFromXml('
			<random>
				<multiple>
					<baseValue baseType="boolean">true</baseValue>
					<baseValue baseType="boolean">false</baseValue>
				</multiple>
			</random>
		');
	}
}