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

/**
 * DeliveryServer Controller
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

class taoDelivery_actions_DeliveryServer extends tao_actions_CommonModule
{
    private $executionService;
    
	/**
	 * constructor: initialize the service and the default data
	 * @return DeliveryServer
	 */
	public function __construct(){
		if(!$this->_isAllowed()){
	        $this->redirect(tao_helpers_Uri::url('index', 'DeliveryServerAuthentification', 'taoDelivery', array('errorMessage' => urlencode(__('Access denied. Please renew your authentication!')))));
		}
		$this->service = taoDelivery_models_classes_DeliveryServerService::singleton();
		$this->executionService = taoDelivery_models_classes_DeliveryExecutionService::singleton();
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
		
		$label = core_kernel_classes_Session::singleton()->getUserLabel();
		$this->setData('login',$label);
		
		//get deliveries for the current user (set in groups extension)
		$userUri = core_kernel_classes_Session::singleton()->getUserUri();
		$started = is_null($userUri) ? array() : $this->executionService->getActiveDeliveryExecutions($userUri);
            
                //quickfix
                foreach ($started as $key=> $activeDelviery) {
                    $time = $this->executionService->getDeliveryExecutionStartTime($activeDelviery);
                    $started[$key]->time =  tao_helpers_Date::displayeDate($time->literal);
                }

                $this->setData('startedDeliveries', $started);
		
		$available = is_null($userUri) ? array() : $this->service->getAvailableDeliveries($userUri);
		$finishedDeliveries = is_null($userUri) ? array() : $this->executionService->getFinishedDeliveryExecutions($userUri);
		
		$this->setData('finishedDeliveries', $finishedDeliveries);
		$this->setData('availableDeliveries', $available);
		$this->setData('processViewData', array());
		$this->setView('runtime/index.tpl');
	}
	
	public function initDeliveryExecution() {
	    $compiledDelivery = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
	    $userUri = common_session_SessionManager::getSession()->getUserUri();
	    if ($this->service->isDeliveryExecutionAllowed($compiledDelivery, $userUri)) {
	       $deliveryExecution = $this->executionService->initDeliveryExecution($compiledDelivery, $userUri);
	    } else {
	        common_Logger::i('Testtaker '.$userUri.' not authorised to initialise delivery '.$compiledDelivery->getUri());
	        return $this->returnError(__('You are no longer allowed to take the test %s', $compiledDelivery->getLabel()), true);
	    }
	    $this->redirect(_url('runDeliveryExecution', null, null, array('uri' => $deliveryExecution->getUri())));
	}

	public function runDeliveryExecution() {
	    $deliveryExecution = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
	    $props = $deliveryExecution->getPropertiesValues(array(
	        PROPERTY_DELVIERYEXECUTION_DELIVERY,
	        PROPERTY_DELVIERYEXECUTION_STATUS,
	        PROPERTY_DELVIERYEXECUTION_SUBJECT
	    ));
	    
	    
	    $status = current($props[PROPERTY_DELVIERYEXECUTION_STATUS]);
	    if ($status->getUri() != INSTANCE_DELIVERYEXEC_ACTIVE) {
	        $this->redirect($this->getReturnUrl());
	    }
	    
	    $userUri = common_session_SessionManager::getSession()->getUserUri();
	    $deUser = current($props[PROPERTY_DELVIERYEXECUTION_SUBJECT]);
	    if ($deUser->getUri() != $userUri) {
	        throw new common_exception_Error('User '.$userUri.' is not the owner of the execution '.$deliveryExecution->getUri());
	    }
	    
	    $compiledDelivery = $deliveryExecution->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_DELIVERY));
	    $this->initResultServer($compiledDelivery, $deliveryExecution->getUri());
	     
	    $runtimeRes = taoDelivery_models_classes_CompilationService::singleton()->getRuntime($compiledDelivery);
	    $runtime = tao_models_classes_service_ServiceCall::fromResource($runtimeRes);
	    $this->setData('serviceApi', tao_helpers_ServiceJavascripts::getServiceApi($runtime, $deliveryExecution->getUri()));

	    $this->setData('userLabel', core_kernel_classes_Session::singleton()->getUserLabel());
	    $this->setData('deliveryExecution', $deliveryExecution->getUri());
	    $this->setData('showControls', $this->showControls());
	    $this->setView('runtime/deliveryExecution.tpl', 'taoDelivery');
	}
	
	public function finishDeliveryExecution() {
	    $deliveryExecution = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('deliveryExecution')));
	    $succcess = $this->executionService->finishDeliveryExecution($deliveryExecution);
	    echo json_encode(array(
	        'success'      => $succcess,
	    	'destination'  => $this->getReturnUrl()
	    ));
	}
	
	/**
	 * intialize the result server using the delivery configuration and for this results session submission
	 * @param compiledDelviery
	 */
	private function initResultServer($compiledDelivery, $executionIdentifier) {
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
	
	
}