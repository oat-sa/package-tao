<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Identifier;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Integer;
use qtism\runtime\expressions\operators\MultipleProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\common\enums\BaseType;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\common\RecordContainer;
use qtism\runtime\common\MultipleContainer;
use qtism\common\datatypes\Duration;
use qtism\common\datatypes\Point;

class MultipleProcessorTest extends QtiSmTestCase {
	
	public function testNull() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$processor = new MultipleProcessor($expression, $operands);
		
		$operands[] = null;
		$result = $processor->process();
		$this->assertSame(null, $result);
		
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::FLOAT)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
		
		$operands = new OperandsCollection(array(null, new Integer(25), new MultipleContainer(BaseType::INTEGER)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\runtime\\common\\MultipleContainer', $result);
		$this->assertEquals(1, count($result));
		$this->assertEquals(BaseType::INTEGER, $result->getBaseType());
		$this->assertEquals(25, $result[0]->getValue());
		
		$operands = new OperandsCollection(array(null, new Integer(25), new MultipleContainer(BaseType::INTEGER, array(new Integer(26)))));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\runtime\\common\\MultipleContainer', $result);
		$this->assertEquals(2, count($result));
		$this->assertEquals(BaseType::INTEGER, $result->getBaseType());
		$this->assertEquals(25, $result[0]->getValue());
		$this->assertEquals(26, $result[1]->getValue());
		
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::INTEGER), new Integer(25), new MultipleContainer(BaseType::INTEGER, array(new Integer(26)))));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\runtime\\common\\MultipleContainer', $result);
		$this->assertEquals(2, count($result));
		$this->assertEquals(BaseType::INTEGER, $result->getBaseType());
		$this->assertEquals(25, $result[0]->getValue());
		$this->assertEquals(26, $result[1]->getValue());
		
		$operands = new OperandsCollection();
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testScalar() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new String('String1');
		$operands[] = new String('String2');
		$operands[] = new String('String3');
		$processor = new MultipleProcessor($expression, $operands);
		
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\runtime\\common\\MultipleContainer', $result);
		$this->assertEquals(3, count($result));
		$this->assertEquals('String1', $result[0]->getValue());
		$this->assertEquals('String2', $result[1]->getValue());
		$this->assertEquals('String3', $result[2]->getValue());
		
		$operands = new OperandsCollection(array(new String('String!')));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\runtime\\common\\MultipleContainer', $result);
		$this->assertEquals(1, count($result));
	}
	
	public function testContainer() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(1, 2), new Point(2, 3)));
		$operands[] = new MultipleContainer(BaseType::POINT);
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(3, 4)));
		$processor = new MultipleProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\runtime\\common\\MultipleContainer', $result);
		$this->assertEquals(3, count($result));
		$this->assertTrue($result[0]->equals(new Point(1, 2)));
		$this->assertTrue($result[1]->equals(new Point(2, 3)));
		$this->assertTrue($result[2]->equals(new Point(3, 4)));
	}
	
	public function testMixed() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Point(1, 2);
		$operands[] = null;
		$operands[] = new MultipleContainer(BaseType::POINT, array(new Point(3, 4)));
		$processor = new MultipleProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\runtime\\common\\MultipleContainer', $result);
		$this->assertEquals(2, count($result));
		$this->assertTrue($result[0]->equals(new Point(1, 2)));
		$this->assertTrue($result[1]->equals(new Point(3, 4)));
	}
	
	public function testWrongBaseTypeOne() {
	    $expression = $this->createFakeExpression();
	    $operands = new OperandsCollection();
	    $operands[] = new MultipleContainer(BaseType::IDENTIFIER, array(new Identifier('identifier1'), new Identifier('identifier2')));
	    $operands[] = new Identifier('identifier3');
	    $operands[] = new MultipleContainer(BaseType::STRING, array(new String('string1'), new String('string2')));
	    $operands[] = null;
	    $processor = new MultipleProcessor($expression, $operands);
	    $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
	    $processor->process();
	}
	
	public function testWrongBaseType() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Point(1, 2);
		$operands[] = new Duration('P2D');
		$operands[] = null;
		$operands[] = new Integer(10);
		$processor = new MultipleProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongCardinality() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Integer(10);
		$operands[] = null;
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(10)));
		$processor = new MultipleProcessor($expression, $operands);
		$result = $processor->process();
		
		$operands[] = new RecordContainer();
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function createFakeExpression() {
		return $this->createComponentFromXml('
			<multiple>
				<baseValue baseType="boolean">false</baseValue>
				<baseValue baseType="boolean">true</baseValue>
				<multiple>
					<baseValue baseType="boolean">false</baseValue>
				</multiple>
			</multiple>
		');
	}
}