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
 */

/**
 * Generic item compiler.
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @package taoItems
 
 */
class taoItems_models_classes_ItemCompiler extends tao_models_classes_Compiler
{
    /**
     * Compile an item.
     * 
     * @param core_kernel_file_File $destinationDirectory
     * @throws taoItems_models_classes_CompilationFailedException
     * @return tao_models_classes_service_ServiceCall
     */
    public function compile() {
        $destinationDirectory = $this->spawnPublicDirectory();
        $item = $this->getResource();
        $itemUri = $item->getUri();
        $report = new common_report_Report(common_report_Report::TYPE_SUCCESS, __('Published %s', $item->getLabel()));
        if (! taoItems_models_classes_ItemsService::singleton()->isItemModelDefined($item)) {
            return $this->fail(__('Item \'%s\' has no model', $item->getLabel()));
        }
        
        $langs = $this->getContentUsedLanguages();
        foreach ($langs as $compilationLanguage) {
        	$compiledFolder = $this->getLanguageCompilationPath($destinationDirectory, $compilationLanguage);
        	if (!is_dir($compiledFolder)){
        		if (!@mkdir($compiledFolder)) {
        		    common_Logger::e('Could not create directory '.$compiledFolder, 'COMPILER');
        		    return $this->fail(__('Could not create language specific directory for item \'%s\'', $item->getLabel()));
        		}
        	}
        	$langReport = $this->deployItem($item, $compilationLanguage, $compiledFolder);
        	$report->add($langReport);
        	if ($langReport->getType() == common_report_Report::TYPE_ERROR) {
        	    $report->setType(common_report_Report::TYPE_ERROR);
        	    break;
        	}
        }
        if ($report->getType() == common_report_Report::TYPE_SUCCESS) {
            $report->setData($this->createService($item, $destinationDirectory));
        } else {
            $report->setMessage(__('Failed to publish %s', $item->getLabel()));
        }
        return $report;
    }
    
    /**
     * Get the languages in use for the item content.
     * 
     * @return array An array of language tags (string).
     */
    protected function getContentUsedLanguages() {
        return $this->getResource()->getUsedLanguages(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));
    }
    
    /**
     * Get the absolute path of the language specific compilation folder for this item to be compiled.
     * 
     * @param core_kernel_file_File $destinationDirectory
     * @param string $compilationLanguage A language tag.
     * @return string The absolute path to the language specific compilation folder for this item to be compiled.
     */
    protected function getLanguageCompilationPath($destinationDirectory, $compilationLanguage) {
        return $destinationDirectory->getPath(). DIRECTORY_SEPARATOR . $compilationLanguage . DIRECTORY_SEPARATOR;
    }

    /**
     * deploys the item into the given absolute directory 
     * 
     * @param core_kernel_classes_Resource $item
     * @param string $languageCode
     * @param string $compiledDirectory
     * @return common_report_Report
     */
    protected function deployItem(core_kernel_classes_Resource $item, $languageCode, $compiledDirectory) {
        $itemService = taoItems_models_classes_ItemsService::singleton();
        	
        // copy local files
        $source = $itemService->getItemFolder($item, $languageCode);
        $success = taoItems_helpers_Deployment::copyResources($source, $compiledDirectory, array('index.html'));
        if (!$success) {
            return $this->fail(__('Unable to copy resources for language %s', $languageCode));
        }
        
        // render item
        
        $xhtml = $itemService->render($item, $languageCode);
        	
        // retrieve external resources
        $subReport = taoItems_helpers_Deployment::retrieveExternalResources($xhtml, $compiledDirectory);
        if ($subReport->getType() == common_report_Report::TYPE_SUCCESS) {
            $xhtml = $subReport->getData();
            // write index.html
            file_put_contents($compiledDirectory.'index.html', $xhtml);
            return new common_report_Report(
                common_report_Report::TYPE_SUCCESS,
                __('Published "%1$s" in language "%2$s"', $item->getLabel(), $languageCode)
            );
        } else {
            return $subReport;
        }
    }
    
    /**
     * Create the item's ServiceCall.
     * 
     * @param core_kernel_classes_Resource $item
     * @param tao_models_classes_service_StorageDirectory $destinationDirectory
     * @return tao_models_classes_service_ServiceCall
     */
    protected function createService(core_kernel_classes_Resource $item, tao_models_classes_service_StorageDirectory $destinationDirectory) {
        $service = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(INSTANCE_SERVICE_ITEMRUNNER));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
            new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMPATH),
            $destinationDirectory->getId()
        ));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
            new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMURI),
            $item
        ));
        
        return $service;        
    }
    
    protected function getSubCompilerClass(core_kernel_classes_Resource $resource) {
        throw new common_Exception('Items cannot include other resources');
    }
}