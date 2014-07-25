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
 * The QTI_Service gives you a central access to the managment methods of the
 * objects
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_Service extends tao_models_classes_Service
{

    /**
     * Load a QTI_Item from an, RDF Item using the itemContent property of the
     * Item as the QTI xml
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource item
     * @return taoQTI_models_classes_QTI_Item
     */
    public function getDataItemByRdfItem(core_kernel_classes_Resource $item, $langCode = ''){
        
        $returnValue = null;

        if(!is_null($item)){

            try{

                $itemService = taoItems_models_classes_ItemsService::singleton();

                //check if the item is QTI item
                if($itemService->hasItemModel($item, array(TAO_ITEM_MODEL_QTI))){

                    //get the QTI xml
                    $itemContent = $itemService->getItemContent($item, $langCode);

                    if(!empty($itemContent)){
                        //Parse it and build the QTI_Data_Item
                        $qtiParser = new taoQTI_models_classes_QTI_Parser($itemContent);
                        $returnValue = $qtiParser->load();

                        if(!$returnValue->getAttributeValue('xml:lang')){
                            $returnValue->setAttribute('xml:lang', core_kernel_classes_Session::singleton()->getDataLanguage());
                        }

                        //load Measures
                        $measurements = $itemService->getItemMeasurements($item);
                        foreach($returnValue->getOutcomes() as $outcome){
                            foreach($measurements as $measurement){
                                if($measurement->getIdentifier() == $outcome->getIdentifier() && !is_null($measurement->getScale())){
                                    $outcome->setScale($measurement->getScale());
                                    break;
                                }
                            }
                        }
                    }else{
                        // fail silently, since file might not have been created yet
                        common_Logger::d('item('.$item->getUri().') is empty, newly created?');
                    }
                }else{
                    throw new common_Exception('Non QTI item('.$item->getUri().') opened via QTI Service');
                }
            }catch(common_Exception $ce){
                print $ce;
            }
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
    public function saveDataItemToRdfItem(taoQTI_models_classes_QTI_Item $qtiItem, core_kernel_classes_Resource $rdfItem, $commitMessage = '', core_kernel_versioning_Repository $fileSource = null){
        
        $returnValue = (bool) false;

        if(!is_null($rdfItem) && !is_null($qtiItem)){

            try{

                $itemService = taoItems_models_classes_ItemsService::singleton();

                //check if the item is QTI item
                if($itemService->hasItemModel($rdfItem, array(TAO_ITEM_MODEL_QTI))){

                    //set the current data lang in the item content to keep the integrity
                    $qtiItem->setAttribute('xml:lang', core_kernel_classes_Session::singleton()->getDataLanguage());

                    //get the QTI xml
                    $itemsaved = $itemService->setItemContent($rdfItem, $qtiItem->toXML(), '', $commitMessage, $fileSource);

                    if($itemsaved){
                        // extract the measurements
                        $measurements = array();
                        foreach($qtiItem->getOutcomes() as $outcome){
                            $measurements[] = $outcome->toMeasurement($qtiItem);
                        }
                        $returnValue = $itemService->setItemMeasurements($rdfItem, $measurements);
                    }
                }
            }catch(common_Exception $ce){
                print $ce;
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
     * @return taoQTI_models_classes_QTI_Item
     */
    public function loadItemFromFile($file){
        
        $returnValue = null;

        if(is_string($file) && !empty($file)){

            //validate the file to import
            try{
                $qtiParser = new taoQTI_models_classes_QTI_Parser($file);
                $qtiParser->validate();

                if(!$qtiParser->isValid()){
                    throw new taoQTI_models_classes_QTI_ParsingException($qtiParser->displayErrors());
                }

                $returnValue = $qtiParser->load();
            }catch(taoQTI_models_classes_QTI_ParsingException $pe){
                throw new taoQTI_models_classes_QTI_ParsingException($pe->getMessage());
            }catch(Exception $e){
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
    public function renderQTIItem(taoQTI_models_classes_QTI_Item $item){
        
        $returnValue = '';

        if(!is_null($item)){
            $returnValue = $item->toXHTML();
        }

        return (string) $returnValue;
    }

    /**
     * force the saving of the object in the persistancy. Usually an object is
     * by destruction.
     * Use this method if you know what your are doing.
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  taoQTI_models_classes_QTI_Item item
     * @return boolean
     */
    public function saveItemToSession(taoQTI_models_classes_QTI_Item $item){
        
        taoQTI_models_classes_QTI_QTISessionCache::singleton()->put($item);
        
        return true;
    }

    /**
     * Retrive a QTI_Data child instance by it's id
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string serial
     * @param  string type
     * @return taoQTI_models_classes_QTI_Data
     */
    public function getItemFromSession($serial){
        
        $returnValue = null;

        try{
            $returnValue = taoQTI_models_classes_QTI_QTISessionCache::singleton()->get($serial);
            if(!empty($type) && !$returnValue instanceof $type){
                throw new common_Exception("object retrieved is a ".get_class($returnValue)." instead of {$type}.");
            }
        }catch(common_cache_NotFoundException $e){
            // do nothing, return null
        }

        return $returnValue;
    }

}