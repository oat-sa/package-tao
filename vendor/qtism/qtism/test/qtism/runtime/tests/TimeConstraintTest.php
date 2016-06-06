<?php
use qtism\data\NavigationMode;
use qtism\data\TimeLimits;
use qtism\common\datatypes\Duration;
use qtism\runtime\tests\TimeConstraint;
use qtism\data\AssessmentItemRef;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

class TimeConstraintTest extends QtiSmTestCase {
    
    public function testInstantiation() {
        $assessmentItemRef = new AssessmentItemRef('Q01', 'Q01.xml');
        $timeConstraint = new TimeConstraint($assessmentItemRef, new Duration('PT20S'));
        
        $this->assertInstanceOf('qtism\\data\\AssessmentItemRef', $timeConstraint->getSource());
        $this->assertInstanceOf('qtism\\common\\datatypes\\Duration', $timeConstraint->getDuration());
    }
    
    public function testNoConstraints() {
        $assessmentItemRef = new AssessmentItemRef('Q01', 'Q01.xml');
        $timeConstraint = new TimeConstraint($assessmentItemRef, new Duration('PT20S'));
        
        // No timelimits in force.
        $this->assertFalse($timeConstraint->getMaximumRemainingTime());
        $this->assertFalse($timeConstraint->getMinimumRemainingTime());
        $this->assertFalse($timeConstraint->minTimeInForce());
        $this->assertFalse($timeConstraint->maxTimeInForce());
    }
    
    public function testNegativeTime() {
        $assessmentItemRef = new AssessmentItemRef('Q01', 'Q01.xml');
        $timeLimits = new TimeLimits(null, new Duration('PT10S'));
        $assessmentItemRef->setTimeLimits($timeLimits);
        $timeConstraint = new TimeConstraint($assessmentItemRef, new Duration('PT20S'));
        
        $maxRemaining = $timeConstraint->getMaximumRemainingTime();
        $this->assertEquals('PT0S', $maxRemaining->__toString());
        
        $minRemaining = $timeConstraint->getMinimumRemainingTime();
        $this->assertFalse($minRemaining);
    }
    
    public function testDoesAllowLateSubmission() {
        $assessmentItemRef = new AssessmentItemRef('Q01', 'Q01.xml');
        $timeLimits = new TimeLimits(null, new Duration('PT10S'), true);
        $assessmentItemRef->setTimeLimits($timeLimits);
        $timeConstraint = new TimeConstraint($assessmentItemRef, new Duration('PT5S'));
        $this->assertTrue($timeConstraint->allowLateSubmission());
        
        $timeLimits->setAllowLateSubmission(false);
        $this->assertFalse($timeConstraint->allowLateSubmission());
        
        $timeLimits->setMaxTime(null);
        $this->assertTrue($timeConstraint->allowLateSubmission());
        
        $assessmentItemRef->setTimeLimits(null);
        $this->assertTrue($timeConstraint->allowLateSubmission());
    }
    
    public function testRemainingTime() {
        $assessmentItemRef = new AssessmentItemRef('Q01', 'Q01.xml');
        $timeConstraint = new TimeConstraint($assessmentItemRef, new Duration('PT20S'));
        $this->assertFalse($timeConstraint->maxTimeInForce());
        $this->assertFalse($timeConstraint->minTimeInForce());
        // There's no max remaining time nor min remaining time.
        $this->assertFalse($timeConstraint->getMaximumRemainingTime());
        $this->assertFalse($timeConstraint->getMinimumRemainingTime());
        
        $timeLimits = new TimeLimits();
        $assessmentItemRef->setTimeLimits($timeLimits);
        // There's still no max nor min remaining time.
        $this->assertFalse($timeConstraint->getMaximumRemainingTime());
        $this->assertFalse($timeConstraint->getMinimumRemainingTime());
        
        $timeLimits->setMinTime(new Duration('PT30S'));
        $this->assertEquals('PT10S', $timeConstraint->getMinimumRemainingTime()->__toString());
        $this->assertFalse($timeConstraint->getMaximumRemainingTime());
        
        $timeLimits->setMaxTime(new Duration('PT50S'));
        $this->assertEquals('PT30S', $timeConstraint->getMaximumRemainingTime()->__toString());
    }
    
    public function testNonLinearNavigationMode() {
        $assessmentItemRef = new AssessmentItemRef('Q01', 'Q01.xml');
        $timeLimits = new TimeLimits(new Duration('PT1S'), new Duration('PT2S'), false);
        $assessmentItemRef->setTimeLimits($timeLimits);
        $timeConstraint = new TimeConstraint($assessmentItemRef, new Duration('PT1S'), NavigationMode::NONLINEAR);
        
        // Minimum times are applicable to assessmentSections and assessmentItems only when linear navigation
        // mode is in effect.
        $this->assertFalse($timeConstraint->minTimeInForce());
        $this->assertFalse($timeConstraint->getMinimumRemainingTime());
    }
    
    public function testLinearNavigationMode() {
        $assessmentItemRef = new AssessmentItemRef('Q01', 'Q01.xml');
        $timeLimits = new TimeLimits(new Duration('PT1S'), new Duration('PT2S'), false);
        $assessmentItemRef->setTimeLimits($timeLimits);
        $timeConstraint = new TimeConstraint($assessmentItemRef, new Duration('PT1S'), NavigationMode::LINEAR);
        
        // Minimum times are applicable to assessmentSections and assessmentItems only when linear navigation
        // mode is in effect, this is the case!
        $this->assertTrue($timeConstraint->minTimeInForce());
        $this->assertEquals('PT0S', $timeConstraint->getMinimumRemainingTime()->__toString());
    }
}