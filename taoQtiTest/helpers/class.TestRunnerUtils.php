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
* Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
*
*/

use qtism\data\NavigationMode;
use qtism\data\SubmissionMode;
use qtism\data\View;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\runtime\tests\AssessmentTestSessionException;
use qtism\runtime\tests\AssessmentItemSessionState;
use qtism\runtime\tests\AssessmentTestSessionState;

/**
* Utility methods for the QtiTest Test Runner.
*
* @author Jérôme Bogaerts <jerome@taotesting.com>
*
*/
class taoQtiTest_helpers_TestRunnerUtils {
    
    /**
     * Get the ServiceCall object represeting how to call the current Assessment Item to be
     * presented to a candidate in a given Assessment Test $session.
     *
     * @param AssessmentTestSession $session An AssessmentTestSession Object.
     * @param string $testDefinition URI The URI of the knowledge base resource representing the folder where the QTI Test Definition is stored.
     * @param string $testCompilation URI The URI of the knowledge base resource representing the folder where the QTI Test Compilation is stored.
     * @return tao_models_classes_service_ServiceCall A ServiceCall object.
     */
    static public function buildItemServiceCall(AssessmentTestSession $session, $testDefinitionUri, $testCompilationUri) {
        
        $href = $session->getCurrentAssessmentItemRef()->getHref();
         
        // retrive itemUri & itemPath.
        $parts = explode('|', $href);
         
        $definition =  new core_kernel_classes_Resource(INSTANCE_QTITEST_ITEMRUNNERSERVICE);
        $serviceCall = new tao_models_classes_service_ServiceCall($definition);
         
        $uriResource = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMURI);
        $uriParam = new tao_models_classes_service_ConstantParameter($uriResource, $parts[0]);
        $serviceCall->addInParameter($uriParam);
         
        $pathResource = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMPATH);
        $pathParam = new tao_models_classes_service_ConstantParameter($pathResource, $parts[1]);
        $serviceCall->addInParameter($pathParam);
        
        $dataPathResource = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMDATAPATH);
        $dataPathParam = new tao_models_classes_service_ConstantParameter($dataPathResource, $parts[2]);
        $serviceCall->addInParameter($dataPathParam);
         
        $parentServiceCallIdResource = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_QTITESTITEMRUNNER_PARENTCALLID);
        $parentServiceCallIdParam = new tao_models_classes_service_ConstantParameter($parentServiceCallIdResource, $session->getSessionId());
        $serviceCall->addInParameter($parentServiceCallIdParam);
         
        $testDefinitionResource = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_QTITEST_TESTDEFINITION);
        $testDefinitionParam = new tao_models_classes_service_ConstantParameter($testDefinitionResource, $testDefinitionUri);
        $serviceCall->addInParameter($testDefinitionParam);
         
        $testCompilationResource = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_QTITEST_TESTCOMPILATION);
        $testCompilationParam = new tao_models_classes_service_ConstantParameter($testCompilationResource, $testCompilationUri);
        $serviceCall->addInParameter($testCompilationParam);
         
        return $serviceCall;
    }
    
    /**
     * Build the Service Call ID of the current Assessment Item to be presented to a candidate
     * in a given Assessment Test $session.
     *
     * @return string A service call id composed of the session identifier,  the identifier of the item and its occurence number in the route.
     */
    static public function buildServiceCallId(AssessmentTestSession $session) {
        
	    $sessionId = $session->getSessionId();
	    $itemId = $session->getCurrentAssessmentItemRef()->getIdentifier();
	    $occurence = $session->getCurrentAssessmentItemRefOccurence();
	    return "${sessionId}.${itemId}.${occurence}";
    }
    
    /**
     * Whether or not the current Assessment Item to be presented to the candidate is timed-out. By timed-out
     * we mean:
     * 
     * * current Assessment Test level time limits are not respected OR,
     * * current Test Part level time limits are not respected OR,
     * * current Assessment Section level time limits are not respected OR,
     * * current Assessment Item level time limits are not respected.
     * 
     * @param AssessmentTestSession $session The AssessmentTestSession object you want to know it is timed-out.
     * @return boolean
     */
    static public function isTimeout(AssessmentTestSession $session) {
        
        try {
            $session->checkTimeLimits(false, true, false);
        }
        catch (AssessmentTestSessionException $e) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get the URI referencing the current Assessment Item (in the knowledge base)
     * to be presented to the candidate.
     * 
     * @param AssessmentTestSession $session An AssessmentTestSession object.
     * @return string A URI.
     */
    static public function getCurrentItemUri(AssessmentTestSession $session) {
        $href = $session->getCurrentAssessmentItemRef()->getHref();
        $parts = explode('|', $href);
        
        return $parts[0];
    }
    
    /**
     * Build the URL to be called to perform a given action on the Test Runner controller.
     * 
     * @param AssessmentTestSession $session An AssessmentTestSession object.
     * @param string $action The action name e.g. 'moveForward', 'moveBackward', 'skip', ... 
     * @param string $qtiTestDefinitionUri The URI of a reference to an Assessment Test definition in the knowledge base.
     * @param string $qtiTestCompilationUri The Uri of a reference to an Assessment Test compilation in the knowledge base.
     * @param string $standalone
     * @return string A URL to be called to perform an action.
     */
    static public function buildActionCallUrl(AssessmentTestSession $session, $action, $qtiTestDefinitionUri, $qtiTestCompilationUri, $standalone) {
        $url = BASE_URL . "TestRunner/${action}";
        $url.= '?QtiTestDefinition=' . urlencode($qtiTestDefinitionUri);
        $url.= '&QtiTestCompilation=' . urlencode($qtiTestCompilationUri);
        $url.= '&standalone=' . urlencode($standalone);
        $url.= '&serviceCallId=' . urlencode($session->getSessionId());
        return $url;
    }
    
    static public function buildServiceApi(AssessmentTestSession $session, $qtiTestDefinitionUri, $qtiTestCompilationUri) {
        $serviceCall = self::buildItemServiceCall($session, $qtiTestDefinitionUri, $qtiTestCompilationUri);         
        $itemServiceCallId = self::buildServiceCallId($session);
        return tao_helpers_ServiceJavascripts::getServiceApi($serviceCall, $itemServiceCallId);
    }
    
    /**
     * Tell the client to not cache the current request. Supports HTTP 1.0 to 1.1.
     */
    static public function noHttpClientCache() {
        // From stackOverflow: http://stackoverflow.com/questions/49547/making-sure-a-web-page-is-not-cached-across-all-browsers
        // license is Creative Commons Attribution Share Alike (author Edward Wilde)
        header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
        header('Pragma: no-cache'); // HTTP 1.0.
        header('Expires: 0'); // Proxies.
    }
    
    /**
     * Make the candidate interact with the current Assessment Item to be presented. A new attempt
     * will begin automatically if the candidate still has available attempts. Otherwise,
     * nothing happends.
     * 
     * @param AssessmentTestSession $session The AssessmentTestSession you want to make the candidate interact with.
     */
    static public function beginCandidateInteraction(AssessmentTestSession $session) {
        $itemSession = $session->getCurrentAssessmentItemSession();
        $itemSessionState = $itemSession->getState();
        
        $initial = $itemSessionState === AssessmentItemSessionState::INITIAL;
        $suspended = $itemSessionState === AssessmentItemSessionState::SUSPENDED;
        $remainingAttempts = $itemSession->getRemainingAttempts();
        $attemptable = $remainingAttempts === -1 || $remainingAttempts > 0;
        
        if ($initial === true || ($suspended === true && $attemptable === true)) {
            // Begin the very first attempt.
            $session->beginAttempt();
        }
        // Otherwise, the item is not attemptable bt the candidate.
    }
    
    /**
     * Whether or not the candidate taking the given $session is allowed
     * to skip the presented Assessment Item.
     * 
     * @param AssessmentTestSession $session A given AssessmentTestSession object.
     * @return boolean
     */
    static public function doesAllowSkipping(AssessmentTestSession $session) {
        $doesAllowSkipping = false;
        $navigationMode = $session->getCurrentNavigationMode();
        $submissionMode = $session->getCurrentSubmissionMode();
         
        $routeItem = $session->getRoute()->current();
        $routeControl = $routeItem->getItemSessionControl();
         
        if (empty($routeControl) === false) {
            $doesAllowSkipping = $routeControl->getItemSessionControl()->doesAllowSkipping();
        }
         
        return $doesAllowSkipping && $navigationMode === NavigationMode::LINEAR && $submissionMode === SubmissionMode::INDIVIDUAL;
    }
    
    /**
     * Whether or not the candidate taking the given $session is allowed to make
     * a comment on the presented Assessment Item.
     * 
     * @param AssessmentTestSession $session A given AssessmentTestSession object.
     * @return boolean
     */
    static public function doesAllowComment(AssessmentTestSession $session) {
        $doesAllowComment = false;
         
        $routeItem = $session->getRoute()->current();
        $routeControl = $routeItem->getItemSessionControl();
         
        if (empty($routeControl) === false) {
            $doesAllowComment = $routeControl->getItemSessionControl()->doesAllowComment();
        }
         
        return $doesAllowComment;
    }
    
    /**
     * Build an array where each cell represent a time constraint (a.k.a. time limits)
     * in force. Each cell is actually an array with two keys:
     * 
     * * 'source': The identifier of the QTI component emitting the constraint (e.g. AssessmentTest, TestPart, AssessmentSection, AssessmentItemRef).
     * * 'seconds': The number of remaining seconds until it times out.
     * 
     * @param AssessmentTestSession $session An AssessmentTestSession object.
     * @return array
     */
    static public function buildTimeConstraints(AssessmentTestSession $session) {
        $constraints = array();
        
        foreach ($session->getTimeConstraints() as $tc) {
            // Only consider time constraints in force.
            if ($tc->getMaximumRemainingTime() !== false) {
                $constraints[] = array(
                    'source' => $tc->getSource()->getIdentifier(),
                    'seconds' => $tc->getMaximumRemainingTime()->getSeconds(true),
                    'allowLateSubmission' => $tc->allowLateSubmission()
                );
            }
        }
        
        return $constraints;
    }
    
    /**
     * Build an array where each cell represent a possible Assessment Item a candidate
     * can jump on during a given $session. Each cell is an array with two keys:
     * 
     * * 'identifier': The identifier of the Assessment Item the candidate is allowed to jump on.
     * * 'position': The position in the route of the Assessment Item.
     * 
     * @param AssessmentTestSession $session A given AssessmentTestSession object.
     * @return array
     */
    static public function buildPossibleJumps(AssessmentTestSession $session) {
        $jumps = array();
         
        foreach ($session->getPossibleJumps() as $jumpObject) {
            $jump = array();
            $jump['identifier'] = $jumpObject->getTarget()->getAssessmentItemRef()->getIdentifier();
            $jump['position'] = $jumpObject->getPosition();
             
            $jumps[] = $jump;
        }
         
        return $jumps;
    }
    
    /**
     * Build the context of the given candidate test $session as an associative array. This array
     * is especially usefull to transmit the test context to a view as JSON data.
     * 
     * The returned array contains the following keys:
     * 
     * * state: The state of test session.
     * * navigationMode: The current navigation mode.
     * * submissionMode: The current submission mode.
     * * remainingAttempts: The number of remaining attempts for the current item.
     * * isAdaptive: Whether or not the current item is adaptive.
     * * itemIdentifier: The identifier of the current item.
     * * itemSessionState: The state of the current assessment item session.
     * * timeConstraints: The time constraints in force.
     * * testTitle: The title of the test.
     * * testPartId: The identifier of the current test part.
     * * sectionTitle: The title of the current section.
     * * numberItems: The total number of items eligible to the candidate.
     * * numberCompleted: The total number items considered to be completed by the candidate.
     * * moveForwardUrl: The URL to be dereferenced to perform a moveNext on the session.
     * * moveBackwardUrl: The URL to be dereferenced to perform a moveBack on the session.
     * * skipUrl: The URL to be dereferenced to perform a skip on the session.
     * * commentUrl: The URL to be dereferenced to leave a comment about the current item.
     * * timeoutUrl: The URL to be dereferenced when the time constraints in force reach their maximum.
     * * canMoveBackward: Whether or not the candidate is allowed/able to move backward.
     * * jumps: The possible jumpers the candidate is allowed to undertake among eligible items.
     * * itemServiceApiCall: The JavaScript code to be executed to instantiate the current item.
     * * rubrics: The XHTML compiled content of the rubric blocks to be displayed for the current item if any.
     * * allowComment: Whether or not the candidate is allowed to leave a comment about the current item.
     * * allowSkipping: Whether or not the candidate is allowed to skip the current item.
     * * considerProgress: Whether or not the test driver view must consider to give a test progress feedback.
     * 
     * @param AssessmentTestSession $session A given AssessmentTestSession object.
     * @param array $testMeta An associative array containing meta-data about the test definition taken by the candidate.
     * @param string $qtiTestDefinitionUri The URI of a reference to an Assessment Test definition in the knowledge base.
     * @param string $qtiTestCompilationUri The Uri of a reference to an Assessment Test compilation in the knowledge base.
     * @param string $standalone
     * @param string $compilationDirs An array containing respectively the private and public compilation directories.
     * @return array The context of the candidate session.
     */
    static public function buildAssessmentTestContext(AssessmentTestSession $session, array $testMeta, $qtiTestDefinitionUri, $qtiTestCompilationUri, $standalone, $compilationDirs) {
        $context = array();
         
        // The state of the test session.
        $context['state'] = $session->getState();
         
        // Default values for the test session context.
        $context['navigationMode'] = null;
        $context['submissionMode'] = null;
        $context['remainingAttempts'] = 0;
        $context['isAdaptive'] = false;
         
        if ($session->getState() === AssessmentTestSessionState::INTERACTING) {
            // The navigation mode.
            $context['navigationMode'] = $session->getCurrentNavigationMode();
        
            // The submission mode.
            $context['submissionMode'] = $session->getCurrentSubmissionMode();
        
            // The number of remaining attempts for the current item.
            $context['remainingAttempts'] = $session->getCurrentRemainingAttempts();
             
            // Whether or not the current step is time out.
            $context['isTimeout'] = self::isTimeout($session);
             
            // The identifier of the current item.
            $context['itemIdentifier'] = $session->getCurrentAssessmentItemRef()->getIdentifier();
             
            // The state of the current AssessmentTestSession.
            $context['itemSessionState'] = $session->getCurrentAssessmentItemSession()->getState();
        
            // Whether the current item is adaptive.
            $context['isAdaptive'] = $session->isCurrentAssessmentItemAdaptive();
            
            // Whether the current item is the very last one of the test.
            $context['isLast'] = $session->getRoute()->isLast();
             
            // Time constraints.
            $context['timeConstraints'] = self::buildTimeConstraints($session);
             
            // Test title.
            $context['testTitle'] = $session->getAssessmentTest()->getTitle();
             
            // Test Part title.
            $context['testPartId'] = $session->getCurrentTestPart()->getIdentifier();
             
            
            $context['sectionTitle'] = $session->getCurrentAssessmentSection()->getTitle();
             
            // Number of items composing the test session.
            $context['numberItems'] = $session->getRoute()->count();
             
            // Number of items completed during the test session.
            $context['numberCompleted'] = self::testCompletion($session);
            
            // Whether or not the progress of the test can be infered.
            $context['considerProgress'] = self::considerProgress($testMeta);
             
            // The URLs to be called to move forward/backward in the Assessment Test Session or skip or comment.
            $context['moveForwardUrl'] = self::buildActionCallUrl($session, 'moveForward', $qtiTestDefinitionUri , $qtiTestCompilationUri, $standalone);
            $context['moveBackwardUrl'] = self::buildActionCallUrl($session, 'moveBackward', $qtiTestDefinitionUri, $qtiTestCompilationUri, $standalone);
            $context['skipUrl'] = self::buildActionCallUrl($session, 'skip', $qtiTestDefinitionUri, $qtiTestCompilationUri, $standalone);
            $context['commentUrl'] = self::buildActionCallUrl($session, 'comment', $qtiTestDefinitionUri, $qtiTestCompilationUri, $standalone);
            $context['timeoutUrl'] = self::buildActionCallUrl($session, 'timeout', $qtiTestDefinitionUri, $qtiTestCompilationUri, $standalone);
             
            // If the candidate is allowed to move backward e.g. first item of the test.
            $context['canMoveBackward'] = $session->canMoveBackward();
             
            // The places in the test session where the candidate is allowed to jump to.
            $context['jumps'] = self::buildPossibleJumps($session);
        
            // The code to be executed to build the ServiceApi object to be injected in the QTI Item frame.
            $context['itemServiceApiCall'] = self::buildServiceApi($session, $qtiTestDefinitionUri, $qtiTestCompilationUri);
             
            // Rubric Blocks.
            $rubrics = array();
             
            // -- variables used in the included rubric block templates.
            // base path (base URI to be used for resource inclusion).
            $basePathVarName = TAOQTITEST_BASE_PATH_NAME;
            $$basePathVarName = $compilationDirs['public']->getPublicAccessUrl();
             
            // state name (the variable to access to get the state of the assessmentTestSession).
            $stateName = TAOQTITEST_RENDERING_STATE_NAME;
            $$stateName = $session;
             
            // views name (the variable to be accessed for the visibility of rubric blocks).
            $viewsName = TAOQTITEST_VIEWS_NAME;
            $$viewsName = array(View::CANDIDATE);
             
            foreach ($session->getRoute()->current()->getRubricBlockRefs() as $rubric) {
                ob_start();
                include($compilationDirs['private']->getPath() . $rubric->getHref());
                $rubrics[] = ob_get_clean();
            }
             
            $context['rubrics'] = $rubrics;
             
            // Comment allowed? Skipping allowed?
            $context['allowComment'] = self::doesAllowComment($session);
            $context['allowSkipping'] = self::doesAllowSkipping($session);
        }
        
        return $context;
    }
    
    /**
     * Compute the the number of completed items during a given
     * candidate test $session.
     * 
     * @param AssessmentTestSession $session
     * @return integer
     */
    static public function testCompletion(AssessmentTestSession $session) {
        $completed = $session->numberCompleted();
        
        if ($session->getCurrentNavigationMode() === NavigationMode::LINEAR && $completed > 0) {
            $completed--;
        }
        
        return $completed;
    }
    
    static public function considerProgress(array $testMeta) {
        $considerProgress = true;
        
        if ($testMeta['preConditions'] === true) {
            $considerProgress = false;
        }
        else if ($testMeta['branchRules'] === true) {
            $considerProgress = false;
        }
        
        return $considerProgress;
    }
}