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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

use oat\tao\helpers\Template;

/**
 * Controller to managed assembled deliveries
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 */
class taoDelivery_actions_Delivery extends tao_actions_SaSModule
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
        $this->service = taoDelivery_models_classes_DeliveryAssemblyService::singleton();
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
     * Edit a delivery instance
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
                
                $this->setData("selectNode", tao_helpers_Uri::encode($delivery->getUri()));
                $this->setData('message', __('Delivery saved'));
                $this->setData('reload', true);
            }
        }
        
        $this->setData('label', $delivery->getLabel());
        
        // history
        $this->setData('date', taoDelivery_models_classes_DeliveryAssemblyService::singleton()->getCompilationDate($delivery));
        if (taoDelivery_models_classes_execution_ServiceProxy::singleton()->implementsMonitoring()) {
            $execs = taoDelivery_models_classes_execution_ServiceProxy::singleton()->getExecutionsByDelivery($delivery);
            $this->setData('exec', count($execs));
        }
        
        // define the groups related to the current delivery
        $property = new core_kernel_classes_Property(PROPERTY_GROUP_DELVIERY);
        $tree = tao_helpers_form_GenerisTreeForm::buildReverseTree($delivery, $property);
        $tree->setTitle(__('Assigned to'));
        $tree->setTemplate(Template::getTemplate('widgets/assignGroup.tpl'));
        $this->setData('groupTree', $tree->render());
        
        // testtaker brick
        $this->setData('assemblyUri', $delivery->getUri());
        
        // define the subjects excluded from the current delivery
        $property = new core_kernel_classes_Property(TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP);
        $excluded = $delivery->getPropertyValues($property);
        $this->setData('ttexcluded', count($excluded));

        $users = taoDelivery_models_classes_AssignmentService::singleton()->getAssignedUsers($delivery);
        $assigned = array_diff(array_unique($users), $excluded);
        $this->setData('ttassigned', count($assigned));
        
        $this->setData('formTitle', __('Properties'));
        $this->setData('myForm', $myForm->render());
        
        if (common_ext_ExtensionsManager::singleton()->isEnabled('taoCampaign')) {
            $this->setData('campaign', taoCampaign_helpers_Campaign::renderCampaignTree($delivery));
        }
        $this->setView('Delivery/editDelivery.tpl');
    }
    
    public function excludeTesttaker()
    {
        $assembly = $this->getCurrentInstance();
        $this->setData('assemblyUri', $assembly->getUri());
        
        // define the subjects excluded from the current delivery
        $property = new core_kernel_classes_Property(TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP);
        $excluded = array(); 
        foreach ($assembly->getPropertyValues($property) as $uri) {
            $user = new core_kernel_classes_Resource($uri);
            $excluded[$uri] = $user->getLabel();
        }
        
        $assigned = array();
        foreach (taoDelivery_models_classes_AssignmentService::singleton()->getAssignedUsers($assembly) as $userId) {
            if (!in_array($userId, array_keys($excluded))) {
                $user = new core_kernel_classes_Resource($userId);
                $assigned[$userId] = $user->getLabel();
            }
        }
        
        $this->setData('assigned', $assigned);
        $this->setData('excluded', $excluded);
        
        
        $this->setView('Delivery/excludeTesttaker.tpl');
    }
    
    public function saveExcluded() {
        if(!tao_helpers_Request::isAjax()){
            throw new common_exception_IsAjaxAction(__FUNCTION__);
        }
        if(!$this->hasRequestParameter('excluded')){
            throw new common_exception_MissingParameter('excluded');
        }
        
        $jsonArray = json_decode($_POST['excluded']);
        if(!is_array($jsonArray)){
            throw new common_Exception('parameter "excluded" should be a json encoded array');
        }
        
        $assembly = $this->getCurrentInstance();
        $success = $assembly->editPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP),$jsonArray);
        
        echo json_encode(array(
        	'saved' => $success
        ));
    }

    public function wizard()
    {
        try {
            $formContainer = new \taoDelivery_actions_form_WizardForm(array('class' => $this->getCurrentClass()));
            $myForm = $formContainer->getForm();
             
            if ($myForm->isValid() && $myForm->isSubmited()) {
                $label = $myForm->getValue('label');
                $test = new core_kernel_classes_Resource($myForm->getValue('test'));
                $label = __("Delivery of %s", $test->getLabel());
                $deliveryClass = new core_kernel_classes_Class($myForm->getValue('classUri'));
                $report = taoDelivery_models_classes_SimpleDeliveryFactory::create($deliveryClass, $test, $label);
                $this->returnReport($report);
            } else {
                $this->setData('myForm', $myForm->render());
                $this->setData('formTitle', __('Create a new delivery'));
                $this->setView('form.tpl', 'tao');
            }
    
        } catch (taoDelivery_actions_form_NoTestsException $e) {
            $this->setView('Delivery/wizard_error.tpl');
        }
    }
}
