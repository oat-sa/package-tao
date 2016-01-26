<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Boolean;
use qtism\common\datatypes\Integer;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Duration;
use qtism\common\enums\BaseType;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\expressions\operators\TruncateProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;

class TruncateProcessorTest extends QtiSmTestCase {
	
	public function testRound() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Float(6.8);
		$processor = new TruncateProcessor($expression, $operands);
		
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(6, $result->getValue());
		
		$operands->reset();
		$operands[] = new Float(6.5);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(6, $result->getValue());
		
		$operands->reset();
		$operands[] = new Float(6.49);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(6, $result->getValue());
		
		$operands->reset();
		$operands[] = new Float(-6.5);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(-6, $result->getValue());
		
		$operands->reset();
		$operands[] = new Float(-6.8);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(-6, $result->getValue());
		
		$operands->reset();
		$operands[] = new Float(-6.49);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(-6, $result->getValue());
		
		$operands->reset();
		$operands[] = new Integer(0);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(0, $result->getValue());
		
		$operands->reset();
		$operands[] = new Float(-0.0);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(0, $result->getValue());
		
		$operands->reset();
		$operands[] = new Float(-0.5);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(0, $result->getValue());
		
		$operands->reset();
		$operands[] = new Float(-0.4);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(0, $result->getValue());
		
		$operands->reset();
		$operands[] = new Float(-0.6);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(0, $result->getValue());
		
		$operands->reset();
		$operands[] = new Float(NAN);
		$result = $processor->process();
		$this->assertSame(null, $result);
		
		$operands->reset();
		$operands[] = new Float(-INF);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(-INF, $result->getValue());
		
		$operands->reset();
		$operands[] = new Float(INF);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(INF, $result->getValue());
	}
	
	public function testNull() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = null;
		$processor = new TruncateProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testWrongCardinality() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::FLOAT, array(new Float(1.1), new Float(2.2)));
		$processor = new TruncateProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongBaseTypeOne() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Boolean(true);
		$processor = new TruncateProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongBaseTypeTwo() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Duration('P1D');
		$processor = new TruncateProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new TruncateProcessor($expression, $operands);
	}
	
	public function testTooMuchOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Integer(10);
		$operands[] = new Float(1.1);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new TruncateProcessor($expression, $operands);
	}
	
	public function createFakeExpression() {
		return $this->createComponentFromXml('
			<truncate>
				<baseValue baseType="float">6.49</baseValue>
			</truncate>
		');
	}
}