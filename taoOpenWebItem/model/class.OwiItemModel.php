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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */

/**
 * Service dedicated to the management of the XHTML Item Model.
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 * @subpackage models_classes_XHTML
 */
class taoOpenWebItem_model_OwiItemModel
	implements  taoItems_models_classes_itemModel
		,taoItems_models_classes_ExportableItemModel
		,taoItems_models_classes_ImportableItemModel
{
    /**
     * default constructor to ensure the implementation
     * can be instanciated
     */
    public function __construct() {
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
        //$xhtml = self::referenceApis($xhtml); // throws ItemModelException.
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
    	$apis = self::buildApisArray();
    	foreach ($apis as $pattern => $infos){
    		$found += taoItems_helpers_Xhtml::removeScriptElements($dom, '/' . $pattern . '/i');
    	}
    	if ($found > 0) {
    		common_Logger::i('found '.$found.' references to deprecated APIs, replacing with legacy API');
	    	$taoItemsExt = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
	    	$legacyApiSrc = $taoItemsExt->getConstant('BASE_WWW') . 'js/legacyApi/taoLegacyApi.min.js';
	    	taoItems_helpers_Xhtml::addScriptElement($dom, $legacyApiSrc);
    	}
    	return $dom->saveHTML();
    }
        
    /**
     * Add script elements to OWI items if there are some missing APIs.
     * Missing APIs could be 
     * - taoApi
     * - taoMatching
     * - wfApi (only if the wfEngine extension is installed)
     * 
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @access protected
     * @param string $xhtml An XHTML stream as a string.
     * @return string An XHTML stream as a string with new references to APIs.
     * @throws taoItems_models_classes_ItemModelException If the item content cannot be parsed or contains errors.
     */
    protected static function referenceApis($xhtml){
    	try{
    		$dom = new DOMDocument('1.0', TAO_DEFAULT_ENCODING);
    		if (!$dom->loadHTML($xhtml)){
    			$msg = "An error occured while loading the XML content of the rendered item.";
    			throw new taoItems_models_classes_ItemModelException($msg);
    		}
    		else{
    			$apis = self::buildApisArray();
    	
    			foreach ($apis as $pattern => $infos){
    				if (!taoItems_helpers_Xhtml::hasScriptElements($dom, '/' . $pattern . '/i')){
    					taoItems_helpers_Xhtml::addScriptElement($dom, $infos['src']);
    	
    					common_Logger::d("Script element '${pattern}' added to item.");
    				}
    			}
    	
    			return $dom->saveHTML();
    		}
    	}
    	catch (DOMException $e){
    		$msg = "An error occured while parsing the XML content of the rendered item.";
    		throw new taoItems_models_classes_ItemModelException($msg);
    	}
    	catch (taoItems_models_classes_ItemModelException $e){
    		throw $e;
    	}
    }
    
    /**
     * Builds an associative array containing information about which APIs must be
     * present to run an OWI item at execution time.
     * 
     * Example:
     * <code>
     * array('taoApi' => array('src' => 'http://www.myplatform.com/taoItems/views/js/taoApi/taoApi.min.js',
     * 						   'path' => '/var/www/tao/taoItems/views/js/taoApi/taoApi.min.js'),
     * 						   ...);
     * </code>
     * 
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return array An associative array.
     */
    protected static function buildApisArray(){
   		$extManager = common_ext_ExtensionsManager::singleton();
    	$taoItemsExt = $extManager->getExtensionById('taoItems');
    	$taoItemsBaseWww = $taoItemsExt->getConstant('BASE_WWW');
    	$taoItemsWwwPath = $taoItemsExt->getConstant('WWW_PATH');
    	
    	// item Api
    	$apis = array();
    	$apis['taoApi'] = array(
    		'src' 	=> $taoItemsBaseWww . 'js/taoApi/taoApi.min.js',
    		'path'	=> $taoItemsWwwPath . 'js' . DIRECTORY_SEPARATOR . 'taoApi' . DIRECTORY_SEPARATOR . 'taoApi.min.js'
    	);
    	
    	// wf Api
    	if (($wfEngineExt = $extManager->getExtensionById('wfEngine')) != null){
    		$wfEngineExt = $extManager->getExtensionById('wfEngine');
    		$wfEngineBaseWww = $wfEngineExt->getConstant('BASE_WWW');
    		$wfEngineWwwPath = $wfEngineExt->getConstant('WWW_PATH');
    		
    		$apis['wfApi'] = array(
    			'src'	=> $wfEngineBaseWww . 'js/wfApi/wfApi.min.js',
    			'path'	=> $wfEngineWwwPath . 'js' . DIRECTORY_SEPARATOR . 'wfApi' . DIRECTORY_SEPARATOR . 'wfApi.min.js'
    		);
    	}
    	
    	if (($taoQtiExt = $extManager->getExtensionById('taoQTI')) != null) {
    		$taoQtiWww = $taoQtiExt->getConstant('BASE_WWW');
    		$apis['taoMatching'] 	= array(
    			'src'	=> $taoQtiWww . 'js/responseProcessing/taoMatching.min.js',
    			'path'	=> $taoQtiWww . 'js' . DIRECTORY_SEPARATOR . 'responseProcessing' . DIRECTORY_SEPARATOR . 'taoMatching.min.js'
    		);
    	}
    	
    	return $apis;
    }

	public function getExportHandlers() {
		return array(
			new taoOpenWebItem_model_export_OwiExportHandler()
		);
	}
	
	public function getImportHandlers() {
		return array(
			new taoOpenWebItem_model_import_OwiImportHandler()
		);
	}
	
	public function getCompiler(core_kernel_classes_Resource $item) {
	    return new taoItems_models_classes_ItemCompiler($item);
	}
}