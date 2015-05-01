<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */

namespace qtism\runtime\tests;

use qtism\data\SectionPart;

use qtism\common\datatypes\Duration;
use qtism\data\NavigationMode;
use qtism\data\QtiComponent;

/**
 * Represents a time constraint during an AssessmentTestSession.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TimeConstraint {
    
    /**
     * The source of the TimeConstraint. Can be
     * an AssessmentTest, TestPart, or SectionPart.
     * 
     * @var QtiComponent
     */
    private $source;
    
    /**
     * The Duration spent by the candidate on the source of
     * the TimeConstraint.
     * 
     * @var Duration
     */
    private $duration;
    
    /**
     * The navigation mode to be taken into account into the time constraint.
     * Indeed, minimum times are applicable to assessmentSections and assessmentItems
     * only if linear navigation mode is in effect.
     * 
     * @var integer
     * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10535 QTI timeLimits
     */
    private $navigationMode;
    
    /**
     * Whether or not to consider minimum time constraints.
     * 
     * @var boolean
     */
    private $considerMinTime;
    
    /**
     * Create a new TimeConstraint object.
     * 
     * @param QtiComponent $source The TestPart or SectionPart the constraint applies on.
     * @param Duration $duration The already spent duration by the candidate on $source.
     * @param NavigationMode $navigationMode The current navigation mode.
     * @param boolean $considerMinTime Whether or not to consider minimum time limits.
     */
    public function __construct(QtiComponent $source, Duration $duration, $navigationMode = NavigationMode::LINEAR, $considerMinTime = true) {
        $this->setSource($source);
        $this->setDuration($duration);
        $this->setNavigationMode($navigationMode);
    }
    
    /**
     * Set the TestPart or SectionPart object the constraint applies on.
     * 
     * @param QtiComponent $source A TestPart or SectionPart object.
     */
    protected function setSource(QtiComponent $source) {
        $this->source = $source;
    }
    
    /**
     * Get the TestPart or SectionPart object the constraint applies on.
     * 
     * @return QtiComponent A TestPart or SectionPart object.
     */
    public function getSource() {
        return $this->source;
    }
    
    /**
     * Set the Duration object representing the time already spent by the candidate
     * on the source of the time constraint.
     * 
     * @param Duration $duration A Duration object.
     */
    protected function setDuration(Duration $duration) {
        $this->duration = $duration;
    }
    
    /**
     * Get the Duration object representing the time already spent by the candidate
     * on the source of the time constraint.
     * 
     * @return Duration A Duration object.
     */
    public function getDuration() {
        return $this->duration;
    }
    
    /**
     * Set the current navigation mode.
     * 
     * @param integer $navigationMode A value from the NavigationMode enumeration.
     */
    protected function setNavigationMode($navigationMode) {
        $this->navigationMode = $navigationMode;
    }
    
    /**
     * Get the current navigation mode.
     * 
     * @return integer A value from the NavigationMode enumeration.
     */
    public function getNavigationMode() {
        return $this->navigationMode;
    }
    
    /**
     * Set whether or not minimum time limits must be taken into account.
     * 
     * @param boolean $considerMinTime
     */
    public function setConsiderMinTime($considerMinTime) {
        $this->considerMinTime = $considerMinTime;
    }
    
    /**
     * Whether or not minimum time limits are taken into account.
     * 
     * @return boolean
     */
    public function doesConsiderMinTime() {
        return $this->considerMinTime;
    }
    
    /**
     * Get the time remaining to be spent by the candidate on the source of the time
     * constraint. Please note that this method will never return negative durations.
     * 
     * @return Duration|boolean A Duration object or false if there is no maxTime constraint running for the source of the time constraint.
     */
    public function getMaximumRemainingTime() {
        if (($timeLimits = $this->getSource()->getTimeLimits()) !== null && ($maxTime = $timeLimits->getMaxTime()) !== null) {
            $remaining = clone $maxTime;
            $remaining->sub($this->getDuration());
            return ($remaining->isNegative() === true) ? new Duration('PT0S') : $remaining;
        }
        else {
            return false;
        }
    }
    
    /**
     * Get the time remaining to be spent by the candidate to be able to move/submit responses
     * from/for the source of the minimum time constraint. Please note that this method
     * will never return negative durations.
     * 
     * @return Duration|boolean A duration object or false if there is no minTime constraint running for the source of the time constraint.
     */
    public function getMinimumRemainingTime() {
        if ($this->minTimeInForce() === true) {
            $remaining = clone $this->getSource()->getTimeLimits()->getMinTime();
            $remaining->sub($this->getDuration());
            return ($remaining->isNegative() === true) ? new Duration('PT0S') : $remaining;
        }
        else {
            return false;
        }
    }
    
    /**
     * Whether or not a maxTime constraint is in force for the timeLimits (if existing) bound to the source
     * of the time constraint.
     * 
     * @return boolean
     */
    public function maxTimeInForce() {
        return ($timeLimits = $this->getSource()->getTimeLimits()) !== null && $timeLimits->hasMaxTime() === true;
    }
    
    /**
     * Whether or not a minTime constraint is in force for the timeLimits (if existing) bound to the source of 
     * the time constraint.
     * 
     * Please note that minTimes are applicable to assessmentSection and assessmentItems only when linear navigation
     * mode is in effect. In the case of an assessmentSection or assessmentItem as the source of the time constraint, with
     * a nonlinear navigation mode, the minTime attribute is not considered to be in force.
     * 
     * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10535 QTI timeLimits
     * @return boolean
     */
    public function minTimeInForce() {
        if ($this->doesConsiderMinTime() === false) {
            return false;
        }
       else if (($source = $this->getSource()) instanceof SectionPart && $this->getNavigationMode() === NavigationMode::NONLINEAR) {
            return false;
        }
        else {
            return ($timeLimits = $this->getSource()->getTimeLimits()) !== null && $timeLimits->hasMinTime() === true;
        }
    }
    
    /**
     * Whether or not a late submission is allowed by the timeLimits (if existing) bound to the source
     * of the time constraint. If no maxTime constraint is set to the bound timeLimits, this method
     * always return true;
     * 
     * @return boolean
     */
    public function allowLateSubmission() {
        if (($timeLimits = $this->getSource()->getTimeLimits()) !== null && $timeLimits->hasMaxTime() === false) {
            return true;
        }
        else if ($timeLimits !== null) {
            return $timeLimits->doesAllowLateSubmission();
        }
        else {
            return true;
        }
    }
}