<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\runtime\expressions\BaseValueProcessor;
use qtism\common\datatypes\Point;

class BaseValueProcessorTest extends QtiSmTestCase {
	
	public function testBaseValue() {
		$baseValue = $this->createComponentFromXml('<baseValue baseType="boolean">true</baseValue>');
		$baseValueProcessor = new BaseValueProcessor($baseValue);
		$this->assertTrue($baseValueProcessor->process()->getValue());
		
		$baseValue = $this->createComponentFromXml('<baseValue baseType="point">150 130</baseValue>');
		$baseValueProcessor->setExpression($baseValue);
		$this->assertTrue($baseValueProcessor->process()->equals(new Point(150, 130)));
	}
}