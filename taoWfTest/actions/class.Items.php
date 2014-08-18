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
* Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
*/

/**
 * Actions related to Test's items
 *
 * @package taoWfTest
 
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoWfTest_actions_Items extends tao_actions_CommonModule {

    
    /**
	 * Get the list of items to populate the checkbox tree of related items.
     * It prints to the HTTP response the tree data formated using json.
	 * @return void
	 */
	public function getTreeData() {
		if($this->hasRequestParameter('classUri')) {
			$classUri = tao_helpers_Uri::decode($this->getRequestParameter('classUri'));
			$class = new core_kernel_classes_Class($classUri);
			$hideNode = true; 
		} elseif ($this->hasRequestParameter('rootNode')) {
			$class = new core_kernel_classes_Class($this->getRequestParameter('rootNode'));
			$hideNode = false;
		} else {
			throw new common_Exception('Missing node information for '.__FUNCTION__);
		}
		
		$openNodes	= array($class->getUri());
		if ($this->hasRequestParameter('openNodes') && is_array($this->getRequestParameter('openNodes'))) {
			$openNodes = array_merge($openNodes, $this->getRequestParameter('openNodes'));
		}
		
		$limit		    = $this->hasRequestParameter('limit') ? $this->getRequestParameter('limit') : 10;
		$offset		    = $this->hasRequestParameter('offset') ? $this->getRequestParameter('offset') : 0;
		$showInst	    = $this->hasRequestParameter('hideInstances') ? !$this->getRequestParameter('hideInstances') : true;
        $propertyFilter = $this->getTreeFilter();
		
		$factory = new tao_models_classes_GenerisTreeFactory();
		$array = $factory->buildTree($class, $showInst, $openNodes, $limit, $offset, $propertyFilter);
		if ($hideNode) {
			$array = isset($array['children']) ? $array['children'] : array();
		}
	
        header('Content-Type : application/json');	
        echo json_encode($array);
	}


    /**
     * Get filter options of the tree form the HTTP paramters
     *  - itemModel: filter by item model (or at least ensure a model is defined)
     * @return $propertyFilter
     */
    private function getTreeFilter(){
        $propertyFilter = array();
        if ($this->hasRequestParameter('itemModel')) {
            $propertyFilter = array(
                TAO_ITEM_MODEL_PROPERTY => tao_helpers_Uri::decode($this->getRequestParameter('itemModel'))
            );
        } else {

           //Get all item model values so we ensure the item has a model. 
           //I know it would be better to be able to filter instances that don't have a model, 
           //but the API is incomplete 
            $itemModels = array();
            $itemModelClass = new core_kernel_classes_Class(TAO_ITEM_MODEL_CLASS); 
            foreach($itemModelClass->getInstances() as $itemModel){
                $itemModels[] = $itemModel->getUri(); 
            }
            if(!empty($itemModels)){
                $propertyFilter = array(
                    TAO_ITEM_MODEL_PROPERTY => $itemModels
                );
            }
        }
        return $propertyFilter;
    }
}
