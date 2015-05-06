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

use qtism\runtime\tests\AbstractSessionManager;
use qtism\runtime\tests\TestResultsSubmission;
use qtism\runtime\tests\Route;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\runtime\tests\AssessmentItemSession;
use qtism\data\AssessmentTest;
use qtism\data\IAssessmentItem;
use qtism\common\datatypes\Duration;

/**
 * A TAO specific implementation of QTISM's AbstractSessionManager.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiTest_helpers_SessionManager extends AbstractSessionManager {
   
    /**
     * The result server to be used by tao_helpers_TestSession created by the factory.
     * 
     * @var taoResultServer_models_classes_ResultServerStateFull
     */
    private $resultServer;
    
    /**
     * The TAO Resource describing the Test definition to be set to the AssessmentTestSession to be built.
     * 
     * @var core_kernel_classes_Resource
     */
    private $test;
    
    /**
     * Create a new SessionManager object.
     * 
     * @param taoResultServer_models_classes_ResultServerStateFull $resultServer The ResultServer to be set to the AssessmentTestSession to be built.
     * @param core_kernel_classes_Resource $test The TAO Resource describing the Test definition to be set to the AssessmentTestSession to be built.
     */
    public function __construct(taoResultServer_models_classes_ResultServerStateFull $resultServer, core_kernel_classes_Resource $test) {
        parent::__construct();
        $this->setAcceptableLatency(new Duration(taoQtiTest_models_classes_QtiTestService::singleton()->getQtiTestAcceptableLatency()));
        $this->setResultServer($resultServer);
        $this->setTest($test);
    }
    
    /**
     * Set the result server to be used by tao_helpers_TestSession created by the factory.
     * 
     * @param taoResultServer_models_classes_ResultServerStateFull $resultServer
     */
    public function setResultServer(taoResultServer_models_classes_ResultServerStateFull $resultServer) {
        $this->resultServer = $resultServer;
    }
    
    /**
     * Get the result server to be used by tao_helpers_TestSession created by the factory.
     * 
     * @return taoResultServer_models_classes_ResultServerStateFull
     */
    public function getResultServer() {
        return $this->resultServer;
    }
    
    /**
     * Set the TAO Resource describing the Test definition to be set to the AssessmentTestSession to be built.
     * 
     * @param core_kernel_classes_Resource $test A TAO Test Resource.
     */
    public function setTest(core_kernel_classes_Resource $test) {
        $this->test = $test;
    }
    
    /**
     * Get the TAO Resource describing the Test definition to be set to the AssessmentTestSession to be built.
     * 
     * @return core_kernel_classes_Resource A TAO Resource.
     */
    public function getTest() {
        return $this->test;
    }
    
    /**
     * Instantiates an AssessmentTestSession with the default implementation provided by QTISM.
     *
     * @return AssessmentTestSession
     */
    protected function instantiateAssessmentTestSession(AssessmentTest $test, Route $route) {
        return new taoQtiTest_helpers_TestSession($test, $this, $route, $this->getResultServer(), $this->getTest());
    }
    
    /**
     * Extra configuration for newly instantiated AssessmentTestSession objects. This implementation
     * forces test results to be sent at the end of the candidate session, and get the acceptable
     * latency time from the taoQtiTest extension's configuration.
     * 
     * @param AssessmentTestSession $assessmentTestSession
     */
    protected function configureAssessmentTestSession(AssessmentTestSession $assessmentTestSession) {
        $assessmentTestSession->setTestResultsSubmission(TestResultsSubmission::END);
    }
    
    /**
     * Instantiates an AssessmentItemSession with the default implementation provided by QTISM.
     *
     * @param IAssessmentItem $assessmentItem
     * @param integer $navigationMode A value from the NavigationMode enumeration.
     * @param integer $submissionMode A value from the SubmissionMode enumeration.
     * @return AssessmentItemSession A freshly instantiated AssessmentItemSession.
     */
    protected function instantiateAssessmentItemSession(IAssessmentItem $assessmentItem, $navigationMode, $submissionMode) {
        return new AssessmentItemSession($assessmentItem, $this, $navigationMode, $submissionMode);
    }
    
    /**
     * Creates a brand new AssessmentItemSessionFactory object.
     *
     * @return AssessmentItemSessionFactory
     */
    public function createAssessmentItemSessionFactory() {
        return new AssessmentItemSessionFactory(false);
    }
}