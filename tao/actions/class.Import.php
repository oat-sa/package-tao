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
 *               2013-     (update and modification) Open Assessment Technologies SA;
 */

/**
 * This controller provide the actions to import resources
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 
 *
 */
class tao_actions_Import extends tao_actions_CommonModule {

	/**
	 * initialize the classUri and execute the upload action
	 * @requiresRight id WRITE
	 * @return void
	 */
	public function index(){
		
		$importer = $this->getCurrentImporter();
		$formContainer = new tao_actions_form_Import(
			$importer,
			$this->getAvailableImportHandlers(),
			$this->getCurrentClass()
		);
		$myForm = $formContainer->getForm();
		
		//if the form is submited and valid
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$report = $importer->import($this->getCurrentClass(), $myForm);
				return $this->returnReport($report);
			}
		}
		
		$this->setData('myForm', $myForm->render());
		$this->setData('formTitle', __('Import '));
		$this->setView('form/import.tpl', 'tao');
	}
	
	/**
	 * Returns the currently selecte importhandler
	 * or the importhandler to use by default
	 * 
	 * @return tao_models_classes_import_ImportHandler
	 */
	private function getCurrentImporter() {
		if ($this->hasRequestParameter('importHandler')) {
			//$importHandlerClass = $this->getRequestParameter('importHandler');
			$importHandlerClass = $_POST['importHandler'];
			foreach ($this->getAvailableImportHandlers() as $importHandler) {
				if (get_class($importHandler) == $importHandlerClass) {
					return $importHandler;
				}
			}
		}
		return current($this->getAvailableImportHandlers());
	}
	
	/**
	 * Gets the available import handlers for this module
	 * Should be overwritten by extensions that want to provide
	 * additional Importhandlers
	 */
	protected function getAvailableImportHandlers() {
		return array(
			new tao_models_classes_import_RdfImporter(),
		    new tao_models_classes_import_CsvImporter()
		);
	}
	
	/**
	 * Helper to get the selected class, needs to be passed as hidden field in the form
	 */
	protected function getCurrentClass() {
		return new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getRequestParameter('classUri')));
	}

	protected function getValidators(){
		return array();
	}

}
