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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 * 
 */

namespace oat\tao\helpers\form\elements;

use tao_helpers_form_elements_MultipleElement;
use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use tao_helpers_Uri;

/**
 * Implementation model selector
 * 
 * @abstract
 * @package tao
 */
abstract class Model
    extends tao_helpers_form_elements_MultipleElement
{

    /**
     * @todo should be a constant
     * @var string
     */
    protected $widget = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ModelSelector';
    
    /**
     * (non-PHPdoc)
     * @see tao_helpers_form_elements_MultipleElement::getOptions()
     */
    public function getOptions() {
        $options = parent::getOptions();
        
        $statusProperty = new core_kernel_classes_Property(PROPERTY_ABSTRACTMODEL_STATUS);
        $current = $this->getEvaluatedValue();
                    
        $options = array();
        foreach (parent::getOptions() as $optUri => $optLabel) {
            $model = new core_kernel_classes_Resource(tao_helpers_Uri::decode($optUri));
            $status = $model->getOnePropertyValue($statusProperty);
            if (!is_null($status) && $status->getUri() != TAO_ITEM_MODEL_STATUS_DEPRECATED) {
                $options[$optUri] = $optLabel; 
            } elseif ($model->getUri() == $current) {
                $options[$optUri] = $optLabel.' ('.(is_null($status) ? __('unknown') : $status->getLabel()).')';
            }
        }
        
        return $options;
    }

}