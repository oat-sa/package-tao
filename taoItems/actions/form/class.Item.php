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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class taoItems_actions_form_Item
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 
 */
class taoItems_actions_form_Item extends tao_actions_form_Instance
{
    /**
     * (non-PHPdoc)
     * @see tao_actions_form_Instance::initElements()
     */
    protected function initElements()
    {
        parent::initElements();
        
        $elementId = tao_helpers_Uri::encode(TAO_ITEM_MODEL_PROPERTY);
        $ele = $this->form->getElement($elementId);
        $ele->feed();
        $modelUri = $ele->getEvaluatedValue();
        
        if (empty($modelUri)) {
            
            // remove deprecated models
            $statusProperty = new core_kernel_classes_Property(TAO_ITEM_MODEL_STATUS_PROPERTY);
            $options = array();
            foreach ($ele->getOptions() as $optUri => $optLabel) {
                $model = new core_kernel_classes_Resource(tao_helpers_Uri::decode($optUri));
                $status = $model->getOnePropertyValue($statusProperty);
                if(!is_null($status) && $status->getUri() != TAO_ITEM_MODEL_STATUS_DEPRECATED){
                    $options[$optUri] = $optLabel; 
                }
            }
            $ele->setOptions($options);
            
        } else {
            // replace radio with hidden element
            $this->form->removeElement($elementId);
            $itemModelElt = tao_helpers_form_FormFactory::getElement($elementId, 'Hidden');
            $itemModelElt->setValue($modelUri);
            $this->form->addElement($itemModelElt);
            
            // display model label
            $model = new core_kernel_classes_Resource($modelUri);
            $itemModelLabelElt = tao_helpers_form_FormFactory::getElement('itemModelLabel', 'Label');
            $itemModelLabelElt->setDescription(__('Item Model'));
            $itemModelLabelElt->setValue($model->getLabel());
            $this->form->addElement($itemModelLabelElt);
        }
    }
}