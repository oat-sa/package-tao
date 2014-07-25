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
 */

use qtism\runtime\common\ProcessingException;
use qtism\runtime\processing\OutcomeProcessingEngine;
use qtism\data\rules\OutcomeRuleCollection;
use qtism\data\processing\OutcomeProcessing;
use qtism\data\rules\SetOutcomeValue;
use qtism\runtime\expressions\operators\DivideProcessor;
use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\operators\Divide;
use qtism\data\expressions\NumberPresented;
use qtism\data\expressions\NumberCorrect;
use qtism\common\enums\BaseType;
use qtism\data\AssessmentTest;
use qtism\runtime\common\State;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\runtime\tests\AssessmentTestSessionException;
use qtism\runtime\tests\Route;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\common\ResponseVariable;
use qtism\data\ExtendedAssessmentItemRef;
use qtism\common\enums\Cardinality;
use qtism\runtime\tests\AssessmentItemSession;

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
     * @param Route $route The Route (sequence of items) to be taken by the candidate for this test session.
     * @param taoResultServer_models_classes_ResultServerStateFull $resultServer The Result Server where Item and Test Results must be sent to.
     * @param core_kernel_classes_Resource $test The TAO Resource describing the test.
     */
    public function __construct(AssessmentTest $assessmentTest, Route $route, taoResultServer_models_classes_ResultServerStateFull $resultServer, core_kernel_classes_Resource $test) {
        parent::__construct($assessmentTest, $route);
        $this->setResultServer($resultServer);
        $this->setResultTransmitter(new taoQtiCommon_helpers_ResultTransmitter($this->getResultServer()));
        $this->setVariable(new OutcomeVariable('LtiOutcome', Cardinality::SINGLE, BaseType::FLOAT));
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
        $item = $itemSession->getAssessmentItem();
        $occurence = $occurence;
        $sessionId = $this->getSessionId();
        
        common_Logger::d("submitting results for item '" . $item->getIdentifier() . "." . $occurence .  "'.");
        
        try {
        
            // Get the item session we just responsed and send to the
            // result server.
            $itemSession = $this->getItemSession($item, $occurence);
            $resultTransmitter = $this->getResultTransmitter();
        
            foreach ($itemSession->getKeys() as $identifier) {
                common_Logger::d("Examination of variable '${identifier}'");
        
                $variable = $itemSession->getVariable($identifier);
                $itemUri = self::getItemRefUri($item);
                $testUri = self::getTestDefinitionUri($item);
                $transmissionId = "${sessionId}.${item}.${occurence}";
        
                $resultTransmitter->transmitItemVariable($variable, $transmissionId, $itemUri, $testUri);
            }
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
    
    protected function outcomeProcessing() {
        try {
            
            // Compute the LtiOutcome variable for LTI support.
            $outcomeProcessingEngine = new OutcomeProcessingEngine($this->buildLtiOutcomeProcessing(), $this);
            $outcomeProcessingEngine->process();
    
            // if numberPresented returned 0, division by 0 -> null.
            $finalLtiOutcomeValue = (is_null($this['LtiOutcome'])) ? 0.0 : $this['LtiOutcome'];
            $this['LtiOutcome'] = $finalLtiOutcomeValue;
    
            $ltiOutcomeVariable = $this->getVariable('LtiOutcome');
            $this->getResultTransmitter()->transmitTestVariable($ltiOutcomeVariable, $this->getSessionId(), $this->getTest()->getUri());
            
        }
        catch (ProcessingException $e) {
            $msg = "An error occured while processing the 'LtiOutcome' outcome variable.";
            throw new taoQtiTest_helpers_TestSessionException($msg, taoQtiTest_helpers_TestSessionException::RESULT_SUBMISSION_ERROR, $e);
        }
        catch (taoQtiCommon_helpers_ResultTransmissionException $e) {
            $msg = "An error occured during test-level outcome results transmission.";
            throw new taoQtiTest_helpers_TestSessionException($msg, taoQtiTest_helpers_TestSessionException::RESULT_SUBMISSION_ERROR, $e);
        }
        catch (AssessmentTestSessionException $e) {
            $msg = "An error occured during test-level outcome processing.";
            throw new taoQtiTest_helpers_TestSessionException($msg, taoQtiTest_helpers_TestSessionException::RESULT_SUBMISSION_ERROR, $e);
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
}