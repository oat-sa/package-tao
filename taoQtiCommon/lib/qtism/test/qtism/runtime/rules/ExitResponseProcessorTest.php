<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\runtime\rules\ExitResponseProcessor;
use qtism\runtime\rules\RuleProcessingException;

class ExitResponseProcessorTest extends QtiSmTestCase {
	
	public function testExitResponse() {
		$rule = $this->createComponentFromXml('<exitResponse/>');
		$processor = new ExitResponseProcessor($rule);
		
		try {
			$processor->process();
			
			// An exception must always be raised!
			$this->assertTrue(false);
		}
		catch (RuleProcessingException $e) {
			$this->assertEquals(RuleProcessingException::EXIT_RESPONSE, $e->getCode());
		}
	}
	
}