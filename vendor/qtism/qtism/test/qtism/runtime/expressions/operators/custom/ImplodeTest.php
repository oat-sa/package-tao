<?php

use qtism\common\datatypes\Point;
use qtism\common\datatypes\Integer;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\expressions\operators\OperatorProcessingException;
use qtism\runtime\expressions\operators\custom\Implode;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\common\datatypes\String;
use qtism\runtime\expressions\operators\OperandsCollection;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class ImplodeProcessorTest extends QtiSmTestCase {
	
	public function testNotEnoughOperandsOne() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException',
		                            "The 'qtism.runtime.expressions.operators.custom.Implode' custom operator takes 2 sub-expressions as parameters, 0 given.",
		                            OperatorProcessingException::NOT_ENOUGH_OPERANDS);
		$processor = new Implode($expression, $operands);
		$result = $processor->process();
	}
	
	public function testNotEnoughOperandsTwo() {
	    $expression = $this->createFakeExpression();
	    $operands = new OperandsCollection(array(new String('Hello-World!')));
	    $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException',
	                    "The 'qtism.runtime.expressions.operators.custom.Implode' custom operator takes 2 sub-expressions as parameters, 1 given.",
	                    OperatorProcessingException::NOT_ENOUGH_OPERANDS);
	    $processor = new Implode($expression, $operands);
	    $result = $processor->process();
	}
	
	public function testWrongBaseType() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(2), new Point(0, 0)));
		$processor = new Implode($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException',
		                            "The 'qtism.runtime.expressions.operators.custom.Implode' custom operator only accepts operands with a string baseType.",
		                            OperatorProcessingException::WRONG_BASETYPE);
		$result = $processor->process();
	}
	
	public function testWrongCardinalityOne() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::STRING, array(new String('String!'))), new String('Hello World!')));
		$processor = new Implode($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException',
		                            "The 'qtism.runtime.expressions.operators.custom.Implode' custom operator only accepts a first operand with single cardinality.",
		                            OperatorProcessingException::WRONG_CARDINALITY);
		$result = $processor->process();
	}
	
	public function testWrongCardinalityTwo() {
	    $expression = $this->createFakeExpression();
	    $operands = new OperandsCollection(array(new String('-'), new String('Hello-World!')));
	    $processor = new Implode($expression, $operands);
	    $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException',
	                    "The 'qtism.runtime.expressions.operators.custom.Implode' custom operator only accepts a second operand with multiple or ordered cardinality.",
	                    OperatorProcessingException::WRONG_CARDINALITY);
	    $result = $processor->process();
	}
	
	public function testNullOperands() {
		$expression = $this->createFakeExpression();
		
		$operands = new OperandsCollection(array(new String(''), null));
		$processor = new Implode($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testImplodeOne() {
	    $expression = $this->createFakeExpression();
	    $operands = new OperandsCollection(array(new String('-'), new MultipleContainer(BaseType::STRING, array(new String('Hello'), new String('World')))));
	    $processor = new Implode($expression, $operands);
	    $result = $processor->process();
	    
	    $this->assertInstanceOf('qtism\\common\\datatypes\\String', $result);
	    $this->assertEquals('Hello-World', $result->getValue());
	}
	
	public function createFakeExpression() {
		return $this->createComponentFromXml('
			<customOperator class="qtism.runtime.expressions.operators.custom.Implode">
		        <baseValue baseType="string">-</baseValue>
				<multiple>
		            <baseValue baseType="string">Hello</baseValue>
		            <baseValue baseType="string">World</baseValue>
		        </multiple>
			</customOperator>
		');
	}
}