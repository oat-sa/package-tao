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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

class taoSimpleDelivery_actions_Authoring extends tao_actions_TaoModule
{
	protected function getRootClass() {
	    $model = new taoSimpleDelivery_models_classes_ContentModel();
		return $model->getClass();
	}
    
    public function wizard()
    {
        $this->defaultData();
        try {
            $formContainer = new \taoSimpleDelivery_actions_form_WizardForm(array('class' => $this->getCurrentClass()));
            $myForm = $formContainer->getForm();
             
            if ($myForm->isValid() && $myForm->isSubmited()) {
                $label = $myForm->getValue('label');
                $test = new core_kernel_classes_Resource($myForm->getValue('test'));
                $label = __("Delivery of %s", $test->getLabel());
                $deliveryClass = new core_kernel_classes_Class($myForm->getValue('classUri'));
                $report = taoSimpleDelivery_models_classes_SimpleDeliveryService::singleton()->create($deliveryClass, $test, $label);
                if ($report->getType() == common_report_Report::TYPE_SUCCESS) {
                    $assembly = $report->getdata();
                    $this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($assembly->getUri()));
                    $this->setData('reload', true);
                    $this->setData('message', __('Delivery created'));
                    $this->setData('formTitle', __('Create a new delivery'));
                    $this->setView('form_container.tpl', 'tao');
                } else {
                    $this->setData('report', $report);
                    $this->setData('title', __('Error'));
                    $this->setView('report.tpl', 'tao');
                }
            } else {
                $this->setData('myForm', $myForm->render());
                $this->setData('formTitle', __('Create a new delivery'));
                $this->setView('form_container.tpl', 'tao');
            }
            
        } catch (taoSimpleDelivery_actions_form_NoTestsException $e) {
            $this->setView('wizard_error.tpl');
        }
    }
    
	public function save()
    {
        $saved = false;
         
        $instance = $this->getCurrentInstance();
        $testUri = tao_helpers_Uri::decode($this->getRequestParameter(tao_helpers_Uri::encode(PROPERTY_DELIVERYCONTENT_TEST)));
    
        $saved = $instance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_DELIVERYCONTENT_TEST ), $testUri);
         
        echo json_encode(array(
            'saved' => $saved
        ));
    }
}