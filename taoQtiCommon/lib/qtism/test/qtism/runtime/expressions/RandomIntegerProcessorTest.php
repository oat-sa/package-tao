<?php
use qtism\runtime\common\State;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\runtime\expressions\RandomIntegerProcessor;

class RandomIntegerProcessorTest extends QtiSmTestCase {

	public function testSimple() {
		$randomIntegerExpr = $this->createComponentFromXml('<randomInteger max="100"/>');
		$randomIntegerProcessor = new RandomIntegerProcessor($randomIntegerExpr);
		
		$result = $randomIntegerProcessor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertGreaterThanOrEqual(0, $result->getValue());
		$this->assertLessThanOrEqual(100, $result->getValue());
		
		$randomIntegerExpr = $this->createComponentFromXml('<randomInteger min="-100" max="100"/>');
		$randomIntegerProcessor->setExpression($randomIntegerExpr);
		$result = $randomIntegerProcessor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertGreaterThanOrEqual(-100, $result->getValue());
		$this->assertLessThanOrEqual(100, $result->getValue());
		
		$randomIntegerExpr = $this->createComponentFromXml('<randomInteger min="-20" max="23" step="4"/>');
		$randomIntegerProcessor->setExpression($randomIntegerExpr);
		$result = $randomIntegerProcessor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertGreaterThanOrEqual(-20, $result->getValue());
		$this->assertLessThanOrEqual(23, $result->getValue());
		$this->assertEquals(0, $result->getValue() % 4);
	}
	
	public function testMinLessThanMax() {
		$expression = $this->createComponentFromXml('<randomInteger min="100" max="10"/>');
		$processor = new RandomIntegerProcessor($expression);
		$this->setExpectedException("qtism\\runtime\\expressions\\ExpressionProcessingException");
		$result = $processor->process();
	}
}