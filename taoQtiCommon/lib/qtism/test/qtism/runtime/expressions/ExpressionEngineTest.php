<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\runtime\expressions\ExpressionEngine;

class ExpressionEngineTest extends QtiSmTestCase {
	
	public function testExpressionEngineBaseValue() {
		$expression = $this->createComponentFromXml('<baseValue baseType="duration">P2D</baseValue>');
		$engine = new ExpressionEngine($expression);
		$result = $engine->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Duration', $result);
		$this->assertEquals(2, $result->getDays());
	}
	
	public function testExpressionEngineSum() {
		$expression = $this->createComponentFromXml('
			<sum> <!-- 60 -->
				<product> <!-- 50 -->
					<baseValue baseType="integer">10</baseValue>
					<baseValue baseType="integer">5</baseValue>
				</product>
				<divide> <!-- 10 -->
					<baseValue baseType="integer">50</baseValue>
					<baseValue baseType="integer">5</baseValue>
				</divide>
			</sum>
		');
		
		$engine = new ExpressionEngine($expression);
		$result = $engine->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(60.0, $result->getValue());
	}
}