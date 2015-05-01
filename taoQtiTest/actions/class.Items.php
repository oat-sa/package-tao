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
 * Actions about Items in a Test context.
 * 
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class taoQtiTest_actions_Items extends tao_actions_CommonModule 
{
    /**
     * Get ALL QTI items within the platform.
     * 
     * The response is encoded in JSON and contains only some basic data about items (uri, label keys).
     * A 'pattern' request parameter parameter is allowed to filter results at search time.
     * A 'notempty' ('1', 'true', 'on' and 'yes' values available) request parameter is allowed to filter empty items.
     * 
     * This method will be refactored (limit, filtering, etc.) with the resource widget.
     */
    public function get() 
    {
        $items = array();
        $propertyFilters = array(TAO_ITEM_MODEL_PROPERTY => TAO_ITEM_MODEL_QTI);
        $options = array('recursive' => true, 'like' => true, 'limit' => 50);
        $notEmpty = filter_var($this->getRequestParameter('notempty'), FILTER_VALIDATE_BOOLEAN);

        if (($pattern = $this->getRequestParameter('pattern')) !== null && $pattern !== '') {
            $propertyFilters[RDFS_LABEL] = $pattern;
        }

        $itemsService = taoItems_models_classes_ItemsService::singleton();
        $itemClass = $itemsService->getRootClass();
        
        $result = $itemClass->searchInstances($propertyFilters, $options);
        
        foreach ($result as $qtiItem) {
            if (!$notEmpty || $itemsService->hasItemContent($qtiItem)) {
                $items[] = array(
                    'uri' => $qtiItem->getUri(),
                    'label' => $qtiItem->getLabel()
                );
            }
        }

        $this->returnJson($items);
    }
}
