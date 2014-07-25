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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * Imprthandler for QTI packages
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoQTI
 * @subpackage models_classes_import
 */
class taoQTI_models_classes_import_QtiPackageImport implements tao_models_classes_import_ImportHandler
{

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::getLabel()
     */
    public function getLabel() {
    	return __('QTI Package');
    }
    
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::getForm()
     */
    public function getForm() {
    	$form = new taoQTI_models_classes_import_QtiPackageImportForm();
    	return $form->getForm();
    }

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::import()
     */
    public function import($class, $form) {
		
        $fileInfo = $form->getValue('source');
        
        if(isset($fileInfo['uploaded_file'])){
			
			$uploadedFile = $fileInfo['uploaded_file'];
			
			//test versioning
			$repository = null;
			if (common_Utils::isUri($form->getValue('repository'))) {
				$repository = new core_kernel_versioning_Repository($form->getValue('repository'));
				if (!$repository->exists()) {
					$repository = null;
				}
			}
			
			$validate = count($form->getValue('disable_validation')) == 0 ? true : false;
			
			set_time_limit(200);	//the zip extraction is a long process that can exced the 30s timeout
			
			try {
				$importService = taoQTI_models_classes_QTI_ImportService::singleton();
				$importedItems = $importService->importQTIPACKFile($uploadedFile, $class, $validate, $repository);
			    if(count($importedItems) > 0) {
				    $report = common_report_Report::createSuccess(__('%s items imported successfully', count($importedItems)), $importedItems);
			    } else {
			        $report = common_report_Report::createFailure(__('No items could be imported'));
			    }
			} catch (taoQTI_models_classes_QTI_exception_ExtractException $e) {
			    $report = common_report_Report::createFailure(__('unable to extract archive content, please check your tmp dir'));
			} catch (taoQTI_models_classes_QTI_ParsingException $e) {
                $report = common_report_Report::createFailure(__('Validation of the imported file has failed'));
				//$this->setData('importErrors', $qtiParser->getErrors());
			} catch (common_Exception $e) {
		        $report = common_report_Report::createFailure(__('An error occurs during the import'));
			}
			
			tao_helpers_File::remove($uploadedFile);
		} else {
		   throw new common_exception_Error('No source file for import');
		}
		return $report;
    }


}