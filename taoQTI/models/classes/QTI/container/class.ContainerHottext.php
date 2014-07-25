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
 * The QTI_Container object represents the generic element container
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @subpackage models_classes_QTI_container
 */
class taoQTI_models_classes_QTI_container_ContainerHottext extends taoQTI_models_classes_QTI_container_Container
{
	
	/**
     * return the list of available element classes
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return array
     */
	public function getValidElementTypes(){
		return array(
			'taoQTI_models_classes_QTI_Math',
			'taoQTI_models_classes_QTI_feedback_Feedback',
			'taoQTI_models_classes_QTI_PrintedVariable',
			'taoQTI_models_classes_QTI_Object',
			'taoQTI_models_classes_QTI_choice_Hottext'
		);
	}
	
	public function afterElementSet(taoQTI_models_classes_QTI_Element $qtiElement){
		parent::afterElementSet($qtiElement);
		if($qtiElement instanceof taoQTI_models_classes_QTI_choice_Hottext){
			$item = $this->getRelatedItem();
			if(isset($item)){
				$qtiElement->setRelatedItem($item);
			}
		}
	}
	
} /* end of class taoQTI_models_classes_QTI_container_ContainerHottext */