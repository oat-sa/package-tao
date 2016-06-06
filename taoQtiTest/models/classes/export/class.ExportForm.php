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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Export form for QTI packages
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 
 */
class taoQtiTest_models_classes_export_ExportForm
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---
    /**
     * Short description of method initForm
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        

    	$this->form = new tao_helpers_form_xhtml_Form('export');

		$this->form->setDecorators(array(
			'element'			=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div')),
			'group'				=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-group')),
			'error'				=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-error ui-state-error ui-corner-all')),
			'actions-bottom'	=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar')),
			'actions-top'		=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar'))
		));

    	$exportElt = tao_helpers_form_FormFactory::getElement('export', 'Free');
		$exportElt->setValue('<a href="#" class="form-submitter btn-success small"><span class="icon-export"></span> ' .__('Export').'</a>');

		$this->form->setActions(array($exportElt), 'bottom');
        
    }
    
    /**
     * overriden
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {

    	$testService = taoTests_models_classes_TestsService::singleton();
    	$testModel = new core_kernel_classes_Resource(INSTANCE_TEST_MODEL_QTI);

		$fileName = '';
    	$options = array();
    	if(isset($this->data['instance'])){
    		$test = $this->data['instance'];
    		if($test instanceof core_kernel_classes_Resource){
    			if ($testModel->equals($testService->getTestModel($test))) {
    				$fileName = strtolower(tao_helpers_Display::textCleaner($test->getLabel()));
    				$options[$test->getUri()] = $test->getLabel();
    			}
    		}
    	}
    	else {
    		if(isset($this->data['class'])){
	    		$class = $this->data['class'];
	    	}
	    	else{
	    		$class = $itemService->getRootClass();
	    	}
    		if($class instanceof core_kernel_classes_Class){
					$fileName =  strtolower(tao_helpers_Display::textCleaner($class->getLabel(), '*'));
					foreach($class->getInstances() as $instance){
						if ($testModel->equals($testService->getTestModel($instance))) {
							$options[$instance->getUri()] = $instance->getLabel();
						}
					}
    		}
    	}

		$nameElt = tao_helpers_form_FormFactory::getElement('filename', 'Textbox');
		$nameElt->setDescription(__('File name'));
		$nameElt->setValue($fileName);
		$nameElt->setUnit(".zip");
		$nameElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
    	$this->form->addElement($nameElt);

    	$instanceElt = tao_helpers_form_FormFactory::getElement('instances', 'Checkbox');
    	$instanceElt->setDescription(__('Test'));
    	//$instanceElt->setAttribute('checkAll', true);
		$instanceElt->setOptions(tao_helpers_Uri::encodeArray($options, tao_helpers_Uri::ENCODE_ARRAY_KEYS));
    	foreach(array_keys($options) as $value){
			$instanceElt->setValue($value);
		}
		$this->form->addElement($instanceElt);


    	$this->form->createGroup('options', __('Export QTI 2.1 Test Package'), array( 'filename', 'instances'));
    }

}