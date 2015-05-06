<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\common\datatypes\Float;
use qtism\common\datatypes\Identifier;
use qtism\runtime\common\ResponseVariable;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\common\State;
use qtism\runtime\rules\ResponseConditionProcessor;
use qtism\runtime\rules\RuleProcessingException;

class ResponseConditionProcessorTest extends QtiSmTestCase {
	
	/**
	 * @dataProvider responseConditionMatchCorrectProvider
	 * 
	 * @param string $response A QTI Identifier
	 * @param float $expectedScore The expected score for a given $response
	 */
	public function testResponseConditionMatchCorrect($response, $expectedScore) {
		
		$rule = $this->createComponentFromXml('
			<responseCondition>
				<responseIf>
					<match>
						<variable identifier="RESPONSE"/>
						<correct identifier="RESPONSE"/>
					</match>
					<setOutcomeValue identifier="SCORE">
						<baseValue baseType="float">1</baseValue>
						</setOutcomeValue>
				</responseIf>
				<responseElse>
					<setOutcomeValue identifier="SCORE">
						<baseValue baseType="float">0</baseValue>
					</setOutcomeValue>
				</responseElse>
			</responseCondition>
		');
		
		$responseVarDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="RESPONSE" cardinality="single" baseType="identifier">
				<correctResponse>
					<value>ChoiceA</value>
				</correctResponse>
			</responseDeclaration>
		');
		$responseVar = ResponseVariable::createFromDataModel($responseVarDeclaration);
		$this->assertTrue($responseVar->getCorrectResponse()->equals(new Identifier('ChoiceA')));
		
		// Set 'ChoiceA' to 'RESPONSE' in order to get a score of 1.0.
		$responseVar->setValue($response);
		
		$outcomeVarDeclaration = $this->createComponentFromXml('
			<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float">
				<defaultValue>
					<value>0</value>
				</defaultValue>
			</outcomeDeclaration>		
		');
		$outcomeVar = OutcomeVariable::createFromDataModel($outcomeVarDeclaration);
		$this->assertEquals(0, $outcomeVar->getDefaultValue()->getValue());
		
		$state = new State(array($responseVar, $outcomeVar));
		$processor = new ResponseConditionProcessor($rule);
		$processor->setState($state);
		$processor->process();
		
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $state['SCORE']);
		$this->assertTrue($expectedScore->equals($state['SCORE']));
	}
	
	public function responseConditionMatchCorrectProvider() {
		return array(
			array(new Identifier('ChoiceA'), new Float(1.0)),
			array(new Identifier('ChoiceB'), new Float(0.0)),
			array(new Identifier('ChoiceC'), new Float(0.0)),
			array(new Identifier('ChoiceD'), new Float(0.0))
		);
	}
}