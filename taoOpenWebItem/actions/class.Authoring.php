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

/**
 * This controller allows the additon and deletion
 * of LTI Oauth Consumers
 * 
 * @author Joel Bout
 * @package taoLti
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */

class taoOpenWebItem_actions_Authoring extends tao_actions_CommonModule {
	
	/**
	 * constructor uses default TaoService
	 */
	public function __construct(){
		parent::__construct();
		$this->service = tao_models_classes_TaoService::singleton();
		$this->defaultData();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see tao_actions_TaoModule::getRootClass()
	 */
	public function index() {
	    if (!$this->hasRequestParameter('instance') || strlen($this->getRequestParameter('instance')) == 0) {
	        throw new common_exception_MissingParameter('instance', __CLASS__);
	    }
	    $item = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('instance')));
	    
	    $formContainer = new taoOpenWebItem_model_import_OwiImportForm();
		$myForm = $formContainer->getForm();
		
		$element = tao_helpers_form_FormFactory::getElement('instance', 'hidden');
		$element->setValue($item->getUri());
		$myForm->addElement($element);
		
		if($myForm->isSubmited()){
		    if($myForm->isValid()){
		        $validate = count($myForm->getValue('disable_validation')) == 0 ? true : false;
		        
		        $fileInfo = $myForm->getValue('source');
		        $uploadedFile = $fileInfo['uploaded_file'];
		        
		        $importer = new taoOpenWebItem_model_import_ImportService();
		        $report = $importer->importContent($uploadedFile, $item, '', $validate);
		        if ($report->containsSuccess()) {
		            $this->setData('message', __('Content saved'));
		        }
		        if ($report->containsError()) {
		            $this->setData('importErrorTitle', $report->getTitle());
		            $this->setData('importErrors', $report->getErrors());
		        }
		    }
		}
		$this->setData('formTitle', __('Import Content'));
		$this->setData('myForm', $myForm->render());
		
        if (isset($_GET['STANDALONE_MODE']) && $_GET['STANDALONE_MODE']) {
            $this->setData('includedView', DIR_VIEWS . 'templates/' . "form.tpl");
            return parent::setView('sas.tpl', true);
       } else {
            $this->setView('form.tpl');
        }
	}
	
}