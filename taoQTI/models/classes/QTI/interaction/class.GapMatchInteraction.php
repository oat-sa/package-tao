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
 * QTI GapMatch Interaction
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_interaction_GapMatchInteraction extends taoQTI_models_classes_QTI_interaction_ContainerInteraction
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'gapMatchInteraction';
    static protected $choiceClass = 'taoQTI_models_classes_QTI_choice_GapText';
    static protected $baseType = 'directedPair';
    static protected $containerType = 'taoQTI_models_classes_QTI_container_ContainerGap';

    protected function getUsedAttributes(){
        return array_merge(
                parent::getUsedAttributes(), array(
            'taoQTI_models_classes_QTI_attribute_Shuffle'
                )
        );
    }

    public function getGaps(){
        return $this->getBody()->getElements('taoQTI_models_classes_QTI_choice_Gap');
    }

    public function addGap($body, taoQTI_models_classes_QTI_choice_Gap $gap){
        return $this->setElements(array($gap), $body);
    }

    public function createGap($body, $gapAttributes = array(), $gapValue = null){
        $returnValue = null;
        $gap = new taoQTI_models_classes_QTI_choice_Gap($gapAttributes, $gapValue);
        if($this->addGap($body, $gap)){
            $returnValue = $gap;
        }
        return $returnValue;
    }

    public function removeGap(taoQTI_models_classes_QTI_choice_Gap $gap){
        return $this->body->removeElement($gap);
    }

    public function getIdentifiedElements(){
        $returnValue = parent::getIdentifiedElements();
        $returnValue->addMultiple($this->getGaps());
        return $returnValue;
    }
    
    public function getChoiceBySerial($serial){
        
        $returnValue = parent::getChoiceBySerial($serial);
        if(is_null($returnValue)){
            $gaps = $this->getGaps();
            if(isset($gaps[$serial])){
                $returnValue = $gaps[$serial];
            }
        }
        return $returnValue;
    }
    
    public function removeChoice(taoQTI_models_classes_QTI_choice_Choice $choice, $setNumber = null){
        if($choice instanceof taoQTI_models_classes_QTI_choice_Gap){
            return $this->body->removeElement($choice);
        }else{
            parent::removeChoice($choice);
        }
    }
    
}