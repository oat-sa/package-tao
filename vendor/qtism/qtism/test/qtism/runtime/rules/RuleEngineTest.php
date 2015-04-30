<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\State;
use qtism\runtime\rules\RuleEngine;
use qtism\runtime\common\OutcomeVariable;

class RuleEngineTest extends QtiSmTestCase {

	public function testSetOutcomeValue() {
		$rule = $this->createComponentFromXml('
			<setOutcomeValue identifier="outcome1">
				<baseValue baseType="integer">1337</baseValue>
			</setOutcomeValue>
		');
		
		$outcome1 = new OutcomeVariable('outcome1', Cardinality::SINGLE, BaseType::INTEGER);
		$context = new State(array($outcome1));
		$engine = new RuleEngine($rule, $context);
		
		$this->assertSame(null, $context['outcome1']);
		
		$engine->process();		
		$this->assertEquals(1337, $context['outcome1']->getValue());
	}
	
	public function testLookupOutcomeValue() {
		$rule = $this->createComponentFromXml('
			<lookupOutcomeValue identifier="outcome1">
				<baseValue baseType="integer">2</baseValue>
			</lookupOutcomeValue>
		');
		
		$outcomeDeclaration = $this->createComponentFromXml('
			<outcomeDeclaration identifier="outcome1" cardinality="single" baseType="string">
				<matchTable>
					<matchTableEntry sourceValue="1" targetValue="String1!"/>
					<matchTableEntry sourceValue="2" targetValue="String2!"/>
				</matchTable>
			</outcomeDeclaration>
		');
		
		$outcomeVariable = OutcomeVariable::createFromDataModel($outcomeDeclaration);
		$context = new State(array($outcomeVariable));
		$engine = new RuleEngine($rule, $context);
		
		$this->assertSame(null, $context['outcome1']);
	
		$engine->process();
		$this->assertEquals('String2!', $context['outcome1']->getValue());
	}
	
	// And it will work for others... x)
}