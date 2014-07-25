<?php
use qtism\common\datatypes\Identifier;
use qtism\runtime\tests\TestResultsSubmission;
use qtism\runtime\tests\AssessmentTestSessionState;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\common\State;
use qtism\data\storage\xml\XmlCompactDocument;

require_once(dirname(__FILE__) . '/../../../QtiSmAssessmentTestSessionTestCase.php');
require_once(dirname(__FILE__) . '/mocks/SimpleResultsSubmittableTestSession.php');
require_once(dirname(__FILE__) . '/mocks/SimpleResultsSubmittableTestSessionFactory.php');

class AssessmentTestSessionResultsTest extends QtiSmAssessmentTestSessionTestCase {
    
    public function testTestResultsSubmissionNonLinearOutcomeProcessing() {
        // This test focuses on test results submission at outcome processing time.
        $file = self::samplesDir() . 'custom/runtime/results_linear.xml';
        $doc = new XmlCompactDocument();
        $doc->load($file);
        $factory = new SimpleResultsSubmittableTestSessionFactory();
        $testSession = $factory->createAssessmentTestSession($doc->getDocumentComponent());
        $testSession->setTestResultsSubmission(TestResultsSubmission::OUTCOME_PROCESSING);
        $this->assertEquals($testSession->getState(), AssessmentTestSessionState::INITIAL);
        $testSession->beginTestSession();
        $this->assertEquals($testSession->getState(), AssessmentTestSessionState::INTERACTING);
        
        // Q01 - Failure
        $testSession->beginAttempt();
        $testSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceB')))));
        $this->assertSame(0.0, $testSession['Q01.SCORE']->getValue());
        $testSession->moveNext();
        
        // Q02 - Success
        $testSession->beginAttempt();
        $testSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceB')))));
        $this->assertSame(1.0, $testSession['Q02.SCORE']->getValue());
        $testSession->moveNext();
        
        // Q03 - Success
        $testSession->beginAttempt();
        $testSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceC')))));
        $this->assertSame(1.0, $testSession['Q03.SCORE']->getValue());
        $testSession->moveNext();
        
        $this->assertEquals(AssessmentTestSessionState::CLOSED, $testSession->getState());
        
        // -- Let's test the submitted results.
        $submittedTestResults = $testSession->getSubmittedTestResults();
        $submittedItemResults = $testSession->getSubmittedItemResults();
        
        // Test Item Q01.
        $this->assertSame(0.0, $submittedItemResults['Q01.0.SCORE'][0]->getValue());
        
        // Test Item Q02.
        $this->assertSame(1.0, $submittedItemResults['Q02.0.SCORE'][0]->getValue());
        
        // Test Item Q03.
        $this->assertSame(1.0, $submittedItemResults['Q03.0.SCORE'][0]->getValue());
        
        // Test Results.
        $this->assertSame(0.0, $submittedTestResults['TEST_SCORE'][0]->getValue());
        $this->assertSame(round(0.50000, 3), round($submittedTestResults['TEST_SCORE'][1]->getValue(), 3));
        $this->assertSame(round(0.66666, 3), round($submittedTestResults['TEST_SCORE'][2]->getValue(), 3));
    }
    
    public function testTestResultsSubmissionNonLinearEnd() {
        // This test focuses on test results submission at outcome processing time.
        $file = self::samplesDir() . 'custom/runtime/results_linear.xml';
        $doc = new XmlCompactDocument();
        $doc->load($file);
        $factory = new SimpleResultsSubmittableTestSessionFactory();
        $testSession = $factory->createAssessmentTestSession($doc->getDocumentComponent());
        $testSession->setTestResultsSubmission(TestResultsSubmission::END);
        $this->assertEquals($testSession->getState(), AssessmentTestSessionState::INITIAL);
        $testSession->beginTestSession();
        $this->assertEquals($testSession->getState(), AssessmentTestSessionState::INTERACTING);
    
        // Q01 - Failure
        $testSession->beginAttempt();
        $testSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceB')))));
        $this->assertSame(0.0, $testSession['Q01.SCORE']->getValue());
        $testSession->moveNext();
    
        // Q02 - Success
        $testSession->beginAttempt();
        $testSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceB')))));
        $this->assertSame(1.0, $testSession['Q02.SCORE']->getValue());
        $testSession->moveNext();
    
        // Q03 - Success
        $testSession->beginAttempt();
        $testSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceC')))));
        $this->assertSame(1.0, $testSession['Q03.SCORE']->getValue());
        $testSession->moveNext();
    
        $this->assertEquals(AssessmentTestSessionState::CLOSED, $testSession->getState());
    
        // -- Let's test the submitted results.
        $submittedTestResults = $testSession->getSubmittedTestResults();
        $submittedItemResults = $testSession->getSubmittedItemResults();
    
        // Test Item Q01.
        $this->assertSame(0.0, $submittedItemResults['Q01.0.SCORE'][0]->getValue());
    
        // Test Item Q02.
        $this->assertSame(1.0, $submittedItemResults['Q02.0.SCORE'][0]->getValue());
    
        // Test Item Q03.
        $this->assertSame(1.0, $submittedItemResults['Q03.0.SCORE'][0]->getValue());
    
        // Test Results (submitted once).
        $this->assertSame(round(0.66666, 3), round($submittedTestResults['TEST_SCORE'][0]->getValue(), 3));
        $this->assertEquals(1, count($submittedTestResults['TEST_SCORE']));
    }
}