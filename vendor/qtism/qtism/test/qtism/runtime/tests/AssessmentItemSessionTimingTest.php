<?php

use qtism\common\datatypes\Float;
use qtism\common\datatypes\Identifier;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\State;
use qtism\runtime\common\ResponseVariable;
use qtism\common\datatypes\Duration;
use qtism\runtime\tests\AssessmentItemSessionState;
use qtism\runtime\tests\AssessmentItemSessionException;
use qtism\data\TimeLimits;
use qtism\data\ItemSessionControl;

require_once (dirname(__FILE__) . '/../../../QtiSmAssessmentItemTestCase.php');

class AssessmentItemSessionTimingTest extends QtiSmAssessmentItemTestCase {
    
    public function testEvolutionBasicTimeLimitsUnderflowOverflow() {
        $itemSession = self::instantiateBasicAssessmentItemSession();
    
        // Give more than one attempt.
        $itemSessionControl = new ItemSessionControl();
        $itemSessionControl->setMaxAttempts(2);
        $itemSession->setItemSessionControl($itemSessionControl);
    
        // No late submission allowed.
        $timeLimits = new TimeLimits(new Duration('PT1S'), new Duration('PT2S'));
        $itemSession->setTimeLimits($timeLimits);
        $itemSession->beginItemSession();
    
        // End the attempt before minTime of 1 second.
        $this->assertEquals(2, $itemSession->getRemainingAttempts());
        $itemSession->beginAttempt();
        $this->assertEquals(1, $itemSession->getRemainingAttempts());

        sleep(1);

        try {
            $itemSession->endAttempt();
            // An exception MUST be thrown.
            $this->assertTrue(false);
        }
        catch (AssessmentItemSessionException $e) {
            $this->assertEquals(AssessmentItemSessionException::DURATION_UNDERFLOW, $e->getCode());
        }
    
        // Check that numAttempts is taken into account &
        // that the session is correctly suspended, waiting for
        // the next attempt.
        $this->assertEquals(1, $itemSession['numAttempts']->getValue());
        $this->assertEquals(AssessmentItemSessionState::SUSPENDED, $itemSession->getState());
    
        // Try again by waiting too much to respect max time.
        $itemSession->beginAttempt();
        $this->assertEquals(0, $itemSession->getRemainingAttempts());
        sleep(3);
    
        try {
            $itemSession->endAttempt();
            $this->assertTrue(false);
        }
        catch (AssessmentItemSessionException $e) {
            $this->assertEquals(AssessmentItemSessionException::DURATION_OVERFLOW, $e->getCode());
        }
    
        $this->assertEquals(2, $itemSession['numAttempts']->getValue());
        $this->assertEquals(AssessmentItemSessionState::CLOSED, $itemSession->getState());
        $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $itemSession['SCORE']);
        $this->assertEquals(0.0, $itemSession['SCORE']->getValue());
    }
    
    public function testAcceptableLatency() {
        $itemSession = self::instantiateBasicAssessmentItemSession(new Duration('PT1S'));
    
        $itemSessionControl = new ItemSessionControl();
        $itemSessionControl->setMaxAttempts(3);
        $itemSession->setItemSessionControl($itemSessionControl);
    
        $timeLimits = new TimeLimits(new Duration('PT1S'), new Duration('PT2S'));
        $itemSession->setTimeLimits($timeLimits);
    
        $itemSession->beginItemSession();
    
        // Sleep 3 second to respect minTime and stay in the acceptable latency time.
        $itemSession->beginAttempt();
        sleep(3);
        $itemSession->endAttempt();
    
        // Sleep 1 more second to achieve the attempt outside the time frame.
        $itemSession->beginAttempt();
        sleep(1);
    
        try {
            $itemSession->endAttempt();
            $this->assertTrue(false);
        }
        catch (AssessmentItemSessionException $e) {
            $this->assertEquals(AssessmentItemSessionException::DURATION_OVERFLOW, $e->getCode());
            $this->assertEquals('PT4S', $itemSession['duration']->round()->__toString());
            $this->assertEquals(AssessmentItemSessionState::CLOSED, $itemSession->getState());
            $this->assertEquals(0, $itemSession->getRemainingAttempts());
        }
    }
    
    public function testEvolutionBasicMultipleAttempts() {
    
        $count = 5;
        $attempts = array(new Identifier('ChoiceA'), new Identifier('ChoiceB'), new Identifier('ChoiceC'), new Identifier('ChoiceD'), new Identifier('ChoiceE'));
        $expected = array(new Float(0.0), new Float(1.0), new Float(0.0), new Float(0.0), new Float(0.0));
    
        $itemSession = self::instantiateBasicAssessmentItemSession();
        $itemSessionControl = new ItemSessionControl();
        $itemSessionControl->setMaxAttempts($count);
        $itemSession->setItemSessionControl($itemSessionControl);
        $itemSession->beginItemSession();
    
        for ($i = 0; $i < $count; $i++) {
            // Here, manual set up of responses.
            $this->assertTrue($itemSession->isAttemptable());
            $itemSession->beginAttempt();
    
            // simulate some time... 1 second to answer the item.
            sleep(1);
    
            $itemSession['RESPONSE'] = $attempts[$i];
            $itemSession->endAttempt();
            $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $itemSession['SCORE']);
            $this->assertTrue($expected[$i]->equals($itemSession['SCORE']));
            $this->assertEquals($i + 1, $itemSession['numAttempts']->getValue());
    
            // 1 more second before the next attempt.
            // we are here in suspended mode so it will not be
            // added to the duration.
            sleep(1);
        }
    
        // The total duration should have taken 5 seconds, the rest of the time was in SUSPENDED state.
        $this->assertEquals(5, $itemSession['duration']->round()->getSeconds(true));
    
        // one more and we get an expection... :)
        try {
            $this->assertFalse($itemSession->isAttemptable());
            $itemSession->beginAttempt();
            $this->assertTrue(false);
        }
        catch (AssessmentItemSessionException $e) {
            $this->assertEquals(AssessmentItemSessionException::STATE_VIOLATION, $e->getCode());
        }
    }
    
    public function testAllowLateSubmissionNonAdaptive() {
        $itemSession = self::instantiateBasicAssessmentItemSession();
    
        $timeLimits = new TimeLimits(null, new Duration('PT1S'), true);
        $itemSession->setTimeLimits($timeLimits);
    
        $itemSession->beginItemSession();
    
        $itemSession->beginAttempt();
        $itemSession['RESPONSE'] = new Identifier('ChoiceB');
        sleep(2);
    
        // No exception because late submission is allowed.
        $itemSession->endAttempt();
        $this->assertEquals(1.0, $itemSession['SCORE']->getValue());
        $this->assertEquals(AssessmentItemSessionState::CLOSED, $itemSession->getState());
    }
    
    public function testDurationBrutalSessionClosing() {
        $itemSession = self::instantiateBasicAssessmentItemSession();
        $itemSession->beginItemSession();
        $this->assertEquals('PT0S', $itemSession['duration']->round()->__toString());
    
        $this->assertTrue($itemSession->isAttemptable());
        $itemSession->beginAttempt();
        sleep(1);
    
        // End session while attempting (brutal x))
        $itemSession->endItemSession();
        $this->assertEquals('PT1S', $itemSession['duration']->round()->__toString());
    }
    
    public function testRemainingTimeOne() {
        $itemSession = self::instantiateBasicAssessmentItemSession();
        $this->assertFalse($itemSession->getRemainingTime());
        $timeLimits = new TimeLimits();
        $timeLimits->setMaxTime(new Duration('PT3S'));
        $itemSession->setTimeLimits($timeLimits);
        $itemSession->beginItemSession();
        $this->assertEquals(1, $itemSession->getRemainingAttempts());
        $this->assertTrue($itemSession->getRemainingTime()->equals(new Duration('PT3S')));
    
        $itemSession->beginAttempt();
        sleep(2);
        $itemSession->updateDuration();
        $this->assertTrue($itemSession->getRemainingTime()->round()->equals(new Duration('PT1S')));
        sleep(1);
        $itemSession->updateDuration();
        $this->assertTrue($itemSession->getRemainingTime()->round()->equals(new Duration('PT0S')));
        sleep(1);
        $itemSession->updateDuration();

        // It is still 0...
        $this->assertTrue($itemSession->getRemainingTime()->equals(new Duration('PT0S')));
    
        try {
            $itemSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceB')))));
            // Must be rejected, no more time remaining!!!
            $this->assertFalse(true);
        }
        catch (AssessmentItemSessionException $e) {
            $this->assertEquals(AssessmentItemSessionException::DURATION_OVERFLOW, $e->getCode());
            $this->assertTrue($itemSession->getRemainingTime()->equals(new Duration('PT0S')));
        }
    }
    
    public function testRemainingTimeTwo() {
        // by default, there is no max time limit.
        $itemSession = self::instantiateBasicAdaptiveAssessmentItem();
        $this->assertFalse($itemSession->getRemainingTime());
         
        $itemSession->beginItemSession();
        $itemSession->beginAttempt();
        $this->assertFalse($itemSession->getRemainingTime());
        $itemSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
        $this->assertEquals('incomplete', $itemSession['completionStatus']->getValue());
         
        $this->assertFalse($itemSession->getRemainingTime());
        $itemSession->beginAttempt();
        $itemSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceB')))));
        $this->assertEquals('completed', $itemSession['completionStatus']->getValue());
        $this->assertFalse($itemSession->getRemainingTime());
    }
    
    public function testForceLateSubmission() {
        $itemSession = self::instantiateBasicAdaptiveAssessmentItem();
        $timeLimits = new TimeLimits(null, new Duration('PT1S'));
        
        $itemSession->beginItemSession();
        $itemSession->beginAttempt();
        
        // reach max time...
        sleep(2);
        
        $itemSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceB')))), true, true);
        $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $itemSession['SCORE']);
        $this->assertEquals(1, $itemSession['SCORE']->getValue());
    }
}
