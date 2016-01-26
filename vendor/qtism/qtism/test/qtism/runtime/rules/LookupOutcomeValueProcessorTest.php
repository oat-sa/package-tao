<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\common\datatypes\Pair;
use qtism\runtime\common\State;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\rules\LookupOutcomeValueProcessor;

class LookupOutcomeValueProcessorTest extends QtiSmTestCase {
	
	public function testLookupOutcomeValueSimpleMatchTable() {
		$rule = $this->createComponentFromXml('
			<lookupOutcomeValue identifier="outcome1">
				<baseValue baseType="integer">2</baseValue>
			</lookupOutcomeValue>
		');
		
		$declaration = $this->createComponentFromXml('
			<outcomeDeclaration identifier="outcome1" cardinality="single" baseType="pair">
				<matchTable defaultValue="Y Z">
					<matchTableEntry sourceValue="1" targetValue="A B"/>
					<matchTableEntry sourceValue="2" targetValue="C D"/>
					<matchTableEntry sourceValue="3" targetValue="E F"/>
				</matchTable>
			</outcomeDeclaration>
		');
		
		$outcome = OutcomeVariable::createFromDataModel($declaration);
		
		$processor = new LookupOutcomeValueProcessor($rule);
		$state = new State(array($outcome));
		$processor->setState($state);
		
		$this->assertSame(null, $state['outcome1']);
		$processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Pair', $state['outcome1']);
		$this->assertTrue($state['outcome1']->equals(new Pair('C', 'D')));
		
		// Try to get the default value.
		$expr = $rule->getExpression();
		$expr->setValue(5);
		$processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Pair', $state['outcome1']);
		$this->assertTrue($state['outcome1']->equals(new Pair('Y', 'Z')));
	}
	
	public function testLookupOutcomeValueSimpleInterpolationTable() {
		$rule = $this->createComponentFromXml('
			<lookupOutcomeValue identifier="outcome1">
				<baseValue baseType="float">2.0</baseValue>
			</lookupOutcomeValue>
		');
		
		$declaration = $this->createComponentFromXml('
			<outcomeDeclaration identifier="outcome1" cardinality="single" baseType="string">
				<interpolationTable defaultValue="What\'s going on?">
					<interpolationTableEntry sourceValue="1.0" includeBoundary="true" targetValue="Come get some!"/>
					<interpolationTableEntry sourceValue="2.0" includeBoundary="false" targetValue="Piece of cake!"/>
					<interpolationTableEntry sourceValue="3.0" includeBoundary="true" targetValue="Awesome!"/>
				</interpolationTable>
			</outcomeDeclaration>
		');
		
		$outcome = OutcomeVariable::createFromDataModel($declaration);
		$state = new State(array($outcome));
		$processor = new LookupOutcomeValueProcessor($rule);
		$processor->setState($state);
		
		$this->assertSame(null, $state['outcome1']);
		$processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\String', $state['outcome1']);
		$this->assertEquals('Awesome!', $state['outcome1']->getValue());
		
		// include the boundary for interpolationTableEntry[1]
		$table = $outcome->getLookupTable();
		$entries = $table->getInterpolationTableEntries();
		$entries[1]->setIncludeBoundary(true);
		
		$processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\String', $state['outcome1']);
		$this->assertEquals('Piece of cake!', $state['outcome1']->getValue());
		
		// get the default value.
		$expr = $rule->getExpression();
		$expr->setValue(4.0);
		$processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\String', $state['outcome1']);
		$this->assertEquals("What's going on?", $state['outcome1']->getValue());
	}
}