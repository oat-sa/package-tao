<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\runtime\rules\ExitTestProcessor;
use qtism\runtime\rules\RuleProcessingException;

class ExitTestProcessorTest extends QtiSmTestCase {
	
	public function testExitTest() {
		$rule = $this->createComponentFromXml('<exitTest/>');
		$processor = new ExitTestProcessor($rule);
		
		try {
			$processor->process();
			
			// An exception must always be raised!
			$this->assertTrue(false);
		}
		catch (RuleProcessingException $e) {
			$this->assertEquals(RuleProcessingException::EXIT_TEST, $e->getCode());
		}
	}
	
}