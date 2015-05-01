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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * This container initialize the settings form.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
class tao_actions_form_UserSettings
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initForm
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    protected function initForm()
    {
        
		
		$this->form = tao_helpers_form_FormFactory::getForm('settings');
		
		$actions = tao_helpers_form_FormFactory::getCommonActions('top');
		$this->form->setActions(array(), 'top');
		$this->form->setActions($actions, 'bottom');
		
        
    }

    /**
     * Short description of method initElements
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    protected function initElements()
    {
        
		$langService = tao_models_classes_LanguageService::singleton();
		
    	// Retrieve languages available for a GUI usage.
    	$guiUsage = new core_kernel_classes_Resource(INSTANCE_LANGUAGE_USAGE_GUI);
		$guiOptions = array();
        foreach($langService->getAvailableLanguagesByUsage($guiUsage) as $lang){
			$guiOptions[tao_helpers_Uri::encode($lang->getUri())] = $lang->getLabel();
		}
        
        // Retrieve languages available for a Data usage.
        $dataUsage = new core_kernel_classes_Resource(INSTANCE_LANGUAGE_USAGE_DATA);
		$dataOptions = array();
        foreach($langService->getAvailableLanguagesByUsage($dataUsage) as $lang){
			$dataOptions[tao_helpers_Uri::encode($lang->getUri())] = $lang->getLabel();
		}

        $uiLangElement = tao_helpers_form_FormFactory::getElement('ui_lang', 'Combobox');
        $uiLangElement->setDescription(__('Interface language'));
        $uiLangElement->setOptions($guiOptions);

        $this->form->addElement($uiLangElement);

        $dataLangElement = tao_helpers_form_FormFactory::getElement('data_lang', 'Combobox');
        $dataLangElement->setDescription(__('Data language'));
        $dataLangElement->setOptions($dataOptions);

        $this->form->addElement($dataLangElement);
        
        $tzElement = tao_helpers_form_FormFactory::getElement('timezone', 'Combobox');
        $tzElement->setDescription(__('Time zone'));
        $options = array();
        foreach (DateTimeZone::listIdentifiers() as $id) {
            $options[$id] = $id;
        }
        $tzElement->setOptions($options);
        
        $this->form->addElement($tzElement);
        
        
    }

}

?>