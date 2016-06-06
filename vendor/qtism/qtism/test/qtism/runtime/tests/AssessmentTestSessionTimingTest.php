<?php
use qtism\common\datatypes\Identifier;
use qtism\data\storage\xml\XmlDocument;
use qtism\runtime\tests\AssessmentTestPlace;
use qtism\runtime\tests\AssessmentItemSessionException;
use qtism\common\datatypes\Point;
use qtism\runtime\common\State;
use qtism\runtime\tests\AssessmentTestSessionState;
use qtism\runtime\tests\AssessmentTestSessionException;
use qtism\runtime\common\MultipleContainer;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\common\datatypes\Duration;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\runtime\tests\AssessmentTestSessionFactory;
use qtism\data\storage\xml\XmlCompactDocument;

require_once (dirname(__FILE__) . '/../../../QtiSmAssessmentTestSessionTestCase.php');

class AssessmentTestSessionTimingTest extends QtiSmAssessmentTestSessionTestCase {
    
    
    public function testTestPartAssessmentSectionsDurations() {
        $session = self::instantiate(self::samplesDir() . 'custom/runtime/itemsubset.xml');
        // Try to get a duration on a non-begun test session.
        $this->assertSame(null, $session['P01.duration']);
        $this->assertSame(null, $session['S01.duration']);
        $this->assertSame(null, $session['itemsubset.duration']);

        // Try the same on a running test session.
        $session->beginTestSession();
        $this->assertTrue($session['P01.duration']->equals(new Duration('PT0S')));
        $this->assertTrue($session['S01.duration']->equals(new Duration('PT0S')));
        $this->assertTrue($session['itemsubset.duration']->equals(new Duration('PT0S')));
         
        // Q01.
        $session->beginAttempt();
        sleep(1);
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));

        $this->assertTrue($session['P01.duration']->round()->equals(new Duration('PT1S')));
        $this->assertTrue($session['S01.duration']->round()->equals(new Duration('PT1S')));
        $this->assertTrue($session['itemsubset.duration']->round()->equals(new Duration('PT1S')));
        $session->moveNext();
         
        // Q02.
        $session->beginAttempt();
        sleep(1);
        $session->skip();
        $this->assertTrue($session['P01.duration']->round()->equals(new Duration('PT2S')));
        $this->assertTrue($session['S01.duration']->round()->equals(new Duration('PT2S')));
        $this->assertTrue($session['itemsubset.duration']->round()->equals(new Duration('PT2S')));
        $session->moveNext();
         
        // Try to get a duration that does not exist.
        $this->assertSame(null, $session['P02.duration']);
    }
    
    public function testTestPartTimeLimitsLinear() {
        $session = self::instantiate(self::samplesDir() . 'custom/runtime/timelimits_testparts_linear_individual.xml');
        $session->beginTestSession();
         
        // Q01.
        $session->beginAttempt();
        sleep(2);
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
        $session->moveNext();
        $timeConstraints = $session->getTimeConstraints(AssessmentTestPlace::TEST_PART);
        $this->assertTrue($timeConstraints[0]->getMaximumRemainingTime()->round()->equals(new Duration('PT3S')));
         
        // Q02.
        $session->beginAttempt();
        sleep(2);
        $session->updateDuration();
        $timeConstraints = $session->getTimeConstraints(AssessmentTestPlace::TEST_PART);
        $this->assertTrue($timeConstraints[0]->getMaximumRemainingTime()->round()->equals(new Duration('PT1S')));
        $session->skip();
        $session->moveNext();
         
        // Q03.
        $session->beginAttempt();
        sleep(2);
        
        try {
            // P01.duration = 6 > maxTime -> exception !
            $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::MULTIPLE, BaseType::IDENTIFIER, new MultipleContainer(BaseType::IDENTIFIER, array(new Identifier('H'), new Identifier('O')))))));
            $this->assertFalse(true);
        }
        catch (AssessmentTestSessionException $e) {
            $this->assertEquals(AssessmentTestSessionException::TEST_PART_DURATION_OVERFLOW, $e->getCode());
            $session->moveNext();
        }

        // We should have automatically be moved to the next test part.
        $this->assertEquals('P02', $session->getCurrentTestPart() ? $session->getCurrentTestPart()->getIdentifier() : null);
        $this->assertEquals('Q04', $session->getCurrentAssessmentItemRef()->getIdentifier());
        $timeConstraints = $session->getTimeConstraints(AssessmentTestPlace::TEST_PART);
        $this->assertTrue($timeConstraints[0]->getMaximumRemainingTime()->round()->equals(new Duration('PT1S')));
         
        // Q04.
        $session->beginAttempt();
        sleep(2);
         
        try {
            $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::POINT, new Point(102, 113)))));
            $this->assertTrue(false);
        }
        catch (AssessmentTestSessionException $e) {
            $this->assertEquals(AssessmentTestSessionException::TEST_PART_DURATION_OVERFLOW, $e->getCode());
            $session->moveNext();
        }
         
        $this->assertEquals(AssessmentTestSessionState::CLOSED, $session->getState());
        $this->assertFalse($session->getCurrentAssessmentItemRef());
         
        // Ok with outcome processing?
        $this->assertEquals(1, $session['NRESPONSED']->getValue());
    }
    
    /**
     * This test aims at testing if it is possible to force the attempt to be performed
     * event if time constraints in force are exceeded, by the use of the $allowLateSubmission
     * argument.
     * 
     */
    public function testForceLateSubmission($forceLateSubmission = true) {
        $session = self::instantiate(self::samplesDir() . 'custom/runtime/timings/force_late_submission.xml');
        $session->beginTestSession();
        
        // outeach maxTime (1sec)
        $session->beginAttempt();
        sleep(2);
        
        try {
            $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', BaseType::IDENTIFIER, Cardinality::SINGLE, new Identifier('ChoiceA')))), $forceLateSubmission);
            $session->moveNext();
            
            $this->assertTrue($forceLateSubmission, '$forceLateSubmission is false but the attempt dit not raised an exception.');
            $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $session['Q01.SCORE']);
            $this->assertEquals(1.0, $session['Q01.SCORE']->getValue());
            $this->assertFalse($session->isRunning());
            
            // What if $forceLateSubmission = false ? :p
            if ($forceLateSubmission === true) {
                $this->testForceLateSubmission(false);
            }
        }
        catch (AssessmentTestSessionException $e) {
            $this->assertFalse($forceLateSubmission, '$forceLateSubmission is true but the attempt should have been correctly ended.');
            $this->assertEquals(AssessmentTestSessionException::ASSESSMENT_ITEM_DURATION_OVERFLOW, $e->getCode());
        }
    }
    
    /**
     * This test aims at testing that an exception is thrown if we move
     * to a next target item which is timed out.
     * 
     */
    public function testJumpToTargetTimeout($allowTimeout = false) {
        $session = self::instantiate(self::samplesDir() . 'custom/runtime/timings/move_next_target_timeout.xml');
        $session->beginTestSession();
        $this->assertEquals('Q01', $session->getCurrentAssessmentItemRef()->getIdentifier());
        
        // Jump to the target item (the 2nd and last one) to outreach timings.
        $session->jumpTo(1);
        $session->beginAttempt();
        sleep(2);
        $session->moveBack();
        
        // Jump on a timed out item.
        try {
            $session->jumpTo(1, $allowTimeout);
            $this->assertTrue($allowTimeout);
            $this->assertEquals('Q02', $session->getCurrentAssessmentItemRef()->getIdentifier());
        }
        catch (AssessmentTestSessionException $e) {
            $this->assertFalse($allowTimeout);
            $this->assertEquals(AssessmentTestSessionException::ASSESSMENT_ITEM_DURATION_OVERFLOW, $e->getCode());
            
            // We did not move then?
            $this->assertTrue($session->isRunning());
            $this->assertEquals('Q01', $session->getCurrentAssessmentItemRef()->getIdentifier());
        }
    }
    
    public function testTimeConstraints() {
        $session = self::instantiate(self::samplesDir() . 'custom/runtime/timings/remaining_time_1.xml');
        $session->beginTestSession();
        
        $session->beginAttempt();
        $timeConstraints = $session->getTimeConstraints();
        $this->assertEquals(4, count($timeConstraints));
        
        // AssessmentTest level
        $this->assertFalse($timeConstraints[0]->getMaximumRemainingTime());
        $this->assertFalse($timeConstraints[0]->getMinimumRemainingTime());
        $this->assertFalse($timeConstraints[0]->maxTimeInForce());
        $this->assertFalse($timeConstraints[0]->minTimeInForce());
        $this->assertInstanceOf('qtism\\data\\AssessmentTest', $timeConstraints[0]->getSource());
        
        // TestPart level
        $this->assertFalse($timeConstraints[1]->getMaximumRemainingTime());
        $this->assertFalse($timeConstraints[1]->getMinimumRemainingTime());
        $this->assertFalse($timeConstraints[1]->maxTimeInForce());
        $this->assertFalse($timeConstraints[1]->minTimeInForce());
        $this->assertInstanceOf('qtism\\data\\TestPart', $timeConstraints[1]->getSource());
        
        // AssessmentSection level (1st)
        $this->assertFalse($timeConstraints[2]->getMaximumRemainingTime());
        $this->assertFalse($timeConstraints[2]->getMinimumRemainingTime());
        $this->assertFalse($timeConstraints[2]->maxTimeInForce());
        $this->assertFalse($timeConstraints[2]->minTimeInForce());
        $this->assertInstanceOf('qtism\\data\\AssessmentSection', $timeConstraints[2]->getSource());
        
        // AssessmentItem level
        $this->assertEquals('PT1S', $timeConstraints[3]->getMinimumRemainingTime()->round()->__toString());
        $this->assertEquals('PT3S', $timeConstraints[3]->getMaximumRemainingTime()->round()->__toString());
        $this->assertTrue($timeConstraints[3]->maxTimeInForce());
        $this->assertTrue($timeConstraints[3]->minTimeInForce());
        $this->assertInstanceOf('qtism\\data\\AssessmentItemRef', $timeConstraints[3]->getSource());
        
        sleep(2);
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
        $timeConstraints = $session->getTimeConstraints(AssessmentTestPlace::ASSESSMENT_ITEM);
        $this->assertEquals(1, count($timeConstraints));
        $this->assertEquals('PT0S', $timeConstraints[0]->getMinimumRemainingTime()->round()->__toString());
        $this->assertEquals('PT1S', $timeConstraints[0]->getMaximumRemainingTime()->round()->__toString());
        $this->assertTrue($timeConstraints[0]->minTimeInForce());
        $this->assertTrue($timeConstraints[0]->maxTimeInForce());
        $session->moveNext();
        
        $session->beginAttempt();
        sleep(3);
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceB')))));
        $timeConstraints = $session->getTimeConstraints();
        $this->assertFalse($timeConstraints[3]->getMinimumRemainingTime());
        $this->assertEquals('PT0S', $timeConstraints[3]->getMaximumRemainingTime()->__toString());
        $this->assertFalse($timeConstraints[3]->minTimeInForce());
        $this->assertTrue($timeConstraints[3]->maxTimeInForce());
        
        $session->moveNext();
        $session->beginAttempt();
        $timeConstraints = $session->getTimeConstraints(AssessmentTestPlace::ASSESSMENT_ITEM);
        $this->assertFalse($timeConstraints[0]->getMinimumRemainingTime());
        $this->assertFalse($timeConstraints[0]->getMaximumRemainingTime());
        $this->assertTrue($timeConstraints[0]->allowLateSubmission());
        $this->assertFalse($timeConstraints[0]->minTimeInForce());
        $this->assertFalse($timeConstraints[0]->maxTimeInForce());
    }
    
    public function testTimeConstraintsConsiderMinTime() {
        $session = self::instantiate(self::samplesDir() . 'custom/runtime/timings/dont_consider_mintime.xml', false);
        $session->beginTestSession();
        
        // Q01 - timeLimits on assessmentItemRef - minTime = 1, maxTime = 3
        try {
            $session->beginAttempt();
            $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
            
            // No exception should be thrown even if minTime = 1. Indeed, min time
            // are not to be considered.
            $this->assertTrue(true);
        }
        catch (AssessmentTestSessionException $e) {
            $this->fail("No exception should be thrown because minTime must not be considered on Q01.");
        }
        
        // On the other hand, if we go back to min time consideration...
        unset($session);
        $session = self::instantiate(self::samplesDir() . 'custom/runtime/timings/dont_consider_mintime.xml', true);
        $session->beginTestSession();
        try {
            // Minimum time not respected...
            $session->beginAttempt();
            $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceB')))));
            $this->fail("An exception should be thrown because minTime must be considered now on Q01.");
        }
        catch (AssessmentTestSessionException $e) {
            $this->assertEquals(AssessmentTestSessionException::ASSESSMENT_ITEM_DURATION_UNDERFLOW, $e->getCode(), "The thrown exception should have code ASSESSMENT_ITEM_DURATION_UNDERFLOW, exception message is: " . $e->getMessage());
        }
    }
    
    public function testDurationBetweenItems() {
        /*
         * This test aims at testing that the duration
         * of the whole test is not incremented while a
         * candidate is between 2 items, and then, not
         * interacting.
         */
        $session = self::instantiate(self::samplesDir() . 'custom/runtime/timings/between_items.xml');
        $session->beginTestSession();
        
        // In this situation, the duration increases.
        $session->beginAttempt();
        sleep(1);
        $this->assertEquals(1, $session['Q01.duration']->getSeconds(true));
        $this->assertEquals(1, $session['S01.duration']->getSeconds(true));
        $this->assertEquals(1, $session['TP01.duration']->getSeconds(true));
        $this->assertEquals(1, $session['duration']->getSeconds(true));
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
        
        // We are now between Q01 and Q02, the duration must not increase.
        sleep(1);
        $this->assertEquals(1, $session['Q01.duration']->getSeconds(true));
        $this->assertEquals(1, $session['S01.duration']->getSeconds(true));
        $this->assertEquals(1, $session['TP01.duration']->getSeconds(true));
        $this->assertEquals(1, $session['duration']->getSeconds(true));
    }
    
    public function testMultipleOccurences() {
        /*
         * This test aims at testing how duration behaves
         * when multiple occurences of the same item are involved in
         * the test.
         */
        $session = self::instantiate(self::samplesDir() . 'custom/runtime/timings/multiple_occurences.xml');
        $session->beginTestSession();
        
        $session->beginAttempt();
        sleep(1);
        $session->skip();
        $session->moveNext();
        
        $session->beginAttempt();
        sleep(2);
        $session->skip();
        $session->moveNext();
        
        $session->beginAttempt();
        $session->skip();
        $session->moveNext();
        
        $this->assertEquals(1, $session['Q01.1.duration']->getSeconds(true));
        $this->assertEquals(2, $session['Q01.2.duration']->getSeconds(true));
        $this->assertEquals(0, $session['Q01.3.duration']->getSeconds(true));
    }
    
    public function testSuspendResume() {
        $assessmentTestSession = self::instantiate(self::samplesDir() . 'custom/runtime/scenario_basic_nonadaptive_linear_singlesection.xml');
        $assessmentTestSession->beginTestSession();
        
        // Q01.
        $this->assertEquals('Q01', $assessmentTestSession->getCurrentAssessmentItemRef()->getIdentifier());
        $assessmentTestSession->beginAttempt();
        $assessmentTestSession->suspend();
        
        sleep(2);
        $this->assertEquals(AssessmentTestSessionState::SUSPENDED, $assessmentTestSession->getState());
        
        $assessmentTestSession->resume();
        
        // Even if we slept two seconds, no additional time taken into account because the test was suspended.
        $this->assertEquals(0, $assessmentTestSession['Q01.duration']->getSeconds(true));
        $this->assertEquals(AssessmentTestSessionState::INTERACTING, $assessmentTestSession->getState());
        
        $assessmentTestSession->endAttempt(new State(array()));
        $assessmentTestSession->moveNext();
        
        // Q02.
        $this->assertEquals('Q02', $assessmentTestSession->getCurrentAssessmentItemRef()->getIdentifier());
    }
    
    public function testLastItemTimeout() {
        $session = self::instantiate(self::samplesDir() . 'custom/runtime/timings/last_item_timeout.xml');
        $session->beginTestSession();
        
        $session->beginAttempt();
        sleep(2);
        $session->moveNext();
        $this->assertEquals(AssessmentTestSessionState::CLOSED, $session->getState());
    }
    
    public function testLastItemSectionTimeout() {
        $session = self::instantiate(self::samplesDir() . 'custom/runtime/timings/last_item_section_timeout.xml');
        $session->beginTestSession();
        
        $session->beginAttempt();
        sleep(2);
        $session->moveNextAssessmentSection();
        $this->assertEquals(AssessmentTestSessionState::CLOSED, $session->getState());
    }
    
    public function testLastItemTestPartTimeout() {
        $session = self::instantiate(self::samplesDir() . 'custom/runtime/timings/last_item_testpart_timeout.xml');
        $session->beginTestSession();
        
        $session->beginAttempt();
        sleep(2);
        $session->moveNextTestPart();
        $this->assertEquals(AssessmentTestSessionState::CLOSED, $session->getState());
    }
 }
