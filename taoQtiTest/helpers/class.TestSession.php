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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use qtism\runtime\common\ProcessingException;
use qtism\runtime\processing\OutcomeProcessingEngine;
use qtism\data\rules\OutcomeRuleCollection;
use qtism\data\processing\OutcomeProcessing;
use qtism\data\rules\SetOutcomeValue;
use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\operators\Divide;
use qtism\data\expressions\NumberPresented;
use qtism\data\expressions\NumberCorrect;
use qtism\common\enums\BaseType;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Duration;
use qtism\data\AssessmentTest;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\runtime\tests\AssessmentTestSessionException;
use qtism\runtime\tests\AssessmentItemSession;
use qtism\runtime\tests\AssessmentTestPlace;
use qtism\runtime\tests\AbstractSessionManager;
use qtism\runtime\tests\Route;
use qtism\runtime\common\OutcomeVariable;
use qtism\data\ExtendedAssessmentItemRef;
use qtism\common\enums\Cardinality;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\event\EventManager;
use oat\taoQtiTest\models\event\QtiTestChangeEvent;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * A TAO Specific extension of QtiSm's AssessmentTestSession class. 
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiTest_helpers_TestSession extends AssessmentTestSession {
    
    /**
     * The ResultServer to be used to transmit Item and Test results.
     * 
     * @var taoResultServer_models_classes_ResultServerStateFull
     */
    private $resultServer;
    
    /**
     * The ResultTransmitter object to be used to transmit test results.
     * 
     * @var taoQtiCommon_helpers_ResultTransmitter
     */
    private $resultTransmitter;
    
    /**
     * The TAO Resource describing the test.
     * 
     * @var core_kernel_classes_Resource
     */
    private $test;
    
    /**
     * Create a new TAO QTI Test Session.
     * 
     * @param AssessmentTest $assessmentTest The AssessmentTest object representing the QTI test definition.
     * @param AbstractSessionManager $sessionManager The manager to be used to create new AssessmentItemSession objects.
     * @param Route $route The Route (sequence of items) to be taken by the candidate for this test session.
     * @param taoResultServer_models_classes_ResultServerStateFull $resultServer The Result Server where Item and Test Results must be sent to.
     * @param core_kernel_classes_Resource $test The TAO Resource describing the test.
     */
    public function __construct(AssessmentTest $assessmentTest, AbstractSessionManager $manager, Route $route, taoResultServer_models_classes_ResultServerStateFull $resultServer, core_kernel_classes_Resource $test) {
        parent::__construct($assessmentTest, $manager, $route);
        $this->setResultServer($resultServer);
        $this->setResultTransmitter(new taoQtiCommon_helpers_ResultTransmitter($this->getResultServer()));
        $this->setTest($test);
    }
    
    /**
     * Set the ResultServer to be used to transmit Item and Test results.
     * 
     * @param taoResultServer_models_classes_ResultServerStateFull $resultServer
     */
    protected function setResultServer(taoResultServer_models_classes_ResultServerStateFull $resultServer) {
        $this->resultServer = $resultServer;
    }
    
    /**
     * Get the ResultServer in use to transmit Item and Test results.
     * 
     * @return taoResultServer_models_classes_ResultServerStateFull
     */
    protected function getResultServer() {
        return $this->resultServer;
    }
    
    /**
     * Set the ResultTransmitter object to be used to transmit test results.
     * 
     * @param taoQtiCommon_helpers_ResultTransmitter $resultTransmitter
     */
    protected function setResultTransmitter(taoQtiCommon_helpers_ResultTransmitter $resultTransmitter) {
        $this->resultTransmitter = $resultTransmitter;
    }
    
    /**
     * Get the ResultTransmitter object to be used to transmit test results.
     * 
     * @return taoQtiCommon_helpers_ResultTransmitter
     */
    protected function getResultTransmitter() {
        return $this->resultTransmitter;
    }
    
    /**
     * Set the TAO Resource describing the test in database.
     * 
     * @param core_kernel_classes_Resource $test A Resource from the database describing a TAO test.
     */
    protected function setTest(core_kernel_classes_Resource $test) {
        $this->test = $test;
    }
    
    /**
     * Get the TAO Resource describing the test in database.
     * 
     * @return core_kernel_classes_Resource A Resource from the database describing a TAO test.
     */
    public function getTest() {
        return $this->test;
    }
    
    protected function submitItemResults(AssessmentItemSession $itemSession, $occurence = 0) {
        parent::submitItemResults($itemSession, $occurence);
        
        $item = $itemSession->getAssessmentItem();
        $occurence = $occurence;
        $sessionId = $this->getSessionId();
        
        common_Logger::t("submitting results for item '" . $item->getIdentifier() . "." . $occurence .  "'.");
        
        try {
        
            $itemVariableSet = array();
            
            // Get the item session we just responsed and send to the
            // result server.
            $itemSession = $this->getItemSession($item, $occurence);
            $resultTransmitter = $this->getResultTransmitter();
        
            foreach ($itemSession->getKeys() as $identifier) {
                common_Logger::t("Examination of variable '${identifier}'");
                $itemVariableSet[] = $itemSession->getVariable($identifier);
            }
            
            $itemUri = self::getItemRefUri($item);
            $testUri = self::getTestDefinitionUri($item);
            $transmissionId = "${sessionId}.${item}.${occurence}";
            
            $resultTransmitter->transmitItemVariable($itemVariableSet, $transmissionId, $itemUri, $testUri);
        }
        catch (AssessmentTestSessionException $e) {
            // Error whith parent::endAttempt().
            $msg = "An error occured while ending the attempt item '" . $item->getIdentifier() . "." . $occurence .  "'.";
            throw new taoQtiTest_helpers_TestSessionException($msg, taoQtiTest_helpers_TestSessionException::RESULT_SUBMISSION_ERROR, $e);
        }
        catch (taoQtiCommon_helpers_ResultTransmissionException $e) {
            // Error with Result Server.
            $msg = "An error occured while transmitting item results for item '" . $item->getIdentifier() . "." . $occurence .  "'.";
            throw new taoQtiTest_helpers_TestSessionException($msg, taoQtiTest_helpers_TestSessionException::RESULT_SUBMISSION_ERROR, $e);
        }
    }
    
    /**
     * QTISM endTestSession method overriding.
     * 
     * It consists of including an additional processing when the test ends,
     * in order to send the LtiOutcome 
     * 
     * @see http://www.imsglobal.org/lis/ Outcome Management Service
     * @throws taoQtiTest_helpers_TestSessionException If the session is already ended or if an error occurs whil transmitting/processing the result.
     */
    public function endTestSession() {
        parent::endTestSession();
        
        common_Logger::i('Ending test session.');
        try {
            // Compute the LtiOutcome variable for LTI support.
            $this->setVariable(new OutcomeVariable('LtiOutcome', Cardinality::SINGLE, BaseType::FLOAT, new Float(0.0)));
            $outcomeProcessingEngine = new OutcomeProcessingEngine($this->buildLtiOutcomeProcessing(), $this);
            $outcomeProcessingEngine->process();
        
            // if numberPresented returned 0, division by 0 -> null.
            $testUri = $this->getTest()->getUri();
            $var = $this->getVariable('LtiOutcome');
            $varIdentifier = $var->getIdentifier();
        
            common_Logger::t("Submitting test result '${varIdentifier}' related to test '${testUri}'.");
            $this->getResultTransmitter()->transmitTestVariable($var, $this->getSessionId(), $testUri);
            
            $this->unsetVariable('LtiOutcome');
        }
        catch (ProcessingException $e) {
            $msg = "An error occured while processing the 'LtiOutcome' outcome variable.";
            throw new taoQtiTest_helpers_TestSessionException($msg, taoQtiTest_helpers_TestSessionException::RESULT_SUBMISSION_ERROR, $e);
        }
        catch (taoQtiCommon_helpers_ResultTransmissionException $e) {
            $msg = "An error occured during test-level outcome results transmission.";
            throw new taoQtiTest_helpers_TestSessionException($msg, taoQtiTest_helpers_TestSessionException::RESULT_SUBMISSION_ERROR, $e);
        }
        
        $this->triggerEventChange();
    }

    /**
     * Rewind the test to its first position
     * @param boolean $allowTimeout Whether or not it is allowed to jump if the timeLimits in force of the jump target are not respected.
     * @throws AssessmentTestSessionException If $position is out of the Route bounds or the jump is not allowed because of time constraints.
     */
    public function rewind($allowTimeout = false)
    {
        $position = 0;
        $this->suspendItemSession();
        $route = $this->getRoute();
        $oldPosition = $route->getPosition();

        try {
            $route->setPosition($position);
            $this->selectEligibleItems();

            // Check the time limits after the jump is trully performed.
            if ($allowTimeout === false) {
                $this->checkTimeLimits(false, true);
            }

            // No exception thrown, interact!
            $this->interactWithItemSession();
        }
        catch (AssessmentTestSessionException $e) {
            // Rollback to previous position.
            $route->setPosition($oldPosition);
            throw $e;
        }
        catch (OutOfBoundsException $e) {
            $msg = "Position '${position}' is out of the Route bounds.";
            throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::FORBIDDEN_JUMP, $e);
        }
    }

    /**
     * AssessmentTestSession implementations must override this method in order to submit test results
     * from the current AssessmentTestSession to the appropriate data source.
     *
     * This method is triggered once at the end of the AssessmentTestSession.
     *
     * * @throws AssessmentTestSessionException With error code RESULT_SUBMISSION_ERROR if an error occurs while transmitting results.
     */
    protected function submitTestResults() {
        $testUri = $this->getTest()->getUri();
        $sessionId = $this->getSessionId();
        
        foreach ($this->getAllVariables() as $var) {
            common_Logger::t("Submitting test result '" . $var->getIdentifier() . "' related to test '" . $testUri . "'.");
            $this->getResultTransmitter()->transmitTestVariable($var, $sessionId, $testUri);
        }
    }
    
    /**
     * Get the TAO URI of an item from an ExtendedAssessmentItemRef object.
     * 
     * @param ExtendedAssessmentItemRef $itemRef
     * @return string A URI.
     */
    protected static function getItemRefUri(ExtendedAssessmentItemRef $itemRef) {
        $parts = explode('|', $itemRef->getHref());
        return $parts[0];
    }
    
    /**
     * Get the TAO Uri of the Test Definition from an ExtendedAssessmentItemRef object.
     * 
     * @param ExtendedAssessmentItemRef $itemRef
     * @return string A URI.
     */
    protected static function getTestDefinitionUri(ExtendedAssessmentItemRef $itemRef) {
        $parts = explode('|', $itemRef->getHref());
        return $parts[2];
    }
    
    /**
     * Build the OutcomeProcessing object representing the set of QTI instructions
     * to be performed to compute the LtiOutcome variable value.
     * 
     * @return OutcomeProcessing A QTI Data Model OutcomeProcessing object.
     */
    protected static function buildLtiOutcomeProcessing() {
        $numberCorrect = new NumberCorrect();
        $numberPresented = new NumberPresented();
        $divide = new Divide(new ExpressionCollection(array($numberCorrect, $numberPresented)));
        $outcomeRule = new SetOutcomeValue('LtiOutcome', $divide);
        return new OutcomeProcessing(new OutcomeRuleCollection(array($outcomeRule)));
    }

    /**
     * Suspend the current test session if it is running.
     */
    public function suspend() {
        parent::suspend();
        $this->triggerEventChange();
    }

    /**
     * Resume the current test session if it is suspended.
     */
    public function resume() {
        parent::resume();
        $this->triggerEventChange();
    }

    /**
     * Begins the test session. Calling this method will make the state
     * change into AssessmentTestSessionState::INTERACTING.
     *
     * @qtism-test-interaction
     * @qtism-test-duration-update
     */
    public function beginTestSession()
    {
        parent::beginTestSession();
        $this->triggerEventChange();
    }

    /**
     * Perform a 'jump' to a given position in the Route sequence. The current navigation
     * mode must be LINEAR to be able to jump.
     *
     * @param integer $position The position in the route the jump has to be made.
     * @param boolean $allowTimeout Whether or not it is allowed to jump if the timeLimits in force of the jump target are not respected.
     * @throws AssessmentTestSessionException If $position is out of the Route bounds or the jump is not allowed because of time constraints.
     * @qtism-test-interaction
     * @qtism-test-duration-update
     */
    public function jumpTo($position, $allowTimeout = false)
    {
        parent::jumpTo($position);
        $this->triggerEventChange();
    }

    /**
     * Ask the test session to move to next RouteItem in the Route sequence.
     *
     * If $allowTimeout is set to true, the very next RouteItem in the Route sequence will bet set
     * as the current RouteItem, whether or not it is timed out or not.
     *
     * On the other hand, if $allowTimeout is set to false, the next RouteItem in the Route sequence
     * which is not timed out will be set as the current RouteItem. If there is no more following RouteItems
     * that are not timed out in the Route sequence, the test session ends gracefully.
     *
     * @param boolean $allowTimeout If set to true, the next RouteItem in the Route sequence does not have to respect the timeLimits in force. Default value is false.
     * @throws AssessmentTestSessionException If the test session is not running or an issue occurs during the transition (e.g. branching, preConditions, ...).
     * @qtism-test-interaction
     * @qtism-test-duration-update
     */
    public function moveNext($allowTimeout = false)
    {
        parent::moveNext($allowTimeout);
        $this->triggerEventChange();
    }

    /**
     * Ask the test session to move to the previous RouteItem in the Route sequence.
     *
     * If $allowTimeout is set to true, the previous RouteItem in the Route sequence will bet set
     * as the current RouteItem, whether or not it is timed out.
     *
     * On the other hand, if $allowTimeout is set to false, the previous RouteItem in the Route sequence
     * which is not timed out will be set as the current RouteItem. If there is no more previous RouteItems
     * that are not timed out in the Route sequence, the current RouteItem remains the same and an
     * AssessmentTestSessionException with the appropriate timing error code is thrown.
     *
     * @param boolean $allowTimeout If set to true, the next RouteItem in the sequence does not have to respect timeLimits in force. Default value is false.
     * @throws AssessmentTestSessionException If the test session is not running or an issue occurs during the transition (e.g. branching, preConditions, ...) or if $allowTimeout = false and there absolutely no possibility to move backward (even the first RouteItem is timed out).
     * @qtism-test-interaction
     * @qtism-test-duration-update
     */
    public function moveBack($allowTimeout = false)
    {
        parent::moveBack($allowTimeout);
        $this->triggerEventChange();
    }

    /**
     * Skip the current item.
     *
     * @throws AssessmentTestSessionException If the test session is not running or it is the last route item of the testPart but the SIMULTANEOUS submission mode is in force and not all responses were provided.
     * @qtism-test-interaction
     * @qtism-test-duration-update
     */
    public function skip()
    {
        parent::skip();
        $this->triggerEventChange();
    }

    /**
     * Set the position in the Route at the very next TestPart in the Route sequence or, if the current
     * testPart is the last one of the test session, the test session ends gracefully. If the submission mode
     * is simultaneous, the pending responses are processed.
     *
     * @throws AssessmentTestSessionException If the test is currently not running.
     */
    public function moveNextTestPart()
    {
        parent::moveNextTestPart();
        $this->triggerEventChange();
    }

    /**
     * Set the position in the Route at the very next assessmentSection in the route sequence.
     *
     * * If there is no assessmentSection left in the flow, the test session ends gracefully.
     * * If there are still pending responses, they are processed.
     *
     * @throws AssessmentTestSessionException If the test is not running.
     */
    public function moveNextAssessmentSection()
    {
        parent::moveNextAssessmentSection();
        $this->triggerEventChange();
    }

    /**
     * Set the position in the Route at the very next assessmentItem in the route sequence.
     *
     * * If there is no item left in the flow, the test session ends gracefully.
     * * If there are still pending responses, they are processed.
     *
     * @throws AssessmentTestSessionException If the test is not running.
     */
    public function moveNextAssessmentItem()
    {
        parent::moveNextAssessmentItem();
        $this->triggerEventChange();
    }

    /**
     * Set the position in the Route at the very next TestPart in the Route sequence or, if the current
     * testPart is the last one of the test session, the test session ends gracefully. If the submission mode
     * is simultaneous, the pending responses are processed.
     *
     * @throws AssessmentTestSessionException If the test is currently not running.
     */
    public function closeTestPart()
    {

        if ($this->isRunning() === false) {
            $msg = "Cannot move to the next testPart while the state of the test session is INITIAL or CLOSED.";
            throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
        }

        $route = $this->getRoute();
        $from = $route->current();

        while ($route->valid() === true && $route->current()->getTestPart() === $from->getTestPart()) {
            $itemSession = $this->getCurrentAssessmentItemSession();
            $itemSession->endItemSession();
            $this->nextRouteItem();
        }

        if ($this->isRunning() === true) {
            $this->interactWithItemSession();
        }

        $this->triggerEventChange();
    }

    /**
     * Set the position in the Route at the very next assessmentSection in the route sequence.
     *
     * * If there is no assessmentSection left in the flow, the test session ends gracefully.
     * * If there are still pending responses, they are processed.
     *
     * @throws AssessmentTestSessionException If the test is not running.
     */
    public function closeAssessmentSection()
    {

        if ($this->isRunning() === false) {
            $msg = "Cannot move to the next assessmentSection while the state of the test session is INITIAL or CLOSED.";
            throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
        }

        $route = $this->getRoute();
        $from = $route->current();

        while ($route->valid() === true && $route->current()->getAssessmentSection() === $from->getAssessmentSection()) {
            $itemSession = $this->getCurrentAssessmentItemSession();
            $itemSession->endItemSession();
            $this->nextRouteItem();
        }

        if ($this->isRunning() === true) {
            $this->interactWithItemSession();
        }

        $this->triggerEventChange();
    }

    /**
     * Set the position in the Route at the very next assessmentItem in the route sequence.
     *
     * * If there is no item left in the flow, the test session ends gracefully.
     * * If there are still pending responses, they are processed.
     *
     * @throws AssessmentTestSessionException If the test is not running.
     */
    public function closeAssessmentItem()
    {

        if ($this->isRunning() === false) {
            $msg = "Cannot move to the next testPart while the state of the test session is INITIAL or CLOSED.";
            throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
        }

        $itemSession = $this->getCurrentAssessmentItemSession();
        $itemSession->endItemSession();
        $this->nextRouteItem();

        if ($this->isRunning() === true) {
            $this->interactWithItemSession();
        }

        $this->triggerEventChange();
    }

    /**
     * Closes a timer
     * @param string $identifier
     * @param string [$type]
     */
    public function closeTimer($identifier, $type = null)
    {
        switch ($type) {
            case 'assessmentTest':
                $places = AssessmentTestPlace::ASSESSMENT_TEST;
                break;

            case 'testPart':
                $places = AssessmentTestPlace::TEST_PART;
                break;

            case 'assessmentSection':
                $places = AssessmentTestPlace::ASSESSMENT_SECTION;
                break;

            case 'assessmentItemRef':
                $places = AssessmentTestPlace::ASSESSMENT_ITEM;
                break;

            default:
                $places = AssessmentTestPlace::ASSESSMENT_TEST | AssessmentTestPlace::TEST_PART | AssessmentTestPlace::ASSESSMENT_SECTION | AssessmentTestPlace::ASSESSMENT_ITEM;
        }

        $constraints = $this->getTimeConstraints($places);
        foreach ($constraints as $constraint) {
            $source = $constraint->getSource();
            $placeId = $source->getIdentifier();
            if ($placeId === $identifier) {
                if (($timeLimits = $source->getTimeLimits()) !== null && ($maxTime = $timeLimits->getMaxTime()) !== null) {
                    $constraintDuration = $constraint->getDuration();
                    if ($constraintDuration instanceof Duration) {
                        $constraintDuration->sub($constraintDuration);
                        $constraintDuration->add($maxTime);
                    }
                }
            }
        }
    }

    protected function triggerEventChange() {
        $event = new QtiTestChangeEvent($this);
        if ($event instanceof ServiceLocatorAwareInterface) {
            $event->setServiceLocator($this->getServiceLocator());
        }
        $this->getEventManager()->trigger($event);
    }
    
    /**
     * @return EventManager
     */
    protected function getEventManager() {
        return $this->getServiceLocator()->get(EventManager::CONFIG_ID);
    }
    
    protected function getServiceLocator() {
        return ServiceManager::getServiceManager();
    }
}
