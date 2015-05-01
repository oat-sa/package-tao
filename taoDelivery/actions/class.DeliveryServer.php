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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

use oat\oatbox\user\User;
/**
 * DeliveryServer Controller
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_actions_DeliveryServer extends tao_actions_CommonModule
{
    /**
     * @var taoDelivery_models_classes_execution_Service
     */
    private $executionService;
    
	/**
	 * constructor: initialize the service and the default data
	 * @return DeliveryServer
	 */
	public function __construct(){
		$this->service = taoDelivery_models_classes_DeliveryServerService::singleton();
		$this->executionService = taoDelivery_models_classes_execution_ServiceProxy::singleton();
	}
	
	/**
	 * @return taoDelivery_models_classes_execution_DeliveryExecution
	 */
	private function getCurrentDeliveryExecution() {
	    $id = tao_helpers_Uri::decode($this->getRequestParameter('deliveryExecution'));
	    return $this->executionService->getDeliveryExecution($id);
	}
		
	/**
     * Set a view with the list of process instances (both started or finished) and available process definitions
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param processDefinitionUri
     * @return void
     */
	public function index(){
		
		$label = common_session_SessionManager::getSession()->getUserLabel();
		$this->setData('login',$label);
		
		//get deliveries for the current user (set in groups extension)
		$user = common_session_SessionManager::getSession()->getUser();
		
        $this->setData('startedDeliveries', $this->service->getResumableDeliveries($user));
		
		$finishedDeliveries = is_null($user) ? array() : $this->executionService->getFinishedDeliveryExecutions($user->getIdentifier());
		$this->setData('finishedDeliveries', $finishedDeliveries);
		
		$deliveryData = array();
		if (!is_null($user)) {
		    $available = taoDelivery_models_classes_AssignmentService::singleton()->getAvailableDeliveries($user);
		    foreach ($available as $uri) {
		        $delivery = new core_kernel_classes_Resource($uri);
		        $deliveryData[] = $this->getDeliverySettings($delivery, $user);
		    }
		}
		
		$this->setData('availableDeliveries', $deliveryData);
		$this->setData('processViewData', array());
        $this->setData('client_config_url', $this->getClientConfigUrl());
		$this->setView('runtime/index.tpl');
	}
	
	public function initDeliveryExecution() {
	    $compiledDelivery = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
	    $user = common_session_SessionManager::getSession()->getUser();
	    if ($this->service->isDeliveryExecutionAllowed($compiledDelivery, $user)) {
	       $deliveryExecution = $this->executionService->initDeliveryExecution($compiledDelivery, $user->getIdentifier());
	    } else {
	        common_Logger::i('Testtaker '.$user->getIdentifier().' not authorised to initialise delivery '.$compiledDelivery->getUri());
	        return $this->returnError(__('You are no longer allowed to take the test %s', $compiledDelivery->getLabel()), true);
	    }
	    $this->redirect(_url('runDeliveryExecution', null, null, array('deliveryExecution' => $deliveryExecution->getIdentifier())));
	}

	public function runDeliveryExecution() {
	    $deliveryExecution = $this->getCurrentDeliveryExecution();
	    if ($deliveryExecution->getState()->getUri() != INSTANCE_DELIVERYEXEC_ACTIVE) {
	        $this->redirect($this->getReturnUrl());
	    }
	    
	    $userUri = common_session_SessionManager::getSession()->getUserUri();
	    if ($deliveryExecution->getUserIdentifier() != $userUri) {
	        throw new common_exception_Error('User '.$userUri.' is not the owner of the execution '.$deliveryExecution->getIdentifier());
	    }
	    
	    $compiledDelivery = $deliveryExecution->getDelivery();
	    $this->initResultServer($compiledDelivery, $deliveryExecution->getIdentifier());
	     
	    $runtime = taoDelivery_models_classes_DeliveryAssemblyService::singleton()->getRuntime($compiledDelivery);
	    $this->setData('serviceApi', tao_helpers_ServiceJavascripts::getServiceApi($runtime, $deliveryExecution->getIdentifier()));

	    $this->setData('userLabel', common_session_SessionManager::getSession()->getUserLabel());
	    $this->setData('deliveryExecution', $deliveryExecution->getIdentifier());
	    $this->setData('showControls', $this->showControls());
        $this->setData('client_config_url', $this->getClientConfigUrl());
        $this->setData('client_timeout', $this->getClientTimeout());
	    $this->setView('runtime/deliveryExecution.tpl', 'taoDelivery');
	}
	
	public function finishDeliveryExecution() {
	    $deliveryExecution = $this->getCurrentDeliveryExecution();
	    if ($deliveryExecution->getUserIdentifier() == common_session_SessionManager::getSession()->getUserUri()) {
	        $success = $deliveryExecution->setState(INSTANCE_DELIVERYEXEC_FINISHED);
	    } else {
	        common_Logger::w('Non owner '.common_session_SessionManager::getSession()->getUserUri().' tried to finish deliveryExecution '.$deliveryExecution->getIdentifier());
	        $success = false;
	    }
	    echo json_encode(array(
	        'success'      => $success,
	    	'destination'  => $this->getReturnUrl()
	    ));
	}
	
	/**
	 * intialize the result server using the delivery configuration and for this results session submission
	 * @param compiledDelviery
	 */
	protected function initResultServer($compiledDelivery, $executionIdentifier) {
	    $resultServerCallOverride =  $this->hasRequestParameter('resultServerCallOverride') ? $this->getRequestParameter('resultServerCallOverride') : false;
	    if (!($resultServerCallOverride)) {
	        $this->service->initResultServer($compiledDelivery, $executionIdentifier);
	    }
	}
	
	protected function showControls() {
	    return true;
	}
	
	protected function getReturnUrl() {
	    return _url('index', 'DeliveryServer', 'taoDelivery');
	}
	
	public function logout(){
	    common_session_SessionManager::endSession();
	    $this->redirect(ROOT_URL);
	}
	
	protected function getDeliverySettings(core_kernel_classes_Resource $delivery, User $user)
	{
	    $deliveryProps = $delivery->getPropertiesValues(array(
	        new core_kernel_classes_Property(TAO_DELIVERY_MAXEXEC_PROP),
	        new core_kernel_classes_Property(TAO_DELIVERY_START_PROP),
	        new core_kernel_classes_Property(TAO_DELIVERY_END_PROP),
	    ));
	
	    $propMaxExec = current($deliveryProps[TAO_DELIVERY_MAXEXEC_PROP]);
	    $propStartExec = current($deliveryProps[TAO_DELIVERY_START_PROP]);
	    $propEndExec = current($deliveryProps[TAO_DELIVERY_END_PROP]);
	    $executions = taoDelivery_models_classes_execution_ServiceProxy::singleton()->getUserExecutions($delivery, $user->getIdentifier());
	    $allowed = $this->service->isDeliveryExecutionAllowed($delivery, $user);
	
	    return array(
            "compiledDelivery"  => $delivery,
            "settingsDelivery"  => array (
    	        TAO_DELIVERY_MAXEXEC_PROP  => (!(is_object($propMaxExec)) or ($propMaxExec=="")) ? 0 : $propMaxExec->literal,
    	        TAO_DELIVERY_START_PROP    => (!(is_object($propStartExec)) or ($propStartExec=="")) ? null : $propStartExec->literal,
    	        TAO_DELIVERY_END_PROP      => (!(is_object($propEndExec)) or ($propEndExec=="")) ? null : $propEndExec->literal,
    	        "TAO_DELIVERY_USED_TOKENS" => count($executions),
    	        "TAO_DELIVERY_TAKABLE"     => $allowed
            )
	    );
	}
	
}
