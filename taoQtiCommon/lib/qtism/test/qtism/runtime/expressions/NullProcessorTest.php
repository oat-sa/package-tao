<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\runtime\expressions\NullProcessor;

class NullProcessorTest extends QtiSmTestCase {
	
	public function testNullProcessor() {
		$nullExpression = $this->createComponentFromXml('<null/>');
		$nullProcessor = new NullProcessor($nullExpression);
		$result = $nullProcessor->process();
		$this->assertTrue($result === null);
	}
}