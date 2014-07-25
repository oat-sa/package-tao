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
 * QTI Hottext Interaction
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_interaction_HottextInteraction extends taoQTI_models_classes_QTI_interaction_ContainerInteraction
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'hottextInteraction';
    static protected $choiceClass = 'taoQTI_models_classes_QTI_choice_Hottext';
    static protected $containerType = 'taoQTI_models_classes_QTI_container_ContainerHottext';
    static protected $baseType = 'identifier';
    
    protected function getUsedAttributes(){
        return array_merge(
                parent::getUsedAttributes(), array(
            'taoQTI_models_classes_QTI_attribute_MaxChoices',
            'taoQTI_models_classes_QTI_attribute_MinChoices'
                )
        );
    }

    public function getChoices($matchSet = null){
        return $this->getBody()->getElements(static::$choiceClass);
    }

    public function addChoice(taoQTI_models_classes_QTI_choice_Choice $choice, $matchSet = null){
        throw new taoQTI_models_classes_QTI_QtiModelException('For Hottext Interaction, the choices are in the container, please use Container::setElement() instead');
    }

    public function createChoice($choiceAttributes = array(), $choiceValue = null, $matchSet = null){
        throw new taoQTI_models_classes_QTI_QtiModelException('For Hottext Interaction, the choices are in the container, please use Container::setElement() instead');
    }

    public function removeChoice(taoQTI_models_classes_QTI_choice_Choice $choice, $matchSet = null){
        return $this->body->removeElement($choice);
    }

    protected function getTemplateQtiVariables(){
        $variables = parent::getTemplateQtiVariables();
        unset($variables['choices']); //hottexts are contained in the container already
        return $variables;
    }
    
    public function toArray(){
        $data = parent::toArray();
        unset($data['choices']);
        return $data;
    }

}