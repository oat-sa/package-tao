<?php
/*
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; under version 2 of the License (non-upgradable). This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA. Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2); 2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER); 2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 */

/**
 * Delivery Controller provide actions performed from url resolution
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @license GPLv2 http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_actions_DeliveryTemplate extends tao_actions_SaSModule
{

    /**
     * constructor: initialize the service and the default data
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return Delivery
     */
    public function __construct()
    {
        parent::__construct();
        
        // the service is initialized by default
        $this->service = taoDelivery_models_classes_DeliveryTemplateService::singleton();
        $this->defaultData();
    }

    /**
     * (non-PHPdoc)
     * @see tao_actions_SaSModule::getClassService()
     */
    protected function getClassService()
    {
        return $this->service;
    }
    
    /*
     * controller actions
    */
    /**
     * Render json data to populate the delivery tree
     * 'modelType' must be in the request parameters
     *
     * @return void
     */
    public function getDeliveries()
    {
        if (! tao_helpers_Request::isAjax()) {
            throw new Exception("wrong request mode");
        }
        $options = array(
            'subclasses' => true,
            'instances' => true,
            'highlightUri' => '',
            'labelFilter' => '',
            'chunk' => false
        );
        if ($this->hasRequestParameter('filter')) {
            $options['labelFilter'] = $this->getRequestParameter('filter');
        }
        if($this->hasRequestParameter("selected")){
            $options['browse'] = array($this->getRequestParameter("selected"));
        }
        if ($this->hasRequestParameter('classUri')) {
            $clazz = $this->getCurrentClass();
            $options['chunk'] = true;
        } else {
            $clazz = $this->getRootClass();
        }
    
        echo json_encode($this->service->toTree($clazz, $options));
    }
    
    public function listDeliveries()
    {
        $this->setData('class', $this->getCurrentClass());
        $deliveries = $this->getCurrentClass()->getInstances();
        $this->setData('deliveries', $deliveries);
        $this->setView('delivery_list.tpl');
    }
    
    /**
     * Edit a delivery class
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return void
     */
    public function editDeliveryClass()
    {
        $clazz = $this->getCurrentClass();
        
        if ($this->hasRequestParameter('property_mode')) {
            $this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
        }
        
        $myForm = $this->editClass($clazz, $this->service->getRootClass());
        if ($myForm->isSubmited()) {
            if ($myForm->isValid()) {
                if ($clazz instanceof core_kernel_classes_Resource) {
                    $this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->getUri()));
                }
                $this->setData('message', __('Delivery Class saved'));
                $this->setData('reload', true);
            }
        }
        $this->setData('formTitle', __('Edit delivery class'));
        $this->setData('myForm', $myForm->render());
        $this->setView('form.tpl');
    }

    /**
     * Edit a delviery instance
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return void
     */
    public function editDelivery()
    {
        $clazz = $this->getCurrentClass();
        $delivery = $this->getCurrentInstance();
        
        $formContainer = new taoDelivery_actions_form_Delivery($clazz, $delivery);
        $myForm = $formContainer->getForm();
        
        $myForm->evaluate();
        
        if ($myForm->isSubmited()) {
            if ($myForm->isValid()) {
                $propertyValues = $myForm->getValues();
                
                // then save the property values as usual
                $binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($delivery);
                $delivery = $binder->bind($propertyValues);
                
                // edit process label:
                $this->service->onChangeLabel($delivery);
                
                $this->setData('message', __('Delivery saved'));
                $this->setData('reload', true);
            }
        }
        $this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($delivery->getUri()));

        $this->setData('contentForm', $this->getContentForm());
        
        $this->setData('uri', tao_helpers_Uri::encode($delivery->getUri()));
        $this->setData('classUri', tao_helpers_Uri::encode($clazz->getUri()));
        
        $this->setData('hasContent', !is_null($this->service->getContent($delivery)));
        
        $this->setData('formTitle', __('Delivery properties'));
        $this->setData('myForm', $myForm->render());
        
        if (common_ext_ExtensionsManager::singleton()->isEnabled('taoCampaign')) {
            $this->setData('campaign', taoCampaign_helpers_Campaign::renderCampaignTree($delivery));
        }
        $this->setView('form_template.tpl');
    }

    /**
     * 
     */
    protected function getContentForm()
    {
        $delivery = $this->getCurrentInstance();
        $content = $this->service->getContent($delivery);
        if (!is_null($content)) {
            // Author
            $modelImpl = $this->service->getImplementationByContent($content);
            return $modelImpl->getAuthoring($content);
        } else {
            // select Model
            $options = array();
            foreach ($this->service->getAllContentClasses() as $class) {
                $options[$class->getUri()] = $class->getLabel();
            }
            $renderer = new Renderer(DIR_VIEWS.'templates'.DIRECTORY_SEPARATOR.'form_content.tpl');
            $renderer->setData('models', $options);
            $renderer->setData('saveUrl', _url('setContentClass', null, null, array('uri' => $delivery->getUri())));
            return $renderer->render();
        }
    }

    /**
     * Set the model to use for the delivery
     */
    public function setContentClass()
    {
        $delivery = $this->getCurrentInstance();
        $contentClass = new core_kernel_classes_Class($this->getRequestParameter('model'));
        
        if (is_null($this->service->getContent($delivery))) {
            $content = $this->service->createContent($delivery, $contentClass);
            $success = true;
        } else {
            common_Logger::w('Content already defined, cannot be replaced');
            $success = false;
        }
        echo json_encode(array(
            'success' => $success
        ));
    }
    
    /**
     * Add a delivery instance
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return void
     */
    public function addDelivery()
    {
        if (! tao_helpers_Request::isAjax()) {
            throw new Exception("wrong request mode");
        }
        $clazz = $this->getCurrentClass();
        $delivery = $this->service->createInstance($clazz, $this->service->createUniqueLabel($clazz));
        
        if (! is_null($delivery) && $delivery instanceof core_kernel_classes_Resource) {
            
            echo json_encode(array(
                'label' => $delivery->getLabel(),
                'uri' => tao_helpers_Uri::encode($delivery->getUri())
            ));
        }
    }

    /**
     * Delete a delivery or a delivery class
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return void
     */
    public function delete()
    {
        if (! tao_helpers_Request::isAjax()) {
            throw new Exception("wrong request mode");
        }
        
        $deleted = false;
        if ($this->getRequestParameter('uri')) {
            $deleted = $this->service->deleteInstance($this->getCurrentInstance());
        } else {
            $deleted = $this->service->deleteDeliveryClass($this->getCurrentClass());
        }
        
        echo json_encode(array(
            'deleted' => $deleted
        ));
    }

    /**
     * Get the data to populate the tree of delivery's subjects
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return void
     */
    public function getSubjects()
    {
        if (! tao_helpers_Request::isAjax()) {
            throw new Exception("wrong request mode");
        }
        $options = array(
            'chunk' => false
        );
        if ($this->hasRequestParameter('classUri')) {
            $clazz = $this->getCurrentClass();
            $options['chunk'] = true;
        } else {
            $clazz = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);
        }
        if ($this->hasRequestParameter('selected')) {
            $selected = $this->getRequestParameter('selected');
            if (! is_array($selected)) {
                $selected = array(
                    $selected
                );
            }
            $options['browse'] = $selected;
        }
        if ($this->hasRequestParameter('offset')) {
            $options['offset'] = $this->getRequestParameter('offset');
        }
        if ($this->hasRequestParameter('limit')) {
            $options['limit'] = $this->getRequestParameter('limit');
        }
        if ($this->hasRequestParameter('subclasses')) {
            $options['subclasses'] = $this->getRequestParameter('subclasses');
        }
        echo json_encode($this->service->toTree($clazz, $options));
    }

    /**
     * Main action
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return void
     */
    public function index()
    {
        
        $this->setView('index.tpl');
    }

    /**
     * create history table
     * 
     * @return void
     */
    public function viewHistory()
    {
        $_SESSION['instances'] = array();
        foreach ($this->getRequestParameters() as $key => $value) {
            if (preg_match("/^uri_[0-9]+$/", $key)) {
                $_SESSION['instances'][tao_helpers_Uri::decode($value)] = tao_helpers_Uri::decode($value);
            }
        }
        $this->setView("create_table.tpl");
    }

    /**
     * historyListing returns the execution history related to a given delivery (and subject)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param
     *            string deliveryUri
     * @param
     *            string subjectUri
     * @return array
     */
    public function getDeliveryHistory($delivery = null, $subject = null)
    {
        $returnValue = array();
        $histories = array();
        
        // check deliveryUri validity
        if (empty($delivery)) {
            $delivery = $this->getCurrentInstance();
        }
        
        $histories = $this->service->getHistory($delivery, $subject);
        
        $propHistorySubject = new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_SUBJECT_PROP);
        $propHistoryTimestamp = new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_TIMESTAMP_PROP);
        $i = 0;
        foreach ($histories as $history) {
            
            $returnValue[$i] = array();
            
            $subject = $history->getUniquePropertyValue($propHistorySubject);
            $returnValue[$i]["subject"] = $subject->getLabel(); // or $subject->literal to get the uri
            
            $timestamp = $history->getUniquePropertyValue($propHistoryTimestamp);
            $returnValue[$i]["time"] = date('d-m-Y G:i:s \(T\)', $timestamp->literal);
            
            $returnValue[$i]["uri"] = tao_helpers_Uri::encode($history->getUri());
            $i ++;
        }
        
        return $returnValue;
    }

    /**
     * provide the user list data via json
     * 
     * @return void
     */
    public function historyData()
    {
        
        // $page = $this->getRequestParameter('page');
        // $limit = $this->getRequestParameter('rows');
        $page = 1;
        $limit = 500;
        // $sidx = $this->getRequestParameter('sidx');
        // $sord = $this->getRequestParameter('sord');
        $start = $limit * $page - $limit;
        
        // if(!$sidx) $sidx =1;
        
        // $users = $this->userService->getAllUsers(array(
        // 'order' => $sidx,
        // 'orderDir' => $sord,
        // 'start' => $start,
        // 'end' => $limit
        // ));
        $histories = $this->getDeliveryHistory($this->getCurrentInstance());
        
        $count = count($histories);
        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) {
            $page = $total_pages;
        }
        
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $i = 0;
        
        foreach ($histories as $history) {
            $cellData = array();
            $cellData[0] = $history['subject'];
            $cellData[1] = $history['time'];
            $cellData[2] = '';
            
            $response->rows[$i]['id'] = tao_helpers_Uri::encode($history['uri']);
            $response->rows[$i]['cell'] = $cellData;
            $i ++;
        }
        
        echo json_encode($response);
    }

    public function deleteHistory()
    {
        $deleted = false;
        $message = __('An error occured during history deletion');
        if ($this->hasRequestParameter('historyUri')) {
            $history = new core_kernel_classes_Resource(tao_helpers_Uri::decode(tao_helpers_Uri::decode($this->getRequestParameter('historyUri'))));
            if ($this->service->deleteHistory($history)) {
                $deleted = true;
                $message = __('History deleted successfully');
            }
        }
        
        echo json_encode(array(
            'deleted' => $deleted,
            'message' => $message
        ));
    }

    /**
     * get all the tests instances in a json response
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return void
     */
    public function getDeliveriesTests()
    {
        if (! tao_helpers_Request::isAjax()) {
            throw new Exception("wrong request mode");
        }
        $tests = tao_helpers_Uri::encodeArray($this->service->getDeliveriesTests(), tao_helpers_Uri::ENCODE_ARRAY_KEYS);
        echo json_encode(array(
            'data' => $tests
        ));
    }
	
	/**
	 * get the compilation view
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function initCompilation(){
		
		$delivery = $this->getCurrentInstance();
		
		//init the value to be returned	
		$deliveryData=array();
		
		$deliveryData["uri"] = $delivery->getUri();
		
		//check if a wsdl contract is set to upload the result:
		$resultServer = $this->service->getResultServer($delivery);
		$deliveryData['resultServer'] = $resultServer;
		
		$deliveryData['tests'] = array();
		if(!empty($resultServer)){
			
			//get the tests list from the delivery id: likely, by parsing the deliveryContent property value
			//array of resource, test set
			$tests = array();
			$tests = $this->service->getRelatedTests($delivery);
			
			foreach($tests as $test){
				$deliveryData['tests'][] = array(
					"label" => $test->getLabel(),
					"uri" => $test->getUri()
				);//url encode maybe?
			}
		}
		
		echo json_encode($deliveryData);
	}
}
?>
