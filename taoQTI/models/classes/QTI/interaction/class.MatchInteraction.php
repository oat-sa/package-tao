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
 * QTI Match Interaction
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10291
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_interaction_MatchInteraction extends taoQTI_models_classes_QTI_interaction_BlockInteraction
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'matchInteraction';
    static protected $choiceClass = 'taoQTI_models_classes_QTI_choice_SimpleAssociableChoice';
    static protected $baseType = 'directedPair';

    public function __construct($attributes = array(), taoQTI_models_classes_QTI_Item $relatedItem = null, $serial = ''){
        parent::__construct($attributes, $relatedItem, $serial);

        //init the two matchSets: a double array
        $this->choices = array(array(), array());
    }

    public function getIdentifiedElements(){

        $returnValue = new taoQTI_models_classes_QTI_IdentifierCollection();
        $returnValue->addMultiple($this->getChoices(0));
        $returnValue->addMultiple($this->getChoices(1));

        return $returnValue;
    }

    protected function getUsedAttributes(){
        return array_merge(
                parent::getUsedAttributes(), array(
            'taoQTI_models_classes_QTI_attribute_Shuffle',
            'taoQTI_models_classes_QTI_attribute_MaxAssociations',
            'taoQTI_models_classes_QTI_attribute_MinAssociations'
                )
        );
    }

    public function getChoiceBySerial($serial){
        
        $returnValue = null;

        for($i = 0; $i < 2; $i++){
            $matchSet = $this->getChoices($i);
            if(isset($matchSet[$serial])){
                $returnValue = $matchSet[$serial];
                break;
            }
        }

        return $returnValue;
    }

    private function isValidMatchSetNumber($setNumber){

        $returnValue = false;

        if(is_int($setNumber)){
            if($setNumber === 0 || $setNumber === 1){
                $returnValue = true;
            }
        }
        if(!$returnValue){
            common_Logger::w($setNumber);
            throw new InvalidArgumentException('For match interactions, the match set number must be either "(int) 0" or "(int) 1"');
        }

        return $returnValue;
    }

    public function getChoices($setNumber = null){

        $returnValue = array();

        if($this->isValidMatchSetNumber($setNumber)){
            $returnValue = $this->choices[$setNumber];
        }else{
            $returnValue = $this->choices;
        }

        return $returnValue;
    }

    public function addChoice(taoQTI_models_classes_QTI_choice_Choice $choice, $setNumber = null){

        $returnValue = false;

        if($this->isValidMatchSetNumber($setNumber)){
            if(!empty(static::$choiceClass) && get_class($choice) == static::$choiceClass){
                $this->choices[$setNumber][$choice->getSerial()] = $choice;
                $relatedItem = $this->getRelatedItem();
                if(!is_null($relatedItem)){
                    $choice->setRelatedItem($relatedItem);
                }
                $returnValue = true;
            }else{
                throw new InvalidArgumentException('Wrong type of choice in argument: '.static::$choiceClass);
            }
        }

        return $returnValue;
    }

    /**
     * 
     * @return taoQTI_models_classes_QTI_choice_Choice
     */
    public function createChoice($choiceAttributes = array(), $choiceValue = null, $setNumber = null){

        $returnValue = null;

        if($this->isValidMatchSetNumber($setNumber)){
            if(!empty(static::$choiceClass) && is_subclass_of(static::$choiceClass, 'taoQTI_models_classes_QTI_choice_Choice')){
                $returnValue = new static::$choiceClass($choiceAttributes, $choiceValue);
                $this->addChoice($returnValue, $setNumber);
            }
        }

        return $returnValue;
    }

    public function removeChoice(taoQTI_models_classes_QTI_choice_Choice $choice, $setNumber = null){
        if(!is_null($setNumber) && isset($this->choices[$setNumber])){
            unset($this->choices[$setNumber][$choice->getSerial()]);
        }else{
            for($i=0;$i<2;$i++){
                $this->removeChoice($choice, $i);
            }
        }
    }

    public function toArray(){

        //need to reimplent it because there are two match choice sets
        $data = array(
            'serial' => $this->getSerial(),
            'type' => $this->getQtiTag(),
            'attributes' => $this->getAttributeValues(),
            'prompt' => $this->getPrompt()->toArray(),
            'choices' => array(array(), array())
        );

        for($i = 0; $i < 2; $i++){
            foreach($this->getChoices($i) as $choice){
                $data['choices'][$i][$choice->getSerial()] = $choice->toArray();
            }
        }

        return $data;
    }

    protected function getTemplateQtiVariables(){

        //need to reimplent it because there are two match choice sets
        $variables = array(
            'tag' => static::$qtiTagName,
            'attributes' => $this->getAttributeValues(),
            'prompt' => $this->prompt->toQTI()
        );
        unset($variables['attributes']['identifier']);
        
        if(trim($this->getPrompt()->getBody()) !== ''){
            //prompt is optional:
            $variables['prompt'] = $this->prompt->toQTI();
        }
        
        $choices = '';
        for($i = 0; $i < 2; $i++){
            $choices .= '<simpleMatchSet>';
            foreach($this->getChoices($i) as $choice){
                $choices .= $choice->toQTI();
            }
            $choices .= '</simpleMatchSet>';
        }
        
        $variables['choices'] = $choices;
        
        return $variables;
    }

}