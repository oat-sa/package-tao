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

use \core_kernel_classes_Class;
use \core_kernel_versioning_Repository;
use \taoItems_models_classes_ItemsService;
use \common_report_Report;
use \common_exception_Error;
use \taoItems_models_classes_Import_ExtractException;
use \tao_models_classes_Parser;
use \common_ext_ExtensionsManager;
use \helpers_File;
use \core_kernel_classes_Property;
use \tao_helpers_File;
use \common_Logger;
use \taoItems_models_classes_Import_ImportException;

/**
 * Class to import Open Web Items
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 
 */
class ImportService
{

	/**
	 * imports a zip file as a new instance of the specified item class
	 * parameter repository is ignored for now
	 *
	 * @access public
	 * @author Joel Bout, <joel.bout@tudor.lu>
	 * @param  string xhtmlFile
	 * @param  Class itemClass
	 * @param  boolean validate
	 * @param  Repository repository
	 * @return common_report_Report
	 */
	public function importXhtmlFile($xhtmlFile,  core_kernel_classes_Class $itemClass, $validate = true,  core_kernel_versioning_Repository $repository = null)
	{
		//get the services instances we will need
		$itemService = taoItems_models_classes_ItemsService::singleton();
		$report = new common_report_Report(common_report_Report::TYPE_SUCCESS, '');
	
		if (!$itemService->isItemClass($itemClass)) {
			throw new common_exception_Error('provided non item class for '.__FUNCTION__);
		}

		//load and validate the package
		$packageParser = new PackageParser($xhtmlFile);
		$packageParser->validate();

		if ($packageParser->isValid()) {
		
    		//extract the package
    		$folder = $packageParser->extract();
    		if (!is_dir($folder)) {
    			throw new taoItems_models_classes_Import_ExtractException();
    		}
    				
    		//load and validate the manifest
    		$fileParser = new tao_models_classes_Parser($folder .'index.html', array('extension' => 'html'));
    		$taoItemsBasePath = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems')->getDir();
    		$fileParser->validate($taoItemsBasePath.'/models/classes/data/xhtml/xhtml.xsd');
    		
    		if(!$validate || $fileParser->isValid()) {
    				
        		//create a new item in the model
        		$rdfItem = $itemService->createInstance($itemClass);
        		if(is_null($rdfItem)){
        			helpers_File::remove($folder);
        			throw new common_exception_Error('Unable to create instance of '.$itemClass->getUri());
        		}
        		
        		//set the XHTML type
        		$rdfItem->setPropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY), TAO_ITEM_MODEL_XHTML);
        		
        		$itemContent = file_get_contents($folder .'index.html');
        		$itemService->setItemContent($rdfItem, $itemContent, null, 'HOLD_COMMIT');
        
        		$itemPath = $itemService->getItemFolder($rdfItem);
        		if (!tao_helpers_File::move($folder, $itemPath)) {
        			common_Logger::w('Unable to move '.$folder.' to '.$itemPath);
        			// clean up
        			$itemService->deleteItem($rdfItem);
        			helpers_File::remove($folder);
        			throw new taoItems_models_classes_Import_ImportException('Unable to copy the resources');
        		}
        		
        		$report->setMessage(__('The OWI Item was successfully imported.'));
        		$report->setData($rdfItem);
        		
    		} 
    		else {
    		    helpers_File::remove($folder);
    		    if ($fileParser->getReport()->hasChildren() === true) {
    		        foreach ($fileParser->getReport() as $r) {
    		            $report->add($r);
    		        }
    		    }
    		    else {
    		        $report->add($fileParser->getReport());
    		    }
    		}
		} 
		else {
		    if ($packageParser->getReport()->hasChildren() === true) {
    		        foreach ($packageParser->getReport() as $r) {
    		            $report->add($r);
    		        }
    		    }
    		    else {
    		        $report->add($packageParser->getReport());
    		    }
		}	
		
		// $folder has been moved, no need to delete it here
		
		if ($report->containsError() === true) {
		    $report->setMessage("The OWI Item could not be imported.");
		    $report->setType(common_report_Report::TYPE_ERROR);
		    $report->setData(null);
		}
		
		return $report;
	}
	
	/**
	 * import the owi as content into an existing item
	 * replacing the old content
	 * 
	 * @param string $package
	 * @param core_kernel_classeS_resource $item
	 * @param string $language
	 * @param string $validate
	 * @throws taoItems_models_classes_Import_ExtractException
	 * @throws taoItems_models_classes_Import_ImportException
	 * @return common_report_Report
	 */
	public function importContent($package, $item, $language = '', $validate = true) {
	    //load and validate the package
	    $packageParser = new PackageParser($package);
	    $packageParser->validate();
	    
	    if($packageParser->isValid()){
	    
	        //extract the package
	        $folder = $packageParser->extract();
	        if(!is_dir($folder)){
	            throw new taoItems_models_classes_Import_ExtractException();
	        }
	    
	        //load and validate the manifest
	        $fileParser = new tao_models_classes_Parser($folder .'index.html', array('extension' => 'html'));
	        $taoItemsBasePath = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems')->getDir();
	        $fileParser->validate($taoItemsBasePath.'/models/classes/data/xhtml/xhtml.xsd');
	    
	        if(!$validate || $fileParser->isValid()){
	    
	            $itemContent = file_get_contents($folder .'index.html');
        		taoItems_models_classes_ItemsService::singleton()->setItemContent($item, $itemContent);
	            $itemPath = taoItems_models_classes_ItemsService::singleton()->getItemFolder($item, $language);
	            if(!tao_helpers_File::move($folder, $itemPath)){
	                common_Logger::w('Unable to move '.$folder.' to '.$itemPath);
	                helpers_File::remove($folder);
	                throw new taoItems_models_classes_Import_ImportException('Unable to copy the resources');
	            }
	            $returnValue = common_report_Report::createSuccess(__('%s was successfully replaced', $item->getLabel()), $item);
	        } else {
	            helpers_File::remove($folder);
	            $returnValue = $fileParser->getReport();
	            $returnValue->setTitle(__('Validation of the imported file has failed'));
	        }
	    } else {
	        $returnValue = $packageParser->getReport();
	        $returnValue->setTitle(__('Validation of the imported package has failed'));
	    }
	    return $returnValue;
	}

}