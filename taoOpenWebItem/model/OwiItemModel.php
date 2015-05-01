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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2014 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoOpenWebItem\model;

use oat\taoOpenWebItem\model\export\OwiExportHandler;
use oat\taoOpenWebItem\model\import\OwiImportHandler;
use \taoItems_models_classes_itemModel;
use \tao_models_classes_export_ExportProvider;
use \tao_models_classes_import_ImportProvider;
use \common_ext_ExtensionsManager;
use \core_kernel_classes_Resource;
use \taoItems_models_classes_ItemsService;
use \DOMDocument;
use \taoItems_models_classes_ItemModelException;
use \taoItems_helpers_Xhtml;
use \common_Logger;
use \common_exception_NotImplemented;
use oat\tao\helpers\Template;

/**
 * Service dedicated to the management of the XHTML Item Model.
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems

 */
class OwiItemModel
	implements  taoItems_models_classes_itemModel
		,tao_models_classes_export_ExportProvider
		,tao_models_classes_import_ImportProvider
{
    
    const ITEMMODEL_URI = 'http://www.tao.lu/Ontologies/TAOItem.rdf#XHTML';
    
    /**
     * default constructor to ensure the implementation
     * can be instanciated
     */
    public function __construct() {
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoOpenWebItem');
    }

    /**
     * Render an XHTML item.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item The item to render.
     * @return string The rendered item.
     * @throws taoItems_models_classes_ItemModelException
     */
    public function render( core_kernel_classes_Resource $item, $langCode)
    {
    	$itemsService = taoItems_models_classes_ItemsService::singleton();
        $xhtml = $itemsService->getItemContent($item, $langCode);

        // Check if all needed APIs are referenced.
        $xhtml = $this->replaceDeprecatedApis($xhtml); // throws ItemModelException.

        return $xhtml;
    }

    /**
     * (non-PHPdoc)
     * @see taoItems_models_classes_itemModel::getPreviewUrl()
     */
    public function getPreviewUrl( core_kernel_classes_Resource $item, $languageCode) {
        return _url('index', 'ItemPreview', 'taoItems', array('uri' => $item->getUri(), 'lang' => $languageCode));
    }

    /**
     * @see taoItems_models_classes_itemModel::getAuthoringUrl()
     */
    public function getAuthoringUrl( core_kernel_classes_Resource $item) {
        return _url('index', 'Authoring', 'taoOpenWebItem', array('instance' => $item->getUri()));
    }

    /**
     * Removes unnescessary API references
     *
     * @param Resource $item
     */
    protected function replaceDeprecatedApis($xhtml) {
    	$dom = new DOMDocument('1.0', TAO_DEFAULT_ENCODING);
    	if (!$dom->loadHTML($xhtml)){
    		throw new taoItems_models_classes_ItemModelException("An error occured while loading the XML content of the rendered item.");
    	}

    	$found = 0;
    	$deprecatedApis = array('taoApi', 'wfApi');
    	foreach ($deprecatedApis as $pattern){
    		$found += taoItems_helpers_Xhtml::removeScriptElements($dom, '/' . $pattern . '/i');
    	}
    	if ($found > 0) {
    		common_Logger::i('found '.$found.' references to deprecated APIs, replacing with legacy API');
	    	$taoItemsExt = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
	    	$legacyApiSrc = Template::js('legacyApi/taoLegacyApi.min.js', 'taoItems');
	    	taoItems_helpers_Xhtml::addScriptElement($dom, $legacyApiSrc);
    	}
    	return $dom->saveHTML();
    }

	public function getExportHandlers() {
		return array(
			new OwiExportHandler()
		);
	}

	public function getImportHandlers() {
		return array(
			new OwiImportHandler()
		);
	}

	public function getCompilerClass() {
	    return 'taoItems_models_classes_ItemCompiler';
	}

    public function getPackerClass() {
        throw new common_exception_NotImplemented("The packer isn't yet implemented for Open Web Items");
    }
}
