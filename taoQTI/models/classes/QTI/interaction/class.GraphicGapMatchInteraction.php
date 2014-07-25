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
 * QTI Graphic Associate Interaction
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10373
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_interaction_GraphicGapMatchInteraction extends taoQTI_models_classes_QTI_interaction_GraphicInteraction
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'graphicGapMatchInteraction';
    static protected $choiceClass = 'taoQTI_models_classes_QTI_choice_AssociableHotspot';
    static protected $baseType = 'directedPair';
    protected $gapImgs = array();

    /**
     * 
     * @return taoQTI_models_classes_QTI_choice_Choice
     */
    public function createGapImg($objectLabel = '', $objectAttributes = array()){

        $returnValue = null;

        if(!empty(static::$choiceClass) && is_subclass_of(static::$choiceClass, 'taoQTI_models_classes_QTI_choice_Choice')){
            $returnValue = new taoQTI_models_classes_QTI_choice_GapImg(empty($objectLabel) ? array() : array('objectLabel' => (string) $objectLabel));
            $returnValue->setContent(new taoQTI_models_classes_QTI_Object($objectAttributes));
            $this->addGapImg($returnValue);
        }

        return $returnValue;
    }

    public function addGapImg(taoQTI_models_classes_QTI_choice_GapImg $gapImg){
        $this->gapImgs[$gapImg->getSerial()] = $gapImg;
        $relatedItem = $this->getRelatedItem();
        if(!is_null($relatedItem)){
            $gapImg->setRelatedItem($relatedItem);
        }
    }

    public function getGapImgs(){
        return $this->gapImgs;
    }

    public function getIdentifiedElements(){
        $returnValue = parent::getIdentifiedElements();
        $returnValue->addMultiple($this->getGapImgs());
        return $returnValue;
    }

    public function toArray(){
        $data = parent::toArray();
        $data['gapImgs'] = array();
        foreach($this->getGapImgs() as $gapImg){
            $data['gapImgs'][$gapImg->getSerial()] = $gapImg->toArray();
        }
        return $data;
    }

    public static function getTemplateQti(){
        return static::getTemplatePath().'interactions/qti.graphicGapMatchInteraction.tpl.php';
    }

    protected function getTemplateQtiVariables(){
        $variables = parent::getTemplateQtiVariables();
        $variables['gapImgs'] = '';
        foreach($this->getGapImgs() as $gapImg){
            $variables['gapImgs'] .= $gapImg->toQTI();
        }
        return $variables;
    }
    
    public function getChoiceBySerial($serial){
        
        $returnValue = parent::getChoiceBySerial($serial);
        if(is_null($returnValue)){
            $gapImgs = $this->getGapImgs();
            if(isset($gapImgs[$serial])){
                $returnValue = $gapImgs[$serial];
            }
        }
        return $returnValue;
    }
    
    public function removeChoice(taoQTI_models_classes_QTI_choice_Choice $choice, $setNumber = null){
        if($choice instanceof taoQTI_models_classes_QTI_choice_GapImg){
            unset($this->gapImgs[$choice->getSerial()]);
        }else{
            parent::removeChoice($choice);
        }
    }
    
}