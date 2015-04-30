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
 * This controller provide the actions to export and manage exported data
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 
 *
 */
class tao_actions_Export extends tao_actions_CommonModule {

	/**
	 * get the path to save and retrieve the exported files regarding the current extension
	 * @return string the path
	 */
	protected function getExportPath(){
		$path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'export';
		if (!file_exists($path)) {
			mkdir($path);
		}
		return $path;
	}

	/**
	 * Does EVERYTHING
	 * @todo cleanup interface
	 */
	public function index()
	{
		$formData = array();
		if($this->hasRequestParameter('classUri')){
			if(trim($this->getRequestParameter('classUri')) != ''){
				$formData['class'] = new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getRequestParameter('classUri')));
			}
		}
		if($this->hasRequestParameter('uri') && $this->hasRequestParameter('classUri')){
			if(trim($this->getRequestParameter('uri')) != ''){
				$formData['instance'] = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
			}
		}
		$formData['id'] = $this->getRequestParameter('id');
		
		$handlers = $this->getAvailableExportHandlers();
		$exporter = $this->getCurrentExporter();

		$selectedResource = isset($formData['instance']) ? $formData['instance'] : $formData['class'];
		$formFactory = new tao_actions_form_Export($handlers, $exporter->getExportForm($selectedResource), $formData);
		$myForm = $formFactory->getForm();
		if (!is_null($exporter)) {
			$myForm->setValues(array('exportHandler' => get_class($exporter)));
		}
		$this->setData('myForm', $myForm->render());
        
        if($this->hasRequestParameter('exportChooser_sent') && $this->getRequestParameter('exportChooser_sent') == 1){
            
            //use method GET to allow direct file download (not ajax compatible)
            $exportData = $_GET;
            
            if(isset($exportData['instances'])) {
                $instanceCount = count($exportData['instances']);
                for($i = 0; $i < $instanceCount ; $i++){
                    $exportData['instances'][$i] = tao_helpers_Uri::decode($exportData['instances'][$i]);
                }
            } elseif (isset($exportData['exportInstance'])) {
                $exportData['exportInstance'] = tao_helpers_Uri::decode($exportData['exportInstance']);
            }
            
            $file = $exporter->export($exportData, tao_helpers_Export::getExportPath());
            if (!is_null($file) && file_exists($file)) {
                return tao_helpers_Export::outputFile(tao_helpers_Export::getRelativPath($file));
            }
            
        }
        
        $context = Context::getInstance();
        $this->setData('export_extension', $context->getExtensionName());
        $this->setData('export_module', $context->getModuleName());
        $this->setData('export_action', $context->getActionName());
        
        $this->setData('formTitle', __('Export '));
        $this->setView('form/export.tpl', 'tao');
		
	}
	
	protected function getResourcesToExport(){
		$returnValue = array();
		if($this->hasRequestParameter('uri') && trim($this->getRequestParameter('uri')) != ''){
			$returnValue[] = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
		}elseif($this->hasRequestParameter('classUri') && trim($this->getRequestParameter('classUri')) != ''){
			$class = new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getRequestParameter('classUri')));
			$returnValue = $class->getInstances(true);
		}else {
			common_Logger::w('No resources to export');
		}
		return $returnValue;
	}
	
	/**
	 * Returns the selected ExportHandler
	 * 
	 * @return tao_models_classes_export_ExportHandler
	 * @throws common_Exception
	 */
	private function getCurrentExporter() {
		if ($this->hasRequestParameter('exportHandler')) {
			$exportHandler = $_REQUEST['exportHandler'];//allow method "GET"
			if (class_exists($exportHandler) && in_array('tao_models_classes_export_ExportHandler', class_implements($exportHandler))) {
				$exporter = new $exportHandler();
				return $exporter;
			} else {
				throw new common_Exception('Unknown or incompatible ExporterHandler: \''.$exportHandler.'\'');
			}
		} else {
			return current($this->getAvailableExportHandlers());
		}
	}

	/**
	 * Override this function to add your own custom ExportHandlers
	 * 
	 * @return array an array of ExportHandlers
	 */
	protected function getAvailableExportHandlers() {
		return array(
			new tao_models_classes_export_RdfExporter()
		);
	}
	
}
