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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */

use oat\taoQtiTest\models\runner\QtiRunnerService;
use oat\taoQtiTest\models\runner\QtiRunnerServiceContext;
use oat\taoQtiTest\models\runner\QtiRunnerClosedException;
use oat\taoQtiTest\models\runner\QtiRunnerPausedException;
use oat\taoQtiTest\models\event\TraceVariableStored;
use qtism\runtime\tests\AssessmentTestSessionState;
use \oat\taoTests\models\runner\CsrfToken;
use \oat\taoTests\models\runner\SessionCsrfToken;

/**
 * Class taoQtiTest_actions_Runner
 * 
 * Serves QTI implementation of the test runner
 */
class taoQtiTest_actions_Runner extends tao_actions_ServiceModule
{
    /**
     * The runner implementation
     * @var QtiRunnerService
     */
    protected $runnerService;

    /**
     * The current test session
     * @var QtiRunnerServiceContext
     */
    protected $serviceContext;

    /**
     * The anti-CSRF manager
     * @var CsrfToken;
     */
    protected $csrf;

    /**
     * taoQtiTest_actions_Runner constructor.
     */
    public function __construct()
    {
        $this->runnerService = $this->getServiceManager()->get(QtiRunnerService::CONFIG_ID);

        // Prevent anything to be cached by the client.
        taoQtiTest_helpers_TestRunnerUtils::noHttpClientCache();
    }

    /**
     * Gets the anti-CSRF manager
     * @return CsrfToken
     */
    protected function getCsrf()
    {
        if (!$this->csrf) {
            $this->csrf = new SessionCsrfToken('TEST_RUNNER');
        }
        return $this->csrf;
    }

    /**
     * @param $data
     * @param int [$httpStatus]
     * @param bool [$token]
     */
    protected function returnJson($data, $httpStatus = 200, $token = true)
    {
        // auto append the CSRF token to the result
        if ($token) {
            if (is_array($data)) {
                if ($data['success'] || $httpStatus != 403) {
                    $data['token'] = $this->getCsrf()->getCsrfToken();
                }
            } else if (is_object($data)) {
                if ($data->success || $httpStatus != 403) {
                    $data->token = $this->getCsrf()->getCsrfToken();
                }
            }
        }

        return parent::returnJson($data, $httpStatus);
    }

    /**
     * Gets the test service context
     * @param bool [$check] Checks the context after create. Default to true.
     * @param bool [$checkToken] Checks the security token.
     * @return QtiRunnerServiceContext
     * @throws \common_Exception
     */
    protected function getServiceContext($check = true, $checkToken = true)
    {
        if (!$this->serviceContext) {
            $testDefinition = $this->getRequestParameter('testDefinition');
            $testCompilation = $this->getRequestParameter('testCompilation');

            if ($checkToken) {

                $config = $this->runnerService->getTestConfig()->getConfigValue('security');
                if(isset($config['csrfToken']) && $config['csrfToken'] == true){

                    $csrfToken = $this->getRequestParameter('X-Auth-Token');
                    if (!$this->getCsrf()->checkCsrfToken($csrfToken)) {
                        \common_Logger::w("CSRF attempt! The token $csrfToken is no longer valid!");
                        throw new \common_exception_Unauthorized();
                    }
                }
            }

            if ($this->hasRequestParameter('testServiceCallId')) {
                $testExecution = $this->getRequestParameter('testServiceCallId');
            } else {
                $testExecution = $this->getRequestParameter('serviceCallId');
            }
            $this->serviceContext = $this->runnerService->getServiceContext($testDefinition, $testCompilation, $testExecution, $check);
        }

        return $this->serviceContext;
    }

    /**
     * Gets an error response object
     * @param Exception [$e] Optional exception from which extract the error context 
     * @return array
     */
    protected function getErrorResponse($e = null) {
        $response = [
            'success' => false,
            'type' => 'error',
        ];
        
        if ($e) {
            if ($e instanceof \Exception) {
                $response['type'] = 'exception';
                $response['code'] = $e->getCode();
            }

            if ($e instanceof \common_exception_UserReadableException) {
                $response['message'] = $e->getUserMessage();
            } else {
                $response['message'] = __('An error occurred!');
            }
            
            switch (true) {
                case $e instanceof QtiRunnerClosedException:
                case $e instanceof QtiRunnerPausedException:
                    $response['type'] = 'TestState';
                    break;
                
                case $e instanceof \tao_models_classes_FileNotFoundException:
                    $response['type'] = 'FileNotFound';
                    $response['message'] = __('File not found');
                    break;

                case $e instanceof \common_exception_Unauthorized:
                    $response['code'] = 403;
                    break;
            }
        }
        
        return $response;
    }

    /**
     * Gets an HTTP response code
     * @param Exception [$e] Optional exception from which extract the error context
     * @return int
     */
    protected function getErrorCode($e = null) {
        $code = 200;
        if ($e) {
            $code = 500;

            switch (true) {
                case $e instanceof QtiRunnerClosedException:
                case $e instanceof QtiRunnerPausedException:
                    $code = 200;
                    break;

                case $e instanceof \common_exception_NotImplemented:
                case $e instanceof \common_exception_NoImplementation:
                case $e instanceof \common_exception_Unauthorized:
                    $code = 403;
                    break;
                
                case $e instanceof \tao_models_classes_FileNotFoundException:
                    $code = 404;
                    break;
            }
        }
        return $code;
    }

    /**
     * Gets the state identifier for the current itemRef
     * @return string
     * @throws QtiRunnerClosedException
     * @throws common_exception_Unauthorized
     */
    protected function getStateId()
    {
        $serviceContext = $this->getServiceContext();
        $serviceCallId = $serviceContext->getTestExecutionUri();
        $testSession = $serviceContext->getTestSession();
        $itemRef = $testSession->getCurrentAssessmentItemRef();

        if ($itemRef instanceof \qtism\data\AssessmentItemRef) {
            return $serviceCallId . $itemRef->getIdentifier();
        }

        throw new QtiRunnerClosedException();
    }

    /**
     * Initializes the delivery session
     */
    public function init()
    {
        $code = 200;
        
        try {
            $this->getCsrf()->revokeCsrfToken();
            $serviceContext = $this->getServiceContext();
            $result = $this->runnerService->init($serviceContext);

            $response = [
                'success' => $result,
            ];

            if ($result) {
                $response['testData'] = $this->runnerService->getTestData($serviceContext);
                $response['testContext'] = $this->runnerService->getTestContext($serviceContext);
                $response['testMap'] = $this->runnerService->getTestMap($serviceContext);
            }
            
            $this->runnerService->persist($serviceContext);
        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }
        

        $this->returnJson($response, $code);
    }

    /**
     * Provides the test definition data
     */
    public function getTestData()
    {
        $code = 200;
        
        try {
            $serviceContext = $this->getServiceContext();
            
            $response = [
                'testData' => $this->runnerService->getTestData($serviceContext),
                'success' => true,
            ];
            
        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }

    /**
     * Provides the test context object
     */
    public function getTestContext()
    {
        $code = 200;
        
        try {
            $serviceContext = $this->getServiceContext();
            
            $response = [
                'testContext' => $this->runnerService->getTestContext($serviceContext),
                'success' => true,
            ];
            
        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }
    
    /**
     * Provides the map of the test items
     */
    public function getTestMap()
    {
        $code = 200;
        
        try {
            $serviceContext = $this->getServiceContext();
            
            $response = [
                'testMap' => $this->runnerService->getTestMap($serviceContext),
                'success' => true,
            ];
            
        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }
    
    /**
     * Provides the definition data and the state for a particular item
     */
    public function getItem()
    {
        $code = 200;
        
        $itemRef = $this->getRequestParameter('itemDefinition');
        
        try {
            $serviceContext = $this->getServiceContext();

            $stateId = $this->getStateId();
            $itemState = $this->runnerService->getItemState($serviceContext, $stateId);
            if (!count($itemState)) {
                $itemState = new stdClass();
            }

            // TODO: make a better implementation
            // As the rubric blocs are nested at the section level, it could be interesting to send these
            // rubric blocs only once per section
            if (count($serviceContext->getTestSession()->getRoute()->current()->getRubricBlockRefs())) {
                $rubrics = $this->runnerService->getRubrics($serviceContext);
            } else {
                $rubrics = null;
            }
            
            $itemData = $this->runnerService->getItemData($serviceContext, $itemRef);
            $baseUrl = $this->runnerService->getItemPublicUrl($serviceContext, $itemRef);
            if (is_string($itemData)) {
                $response = '{' .
                    '"success":true,' .
                    '"token":"' . $this->getCsrf()->getCsrfToken() . '",' .
                    '"baseUrl":"'.$baseUrl.'",' .
                    '"itemData":' . $itemData . ',' .
                    '"itemState":' . json_encode($itemState) . ',' .
                    '"rubrics":' . json_encode($rubrics) .
                '}';
            } else {
                $response = [
                    'success' => true,
                    'itemData' => $itemData,
                    'itemState' => $itemState,
                    'baseUrl' => $baseUrl,
                    'rubrics' => $rubrics
                ];
            }

            $this->runnerService->startTimer($serviceContext);
            
        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }
        if (is_string($response)) {
            header(HTTPToolkit::statusCodeHeader($code));
            Context::getInstance()->getResponse()->setContentHeader('application/json');
            echo $response;
        } else {
            $this->returnJson($response, $code);
        }
    }

    /**
     * Stores the state object and the response set of a particular item
     */
    public function submitItem()
    {
        $code = 200;

        $itemRef = $this->getRequestParameter('itemDefinition');
        $itemDuration = $this->getRequestParameter('itemDuration');

        $data = \taoQtiCommon_helpers_Utils::readJsonPayload();
        if (isset($data['itemDuration'])) {
            $itemDuration = $data['itemDuration'];
        }

        $state = isset($data['itemState']) ? $data['itemState'] : new stdClass();
        $itemResponse = isset($data['itemResponse']) ? $data['itemResponse'] : [];

        try {
            $serviceContext = $this->getServiceContext(false);
            $this->runnerService->endTimer($serviceContext, $itemDuration);
            
            $stateId = $this->getStateId();
            if ($serviceContext->getTestSession()->getState() == AssessmentTestSessionState::CLOSED) {
                throw new QtiRunnerClosedException();
            }
            $storeResponse = true;

            try {
                // do not allow to store the response if the session is in a wrong state
                $this->runnerService->check($serviceContext);
            } catch (QtiRunnerPausedException $e) {
                // allow to store the state but prevent to store the response
                $storeResponse = false;
                \common_Logger::i('Store item state after a test session pause');
            }

            $successState = $this->runnerService->setItemState($serviceContext, $stateId, $state);

            if ($storeResponse) {
                $successResponse = $this->runnerService->storeItemResponse($serviceContext, $itemRef, $itemResponse);
                $displayFeedback = $this->runnerService->displayFeedbacks($serviceContext);
            } else {
                $successResponse = true;
                $displayFeedback = false;
            }

            $response = [
                'success' => $successState && $successResponse,
                'displayFeedbacks' => $displayFeedback,
            ];

            if ($displayFeedback == true) {
                //FIXME there is here a performance issue, at the end we need the defitions only once, not at each storage
                $response['feedbacks']   = $this->runnerService->getFeedbacks($serviceContext, $itemRef);
                $response['itemSession'] = $this->runnerService->getItemSession($serviceContext);
            }

            $this->runnerService->persist($serviceContext);

        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }

    /**
     * Moves the current position to the provided scoped reference: item, section, part
     */
    public function move()
    {
        $code = 200;
        
        $ref = $this->getRequestParameter('ref');
        $direction = $this->getRequestParameter('direction');
        $scope = $this->getRequestParameter('scope');

        try {
            $serviceContext = $this->getServiceContext();
            $result = $this->runnerService->move($serviceContext, $direction, $scope, $ref);
            
            $response = [
                'success' => $result,
            ];

            if ($result) {
                $response['testContext'] = $this->runnerService->getTestContext($serviceContext);
            }
            
            $this->runnerService->persist($serviceContext);
            
        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }
    
    /**
     * Skip the current position to the provided scope: item, section, part
     */
    public function skip()
    {
        $code = 200;
        
        $ref = $this->getRequestParameter('ref');
        $scope = $this->getRequestParameter('scope');
        $itemDuration = $this->getRequestParameter('itemDuration');

        try {
            $serviceContext = $this->getServiceContext();
            $this->runnerService->endTimer($serviceContext, $itemDuration);
            
            $result = $this->runnerService->skip($serviceContext, $scope, $ref);

            $response = [
                'success' => $result,
            ];

            if ($result) {
                $response['testContext'] = $this->runnerService->getTestContext($serviceContext);
            }

            $this->runnerService->persist($serviceContext);
            
        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }
    
    /**
     * Handles a test timeout
     */
    public function timeout()
    {
        $code = 200;
        
        $ref = $this->getRequestParameter('ref');
        $scope = $this->getRequestParameter('scope');

        try {
            $serviceContext = $this->getServiceContext();
            $result = $this->runnerService->timeout($serviceContext, $scope, $ref);

            $response = [
                'success' => $result,
            ];

            if ($result) {
                $response['testContext'] = $this->runnerService->getTestContext($serviceContext);
            }

            $this->runnerService->persist($serviceContext);
            
        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }
    
    /**
     * Exits the test before its end
     */
    public function exitTest()
    {
        $code = 200;
        
        try {
            $serviceContext = $this->getServiceContext();
            
            $response = [
                'success' => $this->runnerService->exitTest($serviceContext),
            ];
            
            $this->runnerService->persist($serviceContext);
            
        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }
    
    /**
     * Finishes the test
     */
    public function finish()
    {
        try {
            $serviceContext = $this->getServiceContext(false);
            
            $response = [
                'success' => $this->runnerService->finish($serviceContext),
            ];
        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
        }

        $this->returnJson($response);
    }

    /**
     * Sets the test in paused state
     */
    public function pause()
    {
        $code = 200;
        
        try {
            $serviceContext = $this->getServiceContext();
            
            $response = [
                'success' => $this->runnerService->pause($serviceContext),
            ];
            
            $this->runnerService->persist($serviceContext);
            
        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }

    /**
     * Resumes the test from paused state
     */
    public function resume()
    {
        $code = 200;
        
        try {
            $serviceContext = $this->getServiceContext();
            $result = $this->runnerService->resume($serviceContext);

            $response = [
                'success' => $result,
            ];

            if ($result) {
                $response['testContext'] = $this->runnerService->getTestContext($serviceContext);
            }

            $this->runnerService->persist($serviceContext);
            
        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }

    /**
     * Flag an item
     */
    public function flagItem()
    {
        $code = 200;

        try {
            $serviceContext = $this->getServiceContext();
            $testSession = $serviceContext->getTestSession();
            
            if ($this->hasRequestParameter('position')) {
                $itemPosition = intval($this->getRequestParameter('position'));
            } else {
                $itemPosition = $testSession->getRoute()->getPosition();
            }
            
            if ($this->hasRequestParameter('flag')) {
                $flag = $this->getRequestParameter('flag');
                if (is_numeric($flag)) {
                    $flag = !!(intval($flag));
                } else {
                    $flag = 'false' != strtolower($flag);
                }
            } else {
                $flag = true;
            }
            
            taoQtiTest_helpers_TestRunnerUtils::setItemFlag($testSession, $itemPosition, $flag);
            
            $response = [
                'success' => true,
            ];
            
        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }
    
    /**
     * Comment the test
     */
    public function comment()
    {
        $code = 200;

        $comment = $this->getRequestParameter('comment');
        
        try {
            $serviceContext = $this->getServiceContext();
            $result = $this->runnerService->comment($serviceContext, $comment);

            $response = [
                'success' => $result,
            ];

        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }

    /**
     * allow client to store information about the test, the section or the item
     */
    public function storeTraceData(){
        $code = 200;

        $itemRef = ($this->hasRequestParameter('itemDefinition'))?$this->getRequestParameter('itemDefinition'): null;

        $traceData = json_decode(html_entity_decode($this->getRequestParameter('traceData')), true);

        try {
            $serviceContext = $this->getServiceContext(false);
            $stored = 0;
            $size   = count($traceData);

            foreach($traceData  as $variableIdentifier => $variableValue){
                if($this->runnerService->storeTraceVariable($serviceContext, $itemRef, $variableIdentifier, json_encode($variableValue))){
                    $stored++;
                }
            }

            $response = [
                'success' => $stored == $size
            ];
            common_Logger::d("Stored {$stored}/{$size} trace variables");
            $eventManager = $this->getServiceManager()->get(\oat\oatbox\event\EventManager::CONFIG_ID);
            $event = new TraceVariableStored($serviceContext->getTestSession()->getSessionId());
            $eventManager->trigger($event);

        } catch (common_Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }
}
