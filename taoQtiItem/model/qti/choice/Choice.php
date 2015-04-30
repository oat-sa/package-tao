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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti\choice;

use oat\taoQtiItem\model\qti\IdentifiedElement;
use oat\taoQtiItem\model\qti\exception\QtiModelException;

/**
 * A choice is a kind of interaction's proposition.
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10271
 
 */
abstract class Choice extends IdentifiedElement
{

    protected function getUsedAttributes(){
        return array(
            'oat\\taoQtiItem\\model\\qti\\attribute\\Fixed',
            'oat\\taoQtiItem\\model\\qti\\attribute\\TemplateIdentifier',
            'oat\\taoQtiItem\\model\\qti\\attribute\\ShowHideChoice',
        );
    }
    
    /**
     * Common method to get the content of a choice.
     * The return value is mostly a string, but could also be a oat\taoQtiItem\model\qti\Object
     * 
     * @return mixed
     */
    abstract public function getContent();
    
    /**
     * Common method to se the content of a choice.
     * The content type is mostly a String, but could also be a oat\taoQtiItem\model\qti\Object or oat\taoQtiItem\model\qti\OutcomeDeclaration
     * 
     * @param mixed content
     */
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
                    $uniqueChoice = $collection->getUnique($newIdentifier, 'oat\\taoQtiItem\\model\\qti\\choice\\Choice');
                    $uniqueOutcome = $collection->getUnique($newIdentifier, 'oat\\taoQtiItem\\model\\qti\\OutcomeDeclaration');
                    if(is_null($uniqueChoice) && is_null($uniqueOutcome)){
                        $returnValue = true;
                    }
                }catch(QtiModelException $e){
                    //return false
                }
            }
        }

        return $returnValue;
    }

    /**
     * Return the form to edit the current instance
     * 
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return tao_helpers_form_Form
     */
    public function toForm(){
        $returnValue = null;

        $choiceFormClass = '\\oat\\taoQtiItem\\controller\\QTIform\\choice\\'.ucfirst(static::$qtiTagName);
        if(!class_exists($choiceFormClass)){
            throw new QtiModelException("the class {$choiceFormClass} does not exist");
        }else{
            $formContainer = new $choiceFormClass($this);
            $myForm = $formContainer->getForm();
            $returnValue = $myForm;
        }

        return $returnValue;
    }
    
}