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
 * @subpackage models_classes
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
    public function compile(core_kernel_file_File $destinationDirectory) {
        $item = $this->getResource();
        $itemUri = $item->getUri();
        $itemService = taoItems_models_classes_ItemsService::singleton();
        if (! $itemService->isItemModelDefined($item)) {
            throw new taoItems_models_classes_CompilationFailedException("No relevant Item Model found for item '${itemUri}' at compilation time.");
        }
        
        $langs = $this->getContentUsedLanguages();
        foreach ($langs as $compilationLanguage) {
        	$compiledFolder = $this->getLanguageCompilationPath($destinationDirectory, $compilationLanguage);
        	if (!is_dir($compiledFolder)){
        		if (!@mkdir($compiledFolder)) {
        		    $msg = "Could not create language specific directory for item '${itemUri}' at compilation time.";
        		    throw new taoItems_models_classes_CompilationFailedException($msg);
        		}
        	}
        	$itemService = taoItems_models_classes_ItemsService::singleton();
        	$this->deployItem($item, $compilationLanguage, $compiledFolder);
        }
        return $this->createService($item, $destinationDirectory);
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
    protected function getLanguageCompilationPath(core_kernel_file_File $destinationDirectory, $compilationLanguage) {
        return $destinationDirectory->getAbsolutePath(). DIRECTORY_SEPARATOR . $compilationLanguage . DIRECTORY_SEPARATOR;
    }

    /**
     * deploys the item into the given absolute directory 
     * 
     * @param core_kernel_classes_Resource $item
     * @param string $languageCode
     * @param string $compiledDirectory
     * @return boolean
     */
    protected function deployItem(core_kernel_classes_Resource $item, $languageCode, $compiledDirectory) {
        $itemService = taoItems_models_classes_ItemsService::singleton();
        	
        // copy local files
        $source = $itemService->getItemFolder($item, $languageCode);
        taoItems_helpers_Deployment::copyResources($source, $compiledDirectory, array('index.html'));
        
        // render item
        
        $xhtml = $itemService->render($item, $languageCode);
        	
        // retrieve external resources
        $xhtml = taoItems_helpers_Deployment::retrieveExternalResources($xhtml, $compiledDirectory);
         
        // write index.html
        file_put_contents($compiledDirectory.'index.html', $xhtml);
        return true;
    }
    
    /**
     * Create the item's ServiceCall.
     * 
     * @param core_kernel_classes_Resource $item
     * @param core_kernel_file_File $destinationDirectory
     * @return tao_models_classes_service_ServiceCall
     */
    protected function createService(core_kernel_classes_Resource $item, core_kernel_file_File $destinationDirectory) {
        $service = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(INSTANCE_SERVICE_ITEMRUNNER));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
            new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMPATH),
            $destinationDirectory
        ));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
            new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMURI),
            $item
        ));
        
        return $service;        
    }
}