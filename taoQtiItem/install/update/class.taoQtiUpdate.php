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
 * Copyright (c) 2013 Open Assessment Technologies S.A.
 * 
 */

use oat\taoQtiItem\model\qti\Parser;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\ItemModel;

/**
 * Update script for qti
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 */
class taoQTI_scripts_update_taoQtiUpdate extends tao_scripts_Runner
{

    public function run()
    {
        $itemService = taoItems_models_classes_ItemsService::singleton();
        $itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
        $items = $itemClass->getInstances(true);

        foreach($items as $item){
            $itemModel = $itemService->getItemModel($item);
            if(!is_null($itemModel) && $itemModel->getUri() == ItemModel::MODEL_URI){
                $this->out('qti item found: '.$item->getLabel());
                $this->convertQtiItem($item);
            }
//            break;
        }
    }

    protected function convertQtiItem(core_kernel_classes_Resource $item){
        
        $itemService = taoItems_models_classes_ItemsService::singleton();
        $itemContentProp = new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
        $usedLanguages = $item->getUsedLanguages($itemContentProp);
        foreach($usedLanguages as $lang){
            $this->out('language:'.$lang);
            $xmlString = $itemService->getItemContent($item, $lang);
            if(empty($xmlString)){
                   $this->out('no qti xml found');
            }else{
                $qti = $this->convertQtiFromV2p0ToV2p1($xmlString);
                if(empty($qti)){
                    $this->out('fail');
                }else{
                    $this->out('done');
                }
            }
            
        }
    }

    protected function convertQtiFromV2p0ToV2p1($xml){
        
        $returnValue = '';
        
        $qtiParser = new Parser($xml);
        $qtiv2p1xsd = ROOT_PATH.'taoQTI/models/classes/QTI/data/qtiv2p0/imsqti_v2p0.xsd';
        $qtiParser->validate($qtiv2p1xsd);
        if($qtiParser->isValid()){
            $this->out('is a qti 2.0 item');
            $item = $qtiParser->load();
            if($item instanceof Item){
                $this->out('item loaded as QTI 2.1');
                $returnValue = $item->toXML();
            }
        }else{
            $this->out('does not seem to be a valid qti 2.0 item, attempt to load it anyway.');
            $item = $qtiParser->load();
            if($item instanceof Item){
                $this->out('Done! Item loaded as QTI 2.1');
                $returnValue = $item->toXML();
            }
        }
        
        return $returnValue;
    }

}
