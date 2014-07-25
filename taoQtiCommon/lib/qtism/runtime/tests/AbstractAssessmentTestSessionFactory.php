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
 * @subpackage 
 *
 */
namespace qtism\runtime\tests;

use qtism\common\datatypes\Duration;
use qtism\data\AssessmentTest;
use \RuntimeException;

/**
 * The AbstractAssessmentTestSessionFactory class is a bed for instantiating
 * various implementations of AssessmentTestSession.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AbstractAssessmentTestSessionFactory {
    
    /**
     * The Route object to be used to instantiate an AssessmentTestSession object.
     * 
     * @var Route
     */
    private $route;
    
    /**
     * The AssessmentTest object to be used to instantiate an AssessmentTestSession object.
     * 
     * @var AssessmentTest
     */
    private $assessmentTest;
    
    /**
     * The acceptable latency time for AssessmentTestSessions and their item sessions.
     * 
     * @var Duration
     */
    private $acceptableLatency;
    
    public function __construct(AssessmentTest $assessmentTest) {
        $this->setAssessmentTest($assessmentTest);
        $this->setAcceptableLatency(new Duration('PT0S'));
    }
    
    /**
     * Set the Route object to be used to instantiate an AssessmentTestSession object.
     * 
     * @param Route $route A Route object.
     */
    public function setRoute(Route $route = null) {
        $this->route = $route;
    }
    
    /**
     * Get the Route object to be used to instantiate An AssessmentTestSession object.
     * 
     * @return Route A Route object.
     */
    public function getRoute() {
        return $this->route;
    }
    
    /**
     * Set the AssessmentTest object to be used to instantiate an AssessmentTestSession object.
     * 
     * @param AssessmentTest $assessmentTest An AssessmentTest object.
     */
    public function setAssessmentTest(AssessmentTest $assessmentTest) {
        $this->assessmentTest = $assessmentTest;
    }
    
    /**
     * Get the AssessmentTest object to be used to instantiate an AssessmentTestSession object.
     * 
     * @return AssessmentTest An AssessmentTest object.
     */
    public function getAssessmentTest() {
        return $this->assessmentTest;
    }
    
    /**
     * Set the acceptable latency for AssessmentTestSessions and their AssessmentItemSessions.
     * 
     * @param Duration $latency A Duration object.
     */
    public function setAcceptableLatency(Duration $latency) {
        $this->acceptableLatency = $latency;
    }
    
    /**
     * Get the acceptable latency for AssessmentTestSessions and their AssessmentItemSessions.
     * 
     * @return Duration A Duration object.
     */
    public function getAcceptableLatency() {
        return $this->acceptableLatency;
    }
    
    /**
     * Create a new AssessmentTestSession object with the content
     * of the factory.
     * 
     * @return AssessmentTestSession An AssessmentTestSession object.
     * @throws RuntimeException If no Route has been provided to the factory yet.
     */
    public function createAssessmentTestSession() {
        if (is_null($this->getRoute() === true)) {
            $msg = "No Route has been set in the factory. The AssessmentTestSession cannot be instantiated without it.";
            throw new RuntimeException($msg);
        }
    }
}