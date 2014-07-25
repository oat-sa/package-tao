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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * The main QTI Interaction Class.
 * Although a QTI Interaction has not the identifier attribute,
 * it is defined as an IdentifiedElement internally to allox building composite Qti Items
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10247
 
 */
namespace oat\taoQtiItem\model\qti\interaction;

use oat\taoQtiItem\model\qti\interaction\Interaction;
use oat\taoQtiItem\model\qti\IdentifiedElement;
use oat\taoQtiItem\model\qti\Element;
use oat\taoQtiItem\model\qti\IdentifiedElementContainer;
use oat\taoQtiItem\model\qti\choice\Choice;
use oat\taoQtiItem\model\qti\IdentifierCollection;
use oat\taoQtiItem\model\qti\ResponseDeclaration;
use oat\taoQtiItem\model\qti\exception\QtiModelException;
use \Exception;

abstract class Interaction extends Element implements IdentifiedElementContainer
{

    /**
     * Define the class of choice associate to the interaction, to be overwritten
     * by concrete class
     *
     * @var choiceClass
     */
    protected static $choiceClass = '';
    static protected $baseType = '';

    /**
     * The response of the interaction
     *
     * @access protected
     * @var Response
     */
    protected $choices = array();

    protected function getUsedAttributes(){
        return array(
            'oat\\taoQtiItem\\model\\qti\\attribute\\ResponseIdentifier'
        );
    }
    
    /**
     * Return the array of Qti Choice objects
     * 
     * @param mixed $setNumber
     * @return array
     */
    public function getChoices($setNumber = null){
        return $this->choices;
    }

    /**
     * Find a choice identified by its serial
     * 
     * @param string $serial
     * @return oat\taoQtiItem\model\qti\choice\Choice
     */
    public function getChoiceBySerial($serial){
        $returnValue = null;
        $choices = $this->getChoices();
        if(isset($choices[$serial])){
            $returnValue = $choices[$serial];
        }
        return $returnValue;
    }
    
    /**
     * Find a choice by its identifier
     * 
     * @param string $identifier
     * @return oat\taoQtiItem\model\qti\choice\Choice
     */
    public function getChoiceByIdentifier($identifier){
        return $this->getIdentifiedElements()->getUnique($identifier, 'oat\\taoQtiItem\\model\\qti\\choice\\Choice');
    }

    /**
     * Add a choice to the interaction
     * 
     * @param oat\taoQtiItem\model\qti\choice\Choice $choice
     * @param mixed $setNumber
     * @return boolean
     * @throws InvalidArgumentException
     */
    public function addChoice(Choice $choice, $setNumber = null){
        $returnValue = false;

        if(!empty(static::$choiceClass) && get_class($choice) == static::$choiceClass){
            $this->choices[$choice->getSerial()] = $choice;
            $relatedItem = $this->getRelatedItem();
            if(!is_null($relatedItem)){
                $choice->setRelatedItem($relatedItem);
            }
            $returnValue = true;
        }else{
            throw new InvalidArgumentException('Wrong type of choice in argument: '.static::$choiceClass);
        }

        return $returnValue;
    }

    /**
     * Create a choice for the interaction
     * 
     * @param array $choiceAttributes
     * @param mixed $choiceValue
     * @param mixed $setNumber
     * @return oat\taoQtiItem\model\qti\choice\Choice
     */
    public function createChoice($choiceAttributes = array(), $choiceValue = null, $setNumber = null){
        $returnValue = null;

        if(!empty(static::$choiceClass) && is_subclass_of(static::$choiceClass, 'oat\\taoQtiItem\\model\\qti\\choice\\Choice')){
            $returnValue = new static::$choiceClass($choiceAttributes);
            $returnValue->setContent($choiceValue);
            $this->addChoice($returnValue);
        }

        return $returnValue;
    }

    /**
     * Remove a choice from the interaction
     * 
     * @param oat\taoQtiItem\model\qti\choice\Choice $choice
     * @param mixed $setNumber
     */
    public function removeChoice(Choice $choice, $setNumber = null){
        unset($this->choices[$choice->getSerial()]);
    }

    /**
	 * Every identified QTI element must declare the list of (string) identifers being used within it
     * This method gets all identified Qti Elements contained in the current Qti Element
     * 
	 * @author Sam, <sam@taotesting.com>
     * @return oat\taoQtiItem\model\qti\IdentifierCollection
	 */
    public function getIdentifiedElements(){
        $returnValue = new IdentifierCollection();
        $returnValue->addMultiple($this->getChoices());

        return $returnValue;
    }

    protected function getTemplateQtiVariables(){
        $variables = parent::getTemplateQtiVariables();
        // remove the identifier attribute to comply with the standard, it is used interally to manage multiple interactions within a single item.
        unset($variables['attributes']['identifier']);
        $variables['choices'] = '';
        foreach($this->getChoices() as $choice){
            $variables['choices'] .= $choice->toQTI();
        }
        return $variables;
    }

    /**
     * Get the response declaration associated to the interaction
     * If no response exists, one will be created
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return oat\taoQtiItem\model\qti\ResponseDeclaration
     */
    public function getResponse(){
        $returnValue = null;

        $responseAttribute = $this->getAttribute('responseIdentifier');
        if(!is_null($responseAttribute)){
            $idenfierBaseType = $responseAttribute->getValue(true);
            if(!is_null($idenfierBaseType)){
                $returnValue = $idenfierBaseType->getReferencedObject();
            }else{
                $responseDeclaration = new ResponseDeclaration();
                if($this->setResponse($responseDeclaration)){
                    $returnValue = $responseDeclaration;
                }else{
                    throw new QtiModelException('cannot create the interaction response');
                }
            }
        }

        return $returnValue;
    }

    /**
     * Define the interaction's response
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param oat\taoQtiItem\model\qti\ResponseDeclaration response
     * @return mixed
     */
    public function setResponse(ResponseDeclaration $response){
        $relatedItem = $this->getRelatedItem();
        if(!is_null($relatedItem)){
            $relatedItem->addResponse($response);
        }
        return $this->setAttribute('responseIdentifier', $response);
    }

    /**
     * Retrieve the interaction cardinality
     * (single, multiple or ordered)
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param boolean numeric
     * @return mixed
     */
    public function getCardinality($numeric = false){
        $returnValue = null;

        // get maximum possibility:
        switch(strtolower($this->getType())){
            case 'choice':
            case 'hottext':
            case 'hotspot':
            case 'selectpoint':
            case 'positionobject':{
                    $max = intval($this->getAttributeValue('maxChoices'));
                    if($numeric){
                        $returnValue = $max;
                    }
                    else {
                        $returnValue = ($max == 1) ? 'single' : 'multiple'; // default=1
                    }
                    break;
                }
            case 'associate':
            case 'match':
            case 'graphicassociate':{
                    $max = intval($this->getAttributeValue('maxAssociations'));
                    if($numeric){
                        $returnValue = $max;
                    }
                    else{
                        $returnValue = ($max == 1) ? 'single' : 'multiple';
                    } // default=1
                    break;
                }
            case 'extendedtext':{
                    // maxStrings + order or not?
                    $cardinality = $this->getAttributeValue('cardinality');
                    if($cardinality == 'ordered'){
                        if($numeric){
                            $returnValue = 0;
                        } // meaning, infinite
                        else {
                            $returnValue = $cardinality;
                        }
                        break;
                    }
                    $max = intval($this->getAttributeValue('maxStrings'));
                    if($numeric){
                        $returnValue = $max;
                    }
                    else {
                        $returnValue = ($max > 1) ? 'multiple' : 'single'; // optional
                    }
                    break;
                }
            case 'gapmatch':{
                    // count the number of gap, i.e. "groups" in the interaction:
                    $max = count($this->getGaps());
                    if($numeric) {
                        $returnValue = $max;
                    }
                    else {
                        $returnValue = ($max > 1) ? 'multiple' : 'single';
                    }
                    break;
                }
            case 'graphicgapmatch':{
                    // strange that the standard always specifies "multiple":
                    $returnValue = 'multiple';
                    break;
                }
            case 'order':
            case 'graphicorder':{
                    $returnValue = ($numeric) ? 1 : 'ordered';
                    break;
                }
            case 'inlinechoice':
            case 'textentry':
            case 'media':
            case 'slider':
            case 'upload':
            case 'endattempt':{
                    $returnValue = ($numeric) ? 1 : 'single';
                    break;
                }
            default:{
                    throw new QtiModelException("the current interaction type \"{$this->type}\" is not available yet");
                }
        }

        return $returnValue;
    }

    /**
     * Get the interaction base type:
     * integer, string, identifier, pair, directedPair float, boolean or point
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return string
     */
    public function getBaseType(){
        return strtolower(static::$baseType);
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){
        $data = parent::toArray($filterVariableContent, $filtered);
        $data['choices'] = array();
        foreach($this->getChoices() as $choice){
            $data['choices'][$choice->getSerial()] = $choice->toArray($filterVariableContent, $filtered);
        }
        return $data;
    }

    /**
     * Short description of method canRenderTesttakerResponse
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return boolean
     */
    public function canRenderTesttakerResponse(){
        $returnValue = in_array(strtolower($this->type), array(
            'extendedtext'
        ));

        return (bool) $returnValue;
    }

    /**
     * Short description of method renderTesttakerResponseXHTML
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param responses
     * @return string
     */
    public function renderTesttakerResponseXHTML($responses){
        throw new QtiModelException('method to be reimplemented');
    }

    /**
     * Get the short name of the interaction
     * 
     * @return string
     */
    public function getType(){
        $tagName = static::$qtiTagName;
        return str_replace('Interaction', '', $tagName);
    }

    public function toForm(){
        $returnValue = null;

        $interactionFormClass = '\\oat\\taoQtiItem\\controller\\QTIform\\interaction\\'.ucfirst(strtolower($this->getType())).'Interaction';
        if(!class_exists($interactionFormClass)){
            throw new Exception("the class {$interactionFormClass} does not exist");
        }else{
            $formContainer = new $interactionFormClass($this);
            $myForm = $formContainer->getForm();
            $returnValue = $myForm;
        }

        return $returnValue;
    }

}