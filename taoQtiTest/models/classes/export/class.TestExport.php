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
 * Export Handler for QTI tests.
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoQtiTest
 
 */
class taoQtiTest_models_classes_export_TestExport implements tao_models_classes_export_ExportHandler
{

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_export_ExportHandler::getLabel()
     */
    public function getLabel() {
    	return __('QTI Test Package 2.1');
    }
    
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_export_ExportHandler::getExportForm()
     */
    public function getExportForm(core_kernel_classes_Resource $resource) {
        if ($resource instanceof core_kernel_classes_Class) {
            $formData= array('class' => $resource);
        } else {
            $formData= array('instance' => $resource);
        }
    	$form = new taoQtiTest_models_classes_export_ExportForm($formData);
    	return $form->getForm();
    }
    
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_export_ExportHandler::export()
     */
    public function export($formValues, $destination) {
    	$file = null;
    	
    	if (isset($formValues['filename']) === true) {
    	    
			$instances = is_string($formValues['instances']) ? array($formValues['instances']) : $formValues['instances'];
			
			if (count($instances) > 0) {
				
				$fileName = $formValues['filename'] .'_' . time() . '.zip';
				$path = tao_helpers_File::concat(array($destination, $fileName));
				
				if (tao_helpers_File::securityCheck($path, true) === false) {
					throw new common_Exception('Unauthorized file name for QTI Test ZIP archive.');
				}
			    // Create a new ZIP archive to store data related to the QTI Test.
			    $zip = new ZipArchive();
			    if ($zip->open($path, ZipArchive::CREATE) !== true){
			        throw new common_Exception("Unable to create ZIP archive for QTI Test at location '" . $path . "'.");
			    }
			    // Create an empty IMS Manifest as a basis.
			    $manifest = taoQtiTest_helpers_Utils::emptyImsManifest();
			    
			    foreach ($instances as $instance) {
			        $testResource = new core_kernel_classes_Resource($instance);
			        $testExporter = new taoQtiTest_models_classes_export_QtiTestExporter($testResource, $zip, $manifest);
			        common_Logger::d('Export ' . $instance);
			        $testExporter->export();
			    }
			    
				$file = $path;
				$zip->close();
				
			}
			else {
			    common_Logger::w("No instance in form to export");
			
			}
		} 
		else {
			common_Logger::w("Missing filename for QTI Test export using Export Handler '" . __CLASS__ . "'.");
		}
		
		return $file;
    }
}