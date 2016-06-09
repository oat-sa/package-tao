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
namespace oat\taoDelivery\controller;


use common_exception_Error;
use common_Logger;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\helper\Delivery as DeliveryHelper;
use taoDelivery_models_classes_DeliveryServerService;
use taoDelivery_models_classes_execution_ServiceProxy;
use common_session_SessionManager;
use oat\taoDelivery\model\AssignmentService;

/**
 * DeliveryServer Controller
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class DeliveryServer extends \tao_actions_CommonModule
{
    /**
     * @var taoDelivery_models_classes_execution_Service
     */
    private $executionService;

	/**
	 * constructor: initialize the service and the default data
	 * @return DeliveryServer
	 */
	public function __construct()
	{
		$this->service = $this->getServiceManager()->get(taoDelivery_models_classes_DeliveryServerService::CONFIG_ID);
		$this->executionService = taoDelivery_models_classes_execution_ServiceProxy::singleton();
	}
	
	/**
	 * @return taoDelivery_models_classes_execution_DeliveryExecution
	 */
	protected function getCurrentDeliveryExecution() {
	    $id = \tao_helpers_Uri::decode($this->getRequestParameter('deliveryExecution'));
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

		$user = common_session_SessionManager::getSession()->getUser();
		
		/**
		 * Retrieve resumable deliveries (via delivery execution)
		 */
		$resumableData = array();
		foreach ($this->service->getResumableDeliveries($user) as $de) {
		    $resumableData[] = DeliveryHelper::buildFromDeliveryExecution($de);
		}
		$this->setData('resumableDeliveries', $resumableData);
		
		$assignmentService= $this->getServiceManager()->get(AssignmentService::CONFIG_ID);
		
		$deliveryData = array();
		foreach ($assignmentService->getAssignments($user) as $delivery)
		{
		    $deliveryData[] = DeliveryHelper::buildFromAssembly($delivery, $user);
		}
		$this->setData('availableDeliveries', $deliveryData);
		
		/**
		 *  Require JS config
		 */
		$this->setData('client_config_url', $this->getClientConfigUrl());
		
		/**
		 * Header & footer info
		 */
		$this->setData('showControls', $this->showControls());
	    $this->setData('userLabel', common_session_SessionManager::getSession()->getUserLabel());
        
        /**
         * Layout template + real template inclusion
         */
        $this->setData('content-template', 'DeliveryServer/index.tpl');
        $this->setData('content-extension', 'taoDelivery');
        $this->setView('DeliveryServer/layout.tpl', 'taoDelivery');
	}
    
    /**
     * Init a delivery execution from the current delivery
     * 
     * @return core_kernel_classes_Resource
     */
    protected function _initDeliveryExecution() {
        $compiledDelivery = new \core_kernel_classes_Resource(\tao_helpers_Uri::decode($this->getRequestParameter('uri')));
	    $user = common_session_SessionManager::getSession()->getUser();
	    $assignmentService= $this->getServiceManager()->get(AssignmentService::CONFIG_ID);
	    if (!$assignmentService->isDeliveryExecutionAllowed($compiledDelivery->getUri(), $user)) {
	        throw new \common_exception_Unauthorized();
	    }
       return $this->executionService->initDeliveryExecution($compiledDelivery, $user->getIdentifier());
    }
    
    /**
     * Init the selected delivery execution and forward to the execution screen
     */
    public function initDeliveryExecution() {
        try {
    	    $deliveryExecution = $this->_initDeliveryExecution();
    	    $this->redirect(_url('runDeliveryExecution', null, null, array('deliveryExecution' => $deliveryExecution->getIdentifier())));
        } catch (\common_exception_Unauthorized $e) {
            return $this->returnError(__('You are no longer allowed to take this test'), true);
        }
	}
    
    /**
     * Displays the execution screen
     * 
     * @throws common_exception_Error
     */
	public function runDeliveryExecution() {
	    $deliveryExecution = $this->getCurrentDeliveryExecution();
	    if ($deliveryExecution->getState()->getUri() != DeliveryExecution::STATE_ACTIVE && $deliveryExecution->getState()->getUri() != DeliveryExecution::STATE_PAUSED) {
	        $this->redirect($this->getReturnUrl());
	    }
	    
	    $userUri = common_session_SessionManager::getSession()->getUserUri();
	    if ($deliveryExecution->getUserIdentifier() != $userUri) {
	        throw new common_exception_Error('User '.$userUri.' is not the owner of the execution '.$deliveryExecution->getIdentifier());
	    }
	    
	    $delivery = $deliveryExecution->getDelivery();
	    $this->initResultServer($delivery, $deliveryExecution->getIdentifier());

        /**
         * Use particular delivery container
         */
        $container = $this->service->getDeliveryContainer($deliveryExecution);

	    // Require JS config
        $container->setData('client_config_url', $this->getClientConfigUrl());
        $container->setData('client_timeout', $this->getClientTimeout());
        // Delivery params
        $container->setData('returnUrl', $this->getReturnUrl());
        $container->setData('finishUrl', $this->getfinishDeliveryExecutionUrl());
        
        $this->setData('additional-header', $container->getContainerHeader());
        $this->setData('container-body', $container->getContainerBody());
        
		
		/**
		 * Delivery header & footer info
		 */
	    $this->setData('userLabel', common_session_SessionManager::getSession()->getUserLabel());
	    $this->setData('showControls', $this->showControls());
        $this->setData('returnUrl', $this->getReturnUrl());
        
        /**
         * Layout template + real template inclusion
         */
        $this->setData('content-template', 'DeliveryServer/runDeliveryExecution.tpl');
        $this->setData('content-extension', 'taoDelivery');
        $this->setView('DeliveryServer/layout.tpl', 'taoDelivery');
	}
	
    /**
     * Finish the delivery execution
     */
	public function finishDeliveryExecution() {
	    $deliveryExecution = $this->getCurrentDeliveryExecution();
	    if ($deliveryExecution->getUserIdentifier() == common_session_SessionManager::getSession()->getUserUri()) {
	        $success = $deliveryExecution->setState(DeliveryExecution::STATE_FINISHIED);
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
	 * Initialize the result server using the delivery configuration and for this results session submission
     *
     * @param $compiledDelivery
     * @param $executionIdentifier
     */
    protected function initResultServer($compiledDelivery, $executionIdentifier)
    {
	    $resultServerCallOverride =  $this->hasRequestParameter('resultServerCallOverride') ? $this->getRequestParameter('resultServerCallOverride') : false;
	    if (!($resultServerCallOverride)) {
	        $this->service->initResultServer($compiledDelivery, $executionIdentifier);
	    }
	}
    
    /**
     * Defines if the top and bottom action menu should be displayed or not
     * 
     * @return boolean
     */
	protected function showControls()
	{
	    return true;
	}
	
    /**
     * Defines the returning URL in the top-right corner action menu
     * 
     * @return string
     */
	protected function getReturnUrl()
	{
	    return _url('index', 'DeliveryServer', 'taoDelivery');
	}
    
    /**
     * Defines the URL of the finish delivery execution action
     * 
     * @return string
     */
	protected function getfinishDeliveryExecutionUrl()
	{
	    return _url('finishDeliveryExecution');
	}

    public function logout()
    {
	    common_session_SessionManager::endSession();
	    $this->redirect(ROOT_URL);
	}
}
