<?php
/*  
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
?>
<?php
/**
 * Campaign Controller provide actions performed from url resolution
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoCampaign
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class taoCampaign_actions_Campaign extends tao_actions_SaSModule {
	
	/**
	 * constructor: initialize the service and the default data
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = taoCampaign_models_classes_CampaignService::singleton();
		$this->defaultData();
		
	}
	
	protected function getClassService(){
		return taoCampaign_models_classes_CampaignService::singleton();
	}
	
/*
 * controller actions
 */
	/**
	 * Render json data to populate the campaign tree 
	 * 'modelType' must be in the request parameters
	 * @return void
	 */
	public function getCampaigns(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$options = array(
			'subclasses' => true, 
			'instances' => true, 
			'highlightUri' => '', 
			'labelFilter' => '', 
			'chunk' => false
		);
		if($this->hasRequestParameter('filter')){
			$options['labelFilter'] = $this->getRequestParameter('filter');
		}
		if($this->hasRequestParameter('classUri')){
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = $this->service->getRootClass();
		}
		
		echo json_encode( $this->service->toTree($clazz , $options));
	}
	
	/**
	 * Edit a campaign class
	 * @return void
	 */
	public function editCampaignClass(){
		$clazz = $this->getCurrentClass();
		
		if($this->hasRequestParameter('property_mode')){
			$this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
		}
		
		$myForm = $this->editClass($clazz, $this->service->getRootClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->getUri()));
				}
				$this->setData('message', __('Campaign Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit campaign class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl', 'tao');
	}
	
	/**
	 * Edit a delviery instance
	 * @return void
	 */
	public function editCampaign(){
		$clazz = $this->getCurrentClass();
		
		$campaign = $this->getCurrentInstance();
		
		$formContainer = new tao_actions_form_Instance($clazz, $campaign);
		$myForm = $formContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($campaign);
				$campaign = $binder->bind($myForm->getValues());
				
				$this->setData('message', __('Campaign saved'));
				$this->setData('reload', true);
			}
		}
		
		$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($campaign->getUri()));
		
		//get the deliveries related to this delivery campaign
		$prop = new core_kernel_classes_Property(TAO_DELIVERY_CAMPAIGN_PROP);
		$tree = tao_helpers_form_GenerisTreeForm::buildReverseTree($campaign, $prop);
		$this->setData('deliveryTree', $tree->render());
		
		$this->setData('formTitle', __('Edit Campaign'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form_campaign.tpl');
	}
	
	/**
	 * Add a campaign instance        
	 * @return void
	 */
	public function addCampaign(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentClass();
		$campaign = $this->service->createInstance($clazz, $this->service->createUniqueLabel($clazz));
		if(!is_null($campaign) && $campaign instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $campaign->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($campaign->getUri())
			));
		}
	}
	
	/**
	 * Add a campaign subclass
	 * @return void
	 */
	public function addCampaignClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->service->createCampaignClass($this->getCurrentClass());
		if(!is_null($clazz) && $clazz instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $clazz->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clazz->getUri())
			));
		}
	}
	
	/**
	 * Delete a campaign or a campaign class
	 * @return void
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteCampaign($this->getCurrentInstance());
		}
		else{
			$deleted = $this->service->deleteCampaignClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * Duplicate a campaign instance
	 * @return void
	 */
	public function cloneCampaign(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$campaign = $this->getCurrentInstance();
		$clazz = $this->getCurrentClass();
		
		$clone = $this->service->createInstance($clazz);
		if(!is_null($clone)){
			
			foreach($clazz->getProperties() as $property){
				foreach($campaign->getPropertyValues($property) as $propertyValue){
					$clone->setPropertyValue($property, $propertyValue);
				}
			}
			$clone->setLabel($campaign->getLabel()."'");
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->getUri())
			));
		}
	}
	
	/**
	 * Get the data to populate the tree of deliveries
	 * @return void
	 */
	public function getDeliveries(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array('chunk' => false);
		if($this->hasRequestParameter('classUri')) {
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
		}
		if($this->hasRequestParameter('selected')){
			$selected = $this->getRequestParameter('selected');
			if(!is_array($selected)){
				$selected = array($selected);
			}
			$options['browse'] = $selected;
		}
		echo json_encode($this->service->toTree($clazz, $options));
	}
}