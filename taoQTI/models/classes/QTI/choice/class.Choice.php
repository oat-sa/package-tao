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
 * A choice is a kind of interaction's proposition.
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10271
 * @subpackage models_classes_QTI
 */
abstract class taoQTI_models_classes_QTI_choice_Choice extends taoQTI_models_classes_QTI_IdentifiedElement
{

    protected function getUsedAttributes(){
        return array(
            'taoQTI_models_classes_QTI_attribute_Fixed',
            'taoQTI_models_classes_QTI_attribute_TemplateIdentifier',
            'taoQTI_models_classes_QTI_attribute_ShowHideChoice',
        );
    }

    abstract public function getContent();

    abstract public function setContent($content);

    /**
     * Check if the given new identifier is valid in the current state of the qti element
     * 
     * @param string $newIdentifier
     * @return booean
     * @throws InvalidArgumentException
     */
    public function isIdentifierAvailable($newIdentifier){

        $returnValue = false;

        if(empty($newIdentifier) || is_null($newIdentifier)){
            throw new InvalidArgumentException("newIdentifier must be set");
        }

        if(!empty($this->identifier) && $newIdentifier == $this->identifier){
            $returnValue = true;
        }else{
            $relatedItem = $this->getRelatedItem();
            if(is_null($relatedItem)){
                $returnValue = true; //no restriction on identifier since not attached to any qti item
            }else{

                $collection = $relatedItem->getIdentifiedElements();

                try{
                    $uniqueChoice = $collection->getUnique($newIdentifier, 'taoQTI_models_classes_QTI_choice_Choice');
                    $uniqueOutcome = $collection->getUnique($newIdentifier, 'taoQTI_models_classes_QTI_OutcomeDeclaration');
                    if(is_null($uniqueChoice) && is_null($uniqueOutcome)){
                        $returnValue = true;
                    }
                }catch(taoQTI_models_classes_QTI_QtiModelException $e){
                    //return false
                }
            }
        }

        return $returnValue;
    }

    /**
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return tao_helpers_form_Form
     */
    public function toForm(){
        $returnValue = null;

        $choiceFormClass = 'taoQTI_actions_QTIform_choice_'.ucfirst(static::$qtiTagName);
        if(!class_exists($choiceFormClass)){
            throw new taoQTI_models_classes_QTI_QtiModelException("the class {$choiceFormClass} does not exist");
        }else{
            $formContainer = new $choiceFormClass($this);
            $myForm = $formContainer->getForm();
            $returnValue = $myForm;
        }

        return $returnValue;
    }
    
}