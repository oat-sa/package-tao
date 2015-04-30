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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoOpenWebItem\model\import;

use \tao_models_classes_import_ImportHandler;
use \helpers_TimeOutHelper;
use \taoItems_models_classes_ItemsService;
use \Exception;
use \common_report_Report;
use \common_exception_UserReadableException;
use \tao_helpers_File;
use \common_exception_Error;

/**
 * Short description of class taoOpenWebItem_models_classes_ItemModel
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoOpenWebItem
 
 */
class OwiImportHandler implements tao_models_classes_import_ImportHandler
{

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::getLabel()
     */
    public function getLabel() {
    	return __('Open Web Item');
    }
    
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::getForm()
     */
    public function getForm() {
    	$form = new OwiImportForm();
    	return $form->getForm();
    }

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::import()
     */
    public function import($class, $form) {
		
        $fileInfo = $form->getValue('source');

        if(isset($fileInfo)){
			
			helpers_TimeOutHelper::setTimeOutLimit(helpers_TimeOutHelper::LONG);	//the zip extraction is a long process that can exced the 30s timeout
			
			//get the services instances we will need
			$itemService = taoItems_models_classes_ItemsService::singleton();
			
			$uploadedFile = $fileInfo['uploaded_file'];
			$uploadedFileBaseName = basename($uploadedFile);
			// uploaded file name contains an extra prefix that we have to remove.
			$uploadedFileBaseName = preg_replace('/^([0-9a-z])+_/', '', $uploadedFileBaseName, 1);
			$uploadedFileBaseName = preg_replace('/.zip|.ZIP$/', '', $uploadedFileBaseName);
			
			$validate = count($form->getValue('disable_validation')) == 0 ? true : false;
			
			$importService = new ImportService();
			try {
				$report = $importService->importXhtmlFile($uploadedFile, $class, $validate);
			}
			catch (Exception $e) {
			    $report = common_report_Report::createFailure(__('An unexpected error occured during the OWI Item import.'));
			    
			    if ($e instanceof common_exception_UserReadableException) {
			        $report->add($e);
			    }
			}
			helpers_TimeOutHelper::reset();
			tao_helpers_File::remove($uploadedFile);
			
		} 
		else {
		    throw new common_exception_Error('No file provided as parameter \'source\' for OWI import');
		}
		
		return $report;
    }


}

?>