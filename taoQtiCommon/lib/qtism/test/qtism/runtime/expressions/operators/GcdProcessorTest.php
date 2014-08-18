<?php

use qtism\common\datatypes\Integer;

use qtism\common\datatypes\String;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\runtime\common\RecordContainer;
use qtism\runtime\expressions\operators\GcdProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\runtime\common\OrderedContainer;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;

class GcdProcessorTest extends QtiSmTestCase {
	
	/**
	 * @dataProvider gcdProvider
	 * 
	 * @param array $operands
	 * @param integer $expected
	 */
	public function testGcd(array $operands, $expected) {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection($operands);
		$processor = new GcdProcessor($expression, $operands);
		$this->assertSame($expected, $processor->process()->getValue());
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\operators\\OperatorProcessingException');
		$processor = new GcdProcessor($expression, $operands);
	}
	
	public function testWrongBaseType() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new MultipleContainer(BaseType::STRING, array(new String('String!'))), new Integer(10)));
		$processor = new GcdProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\operators\\OperatorProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongCardinality() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(10), new Integer(20), new RecordContainer(array('A' => new Integer(10))), new Integer(30)));
		$processor = new GcdProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\operators\\OperatorProcessingException');
		$result = $processor->process();
	}
	
	/**
	 * @dataProvider gcdWithNullValuesProvider
	 * 
	 * @param array $operands
	 */
	public function testGcdWithNullValues(array $operands) {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection($operands);
		$processor = new GcdProcessor($expression, $operands);
		$this->assertSame(null, $processor->process());
	}
	
	public function gcdProvider() {
		return array(
			array(array(new Integer(45), new Integer(60), new Integer(330)), 15),
			array(array(new Integer(0), new Integer(45), new Integer(60), new Integer(0), new Integer(330), new Integer(15), new Integer(0)), 15), // gcd (0, 45, 60, 330, 15, 0)
			array(array(new Integer(0)), 0),
			array(array(new Integer(0), new Integer(0), new Integer(0)), 0),
			array(array(new MultipleContainer(BaseType::INTEGER, array(new Integer(45), new Integer(60), new Integer(330)))), 15), // gcd(45, 60, 330)
			array(array(new Integer(0), new OrderedContainer(BaseType::INTEGER, array(new Integer(0)))), 0), // gcd(0, 0, 0)
			array(array(new MultipleContainer(BaseType::INTEGER, array(new Integer(45), new Integer(60), new Integer(0), new Integer(330)))), 15), // gcd(45, 60, 0, 330)
			array(array(new MultipleContainer(BaseType::INTEGER, array(new Integer(45))), new OrderedContainer(BaseType::INTEGER, array(new Integer(60))), new MultipleContainer(BaseType::INTEGER, array(new Integer(330)))), 15),
			array(array(new Integer(45)), 45),
			array(array(new Integer(0), new Integer(45)), 45),
			array(array(new Integer(45), new Integer(0)), 45),
			array(array(new Integer(0), new Integer(45), new Integer(0)), 45)
		);
	}
	
	public function gcdWithNullValuesProvider() {
		return array(
			array(array(new Integer(45), null, new Integer(330))),
			array(array(new String(''), new Integer(550), new Integer(330))),
			array(array(new Integer(230), new OrderedContainer(BaseType::INTEGER), new Integer(25), new Integer(33))),
			array(array(new OrderedContainer(BaseType::INTEGER, array(null)))),
			array(array(new OrderedContainer(BaseType::INTEGER, array(null, null, null)))),
			array(array(new OrderedContainer(BaseType::INTEGER, array(new Integer(25), new Integer(30))), new Integer(200), new MultipleContainer(BaseType::INTEGER, array(new Integer(25), null, new Integer(30)))))
		);
	}
	
	public function createFakeExpression() {
		return $this->createComponentFromXml('
			<gcd>
				<baseValue baseType="integer">40</baseValue>
				<baseValue baseType="integer">60</baseValue>
				<baseValue baseType="integer">330</baseValue>
			</gcd>
		');
	}
}