<?php

use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\RecordContainer;
use qtism\common\datatypes\Point;
use qtism\common\datatypes\Integer;
use qtism\common\datatypes\String;
use qtism\runtime\expressions\operators\OperatorProcessingException;
use qtism\runtime\expressions\operators\custom\Explode;
use qtism\runtime\expressions\operators\OperandsCollection;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class ExplodeProcessorTest extends QtiSmTestCase {
	
	public function testNotEnoughOperandsOne() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException',
		                            "The 'qtism.runtime.expressions.operators.custom.Explode' custom operator takes 2 sub-expressions as parameters, 0 given.",
		                            OperatorProcessingException::NOT_ENOUGH_OPERANDS);
		$processor = new Explode($expression, $operands);
		$result = $processor->process();
	}
	
	public function testNotEnoughOperandsTwo() {
	    $expression = $this->createFakeExpression();
	    $operands = new OperandsCollection(array(new String('Hello-World!')));
	    $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException',
	                    "The 'qtism.runtime.expressions.operators.custom.Explode' custom operator takes 2 sub-expressions as parameters, 1 given.",
	                    OperatorProcessingException::NOT_ENOUGH_OPERANDS);
	    $processor = new Explode($expression, $operands);
	    $result = $processor->process();
	}
	
	public function testWrongBaseType() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(2), new Point(0, 0)));
		$processor = new Explode($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException',
		                            "The 'qtism.runtime.expressions.operators.custom.Explode' custom operator only accepts operands with a string baseType.",
		                            OperatorProcessingException::WRONG_BASETYPE);
		$result = $processor->process();
	}
	
	public function testWrongCardinality() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new RecordContainer(array('a' => new String('String!'))), new String('Hey!')));
		$processor = new Explode($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException',
		                            "The 'qtism.runtime.expressions.operators.custom.Explode' custom operator only accepts operands with single cardinality.",
		                            OperatorProcessingException::WRONG_CARDINALITY);
		$result = $processor->process();
	}
	
	public function testNullOperands() {
		$expression = $this->createFakeExpression();
		
		// Edge case, empty multiple container, considered as null.
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::FLOAT), new MultipleContainer(BaseType::FLOAT)));
		$processor = new Explode($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testExplodeOne() {
	    $expression = $this->createFakeExpression();
	    $operands = new OperandsCollection(array(new String('-'), new String('Hello-World-This-Is-Me')));
	    $processor = new Explode($expression, $operands);
	    $result = $processor->process();
	    
	    $this->assertInstanceOf('qtism\\runtime\\common\\OrderedContainer', $result);
	    $this->assertSame(5, count($result));
	    $this->assertEquals(array('Hello', 'World', 'This', 'Is', 'Me'), $result->getArrayCopy());
	}
	
	public function testExplodeTwo() {
	    // Specific case, the delimiter is not found in the original string.
	    $expression = $this->createFakeExpression();
	    $operands = new OperandsCollection(array(new String('-'), new String('Hello World!')));
	    $processor = new Explode($expression, $operands);
	    $result = $processor->process();
	    
	    $this->assertInstanceOf('qtism\\runtime\\common\\OrderedContainer', $result);
	    $this->assertSame(1, count($result));
	    $this->assertEquals(array('Hello World!'), $result->getArrayCopy());
	}
	
	public function testExplodeThree() {
	    $expression = $this->createFakeExpression();
	    $operands = new OperandsCollection(array(new String(' '), new String('Hello World!')));
	    $processor = new Explode($expression, $operands);
	    $result = $processor->process();
	     
	    $this->assertInstanceOf('qtism\\runtime\\common\\OrderedContainer', $result);
	    $this->assertSame(2, count($result));
	    $this->assertEquals(array('Hello',  'World!'), $result->getArrayCopy());
	}
	
	public function createFakeExpression() {
		return $this->createComponentFromXml('
			<customOperator class="qtism.runtime.expressions.operators.custom.Explode">
		        <baseValue baseType="string"> </baseValue>
				<baseValue baseType="string">Hello World!</baseValue>
			</customOperator>
		');
	}
}