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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\taoQtiTest\models\runner\session;

use oat\taoQtiTest\models\runner\time\QtiTimer;
use oat\taoQtiTest\models\runner\time\QtiTimeStorage;
use oat\taoTests\models\runner\time\InconsistentRangeException;
use oat\taoTests\models\runner\time\TimePoint;
use qtism\common\datatypes\Duration;
use qtism\runtime\tests\AssessmentItemSession;
use qtism\runtime\tests\AssessmentTestPlace;
use qtism\runtime\tests\AssessmentTestSessionException;
use qtism\runtime\tests\RouteItem;
use qtism\runtime\tests\TimeConstraint;
use qtism\runtime\tests\TimeConstraintCollection;
use taoQtiTest_helpers_TestSession;

/**
 * TestSession override
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class TestSession extends taoQtiTest_helpers_TestSession
{
    /**
     * The Timer bound to the test session
     * @var QtiTimer
     */
    protected $timer;

    /**
     * The target from which compute the durations
     * @var int
     */
    protected $timerTarget = TimePoint::TARGET_SERVER;

    /**
     * A temporary cache for computed durations
     * @var array
     */
    protected $durationCache = [];

    /**
     * Gets the Timer bound to the test session
     * @return QtiTimer
     * @throws \oat\taoTests\models\runner\time\InvalidDataException
     * @throws \oat\taoTests\models\runner\time\InvalidStorageException
     */
    public function getTimer()
    {
        if (!$this->timer) {
            $this->timer = new QtiTimer();
            $this->timer->setStorage(new QtiTimeStorage($this->getSessionId()));
            $this->timer->load();
        }
        return $this->timer;
    }

    /**
     * Gets the target from which compute the durations
     * @return int
     */
    public function getTimerTarget()
    {
        return $this->timerTarget;
    }

    /**
     * Set the target from which compute the durations
     * @param int $timerTarget
     */
    public function setTimerTarget($timerTarget)
    {
        $this->timerTarget = intval($timerTarget);
    }

    /**
     * Gets the tags describing a particular item with an assessment test
     * @param RouteItem $routeItem
     * @return array
     */
    public function getItemTags(RouteItem $routeItem)
    {
        $test = $routeItem->getAssessmentTest();
        $testPart = $routeItem->getTestPart();
        $sections = $routeItem->getAssessmentSections();
        $sectionId = key(current($sections));
        $itemRef = $routeItem->getAssessmentItemRef();
        $itemId = $itemRef->getIdentifier();
        $occurrence = $routeItem->getOccurence();

        $tags = [
            $itemId,
            $itemId . '#' . $occurrence,
            $sectionId,
            $testPart->getIdentifier(),
            $test->getIdentifier(),
            $itemRef->getHref(),
        ];

        if ($this->isRunning() === true) {
            $itemSession = $this->getAssessmentItemSessionStore()->getAssessmentItemSession($itemRef, $occurrence);
            $tags[] = $itemId . '#' . $occurrence . '-' . $itemSession['numAttempts']->getValue();
        }

        return $tags;
    }

    /**
     * Initializes the timer for the current item in the TestSession
     * @throws \oat\taoTests\models\runner\time\InvalidDataException
     */
    public function initItemTimer()
    {
        try {
            // try to close existing time range if any, in order to be sure the test will start or restart a new range.
            $tags = $this->getItemTags($this->getCurrentRouteItem());
            $this->getTimer()->end($tags, microtime(true))->save();
            \common_Logger::i('Existing timer initialized.');
        } catch(InconsistentRangeException $e) {
            \common_Logger::i('New timer initialized.');
        }
    }

    /**
     * Starts the timer for the current item in the TestSession
     * @throws \oat\taoTests\models\runner\time\InvalidDataException
     */
    public function startItemTimer()
    {
        $tags = $this->getItemTags($this->getCurrentRouteItem());
        $this->getTimer()->start($tags, microtime(true))->save();
    }

    /**
     * Ends the timer for the current item in the TestSession
     * @throws \oat\taoTests\models\runner\time\InconsistentRangeException
     * @throws \oat\taoTests\models\runner\time\InvalidDataException
     */
    public function endItemTimer()
    {
        $tags = $this->getItemTags($this->getCurrentRouteItem());
        $this->getTimer()->end($tags, microtime(true))->save();
    }

    /**
     * Adjusts the timer for the current item in the TestSession
     * @param float $duration
     * @throws \oat\taoTests\models\runner\time\InconsistentRangeException
     * @throws \oat\taoTests\models\runner\time\InvalidDataException
     */
    public function adjustItemTimer($duration)
    {
        if (!is_null($duration)) {
            $duration = floatval($duration);
        }
        $tags = $this->getItemTags($this->getCurrentRouteItem());
        $this->getTimer()->adjust($tags, $duration)->save();
    }

    /**
     * Gets the timer duration for a particular identifier
     * @param string|array $identifier
     * @param int $target
     * @return Duration
     * @throws \oat\taoTests\models\runner\time\InconsistentCriteriaException
     */
    public function getTimerDuration($identifier, $target = 0)
    {
        if (!$target) {
            $target = $this->timerTarget;
        }

        $durationKey = $target . '-';
        if (is_array($identifier)) {
            sort($identifier);
            $durationKey .= implode('-', $identifier);
        } else {
            $durationKey .= $identifier;
        }

        if (!isset($this->durationCache[$durationKey])) {
            $duration = round($this->getTimer()->compute($identifier, $target), 6);
            $this->durationCache[$durationKey] = new Duration('PT' . $duration . 'S');
        }

        return $this->durationCache[$durationKey];
    }

    /**
     * Gets the total duration for the current item in the TestSession
     * @param int $target
     * @return Duration
     * @throws \oat\taoTests\models\runner\time\InconsistentCriteriaException
     */
    public function computeItemTime($target = 0)
    {
        $currentItem = $this->getCurrentAssessmentItemRef();
        return $this->getTimerDuration($currentItem->getIdentifier(), $target);
    }

    /**
     * Gets the total duration for the current section in the TestSession
     * @param int $target
     * @return Duration
     * @throws \oat\taoTests\models\runner\time\InconsistentCriteriaException
     */
    public function computeSectionTime($target = 0)
    {
        $routeItem = $this->getCurrentRouteItem();
        $sections = $routeItem->getAssessmentSections();
        return $this->getTimerDuration(key(current($sections)), $target);
    }

    /**
     * Gets the total duration for the current test part in the TestSession
     * @param int $target
     * @return Duration
     * @throws \oat\taoTests\models\runner\time\InconsistentCriteriaException
     */
    public function computeTestPartTime($target = 0)
    {
        $routeItem = $this->getCurrentRouteItem();
        $testPart = $routeItem->getTestPart();
        return $this->getTimerDuration($testPart->getIdentifier(), $target);
    }

    /**
     * Gets the total duration for the whole assessment test
     * @param int $target
     * @return Duration
     * @throws \oat\taoTests\models\runner\time\InconsistentCriteriaException
     */
    public function computeTestTime($target = 0)
    {
        $routeItem = $this->getCurrentRouteItem();
        $test = $routeItem->getAssessmentTest();
        return $this->getTimerDuration($test->getIdentifier(), $target);
    }

    /**
     * Update the durations involved in the AssessmentTestSession to mirror the durations at the current time.
     * This method can be useful for stateless systems that make use of QtiSm.
     */
    public function updateDuration() {
        // not needed anymore
        \common_Logger::t('Call to disabled updateDuration()');
    }

    /**
     * Gets a TimeConstraint from a particular source
     * @param $source
     * @param $navigationMode
     * @param $considerMinTime
     * @return TimeConstraint
     * @throws \oat\taoTests\models\runner\time\InconsistentCriteriaException
     */
    protected function getTimeConstraint($source, $navigationMode, $considerMinTime)
    {
        return new TimeConstraint($source, $this->getTimerDuration($source->getIdentifier()), $navigationMode, $considerMinTime);
    }

    /**
     * Get the time constraints running for the current testPart or/and current assessmentSection
     * or/and assessmentItem.
     *
     * @param integer $places A composition of values (use | operator) from the AssessmentTestPlace enumeration. If the null value is given, all places will be taken into account.
     * @return TimeConstraintCollection A collection of TimeConstraint objects.
     * @qtism-test-duration-update
     */
    public function getTimeConstraints($places = null) {

        if ($places === null) {
            // Get the constraints from all places in the Assessment Test.
            $places = (AssessmentTestPlace::ASSESSMENT_TEST | AssessmentTestPlace::TEST_PART | AssessmentTestPlace::ASSESSMENT_SECTION | AssessmentTestPlace::ASSESSMENT_ITEM);
        }

        $constraints = new TimeConstraintCollection();
        $navigationMode = $this->getCurrentNavigationMode();
        $routeItem = $this->getCurrentRouteItem();
        $considerMinTime = $this->mustConsiderMinTime();

        if ($places & AssessmentTestPlace::ASSESSMENT_TEST) {
            $constraints[] = $this->getTimeConstraint($routeItem->getAssessmentTest(), $navigationMode, $considerMinTime);
        }

        if ($places & AssessmentTestPlace::TEST_PART) {
            $constraints[] = $this->getTimeConstraint($this->getCurrentTestPart(), $navigationMode, $considerMinTime);
        }

        if ($places & AssessmentTestPlace::ASSESSMENT_SECTION) {
            $constraints[] = $this->getTimeConstraint($this->getCurrentAssessmentSection(), $navigationMode, $considerMinTime);
        }

        if ($places & AssessmentTestPlace::ASSESSMENT_ITEM) {
            $constraints[] = $this->getTimeConstraint($routeItem->getAssessmentItemRef(), $navigationMode, $considerMinTime);
        }

        return $constraints;
    }

    /**
     * AssessmentTestSession implementations must override this method in order
     * to submit item results from a given $assessmentItemSession to the appropriate
     * data source.
     *
     * This method is triggered each time response processing takes place.
     *
     * @param AssessmentItemSession $itemSession The lastly updated AssessmentItemSession.
     * @param integer $occurrence The occurrence number of the item bound to $assessmentItemSession.
     * @throws AssessmentTestSessionException With error code RESULT_SUBMISSION_ERROR if an error occurs while transmitting results.
     */
    public function submitItemResults(AssessmentItemSession $itemSession, $occurrence = 0)
    {
        $itemRef = $itemSession->getAssessmentItem();
        $identifier = $itemRef->getIdentifier();
        $duration = $this->getTimerDuration($identifier);

        $itemDurationVar = $itemSession->getVariable('duration');
        $sessionDuration = $itemDurationVar->getValue();
        \common_Logger::i("Force duration of item '${identifier}' to ${duration} instead of ${sessionDuration}");
        $itemSession->getVariable('duration')->setValue($duration);

        parent::submitItemResults($itemSession, $occurrence);
    }
}
