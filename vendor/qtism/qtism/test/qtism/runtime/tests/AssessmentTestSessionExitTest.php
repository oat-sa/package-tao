<?php

use qtism\common\datatypes\Identifier;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\common\State;
use qtism\runtime\tests\AssessmentItemSessionState;
use qtism\runtime\tests\AssessmentTestSessionState;

require_once (dirname(__FILE__) . '/../../../QtiSmAssessmentTestSessionTestCase.php');

class AssessmentTestSessionExitTest extends QtiSmAssessmentTestSessionTestCase {
    
    public function testExitSection() {
        $url = self::samplesDir() . 'custom/runtime/exits/exitsection.xml';
        $testSession = self::instantiate($url);
        
        $testSession->beginTestSession();
        
        // If we get correct to the first question, we should EXIT_SECTION. We should
        // then be redirected to S02.
        $testSession->beginAttempt();
        $testSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
        
        // We should arrive at section 2.
        $testSession->moveNext();
        $this->assertEquals('S02', $testSession->getCurrentAssessmentSection()->getIdentifier());
    }
    
    public function testExitSectionEndOfTest() {
        $url = self::samplesDir() . 'custom/runtime/exits/exitsectionendoftest.xml';
        $testSession = self::instantiate($url);
        
        $testSession->beginTestSession();
        
        // If we get correct to the first question, we will EXIT_SECTION. We should
        // be then redirected to the end of the test, because S01 is the unique section
        // of the test.
        $testSession->beginAttempt();
        $testSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
        
        // We should be at the end of the test.
        $testSession->moveNext();
        
        $this->assertEquals(AssessmentTestSessionState::CLOSED, $testSession->getState());
        
        // All session closed (parano mode)?
        $itemSessions = $testSession->getAssessmentItemSessions('Q01');
        $q01Session = $itemSessions[0];
        $this->assertEquals(AssessmentItemSessionState::CLOSED, $q01Session->getState());
    }
    
    public function testExitSectionPreconditionsEndOfTest() {
        $url = self::samplesDir() . 'custom/runtime/exits/exitsectionpreconditions.xml';
        $testSession = self::instantiate($url);
        
        $testSession->beginTestSession();
        
        // If we get correct to the first question, we will EXIT_SECTION. We should
        // be then redirected to the end of the test, because Q03 has a precondition
        // which is never satisfied (return always false).
        $testSession->beginAttempt();
        $testSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
        
        // We should be at the end of the test.
        $testSession->moveNext();
        
        $this->assertEquals(AssessmentTestSessionState::CLOSED, $testSession->getState());
        
        // All session closed (parano mode again)?
        $itemSessions = $testSession->getAssessmentItemSessions('Q01');
        $q01Session = $itemSessions[0];
        $this->assertEquals(AssessmentItemSessionState::CLOSED, $q01Session->getState());
    }
    
    public function testExitTestPart() {
        $url = self::samplesDir() . 'custom/runtime/exits/exittestpart.xml';
        $testSession = self::instantiate($url);
    
        $testSession->beginTestSession();
    
        // If we get correct to the first question, we should EXIT_TESTPART. We should
        // then be redirected to P02.
        $testSession->beginAttempt();
        $testSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
    
        // We should arrive at testPart 2
        $testSession->moveNext();
        $this->assertEquals('P02', $testSession->getCurrentTestPart()->getIdentifier());
    }
    
    public function testExitTestPartEndOfTest() {
        $url = self::samplesDir() . 'custom/runtime/exits/exittestpartendoftest.xml';
        $testSession = self::instantiate($url);
    
        $testSession->beginTestSession();
    
        // If we get correct to the first question, we will EXIT_TESTPART. We should
        // be then redirected to the end of the test, because T01 is the unique testPart
        // of the test.
        $testSession->beginAttempt();
        $testSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
    
        // We should be at the end of the test.
        $testSession->moveNext();
    
        $this->assertEquals(AssessmentTestSessionState::CLOSED, $testSession->getState());
    
        // All session closed (parano mode all over again)?
        $itemSessions = $testSession->getAssessmentItemSessions('Q01');
        $q01Session = $itemSessions[0];
        $this->assertEquals(AssessmentItemSessionState::CLOSED, $q01Session->getState());
    }
    
    public function testExitTestPartPreconditionsEndOfTest() {
        $url = self::samplesDir() . 'custom/runtime/exits/exittestpartpreconditions.xml';
        $testSession = self::instantiate($url);
    
        $testSession->beginTestSession();
    
        // If we get correct to the first question, we will EXIT_TESTPART. We should
        // be then redirected to the end of the test, because Q03 has a precondition
        // which is never satisfied (return always false).
        $testSession->beginAttempt();
        $testSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
    
        // We should be at the end of the test.
        $testSession->moveNext();
    
        $this->assertEquals(AssessmentTestSessionState::CLOSED, $testSession->getState());
    
        // All session closed (parano mode again)?
        $itemSessions = $testSession->getAssessmentItemSessions('Q01');
        $q01Session = $itemSessions[0];
        $this->assertEquals(AssessmentItemSessionState::CLOSED, $q01Session->getState());
    }
    
    public function testExitTest() {
        $url = self::samplesDir() . 'custom/runtime/exits/exittest.xml';
        $testSession = self::instantiate($url);
    
        $testSession->beginTestSession();
    
        // If we get correct to the first question, we should EXIT_TEST. We should
        // then be redirected to end of the test.
        $testSession->beginAttempt();
        $testSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
    
        // We should arrive at section 2.
        $testSession->moveNext();
        $this->assertEquals(AssessmentTestSessionState::CLOSED, $testSession->getState());
    }
}
