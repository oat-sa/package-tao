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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 */

namespace oat\taoQtiItem\model\qti;

use oat\taoQtiItem\model\qti\exception\XIncludeException;
use oat\taoQtiItem\model\qti\metadata\MetadataRegistry;
use oat\taoQtiItem\model\SharedLibrariesRegistry;
use oat\taoQtiItem\model\qti\Parser;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\XIncludeLoader;
use oat\taoQtiItem\model\qti\exception\ParsingException;
use \tao_models_classes_Service;
use \core_kernel_classes_Resource;
use \taoItems_models_classes_ItemsService;
use \common_Logger;
use \common_Exception;
use \core_kernel_versioning_Repository;
use \Exception;
use oat\taoQtiItem\model\ItemModel;
use oat\taoItems\model\media\ItemMediaResolver;

/**
 * The QTI_Service gives you a central access to the managment methods of the
 * objects
 *
 * @author Somsack Sipasseuth <sam@taotesting.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class Service extends tao_models_classes_Service
{

    /**
     * Load a QTI_Item from an, RDF Item using the itemContent property of the
     * Item as the QTI xml
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource item
     * @throws \common_Exception If $item is not representing an item with a QTI item model.
     * @return oat\taoQtiItem\model\qti\Item An item as a business object.
     */
    public function getDataItemByRdfItem(core_kernel_classes_Resource $item, $langCode = '', $resolveXInclude = false)
    {
        
        $returnValue = null;
        $itemService = taoItems_models_classes_ItemsService::singleton();

        //check if the item is QTI item
        if ($itemService->hasItemModel($item, array(ItemModel::MODEL_URI))) {

            //get the QTI xml
            $itemContent = $itemService->getItemContent($item, $langCode);

            if (!empty($itemContent)) {
                //Parse it and build the QTI_Data_Item
                $qtiParser = new Parser($itemContent);
                $returnValue = $qtiParser->load();
                
                if($resolveXInclude && !empty($langCode)){
                    try{
                        //loadxinclude
                        $resolver = new ItemMediaResolver($item, $langCode);
                        $xincludeLoader = new XIncludeLoader($returnValue, $resolver);
                        $xincludeLoader->load(true);
                    } catch(XIncludeException $exception){
                        common_Logger::e($exception->getMessage());
                    }
                }
            
                if (!$returnValue->getAttributeValue('xml:lang')) {
                    $returnValue->setAttribute('xml:lang', \common_session_SessionManager::getSession()->getDataLanguage());
                }
            } else {
                // fail silently, since file might not have been created yet
                // $returnValue is then NULL.
                common_Logger::d('item('.$item->getUri().') is empty, newly created?');
            }
        } else {
            throw new common_Exception('Non QTI item('.$item->getUri().') opened via QTI Service');
        }
        
        return $returnValue;
    }

    /**
     * Save a QTI_Item into an RDF Item, by exporting the QTI_Item to QTI xml
     * and saving it in the itemContent prioperty of the RDF Item
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Item qtiItem
     * @param  Resource rdfItem
     * @param  string commitMessage
     * @param  Repository fileSource
     * @return boolean
     */
    public function saveDataItemToRdfItem(Item $qtiItem, core_kernel_classes_Resource $rdfItem, $commitMessage = '', core_kernel_versioning_Repository $fileSource = null)
    {
        $returnValue = (bool) false;

        if (!is_null($rdfItem) && !is_null($qtiItem)) {

            $itemService = taoItems_models_classes_ItemsService::singleton();

            //check if the item is QTI item
            if ($itemService->hasItemModel($rdfItem, array(ItemModel::MODEL_URI))) {

                //set the current data lang in the item content to keep the integrity
                $qtiItem->setAttribute('xml:lang', \common_session_SessionManager::getSession()->getDataLanguage());

                //get the QTI xml
                $returnValue = $itemService->setItemContent($rdfItem, $qtiItem->toXML(), '', $commitMessage, $fileSource);
            }
        }

        return (bool) $returnValue;
    }

    /**
     * Load a QTI item from a qti file in parameter.
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string file
     * @return oat\taoQtiItem\model\qti\Item
     */
    public function loadItemFromFile($file)
    {
        $returnValue = null;

        if (is_string($file) && !empty($file)) {

            //validate the file to import
            try {
                $qtiParser = new Parser($file);
                $qtiParser->validate();

                if (!$qtiParser->isValid()) {
                    throw new ParsingException($qtiParser->displayErrors());
                }

                $returnValue = $qtiParser->load();
            } catch(ParsingException $pe) {
                throw new ParsingException($pe->getMessage());
            } catch(Exception $e) {
                throw new Exception("Unable to load file {$file} caused  by {$e->getMessage()}");
            }
        }

        return $returnValue;
    }

    /**
     * Build the XHTML/CSS/JS from a QTI_Item to be rendered.
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Item item the item to render
     * @return string
     */
    public function renderQTIItem(Item $item, $langCode = 'en-US')
    {
        $returnValue = '';

        if (!is_null($item)) {
            $returnValue = $item->toXHTML(array('lang' => $langCode));
        }

        return (string) $returnValue;
    }
    
    public function getVariableElements(Item $item)
    {
        $allData = $item->getDataForDelivery();
        return $allData['variable'];
    }

    /**
     * Obtain a reference on the PCI/PIC Shared Library Registry.
     * 
     * @return \oat\taoQtiItem\model\SharedLibrariesRegistry
     */
    public function getSharedLibrariesRegistry()
    {
        $basePath = ROOT_PATH . 'taoQtiItem/views/js/portableSharedLibraries';
        $baseUrl = ROOT_URL . 'taoQtiItem/views/js/portableSharedLibraries';
        
        return new SharedLibrariesRegistry($basePath, $baseUrl);
    }
    
    /**
     * Obtain a reference on the Metadata Injector/Extractor Registry.
     * 
     * @return \oat\taoQtiItem\model\qti\metadata\MetadataRegistry
     */
    public function getMetadataRegistry()
    {
        return new MetadataRegistry();
    }
}