<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\runtime\rules\RuleProcessorFactory;
use qtism\data\rules\Rule;

class RuleProcessorFactoryTest extends QtiSmTestCase {
	
	public function testCreateProcessor() {
		$rule = $this->createComponentFromXml('
			<setOutcomeValue identifier="outcomeX">
				<baseValue baseType="integer">1337</baseValue>
			</setOutcomeValue>
		');
		
		$factory = new RuleProcessorFactory();
		$processor = $factory->createProcessor($rule);
		$this->assertInstanceOf('qtism\\runtime\\rules\\SetOutcomeValueProcessor', $processor);
		$this->assertEquals('setOutcomeValue', $processor->getRule()->getQtiClassName());
	}
	
	public function testCreateProcessorNoProcessor() {
		$rule = $this->createComponentFromXml('
			<product>
				<baseValue baseType="integer">2</baseValue>
				<baseValue baseType="integer">3</baseValue>
			</product>'
		);
		
		$factory = new RuleProcessorFactory();
		$this->setExpectedException('\\RuntimeException');
		$processor = $factory->createProcessor($rule);
	}
}