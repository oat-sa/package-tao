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
 * QTI Graphic Order Interaction
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10366
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_interaction_GraphicOrderInteraction extends taoQTI_models_classes_QTI_interaction_GraphicInteraction
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'graphicOrderInteraction';
    static protected $choiceClass = 'taoQTI_models_classes_QTI_choice_HotspotChoice';
    static protected $baseType = 'identifier';
    
    protected function getUsedAttributes(){
        return array_merge(
                parent::getUsedAttributes(), array(
            'taoQTI_models_classes_QTI_attribute_MaxChoicesOrderInteraction',
            'taoQTI_models_classes_QTI_attribute_MinChoicesOrderInteraction'
                )
        );
    }

}