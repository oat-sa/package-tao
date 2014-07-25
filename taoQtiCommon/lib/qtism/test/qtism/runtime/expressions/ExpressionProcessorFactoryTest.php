<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\runtime\expressions\ExpressionProcessorFactory;
use qtism\data\expressions\Expression;

class ExpressionProcessorFactoryTest extends QtiSmTestCase {
	
	public function testCreateProcessor() {
		$expression = $this->createComponentFromXml('<baseValue baseType="integer">1337</baseValue>');
		
		$factory = new ExpressionProcessorFactory();
		$processor = $factory->createProcessor($expression);
		$this->assertInstanceOf('qtism\\runtime\\expressions\\BaseValueProcessor', $processor);
		$this->assertEquals('baseValue', $processor->getExpression()->getQtiClassName());
	}
	
	public function testCreateProcessorNoProcessor() {
		$expression = $this->createComponentFromXml('
			<sum>
				<baseValue baseType="integer">1</baseValue>
				<baseValue baseType="integer">1</baseValue>
			</sum>'
		);
		
		$factory = new ExpressionProcessorFactory();
		$this->setExpectedException('\\RuntimeException');
		$processor = $factory->createProcessor($expression);
	}
}