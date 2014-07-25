<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\String;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\runtime\common\RecordContainer;
use qtism\common\datatypes\Point;
use qtism\runtime\common\Container;
use qtism\data\expressions\operators\Statistics;
use qtism\runtime\expressions\operators\StatsOperatorProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\expressions\operators\OperatorProcessingException;

class StatsOperatorProcessorTest extends QtiSmTestCase {
	
	/**
	 * @dataProvider meanProvider
	 * 
	 * @param Container $container
	 * @param float|null $expected
	 */
	public function testMean(Container $container = null, $expected) {
		$expression = $this->createFakeExpression(Statistics::MEAN);
		$operands = new OperandsCollection(array($container));
		$processor = new StatsOperatorProcessor($expression, $operands);
		$this->check($expected, $processor->process());
	}
	
	/**
	 * @dataProvider sampleVarianceProvider
	 * 
	 * @param Container $container
	 * @param float|null $expected
	 */
	public function testSampleVariance(Container $container = null, $expected) {
		$expression = $this->createFakeExpression(Statistics::SAMPLE_VARIANCE);
		$operands = new OperandsCollection(array($container));
		$processor = new StatsOperatorProcessor($expression, $operands);
		$this->check($expected, $processor->process());
	}
	
	/**
	 * @dataProvider sampleSDProvider
	 *
	 * @param Container $container
	 * @param float|null $expected
	 */
	public function testSampleSD(Container $container = null, $expected) {
		$expression = $this->createFakeExpression(Statistics::SAMPLE_SD);
		$operands = new OperandsCollection(array($container));
		$processor = new StatsOperatorProcessor($expression, $operands);
		$this->check($expected, $processor->process());
	}
	
	/**
	 * @dataProvider popVarianceProvider
	 *
	 * @param Container $container
	 * @param float|null $expected
	 */
	public function testPopVariance(Container $container = null, $expected) {
		$expression = $this->createFakeExpression(Statistics::POP_VARIANCE);
		$operands = new OperandsCollection(array($container));
		$processor = new StatsOperatorProcessor($expression, $operands);
		$this->check($expected, $processor->process());
	}
	
	/**
	 * @dataProvider popSDProvider
	 *
	 * @param Container $container
	 * @param float|null $expected
	 */
	public function testPopSD(Container $container = null, $expected) {
		$expression = $this->createFakeExpression(Statistics::POP_SD);
		$operands = new OperandsCollection(array($container));
		$processor = new StatsOperatorProcessor($expression, $operands);
		$this->check($expected, $processor->process());
	}
	
	/**
	 * @dataProvider wrongCardinalityProvider
	 * 
	 * @param OperandsCollection $operands
	 */
	public function testWrongCardinality(array $operands) {
		$expression = $this->createFakeExpression(Statistics::MEAN);
		$operands = new OperandsCollection($operands);
		$processor = new StatsOperatorProcessor($expression, $operands);
		
		try {
			$result = $processor->process();
			$this->assertTrue(false); // cannot happen.
		}
		catch (OperatorProcessingException $e) {
			$this->assertTrue(true); // exception thrown, good!
			$this->assertEquals(OperatorProcessingException::WRONG_CARDINALITY, $e->getCode());
		}
	}
	
	/**
	 * @dataProvider wrongBaseTypeProvider
	 *
	 * @param OperandsCollection $operands
	 */
	public function testWrongBaseType(array $operands) {
		$expression = $this->createFakeExpression(Statistics::MEAN);
		$operands = new OperandsCollection($operands);
		$processor = new StatsOperatorProcessor($expression, $operands);
	
		try {
			$result = $processor->process();
			$this->assertTrue(false); // cannot happen.
		}
		catch (OperatorProcessingException $e) {
			$this->assertTrue(true); // exception thrown, good!
			$this->assertEquals(OperatorProcessingException::WRONG_BASETYPE, $e->getCode());
		}
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression(Statistics::MEAN);
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\operators\\OperatorProcessingException');
		$processor = new StatsOperatorProcessor($expression, $operands);
	}
	
	public function testTooMuchOperands() {
		$expression = $this->createFakeExpression(Statistics::MEAN);
		$operands = new OperandsCollection(array(new OrderedContainer(BaseType::INTEGER, array(new Integer(10))), new MultipleContainer(BaseType::FLOAT, array(new Float(10.0)))));
		$this->setExpectedException('qtism\\runtime\\expressions\\operators\\OperatorProcessingException');
		$processor = new StatsOperatorProcessor($expression, $operands);
	}
	
	protected function check($expected, $value) {
		if (is_null($expected)) {
			$this->assertTrue($value === null);
		}
		else {
			$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $value);
			$this->assertSame(round($expected, 3), round($value->getValue(), 3));
		}
	}
	
	public function meanProvider() {
		return array(
			array(new OrderedContainer(BaseType::FLOAT, array(new Float(10.0), new Float(20.0), new Float(30.0))), 20.0),
			array(new MultipleContainer(BaseType::INTEGER, array(new Integer(0))), 0.0),
			array(new MultipleContainer(BaseType::FLOAT, array(new Float(10.0), null, new Float(23.3))), null), // contains a null value
			array(null, null)
		);
	}
	
	public function sampleVarianceProvider() {
		return array(
			array(new OrderedContainer(BaseType::FLOAT, array(new Float(10.0))), null), // fails because containerSize <= 1
			array(new MultipleContainer(BaseType::INTEGER, array(new Integer(600), new Integer(470), new Integer(170), new Integer(430), new Integer(300))), 27130),
			array(new MultipleContainer(BaseType::FLOAT, array(new Float(10.0), null, new Float(23.3))), null), // contains a null value
			array(null, null)
		);
	}
	
	public function sampleSDProvider() {
		return array(
			array(new OrderedContainer(BaseType::INTEGER, array(new Integer(10))), null), // containerSize <= 1
			array(new OrderedContainer(BaseType::INTEGER, array(new Integer(600), new Integer(470), new Integer(170), new Integer(430), new Integer(300))), 164.712),
			array(new MultipleContainer(BaseType::FLOAT, array(new Float(10.0), null, new Float(23.3))), null), // contains a null value
			array(null, null)
		);
	}
	
	public function popVarianceProvider() {
		return array(
			array(new OrderedContainer(BaseType::INTEGER, array(new Integer(10))), 0), // containerSize <= 1 but applied on a population -> OK.
			array(new MultipleContainer(BaseType::INTEGER, array(new Integer(600), new Integer(470), new Integer(170), new Integer(430), new Integer(300))), 21704),
			array(new MultipleContainer(BaseType::FLOAT, array(new Float(10.0), null, new Float(23.33333))), null), // contains a null value
		);
	}
	
	public function popSDProvider() {
		return array(
			array(new OrderedContainer(BaseType::INTEGER, array(new Integer(10))), 0), // containerSize <= 1 but applied on population
			array(new OrderedContainer(BaseType::FLOAT, array(new Float(600.0), new Float(470.0), new Float(170.0), new Float(430.0), new Float(300.0))), 147.323),
			array(new MultipleContainer(BaseType::FLOAT, array(new Float(10.0), null, new Float(23.33333))), null), // contains a null value
		);
	}
	
	public function wrongCardinalityProvider() {
		return array(
			array(array(new Float(25.3))),
			array(array(new Integer(-10))),
			array(array(new RecordContainer(array('A' => new Integer(1))))),
		);
	}
	
	public function wrongBaseTypeProvider() {
		return array(
			array(array(new MultipleContainer(BaseType::POINT, array(new Point(1, 2))))),
			array(array(new OrderedContainer(BaseType::STRING, array(new String('String!')))))		
		);
	}
	
	public function createFakeExpression($name) {
		
		$name = Statistics::getNameByConstant($name);
		
		return $this->createComponentFromXml('
			<statsOperator name="' . $name . '">
				<multiple>
					<baseValue baseType="integer">10</baseValue>
					<baseValue baseType="integer">20</baseValue>
					<baseValue baseType="integer">30</baseValue>
				</multiple>
			</statsOperator>
		');
	}
}