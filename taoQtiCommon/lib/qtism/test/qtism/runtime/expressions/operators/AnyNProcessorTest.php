<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\common\datatypes\String;
use qtism\runtime\common\State;
use qtism\common\enums\Cardinality;
use qtism\common\datatypes\Boolean;
use qtism\runtime\common\OutcomeVariable;
use qtism\common\datatypes\Point;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\expressions\operators\AnyNProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;

class AnyNProcessorTest extends QtiSmTestCase {
	
	/**
	 * 
	 * @dataProvider anyNProvider
	 * 
	 * @param integer $min
	 * @param integer $max
	 * @param array $booleans
	 * @param boolean $expected
	 */
	public function testAnyN($min, $max, array $booleans, $expected) {
		$expression = $this->createFakeExpression($min, $max);
		$operands = new OperandsCollection($booleans);
		$processor = new AnyNProcessor($expression, $operands);
		$result = $processor->process();
		
		if ($result === null) {
		    $this->assertSame($expected, $result);
		}
		else {
		    $this->assertSame($expected, $result->getValue());
		}
		
	}
	
	public function testWrongCardinality() {
		$expression = $this->createFakeExpression(2, 3);
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::INTEGER)));
		$processor = new AnyNProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongBaseTypeOne() {
		$expression = $this->createFakeExpression(2, 3);
		$operands = new OperandsCollection(array(new String('String')));
		$processor = new AnyNProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongBaseTypeTwo() {
		$expression = $this->createFakeExpression(2, 3);
		$operands = new OperandsCollection(array(new Point(1, 2)));
		$processor = new AnyNProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression(2, 3);
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new AnyNProcessor($expression, $operands);
	}
	
	public function testWithMinFromVariableReference() {
		$expression = $this->createFakeExpression('var1', 4);
		$var1 = new OutcomeVariable('var1', Cardinality::SINGLE, BaseType::INTEGER, new Integer(3));
		$operands = new OperandsCollection(array(new Boolean(true), new Boolean(false), new Boolean(false), null));
		$state = new State();
		$state->setVariable($var1);
		$processor = new AnyNProcessor($expression, $operands);
		$processor->setState($state);
		$result = $processor->process();
		$this->assertSame(false, $result->getValue());
	}
	
	public function testWithMaxFromVariableReference() {
		$expression = $this->createFakeExpression(3, 'var1');
		$var1 = new OutcomeVariable('var1', Cardinality::SINGLE, BaseType::INTEGER, new Integer(4));
		$operands = new OperandsCollection(array(new Boolean(true), new Boolean(true), new Boolean(true), null));
		$state = new State();
		$state->setVariable($var1);
		$processor = new AnyNProcessor($expression, $operands);
		$processor->setState($state);
		$result = $processor->process();
		$this->assertSame(true, $result->getValue());
	}
	
	public function testMinCannotBeResolved() {
		$expression = $this->createFakeExpression('min', 4);
		$operands = new OperandsCollection(array(new Boolean(true), new Boolean(true), new Boolean(true), null));
		$processor = new AnyNProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testMaxCannotBeResolved() {
		$expression = $this->createFakeExpression(3, 'max');
		$operands = new OperandsCollection(array(new Boolean(true), new Boolean(true), new Boolean(true), null));
		$processor = new AnyNProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testMinReferenceWrongBaseType() {
		$expression = $this->createFakeExpression('min', 4);
		$min = new OutcomeVariable('min', Cardinality::SINGLE, BaseType::FLOAT, new Float(2.3));
		$operands = new OperandsCollection(array(new Boolean(true), new Boolean(true), new Boolean(true), null));
		$state = new State();
		$state->setVariable($min);
		$processor = new AnyNProcessor($expression, $operands);
		$processor->setState($state);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testMaxReferenceWrongBaseType() {
		$expression = $this->createFakeExpression(3, 'max');
		$max = new OutcomeVariable('max', Cardinality::SINGLE, BaseType::FLOAT, new Float(4.5356));
		$operands = new OperandsCollection(array(new Boolean(true), new Boolean(true), new Boolean(true), null));
		$state = new State();
		$state->setVariable($max);
		$processor = new AnyNProcessor($expression, $operands);
		$processor->setState($state);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function createFakeExpression($min, $max) {
		return $this->createComponentFromXml('
			<anyN min="' . $min . '" max="' . $max . '">
				<baseValue baseType="boolean">true</baseValue>
				<baseValue baseType="boolean">true</baseValue>
				<baseValue baseType="boolean">false</baseValue>
			</anyN>
		');
	}
	
	public function anyNProvider() {
		$returnValue = array();
		
		$returnValue[] = array(3, 5, array(new Boolean(true), new Boolean(true), new Boolean(true)), true);
		$returnValue[] = array(3, 5, array(new Boolean(true), new Boolean(true), new Boolean(true), new Boolean(true)), true);
		$returnValue[] = array(3, 5, array(new Boolean(true), new Boolean(true), new Boolean(true), new Boolean(true), new Boolean(true)), true);
		$returnValue[] = array(3, 5, array(new Boolean(true), new Boolean(true), new Boolean(true), new Boolean(true), new Boolean(true), new Boolean(true)), false);
		$returnValue[] = array(3, 5, array(new Boolean(true)), false);
		$returnValue[] = array(3, 5, array(new Boolean(false), new Boolean(true), new Boolean(false), new Boolean(true), new Boolean(true)), true);
		$returnValue[] = array(3, 5, array(new Boolean(true), new Boolean(false), new Boolean(true), new Boolean(false)), false);
		$returnValue[] = array(3, 5, array(new Boolean(false)), false);
		$returnValue[] = array(3, 5, array(new Boolean(false), new Boolean(false), new Boolean(false), null), false);
		$returnValue[] = array(3, 5, array(new Boolean(false), new Boolean(false), null, null), false);
		$returnValue[] = array(3, 5, array(new Boolean(false), new Boolean(false), null, null), false);
		$returnValue[] = array(3, 5, array(new Boolean(false), new Boolean(false), null, null, null), null);
		$returnValue[] = array(3, 5, array(new Boolean(false), new Boolean(false), new Boolean(true), null, new Boolean(true)), null);
		$returnValue[] = array(3, 5, array(null, null, null, null), null);
		$returnValue[] = array(3, 5, array(null), false);
		$returnValue[] = array(0, 0, array(new Boolean(true)), false);
		
		// From IMS Spec
		$returnValue[] = array(3, 4, array(new Boolean(true), new Boolean(true), new Boolean(false), null), null);
		$returnValue[] = array(3, 4, array(new Boolean(true), new Boolean(false), new Boolean(false), null), false);
		$returnValue[] = array(3, 4, array(new Boolean(true), new Boolean(true), new Boolean(true), null), true);
		
		return $returnValue;
	}
}