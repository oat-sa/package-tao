<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\String;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\common\State;
use qtism\runtime\common\RecordContainer;
use qtism\data\expressions\operators\ToleranceMode;
use qtism\runtime\expressions\operators\EqualProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;

class EqualProcessorTest extends QtiSmTestCase {
	
	public function testExact() {
		$expression = $this->createFakeExpression(ToleranceMode::EXACT);
		$operands = new OperandsCollection(array(new Integer(10), new Integer(10)));
		$processor = new EqualProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(0), new Integer(1)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(10), new Float(10.0)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(10), new Float(10.1)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testRelative() {
		// Only one tolerance attribute.
		$expression = $this->createFakeExpression(ToleranceMode::RELATIVE, array(90));
		$operands = new OperandsCollection(array(new Integer(10), new Integer(10)));
		$processor = new EqualProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		// -- lowerBound = 1; upperBound = 19
		$operands = new OperandsCollection(array(new Integer(10), new Integer(19)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(10), new Float(19.1)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(10), new Integer(20)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(10), new Integer(0)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
		
		// -- do not include upper bound.
		$expression = $this->createFakeExpression(ToleranceMode::RELATIVE, array(90), true, false);
		$processor->setExpression($expression);
		
		$operands = new OperandsCollection(array(new Integer(10), new Integer(1)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(10), new Integer(19)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
		
		// do not include lower bound.
		$expression = $this->createFakeExpression(ToleranceMode::RELATIVE, array(90), false, false);
		$processor->setExpression($expression);
		
		$operands = new OperandsCollection(array(new Float(10.0), new Float(0.9999)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testAbsolute() {
		$expression = $this->createFakeExpression(ToleranceMode::ABSOLUTE, array(0.1, 0.2));
		$operands = new OperandsCollection(array(new Integer(10), new Float(9.9)));
		$processor = new EqualProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(10), new Float(10.2)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(10), new Float(9.8)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(10), new Float(10.3)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testWithVariableRef() {
		$expression = $this->createFakeExpression(ToleranceMode::ABSOLUTE, array('t0', 't1'));
		$operands = new OperandsCollection(array(new Integer(10), new Float(9.9)));
		$processor = new EqualProcessor($expression, $operands);
		
		$state = new State();
		$state->setVariable(new OutcomeVariable('t0', Cardinality::SINGLE, BaseType::FLOAT, new Float(0.1)));
		$state->setVariable(new OutcomeVariable('t1', Cardinality::SINGLE, BaseType::FLOAT, new Float(0.1)));
		$processor->setState($state);
		
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(10), new Float(9.8)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertFalse($result->getValue());
		
		// only one t
		$expression = $this->createFakeExpression(ToleranceMode::ABSOLUTE, array('t0'));
		$operands = new OperandsCollection(array(new Integer(10), new Integer(12)));
		$processor = new EqualProcessor($expression, $operands);
		
		$state = new State();
		$state->setVariable(new OutcomeVariable('t0', Cardinality::SINGLE, BaseType::FLOAT, new Float(2.0)));
		$processor->setState($state);
		
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Boolean', $result);
		$this->assertTrue($result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(10), new Integer(13)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertFalse($result->getValue());
	}
	
	public function testNull() {
		$expression = $this->createFakeExpression(ToleranceMode::ABSOLUTE, array(0.1, 0.2));
		$operands = new OperandsCollection(array(new Integer(10), null));
		$processor = new EqualProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testNoVariableRef() {
		$expression = $this->createFakeExpression(ToleranceMode::ABSOLUTE, array('t0'));
		$operands = new OperandsCollection(array(new Integer(10), new Float(9.9)));
		$processor = new EqualProcessor($expression, $operands);
		
		$state = new State();
		$processor->setState($state);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor->process();
	}
	
	public function testNoSecondVariableRef() {
		$expression = $this->createFakeExpression(ToleranceMode::ABSOLUTE, array('t0', 't1'));
		$operands = new OperandsCollection(array(new Integer(10), new Float(9.9)));
		$processor = new EqualProcessor($expression, $operands);
		
		$state = new State();
		$state->setVariable(new OutcomeVariable('t0', Cardinality::SINGLE, BaseType::FLOAT, new Float(0.1)));
		$processor->setState($state);
		$result = $processor->process();
		$this->assertTrue($result->getValue());
		
		$operands = new OperandsCollection(array(new Integer(10), new Float(9.8)));
		$processor->setOperands($operands);
		$result = $processor->process();
		$this->assertFalse($result->getValue());
	}
	
	public function testWrongBaseType() {
		$expression = $this->createFakeExpression(ToleranceMode::ABSOLUTE, array(0.1, 0.2));
		$operands = new OperandsCollection(array(new Integer(10), new String('String!')));
		$processor = new EqualProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongCardinality() {
		$expression = $this->createFakeExpression(ToleranceMode::ABSOLUTE, array(0.1, 0.2));
		$operands = new OperandsCollection(array(new RecordContainer(array('A' => new Integer(1))), new Integer(10)));
		$processor = new EqualProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression(ToleranceMode::ABSOLUTE, array(0.1, 0.2));
		$operands = new OperandsCollection(array(new Integer(10)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new EqualProcessor($expression, $operands);
	}
	
	public function testTooMuchOperands() {
		$expression = $this->createFakeExpression(ToleranceMode::ABSOLUTE, array(0.1, 0.2));
		$operands = new OperandsCollection(array(new Integer(10), new Integer(10), new Integer(10)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new EqualProcessor($expression, $operands);
	}
	
	public function createFakeExpression($toleranceMode, array $tolerance = array(), $includeLowerBound = true, $includeUpperBound = true) {
		
		$tm =  ($toleranceMode != ToleranceMode::EXACT) ? ('tolerance="' . implode(' ', $tolerance) . '"') : '';
		$toleranceMode = ToleranceMode::getNameByConstant($toleranceMode);
		$iL = ($includeLowerBound === true) ? 'true' : 'false';
		$iU = ($includeUpperBound === true) ? 'true' : 'false';
		
		$str = '
			<equal toleranceMode="' . $toleranceMode . '" ' . $tm . ' includeLowerBound="' . $iL . '" includeUpperBound="' . $iU . '">
				<baseValue baseType="integer">10</baseValue>
				<baseValue baseType="integer">10</baseValue>
			</equal>
		';
		
		return $this->createComponentFromXml($str);
	}
}