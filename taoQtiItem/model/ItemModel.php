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

namespace oat\taoQtiItem\model;

use oat\taoQtiItem\model\Export\ApipPackageExportHandler;
use oat\taoQtiItem\model\import\ApipPackageImport;
use oat\taoQtiItem\model\qti\Service;
use oat\taoQtiItem\model\Export\QtiPackageExportHandler;
use oat\taoQtiItem\model\import\QtiPackageImport;
use oat\taoQtiItem\model\import\QtiItemImport;
use \tao_models_classes_export_ExportProvider;
use \tao_models_classes_import_ImportProvider;
use \common_ext_ExtensionsManager;
use \core_kernel_classes_Resource;
use \common_Logger;
use taoItems_models_classes_itemModel;

/**
 * Short description of class oat\taoQtiItem\model\ItemModel
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoQTI
 
 */
class ItemModel
        implements taoItems_models_classes_itemModel,
                   tao_models_classes_export_ExportProvider,
                   tao_models_classes_import_ImportProvider
{

    const MODEL_URI = "http://www.tao.lu/Ontologies/TAOItem.rdf#QTI";
    
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
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem');
    }

    /**
     * render used for deploy and preview
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param core_kernel_classes_Resource $item
     * @param $langCode
     * @throws \common_Exception
     * @return string
     */
    public function render( core_kernel_classes_Resource $item, $langCode)
    {
        $returnValue = (string) '';

		$qitService = Service::singleton();
		
		$qtiItem = $qitService->getDataItemByRdfItem($item, $langCode);
    	
		if(!is_null($qtiItem)) {
			$returnValue = $qitService->renderQTIItem($qtiItem, $langCode);
		} else {
			common_Logger::w('No qti data for item '.$item->getUri().' in '.__FUNCTION__, 'taoQtiItem');
		}

        return (string) $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see taoItems_models_classes_itemModel::getPreviewUrl()
     */
    public function getPreviewUrl( core_kernel_classes_Resource $item, $languageCode) {
       return _url('index', 'QtiPreview', 'taoQtiItem', array('uri' => $item->getUri(), 'lang' => $languageCode)); 
    }
    
    /**
     * @see taoItems_models_classes_itemModel::getPreviewUrl()
     */
    public function getAuthoringUrl( core_kernel_classes_Resource $item) {
       return _url('index', 'QtiCreator', 'taoQtiItem', array(
            'instance' => $item->getUri(), 
            'STANDALONE_MODE' => intval(\tao_helpers_Context::check('STANDALONE_MODE'))
        )); 
    }

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_export_ExportProvider::getExportHandlers()
     */
    public function getExportHandlers() {
    	return array(
    	    new ApipPackageExportHandler(),
    		new QtiPackageExportHandler()
    	);
    }
    
    public function getImportHandlers() {
    	return array(
    	    new ApipPackageImport(),
    		new QtiItemImport(),
    	    new QtiPackageImport(),
    	);
    }

    public function getCompilerClass() {
        return 'oat\\taoQtiItem\\model\\QtiItemCompiler';
    }

    public function getPackerClass() {
        return 'oat\\taoQtiItem\\model\\pack\\QtiItemPacker';
    }
}
