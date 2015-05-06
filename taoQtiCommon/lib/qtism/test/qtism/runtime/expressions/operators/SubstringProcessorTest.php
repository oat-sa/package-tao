<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Integer;
use qtism\common\datatypes\String;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\expressions\operators\SubstringProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;

class SubstringProcessorTest extends QtiSmTestCase {
	
	public function testCaseSensitive() {
		$expression = $this->createFakeExpression(true);
		$operands = new OperandsCollection();
		$operands[] = new String('hell');
		$operands[] = new String('Shell');
		$processor = new SubstringProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		$operands->reset();
		$operands[] = new String('Hell');
		$operands[] = new String('Shell');
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testCaseInsensitive() {
		$expression = $this->createFakeExpression(false);
		$operands = new OperandsCollection();
		$operands[] = new String('hell');
		$operands[] = new String('Shell');
		$processor = new SubstringProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		$operands->reset();
		$operands[] = new String('Hell');
		$operands[] = new String('Shell');
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		$operands->reset();
		$operands[] = new String('Hello world!');
		$operands[] = new String('Bye world!');
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
		
		$operands->reset();
		$operands[] = new String('Hello World!');
		$operands[] = new String('hello world!');
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		// Unicode ? x)
		$operands->reset();
		$operands[] = new String('界您');
		$operands[] = new String('世界您好！'); // Hello World!
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		$operands->reset();
		$operands[] = new String('假'); // 'Fake' in traditional chinese
		$operands[] = new String('世界您好！'); // Hello World!
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testNull() {
		$expression = $this->createFakeExpression(false);
		$operands = new OperandsCollection();
		$operands[] = new String('test');
		$operands[] = null;
		$processor = new SubstringProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
		
		$operands->reset();
		$operands[] = new String(''); // in QTI, empty string considered to be NULL.
		$operands[] = new String('blah!');
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testWrongBaseType() {
		$expression = $this->createFakeExpression(false);
		$operands = new OperandsCollection();
		$operands[] = new String('10');
		$operands[] = new Integer(100);
		$processor = new SubstringProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongCardinality() {
		$expression = $this->createFakeExpression(false);
		$operands = new OperandsCollection();
		$operands[] = new String('Wrong Cardinality');
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('Wrong'), new String('Cardinality')));
		$processor = new SubstringProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression(false);
		$operands = new OperandsCollection(array(new String('only 1 operand')));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new SubstringProcessor($expression, $operands);
	}
	
	public function testTooMuchOperands() {
		$expression = $this->createFakeExpression(false);
		$operands = new OperandsCollection(array(new String('exactly'), new String('three'), new String('operands')));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new SubstringProcessor($expression, $operands);
	}
	
	public function createFakeExpression($caseSensitive = true) {
		
		$str = ($caseSensitive === true) ? 'true' : 'false';
		
		return $this->createComponentFromXml('
			<substring caseSensitive="' . $str . '">
				<baseValue baseType="string">hell</baseValue>
				<baseValue baseType="string">shell</baseValue>
			</substring>
		');
	}
}