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
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 *  
 *
 */
namespace qtism\runtime\tests;

use qtism\data\SubmissionMode;
use qtism\data\NavigationMode;
use qtism\data\IAssessmentItem;
use qtism\common\datatypes\Duration;
use qtism\data\AssessmentTest;
use qtism\data\AssessmentSection;
use qtism\data\AssessmentSectionCollection;
use qtism\data\AssessmentItemRef;

/**
 * The AbstractSessionManager class is a bed for instantiating
 * various implementations of AssessmentTestSession and AssessmentItemSession.
 * 
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class AbstractSessionManager {
    
    /**
     * The acceptable latency time for AssessmentTestSession and AssessmentItemSession objects.
     * 
     * @var Duration
     */
    private $acceptableLatency;
    
    /**
     * Whether or not created AssessmentTestSession and AssessmentItemSession objects
     * must consider the minimum time constraints.
     * 
     * @var booolean
     */
    private $considerMinTime;
    
    public function __construct() {
        $this->setAcceptableLatency(new Duration('PT0S'));
        $this->setConsiderMinTime(true);
    }
    
    /**
     * Set the acceptable latency for AssessmentTestSessions and their AssessmentItemSessions.
     * 
     * @param Duration $latency
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
     * Set whether or not created AssessmentTestSessions must consider
     * minimum time limits.
     * 
     * @param boolean $considerMinTime
     */
    public function setConsiderMinTime($considerMinTime) {
        $this->considerMinTime = $considerMinTime;
    }
    
    /**
     * Whether or not created AssessmentTestSessions must consider
     * minimum time limits.
     * 
     * @return boolean
     */
    public function mustConsiderMinTime() {
        return $this->considerMinTime;
    }
    
    /**
     * Create a new AssessmentTestSession object.
     * 
     * @return AssessmentTestSession An AssessmentTestSession object.
     */
    public function createAssessmentTestSession(AssessmentTest $test, Route $route = null) {
        $session = $this->instantiateAssessmentTestSession($test, $this->getRoute($test, $route));
        $this->configureAssessmentTestSession($session);
        return $session;
    }
    
    /**
     * Contains the logic of instantiating the appropriate AssessmentTestSession implementation.
     * 
     * @param AssessmentTest $assessmentTest
     * @param Route $route
     * @return AssessmentTestSession A freshly instantiated AssessmentTestSession.
     */
    abstract protected function instantiateAssessmentTestSession(AssessmentTest $test, Route $route);
    
    /**
     * Contains the logic of instantiating the appropriate AssessmentItemSession implementation.
     * 
     * @param IAssessmentItem $assessmentItem
     * @param integer $navigationMode A value from the NavigationMode enumeration.
     * @param integer $submissionMode A value from the SubmissionMode enumeration.
     * @return AssessmentItemSession A freshly instantiated AssessmentItemSession.
     */
    abstract protected function instantiateAssessmentItemSession(IAssessmentItem $assessmentItem, $navigationMode, $submissionMode);
    
    /**
     * 
     * @param IAssessmentItem $assessmentItem
     * @param integer $navigationMode A value from the NavigationMode enumeration.
     * @param A value from the SubmissionMode enumeration $submissionMode.
     * 
     * @return AssessmentItemSession
     */
    public function createAssessmentItemSession(IAssessmentItem $assessmentItem, $navigationMode = NavigationMode::LINEAR, $submissionMode = SubmissionMode::INDIVIDUAL) {
        $session = $this->instantiateAssessmentItemSession($assessmentItem, $navigationMode, $submissionMode);
        $this->configureAssessmentItemSession($session);
        return $session;
    }
    
    /**
     * Contains the Route create logic depending on whether or not
     * an optional Route to be used is given or not.
     * 
     * @param AssessmentTest $test
     * @param Route $route
     * @return Route
     */
    protected function getRoute(AssessmentTest $test, Route $route = null) {
        return (is_null($route) === true) ? $this->createRoute($test) : $route;
    }
    
    /**
     * Contains the logic of configuring a newly instantiated AssessmentTestSession object
     * with additional configuration values held by the factory.
     * 
     * @param AssessmentTestSession $assessmentTestSession
     */
    protected function configureAssessmentTestSession(AssessmentTestSession $assessmentTestSession) {
        return;
    }
    
    /**
     * Contains the logic of configuring a newly instantiated AssessmentItemSession object
     * with additional configuration values held by the factory.
     * 
     * @param AssessmentItemSession $assessmentItemSession
     */
    protected function configureAssessmentItemSession(AssessmentItemSession $assessmentItemSession) {
        return;
    }
    
    /**
     * Contains the logic of creating the Route of a brand new AssessmentTestSession object.
     * The resulting Route object will be injected in the created AssessmentTestSession.
     *
     * @param AssessmentTest $test
     * @return Route A newly instantiated Route object.
     */
    protected function createRoute(AssessmentTest $test) {
        $routeStack = array();
         
        foreach ($test->getTestParts() as $testPart) {
    
            $assessmentSectionStack = array();
             
            foreach ($testPart->getAssessmentSections() as $assessmentSection) {
                $trail = array();
                $mark = array();
                 
                array_push($trail, $assessmentSection);
                 
                while (count($trail) > 0) {
    
                    $current = array_pop($trail);
                     
                    if (!in_array($current, $mark, true) && $current instanceof AssessmentSection) {
                        // 1st pass on assessmentSection.
                        $currentAssessmentSection = $current;
                        array_push($assessmentSectionStack, $currentAssessmentSection);
                         
                        array_push($mark, $current);
                        array_push($trail, $current);
                         
                        foreach (array_reverse($current->getSectionParts()->getArrayCopy()) as $sectionPart) {
                            array_push($trail, $sectionPart);
                        }
                    }
                    else if (in_array($current, $mark, true)) {
                        // 2nd pass on assessmentSection.
                        // Pop N routeItems where N is the children count of $current.
                        $poppedRoutes = array();
                        for ($i = 0; $i < count($current->getSectionParts()); $i++) {
                            $poppedRoutes[] = array_pop($routeStack);
                        }
                         
                        $selection = new BasicSelection($current, new SelectableRouteCollection(array_reverse($poppedRoutes)));
                        $selectedRoutes = $selection->select();
                         
                        // Shuffling can be applied on selected routes.
                        // $route will contain the final result of the selection + ordering.
                        $ordering = new BasicOrdering($current, $selectedRoutes);
                        $selectedRoutes = $ordering->order();
                         
                        $route = new SelectableRoute($current->isFixed(), $current->isRequired(), $current->isVisible(), $current->mustKeepTogether());
                        foreach ($selectedRoutes as $r) {
                            $route->appendRoute($r);
                        }
                         
                        // Add to the last item of the selection the branch rules of the AssessmentSection/testPart
                        // on which the selection is applied... Only if the route contains something (empty assessmentSection edge case).
                        if ($route->count() > 0) {
                            $route->getLastRouteItem()->addBranchRules($current->getBranchRules());
                             
                            // Do the same as for branch rules for pre conditions, except that they must be
                            // attached on the first item of the route.
                            $route->getFirstRouteItem()->addPreConditions($current->getPreConditions());
                        }
                         
                        array_push($routeStack, $route);
                        array_pop($assessmentSectionStack);
                    }
                    else if ($current instanceof AssessmentItemRef) {
                        // leaf node.
                        $route = new SelectableRoute($current->isFixed(), $current->isRequired());
                        $route->addRouteItem($current, new AssessmentSectionCollection($assessmentSectionStack), $testPart, $test);
                        array_push($routeStack, $route);
                    }
                }
            }
        }
         
        $finalRoutes = $routeStack;
        $route = new SelectableRoute();
        foreach ($finalRoutes as $finalRoute) {
            $route->appendRoute($finalRoute);
        }
         
        return $route;
    }
}