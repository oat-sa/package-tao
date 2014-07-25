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
 * Short description of class taoQTI_models_classes_ItemModel
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoQTI
 * @subpackage models_classes
 */
class taoQTI_models_classes_ItemModel
        implements taoItems_models_classes_ExportableItemModel,
                    taoItems_models_classes_ImportableItemModel
{

    /**
     * constructor called by itemService
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return mixed
     */
    public function __construct()
    {
        // ensure qti extension is loaded
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoQTI');
    }

    /**
     * render used for deploy and preview
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return string
     */
    public function render( core_kernel_classes_Resource $item, $langCode)
    {
        $returnValue = (string) '';

		$qitService = taoQTI_models_classes_QTI_Service::singleton();
		
		$qtiItem = $qitService->getDataItemByRdfItem($item, $langCode);
    	
		if(!is_null($qtiItem)) {
			$returnValue = $qitService->renderQTIItem($qtiItem);
		} else {
			common_Logger::w('No qti data for item '.$item->getUri().' in '.__FUNCTION__, 'taoQTI');
		}

        return (string) $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see taoItems_models_classes_itemModel::getPreviewUrl()
     */
    public function getPreviewUrl( core_kernel_classes_Resource $item, $languageCode) {
       return _url('index', 'QtiPreview', 'taoQTI', array('uri' => $item->getUri(), 'lang' => $languageCode)); 
    }
    
    /**
     * (non-PHPdoc)
     * @see taoItems_models_classes_ExportableItemModel::getExportHandlers()
     */
    public function getExportHandlers() {
    	return array(
    		new taoQTI_models_classes_Export_QtiPackage20ExportHandler()
    	);
    }
    
    public function getImportHandlers() {
    	return array(
    		new taoQTI_models_classes_import_QtiPackageImport(),
    		new taoQTI_models_classes_import_QtiItemImport()
    	);
    }

    public function getCompiler(core_kernel_classes_Resource $item) {
        return new taoQTI_models_classes_QtiItemCompiler($item);
    }
}