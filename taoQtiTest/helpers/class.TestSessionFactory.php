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

use qtism\runtime\tests\AbstractAssessmentTestSessionFactory;
use qtism\data\AssessmentTest;

/**
 * A TAO specific implementation of QtiSm's AbstractAssessmentTestSessionFactory.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiTest_helpers_TestSessionFactory extends AbstractAssessmentTestSessionFactory {
   
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
     * Create a new TestSessionFactory.
     * 
     * @param AssessmentTest $assessmentTest The QtiSm QTI AssessmentTest definition of the AssessmentTestSession to be built.
     * @param taoResultServer_models_classes_ResultServerStateFull $resultServer The ResultServer to be set to the AssessmentTestSession to be built.
     * @param core_kernel_classes_Resource $test The TAO Resource describing the Test definition to be set to the AssessmentTestSession to be built.
     */
    public function __construct(AssessmentTest $assessmentTest, taoResultServer_models_classes_ResultServerStateFull $resultServer, core_kernel_classes_Resource $test) {
        parent::__construct($assessmentTest);
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
     * Create a tao_helpers_TestSession with the content of the factory.
     * 
     * When creating the AssessmentTestSession object, the AssessmentTestSession::setAutoForward
     * method will be called with false.
     * 
     * @return taoQtiTest_helpers_TestSession
     */
    public function createAssessmentTestSession() {
        parent::createAssessmentTestSession();
        
        $session = new taoQtiTest_helpers_TestSession(
            $this->getAssessmentTest(),
            $this->getRoute(),
            $this->getResultServer(),
            $this->getTest()
        );
        
        $session->setAutoForward(false);
        return $session;
    }
}