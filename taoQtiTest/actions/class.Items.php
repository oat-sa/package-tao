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
*/

/**
 * Common test related actions
 *
 * @package taoQtiTest
 
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoQtiTest_actions_Items extends tao_actions_CommonModule {

    
    /**
     * Get ALL QTI items.
     * The response is encoded in json and contains only the usefull data.
     * A pattern parameter is allowed to filter results.
     * 
     * This method will be refactored (limit, filtering, etc.) with the resource widget.
     */
    public function get() {

        $items = array();
        
        $limit = 50;
        
        $pattern = null;
        if($this->hasRequestParameter('pattern') && trim($this->getRequestParameter('pattern')) != '' ){
            $pattern = preg_quote($this->getRequestParameter('pattern'));
        }

        //get QTI Items
        $itemsService = taoItems_models_classes_ItemsService::singleton();
        $i = 0;
        foreach ($itemsService->getAllByModel(TAO_ITEM_MODEL_QTI) as $itemResource) {
            if($i > $limit){
                break;
            }
            
            //reformat them
            $item = array(
                'uri' => tao_helpers_Uri::encode($itemResource->getUri()),
                'label' => $itemResource->getLabel()
            );
            
            //add the type in case of TAO_ITEM subclass
            $types = $itemResource->getTypes();
            foreach ($types as $type) {
                if ($type->getUri() != TAO_ITEM_CLASS) {
                    $item['parent'] = $type->getLabel();
                }
            }
            if(!is_null($pattern) && !preg_match('/'.$pattern.'+/i', $item['label']) && (!array_key_exists('parent', $item) || !preg_match('/'.$pattern.'+/i', $item['parent']))){
                continue;
            }
            
            $items[] = $item;
        }

        $this->setContentHeader('application/json');
        print json_encode($items);
    }
}
