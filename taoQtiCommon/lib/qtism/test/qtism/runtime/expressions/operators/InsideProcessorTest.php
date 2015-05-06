<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Integer;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\common\datatypes\Duration;
use qtism\common\datatypes\Shape;
use qtism\common\datatypes\Coords;
use qtism\common\datatypes\Point;
use qtism\runtime\expressions\operators\InsideProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;

class InsideProcessorTest extends QtiSmTestCase {
	
	public function testRect() {
		$coords = new Coords(Shape::RECT, array(0, 0, 5, 3));
		$point = new Point(0, 0); // 0, 0 is inside.
		$expression = $this->createFakeExpression($point, $coords);
		$operands = new OperandsCollection(array($point));
		$processor = new InsideProcessor($expression, $operands);
		
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		$point = new Point(-1, -1); // -1, -1 is outside.
		$operands = new OperandsCollection(array($point));
		$expression = $this->createFakeExpression($point, $coords);
		$processor->setExpression($expression);
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testPoly() {
		$coords = new Coords(Shape::POLY, array(0, 8, 7, 4, 2, 2, 8, -4, -2, 1));
		$point = new Point(0, 8); // 0, 8 is inside.
		$expression = $this->createFakeExpression($point, $coords);
		$operands = new OperandsCollection(array($point));
		$processor = new InsideProcessor($expression, $operands);
	
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	
		$point = new Point(10, 9); // 10, 9 is outside.
		$operands = new OperandsCollection(array($point));
		$expression = $this->createFakeExpression($point, $coords);
		$processor->setExpression($expression);
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testCircle() {
		$coords = new Coords(Shape::CIRCLE, array(5, 5, 5));
		$point = new Point(3, 3); // 3,3 is inside
		$expression = $this->createFakeExpression($point, $coords);
		$operands = new OperandsCollection(array($point));
		$processor = new InsideProcessor($expression, $operands);
	
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
	
		$point = new Point(1, 1); // 1,1 is outside
		$operands = new OperandsCollection(array($point));
		$expression = $this->createFakeExpression($point, $coords);
		$processor->setExpression($expression);
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testNull() {
		$coords = new Coords(Shape::RECT, array(0, 0, 5, 3));
		$point = null;
		$expression = $this->createFakeExpression($point, $coords);
		$operands = new OperandsCollection(array($point));
		$processor = new InsideProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testWrongBaseTypeOne() {
		$coords = new Coords(Shape::RECT, array(0, 0, 5, 3));
		$point = new Duration('P1D');
		$expression = $this->createFakeExpression($point, $coords);
		$operands = new OperandsCollection(array($point));
		$processor = new InsideProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongBaseTypeTwo() {
		$coords = new Coords(Shape::RECT, array(0, 0, 5, 3));
		$point = new Integer(10);
		$expression = $this->createFakeExpression($point, $coords);
		$operands = new OperandsCollection(array($point));
		$processor = new InsideProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongCardinality() {
		$coords = new Coords(Shape::RECT, array(0, 0, 5, 3));
		$point = new MultipleContainer(BaseType::POINT, array(new Point(1, 2)));
		$expression = $this->createFakeExpression($point, $coords);
		$operands = new OperandsCollection(array($point));
		$processor = new InsideProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotEnoughOperands() {
		$coords = new Coords(Shape::RECT, array(0, 0, 5, 3));
		$point = new Point(1, 2);
		$expression = $this->createFakeExpression($point, $coords);
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new InsideProcessor($expression, $operands);
	}
	
	public function testTooMuchOperands() {
		$coords = new Coords(Shape::RECT, array(0, 0, 5, 3));
		$point = new Point(1, 2);
		$expression = $this->createFakeExpression($point, $coords);
		$operands = new OperandsCollection(array(new Point(1, 2), new Point(2, 3)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new InsideProcessor($expression, $operands);
	}
	
	public function createFakeExpression($point = null, Coords $coords = null) {
		$point = (is_null($point) || !$point instanceof Point) ? new Point(2, 2) : $point;
		$coords = (is_null($coords)) ? new Coords(Shape::RECT, array(0, 0, 5, 3)) : $coords;
		
		return $this->createComponentFromXml('
			<inside shape="' . Shape::getNameByConstant($coords->getShape()) . '" coords="' . $coords . '">
				<baseValue baseType="point">' . $point . '</baseValue>
			</inside>
		');
	}
}