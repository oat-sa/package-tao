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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

use qtism\runtime\tests\AssessmentItemSession;
use qtism\runtime\tests\AssessmentItemSessionState;
use qtism\runtime\tests\AssessmentTestSessionException;
use qtism\runtime\tests\AssessmentTestSessionFactory;
use qtism\data\AssessmentTest;
use qtism\data\storage\xml\XmlCompactAssessmentTestDocument;
use qtism\runtime\common\State;
use qtism\runtime\tests\AssessmentTestSessionState;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\runtime\tests\AssessmentItemSessionException;
use qtism\runtime\storage\common\AbstractStorage;
use qtism\data\SubmissionMode;

/**
 * Runs a QTI Test.
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @package taoQtiTest
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoQtiTest_actions_TestRunner extends tao_actions_ServiceModule {

    const ERROR_UNKNOWN = 0;
    
    const ERROR_TESTPART_TIME_OVERFLOW = 1;
    
    const ERROR_TESTPART_TIME_UNDERFLOW = 2;
    
    /**
     * The current AssessmentTestSession object.
     * 
     * @var AssessmentTestSession
     */
    private $testSession = null;
    
    /**
     * The current AssessmentTest definition object.
     * 
     * @var AssessmentTest
     */
    private $testDefinition = null;
    
    /**
     * The TAO Resource describing the test to be run.
     * 
     * @var core_kernel_classes_Resource
     */
    private $testResource = null;
    
    /**
     * The current AbstractStorage object.
     * 
     * @var AbstractStorage
     */
    private $storage = null;
    
    /**
     * Whether an attempt has begun during the request.
     * 
     * @var boolean
     */
    private $attemptBegun = false;
    
    /**
     * The error that occured during the current request.
     * 
     */
    private $currentError = -1;
    
    /**
     * Get the current assessment test session.
     * 
     * @return AssessmentTestSession An AssessmentTestSession object.
     */
    protected function getTestSession() {
        return $this->testSession;
    }
    
    /**
     * Set the current assessment test session.
     * 
     * @param AssessmentTestSession $testSession An AssessmentTestSession object.
     */
    protected function setTestSession(AssessmentTestSession $testSession) {
        $this->testSession = $testSession;
    }
    
    /**
     * Get the current test definition.
     * 
     * @return AssessmentTest An AssessmentTest object.
     */
    protected function getTestDefinition() {
        return $this->testDefinition;
    }
    
    /**
     * Set the current test defintion.
     * 
     * @param AssessmentTest $testDefinition An AssessmentTest object.
     */
    protected function setTestDefinition(AssessmentTest $testDefinition) {
        $this->testDefinition = $testDefinition;
    }
    
    /**
	 * Get the QtiSm AssessmentTestSession Storage Service.
	 * 
	 * @return AbstractStorage An AssessmentTestSession Storage Service.
	 */
	protected function getStorage() {
	    return $this->storage;    
	}
	
	/**
	 * Set the QtiSm AssessmentTestSession Storage Service.
	 * 
	 * @param AbstractStorage $storage An AssessmentTestSession Storage Service.
	 */
	protected function setStorage(AbstractStorage $storage) {
	    $this->storage = $storage;
	}
	
	/**
	 * Get the TAO Resource describing the test to be run.
	 * 
	 * @return core_kernel_classes_Resource A TAO Resource in database.
	 */
	protected function getTestResource() {
	    return $this->testResource;
	}
	
	/**
	 * Set the TAO Resource describing the test to be run.
	 * 
	 * @param core_kernel_classes_Resource $testResource A TAO Resource in database.
	 */
	protected function setTestResource(core_kernel_classes_Resource $testResource) {
	    $this->testResource = $testResource;
	}
	
	/**
	 * Set whether a new attempt begun during this request.
	 * 
	 * @param boolean $begun
	 */
	protected function setAttemptBegun($begun) {
	    $this->attemptBegun = true;
	}
	
	/**
	 * Whether a new attempt begun during this request.
	 * 
	 * @return boolean
	 */
	protected function hasAttemptBegun() {
	    return $this->attemptBegun;
	}
	
	/**
	 * Get the error that occured during the previous request.
	 * 
	 * @return integer
	 */
	protected function getPreviousError() {
	    $state = $this->getState();
	    if (empty($state)) {
	        return -1;
	    }
	    else {
	        $previousError = current(unpack('s', mb_substr($state, 28, 2, TAO_DEFAULT_ENCODING)));
	        return $previousError;
	    }
	}
	
	/**
	 * Set the error that occured during the current request.
	 * 
	 * @param integer $error
	 */
	protected function setCurrentError($currentError) {
	    $this->currentError = $currentError;
	}
	
	/**
	 * Get the error that occured during the current request.
	 * 
	 * @return integer
	 */
	protected function getCurrentError() {
	    return $this->currentError;
	}
	
    public function __construct() {
        parent::__construct();
        $this->retrieveTestResource();
        $this->retrieveTestDefinition();
        $resultServer = taoResultServer_models_classes_ResultServerStateFull::singleton();
        $testSessionFactory = new taoQtiTest_helpers_TestSessionFactory($this->getTestDefinition(), $resultServer, $this->getTestResource());
        $this->setStorage(new taoQtiTest_helpers_TestSessionStorage($testSessionFactory, $this));
        $this->retrieveTestSession();
    }
    
    protected function beforeAction() {
        
        // Do the required stuff
        // --- If the session has just been instantiated, begin the test session.
        $testSession = $this->getTestSession();
        
        if ($testSession->getState() === AssessmentTestSessionState::INITIAL) {
            // The test has just been instantiated.
            common_Logger::d("Beginning Assessment Test Session.");
            $testSession->beginTestSession();
        }
        
        if ($testSession->getState() === AssessmentTestSessionState::INTERACTING) {
            // Log current [itemId].[occurence].
            common_Logger::d("Current Route Item is '" . $testSession->getCurrentAssessmentItemRef()->getIdentifier() . "." . $testSession->getCurrentAssessmentItemRefOccurence() . "'");
            
            $itemSession = $testSession->getCurrentAssessmentItemSession();
            $itemSessionState = $itemSession->getState();
            
            if ($itemSessionState === AssessmentItemSessionState::INITIAL) {
                // Begin the very first attempt.
                $this->beginAttempt();
            }
            else if ($itemSessionState === AssessmentItemSessionState::SUSPENDED) {
                
                if ($itemSession->isAttempting() === true) {
                    // Re-interact with session if currently attempting.
                    common_Logger::i("Attempt on item '" . $this->buildServiceCallId() . "' recovered.");
                    $testSession->interactWithItemSession();
                }
                else {
                    // Begin a brand new attempt.
                    $this->beginAttempt();
                }
            }
        }  
    }
    
    protected function afterAction() {
        $this->persistTestSession();
    }
    
    protected function information() {
        $testSession = $this->getTestSession();
        
        $info = null;
        
        if ($testSession->getState() === AssessmentTestSessionState::INTERACTING) {
            
            $itemSession = $testSession->getCurrentAssessmentItemSession();
            
            // Not interacting, there is no more attempts possible.
            if ($itemSession->getState() !== AssessmentItemSessionState::INTERACTING) {
                $info = sprintf(__('No more attempts allowed for item "%s".'), $itemSession->getAssessmentItem()->getIdentifier());
            }
        }
        
        return $info;
    }
    
    /**
     * Main action of the TestRunner module.
     * 
     */
	public function index()
	{
	    $testSession = $this->getTestSession();
	    $testSession->updateDuration();
	    $this->beforeAction();

	    try {
	        // Maybe the current testPart max time is reached.
	        $testSession->checkTimeLimits();
	    }
	    catch (AssessmentTestSessionException $e) {
	        $this->registerAssessmentTestSessionException($e);
	        $testSession->moveNextTestPart();
	    }
	    
        // Prepare the AssessmentTestContext for the client.
        // The built data is availabe with get_data('assessmentTestContext').
        $this->buildAssessmentTestContext();
	    	    
	    $this->setView('test_runner.tpl');
	    
	    $this->afterAction();
	}
	
	/**
	 * Move forward in the Assessment Test Session flow.
	 * 
	 */
	public function moveForward() {
	    
	    $testSession = $this->getTestSession();
	    
	    try {
	        
	        if ($testSession->getState() === AssessmentTestSessionState::INTERACTING) {
	            
	            /*
	             * If the current item session is still interacting, we suspend it before moving forward.
	             */
	            $currentItemSession = $testSession->getCurrentAssessmentItemSession();
	            if ($currentItemSession->getState() === AssessmentItemSessionState::INTERACTING) {
	                $testSession->suspendItemSession();
	                common_Logger::i("Item session for '" . $this->buildServiceCallId() . "' suspended.");
	            }
	            
	            $testSession->updateDuration();
	            $testSession->moveNext();
	        }	        
	        
	        $this->beforeAction();
	    }
	    catch (AssessmentTestSessionException $e) {
	        common_Logger::i($e->getMessage());
	        $this->registerAssessmentTestSessionException($e);
	        $testSession->moveNextTestPart();
	    }
	    
	    $context = $this->buildAssessmentTestContext();
	    
	    if ($testSession->getState() === AssessmentTestSessionState::INTERACTING) {
	        $context['newItemUrl'] = $this->buildCurrentItemSrc();
	    }
	    
	    echo json_encode($context);
	    
	    $this->afterAction();
	}
	
	/**
	 * Move backward in the Assessment Test Session flow.
	 *
	 */
	public function moveBackward() {
	    
	    $testSession = $this->getTestSession();
	    
	    try {
	        /*
	         * If the current item session is still interacting, we close suspend it before
	         * going backward.
	         */
	        $currentItemSession = $testSession->getCurrentAssessmentItemSession();
	        if ($currentItemSession->getState() === AssessmentItemSessionState::INTERACTING) {
	            $testSession->suspendItemSession();
	            common_Logger::i("Item session for '" . $this->buildServiceCallId() . "' suspended.");
	        }
	        
	        $testSession->updateDuration();
	        $testSession->moveBack();
	        $this->beforeAction();
	    }
	    catch (AssessmentTestSessionException $e) {
	        common_Logger::i($e->getMessage());
	        $this->registerAssessmentTestSessionException($e);
	        $testSession->moveNextTestPart();
	    }
	     
	    $context = $this->buildAssessmentTestContext();
	     
	    if ($testSession->getState() === AssessmentTestSessionState::INTERACTING) {
	        $context['newItemUrl'] = $this->buildCurrentItemSrc();
	    }
	     
	    echo json_encode($context);
	     
	    $this->afterAction();
	}
	
	/**
	 * Skip the current item in the Assessment Test Session flow.
	 * 
	 */
	public function skip() {

	    $testSession = $this->getTestSession();
	    $testSession->updateDuration();
	    
	    try {
	        $testSession->skip();
	        $testSession->moveNext();
	         
	        $this->beforeAction();
	    }
	    catch (AssessmentTestSessionException $e) {
	        $this->registerAssessmentTestSessionException($e);
	        $testSession->moveNextTestPart();
	    }
	    
	    $context = $this->buildAssessmentTestContext();
	    
	    if ($testSession->getState() === AssessmentTestSessionState::INTERACTING) {
	        $context['newItemUrl'] = $this->buildCurrentItemSrc();
	    }
	     
	    echo json_encode($context);
	    
	    $this->afterAction();
	}
	
	/**
	 * Action called when a QTI Item embedded in a QTI Test submit responses.
	 * 
	 */
	public function storeItemVariableSet() {
	    
	    $this->getTestSession()->updateDuration();
	    $this->beforeAction();
	    
	    // --- Deal with provided responses.
	    $responses = new State();
	    if ($this->hasRequestParameter('responseVariables')) {

	        // Transform the values from the client-side in a QtiSm form.
	        foreach ($this->getRequestParameter('responseVariables') as $id => $val) {
	            if (empty($val) === false) {
	                $filler = new taoQtiCommon_helpers_VariableFiller($this->getTestSession()->getCurrentAssessmentItemRef());
	                
	                try {
	                    $var = $filler->fill($id, $val);
	                    $responses->setVariable($var);
	                }
	                catch (OutOfRangeException $e) {
	                    // The value could not be transformed, ignore it.
	                    // format the logger message.
	                    common_Logger::d("Could not convert client-side value for variable '${id}'.");
	                }
	            }
	        }
	    }
	    
	    $currentItem = $this->getTestSession()->getCurrentAssessmentItemRef();
	    $currentOccurence = $this->getTestSession()->getCurrentAssessmentItemRefOccurence();
	    $displayFeedback = $this->getTestSession()->getCurrentSubmissionMode() !== SubmissionMode::SIMULTANEOUS;
	    $stateOutput = new taoQtiCommon_helpers_StateOutput();
	    
	    try {
	        common_Logger::i('Responses sent from the client-side. The Response Processing will take place.');
	        $this->getTestSession()->endAttempt($responses);
	         
	        // Return the item session state to the client side.
	        $itemSession = $this->getTestSession()->getAssessmentItemSessionStore()->getAssessmentItemSession($currentItem, $currentOccurence);
	         
	        foreach ($itemSession->getAllVariables() as $var) {
	            $stateOutput->addVariable($var);
	        }
	    }
	    catch (AssessmentItemSessionException $e) {
	        $this->registerAssessmentItemSessionException($e);
	    }
	    catch (AssessmentTestSessionException $e) {
	        $this->registerAssessmentTestSessionException($e);
	        
	        if ($e->getCode() === AssessmentTestSessionException::TEST_PART_DURATION_OVERFLOW) {
	            $this->getTestSession()->moveNextTestPart();
	        }
	    }
	    
	    echo json_encode(array('success' => true, 'displayFeedback' => $displayFeedback, 'itemSession' => $stateOutput->getOutput()));
	    
	    $this->afterAction();
	}
	
	/**
	 * Retrieve the Test Definition the test session is built
	 * from as an AssessmentTest object.
	 * 
	 * @return AssessmentTest The AssessmentTest object the current test session is built from.
	 */
	protected function retrieveTestDefinition() {
	    $compilationResource = new core_kernel_file_File($this->getRequestParameter('QtiTestCompilation'));
	    $testFilePath = $compilationResource->getAbsolutePath();
	    
	    common_Logger::d("Loading QTI-XML file at '${testFilePath}'.");
	    $doc = new XmlCompactAssessmentTestDocument();
	    $doc->load($testFilePath);
	    
	    $this->setTestDefinition($doc);
	}
	
	/**
	 * Retrieve the current test session as an AssessmentTestSession object from
	 * persistent storage.
	 * 
	 */
	protected function retrieveTestSession() {
	    $qtiStorage = $this->getStorage();
	    $state = $this->getState();
	    
	    if (empty($state)) {
	        common_Logger::d("Instantiating QTI Assessment Test Session");
	        $this->setTestSession($qtiStorage->instantiate());
	    }
	    else {
	        $sessionId = $this->getSessionId();
	        common_Logger::d("Retrieving QTI Assessment Test Session '${sessionId}'");
	        $this->setTestSession($qtiStorage->retrieve($sessionId));
	    }
	}
	
	/**
	 * Retrieve the TAO Resource describing the test to be run.
	 * 
	 * @return core_kernel_classes_Resource A TAO Test Resource in database.
	 */
	protected function retrieveTestResource() {
	    $this->setTestResource(new core_kernel_classes_Resource($this->getRequestParameter('QtiTestDefinition')));
	}
	
	/**
	 * Persist the current assessment test session.
	 * 
	 * @throws RuntimeException If no assessment test session has started yet.
	 */
	protected function persistTestSession() {
	
	    $storage = $this->getStorage();
	    common_Logger::d("Persisting Assessment Test Session.");
	    try {
	        $storage->persist($this->getTestSession());
	    }
	    catch (Exception $e) {
	        throw $e->getPrevious();
	    }
	}
	
	/**
	 * Get the unique identifier of the assessment test session.
	 * 
	 * @return string|false A 28 characters long unique ID or false if the session has not started yet.
	 */
	protected function getSessionId() {
	    $state = $this->getState();
	    if (empty($state)) {
	        return false;
	    }
	    else {
	        $sessionId = mb_substr($state, 0, 28, TAO_DEFAULT_ENCODING);
	        return $sessionId;
	    }
	}
	
	/**
	 * Builds an associative array which describes the current AssessmentTestContext and set
	 * into the request data for a later use.
	 * 
	 * @return array The built AssessmentTestContext.
	 */
	protected function buildAssessmentTestContext() {
	    $session = $this->getTestSession();
	    $context = array();
	    
	    // The state of the test session.
	    $context['state'] = $session->getState();
	    
	    // Default values for the test session context.
	    $context['navigationMode'] = null;
	    $context['submissionMode'] = null;
	    $context['remainingAttempts'] = 0;
	    $context['isAdaptive'] = false;
	    $context['allowedToAttempt'] = false;
	    $context['testPartRemainingTime'] = null;
	    
	    if ($session->getState() === AssessmentTestSessionState::INTERACTING) {
	        // The navigation mode.
	        $context['navigationMode'] = $session->getCurrentNavigationMode();
	         
	        // The submission mode.
	        $context['submissionMode'] = $session->getCurrentSubmissionMode();
	         
	        // The number of remaining attempts for the current item.
	        $context['remainingAttempts'] = $session->getCurrentRemainingAttempts();
	        
	        // If an attempt was begun during the request.
	        $context['attemptBegun'] = $this->hasAttemptBegun();
	        
	        // The state of the current AssessmentTestSession.
	        $context['itemSessionState'] = $session->getCurrentAssessmentItemSession()->getState();
	             
	        // Whether the current item is adaptive.
	        $context['isAdaptive'] = $session->isCurrentAssessmentItemAdaptive();
	        
	        // The URLs to be called to move forward/backward in the Assessment Test Session or skip.
	        $context['moveForwardUrl'] = $this->buildActionCallUrl('moveForward');
	        $context['moveBackwardUrl'] = $this->buildActionCallUrl('moveBackward');
	        $context['skipUrl'] = $this->buildActionCallUrl('skip');
	        
	        // If the candidate is allowed to move backward e.g. first item of the test.
	        $context['canMoveBackward'] = $session->canMoveBackward();
	        
	        // The places in the test session where the candidate is allowed to jump to.
	        $context['jumps'] = $this->buildPossibleJumps();
	        
	        // Display information.
	        $context['info'] = $this->information();
	        
	        // Timings.
	        if (($remainingTimeTestPart = $session->getRemainingTimeTestPart()) !== null) {
	            $context['testPartRemainingTime'] = $session->getRemainingTimeTestPart()->getSeconds(true);
	        }
	        
	        
	        // The code to be executed to build the ServiceApi object to be injected in the QTI Item frame.
	        $context['itemServiceApiCall'] = $this->buildServiceApi();
	    }
	    
	    $this->setData('assessmentTestContext', $context);
	    return $context;
	}
	
	/**
	 * Begin an attempt on the current item.
	 * 
	 */
	protected function beginAttempt() {
	    common_Logger::i("Beginning attempt for item '" . $this->buildServiceCallId() .  "'.");
	    $this->getTestSession()->beginAttempt();
	    $this->setAttemptBegun(true);
	}
	
	/**
	 * Get the service call for the current item.
	 * 
	 * @return tao_models_classes_service_ServiceCall A ServiceCall object.
	 */
	protected function getItemServiceCall() {
	    $href = $this->getTestSession()->getCurrentAssessmentItemRef()->getHref();
	    
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
	    
	    $parentServiceCallIdResource = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_QTITESTITEMRUNNER_PARENTCALLID);
	    $parentServiceCallIdParam = new tao_models_classes_service_ConstantParameter($parentServiceCallIdResource, $this->getServiceCallId());
	    $serviceCall->addInParameter($parentServiceCallIdParam);
	    
	    $testDefinitionResource = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_QTITEST_TESTDEFINITION);
	    $testDefinitionParam = new tao_models_classes_service_ConstantParameter($testDefinitionResource, $this->getRequestParameter('QtiTestDefinition'));
	    $serviceCall->addInParameter($testDefinitionParam);
	    
	    $testCompilationResource = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_QTITEST_TESTCOMPILATION);
	    $testCompilationParam = new tao_models_classes_service_ConstantParameter($testCompilationResource, $this->getRequestParameter('QtiTestCompilation'));
	    $serviceCall->addInParameter($testCompilationParam);
	    
	    return $serviceCall;
	}
	
	/**
	 * Build the service call id for the current item.
	 * 
	 * @return string A service call id composed of the identifier of the item and its occurence number in the route.
	 */
	protected function buildServiceCallId() {
	    $testSession = $this->getTestSession();
	    $sessionId = $testSession->getSessionId();
	    $itemId = $testSession->getCurrentAssessmentItemRef()->getIdentifier();
	    $occurence = $testSession->getCurrentAssessmentItemRefOccurence();
	    return "${sessionId}.${itemId}.${occurence}";
	}
	
	/**
	 * Build the serviceApi call for the current item and store
	 * it in the request parameters with key 'itemServiceApi'.
	 */
	protected function buildServiceApi() {
	    $serviceCall = $this->getItemServiceCall();
	    $serviceCallId = $this->buildServiceCallId();
	    $call = tao_helpers_ServiceJavascripts::getServiceApi($serviceCall, $serviceCallId);
	    $this->setData('itemServiceApi', $call);
	    return $call;
	}
	
	protected function buildCurrentItemSrc() {
	    $href = $this->getTestSession()->getCurrentAssessmentItemRef()->getHref();
	    $parts = explode('|', $href);
	     
	    return $this->buildItemSrc($parts[0], $parts[1]);
	}
	
	protected function buildItemSrc($itemUri, $itemPath) {
	    $src = BASE_URL . 'ItemRunner/index?';
	    $src .= 'itemUri=' . urlencode($itemUri);
	    $src.= '&itemPath=' . urlencode($itemPath);
	    $src.= '&QtiTestParentServiceCallId=' . urlencode($this->getServiceCallId());
	    $src.= '&QtiTestDefinition=' . urlencode($this->getRequestParameter('QtiTestDefinition'));
	    $src.= '&QtiTestCompilation=' . urlencode($this->getRequestParameter('QtiTestCompilation'));
	    $src.= '&standalone=true';
	    $src.= '&serviceCallId=' . $this->buildServiceCallId();
	    
	    return $src;
	}
	
	protected function buildActionCallUrl($action) {
	    $url = BASE_URL . "TestRunner/${action}";
	    $url.= '?QtiTestDefinition=' . urlencode($this->getRequestParameter('QtiTestDefinition'));
	    $url.= '&QtiTestCompilation=' . urlencode($this->getRequestParameter('QtiTestCompilation'));
	    $url.= '&standalone=' . urlencode($this->getRequestParameter('standalone'));
	    $url.= '&serviceCallId=' . urlencode($this->getRequestParameter('serviceCallId'));
	    return $url;
	}
	
	protected function buildPossibleJumps() {
	    $jumps = array();
	    
	    foreach ($this->getTestSession()->getPossibleJumps() as $jumpObject) {
	        $jump = array();
	        $jump['assessmentItemRefIdentifier'] = $jumpObject->getAssessmentItemRef()->getIdentifier();
	        $jump['assessmentItemRefOccurence'] = $jumpObject->getOccurence();
	        
	        $jumps[] = $jump;
	    }
	    
	    return $jumps;
	}
	
	protected function registerAssessmentItemSessionException(AssessmentItemSessionException $e) {
	    switch ($e->getCode()) {
	        case AssessmentItemSessionException::ATTEMPTS_OVERFLOW:
	            
	        break;
	        
	        case AssessmentItemSessionException::DURATION_OVERFLOW:
	            
	        break;
	        
	        case AssessmentItemSessionException::DURATION_UNDERFLOW:
	            
	        break;
	        
	        case AssessmentItemSessionException::INVALID_RESPONSE:
	            
	        break;
	        
	        case AssessmentItemSessionException::RUNTIME_ERROR:
	            
	        break;
	        
	        case AssessmentItemSessionException::SKIPPING_FORBIDDEN:
	            
	        break;
	        
	        case AssessmentItemSessionException::STATE_VIOLATION:
	            
	        break;
	        
	        case AssessmentItemSessionException::UNKNOWN:
	            
	        break;
	        
	        default:
	            
	        break;
	    }
	}
	
	protected function registerAssessmentTestSessionException(AssessmentTestSessionException $e) {
	    switch ($e->getCode()) {
	        case AssessmentTestSessionException::ASSESSMENT_SECTION_DURATION_OVERFLOW:
	            
	        break;
	        
	        case AssessmentTestSessionException::FORBIDDEN_JUMP:
	            
	        break;
	        
	        case AssessmentTestSessionException::LOGIC_ERROR:
	            
	        break;
	        
	        case AssessmentTestSessionException::MISSING_RESPONSES:
	            
	        break;
	        
	        case AssessmentTestSessionException::NAVIGATION_MODE_VIOLATION:
	            
	        break;
	        
	        case AssessmentTestSessionException::OUTCOME_PROCESSING_ERROR:
	            
	        break;
	        
	        case AssessmentTestSessionException::RESPONSE_PROCESSING_ERROR:
	            
	        break;
	        
	        case AssessmentTestSessionException::RESULT_SUBMISSION_ERROR:
	            
	        break;
	        
	        case AssessmentTestSessionException::STATE_VIOLATION:
	            
	        break;
	        
	        case AssessmentTestSessionException::TEST_PART_DURATION_OVERFLOW:
	            $this->setCurrentError(self::ERROR_TESTPART_TIME_OVERFLOW);
	        break;
	        
	        case AssessmentTestSessionException::UNKNOWN:
	        default:
	            $this->setCurrentError(self::ERROR_UNKNOWN);
	        break;
	    }
	}
}
