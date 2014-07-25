<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\common\datatypes\Identifier;
use qtism\runtime\rules\RuleProcessingException;
use qtism\runtime\common\ProcessingException;
use qtism\runtime\common\State;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\processing\ResponseProcessingEngine;

class ResponseProcessingEngineTest extends QtiSmTestCase {
	
	public function testResponseProcessingMatchCorrect() {
		$responseProcessing = $this->createComponentFromXml('
			<responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct"/>
		');
		
		$responseDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="RESPONSE" cardinality="single" baseType="identifier">
				<correctResponse>
					<value>ChoiceA</value>
				</correctResponse>
			</responseDeclaration>		
		');
		
		$outcomeDeclaration = $this->createComponentFromXml('
			<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float">
				<defaultValue>
					<value>0</value>
				</defaultValue>
			</outcomeDeclaration>
		');
		
		$respVar = ResponseVariable::createFromDataModel($responseDeclaration);
		$outVar = OutcomeVariable::createFromDataModel($outcomeDeclaration);
		$context = new State(array($respVar, $outVar));
		
		$engine = new ResponseProcessingEngine($responseProcessing, $context);
		
		// --> answer as a correct response.
		$context['RESPONSE'] = new Identifier('ChoiceA');
		$engine->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $context['SCORE']);
		$this->assertEquals(1.0, $context['SCORE']->getValue());
	}
	
	public function testResponseProcessingExitResponse() {
	    $responseProcessing = $this->createComponentFromXml('
	        <responseProcessing>
                <exitResponse/>
	        </responseProcessing>
	    ');
	    
	    $engine = new ResponseProcessingEngine($responseProcessing);
	    
	    try {
	        $engine->process();
	        // An exception MUST be thrown.
	        $this->assertTrue(true);
	    }
	    catch (ProcessingException $e) {
	        $this->assertInstanceOf('qtism\\runtime\\rules\\RuleProcessingException', $e);
	        $this->assertEquals(RuleProcessingException::EXIT_RESPONSE, $e->getCode());
	    }
	}
}