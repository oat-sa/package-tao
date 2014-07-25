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
 * QTI Inline Choice Interaction
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10321
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_interaction_InlineChoiceInteraction extends taoQTI_models_classes_QTI_interaction_InlineInteraction
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'inlineChoiceInteraction';
    static protected $choiceClass = 'taoQTI_models_classes_QTI_choice_InlineChoice';
    static protected $baseType = 'identifier';
    
    public static function getTemplateQti(){
        //use block type interaction to output choices
        return static::getTemplatePath().'interactions/qti.blockInteraction.tpl.php';
    }

    protected function getUsedAttributes(){
        return array_merge(
                parent::getUsedAttributes(), array(
            'taoQTI_models_classes_QTI_attribute_Shuffle',
            'taoQTI_models_classes_QTI_attribute_Required'
                )
        );
    }

}