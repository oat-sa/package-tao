<?php

use qtism\runtime\tests\AssessmentItemSession;
use qtism\common\datatypes\Identifier;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\common\State;

require_once (dirname(__FILE__) . '/../../../QtiSmAssessmentTestSessionTestCase.php');

class AssessmentTestSessionAttemptsTest extends QtiSmAssessmentTestSessionTestCase {
	
    public function testMultipleAttempts() {
        $session = self::instantiate(self::samplesDir() . 'custom/runtime/attempts/max_3_attempts_nonlinear.xml');
        $session->beginTestSession();
        
        // Q01 - first attempt.
        $session->beginAttempt();
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
        
        $this->assertEquals(AssessmentItemSession::COMPLETION_STATUS_COMPLETED, $session['Q01.completionStatus']);
        
        // Q02 - second attempt.
        $session->beginAttempt();
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceC')))));
        
        $this->assertEquals(AssessmentItemSession::COMPLETION_STATUS_COMPLETED, $session['Q01.completionStatus']);
        
        // Q03 - third attempt. The completion status is now completed.
        $session->beginAttempt();
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
        
        $this->assertEquals(AssessmentItemSession::COMPLETION_STATUS_COMPLETED, $session['Q01.completionStatus']);
    }
    
}