<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\runtime\expressions\MathConstantProcessor;

class MathConstantProcessorTest extends QtiSmTestCase {
	
	public function testSimple() {
		$mathConstantExpr = $this->createComponentFromXml('<mathConstant name="e"/>');
		$mathConstantProcessor = new MathConstantProcessor($mathConstantExpr);
		
		$result = $mathConstantProcessor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(M_E, $result->getValue());
		
		$mathConstantExpr = $this->createComponentFromXml('<mathConstant name="pi"/>');
		$mathConstantProcessor->setExpression($mathConstantExpr);
		$result = $mathConstantProcessor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(M_PI, $result->getValue());
	}
}