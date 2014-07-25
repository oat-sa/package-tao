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
 * The QTI_Container object represents the generic element container
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @subpackage models_classes_QTI_container
 */
abstract class taoQTI_models_classes_QTI_container_Container extends taoQTI_models_classes_QTI_Element implements taoQTI_models_classes_QTI_IdentifiedElementContainer
{

    /**
     * The data containing the position of qti elements within the html body
     *
     * @access protected
     * @var string
     */
    protected $body = '';

    /**
     * The list of available elements
     *
     * @access protected
     * @var array
     */
    protected $elements = array();

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  string body
     * @return mixed
     */
    public function __construct($body = '', taoQTI_models_classes_QTI_Item $relatedItem = null, $serial = ''){
        parent::__construct(array(), $relatedItem, $serial);
        $this->body = $body;
    }

    public function __toString(){
        return $this->body;
    }

    protected function getUsedAttributes(){
        return array();
    }

    /**
     * add one qtiElement into the body
     * if the body content is not specified, it appends to the end
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return boolean
     */
    public function setElement(taoQTI_models_classes_QTI_Element $qtiElement, $body = '', $integrityCheck = true, $requiredPlaceholder = true){
        return $this->setElements(array($qtiElement), $body, $integrityCheck, $requiredPlaceholder);
    }

    public function setElements($qtiElements, $body = '', $integrityCheck = true, $requiredPlaceholder = true){

        $missingElements = array();
        if($integrityCheck && !empty($body) && !$this->checkIntegrity($body, $missingElements)){
            return false;
        }

        if(empty($body)){
            $body = $this->body;
        }

        foreach($qtiElements as $qtiElement){
            if($this->isValidElement($qtiElement)){
                $placeholder = $qtiElement->getPlaceholder();
                if(strpos($body, $placeholder) === false){
                    if($requiredPlaceholder){
                        throw new InvalidArgumentException('no placeholder found for the element in the new container body: '.get_class($qtiElement).':'.$placeholder);
                    }else{
                        //assume implicitly add to the end
                        $body .= $placeholder;
                    }
                }
                
                $relatedItem = $this->getRelatedItem();
                if(!is_null($relatedItem)){
                    $qtiElement->setRelatedItem($relatedItem);
                    if($qtiElement instanceof taoQTI_models_classes_QTI_IdentifiedElement){
                        $qtiElement->getIdentifier();//generate one
                    }
                }
                $this->elements[$qtiElement->getSerial()] = $qtiElement;
                $this->afterElementSet($qtiElement);
            }else{
                throw new taoQTI_models_classes_QTI_QtiModelException('The container '.get_class($this).' cannot contain element of type '.get_class($qtiElement));
            }
        }

        $this->edit($body);

        return true;
    }
    
    public function afterElementSet(taoQTI_models_classes_QTI_Element $qtiElement){

        if($qtiElement instanceof taoQTI_models_classes_QTI_IdentifiedElement){
            //check ids
        }
    }

    public function afterElementRemove(taoQTI_models_classes_QTI_Element $qtiElement){
        
    }

    public function getBody(){
        return $this->body;
    }

    /**
     * modify the content of the body
     * 
     * @param string $body
     */
    public function edit($body, $integrityCheck = false){
        if(!is_string($body)){
            throw new InvalidArgumentException('a QTI container must have a body of string type');
        }
        if($integrityCheck && !$this->checkIntegrity($body)){
            return false;
        }
        $this->body = $body;
        return true;
    }

    /**
     * Check if modifying the body won't have an element placeholder deleted
     * 
     * @param string $body
     * @return boolean
     */
    public function checkIntegrity($body, &$missingElements = null){

        $returnValue = true;

        foreach($this->elements as $element){
            if(strpos($body, $element->getPlaceholder()) === false){
                $returnValue = false;
                if(is_array($missingElements)){
                    $missingElements[$element->getSerial()] = $element;
                }else{
                    break;
                }
            }
        }


        return (bool) $returnValue;
    }

    /**
     * Clean the html in the body
     */
    public function cleanBody(){
        
    }

    public function isValidElement(taoQTI_models_classes_QTI_Element $element){
        $returnValue = false;

        $validClasses = $this->getValidElementTypes();
        foreach($validClasses as $validClass){
            if($element instanceof $validClass){
                $returnValue = true;
                break;
            }
        }
        return $returnValue;
    }

    /**
     * return the list of available element classes
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return array
     */
    abstract function getValidElementTypes();

    /**
     * Get the element by its serial
     * 
     * @param string $serial
     * @return taoQTI_models_classes_QTI_Element
     */
    public function getElement($serial){

        $returnValue = null;

        if(isset($this->elements[$serial])){
            $returnValue = $this->elements[$serial];
        }

        return $returnValue;
    }

    /**
     * Get all elements of the given type
     * Returns all elements if class name is not specified
     * 
     * @param string $className
     * @return array
     */
    public function getElements($className = ''){

        $returnValue = array();

        if($className){
            foreach($this->elements as $serial => $element){
                if($element instanceof $className){
                    $returnValue[$serial] = $element;
                }
            }
        }else{
            $returnValue = $this->elements;
        }


        return $returnValue;
    }

    public function removeElement($element){

        $returnValue = false;

        $serial = '';
        if($element instanceof taoQTI_models_classes_QTI_Element){
            $serial = $element->getSerial();
        }elseif(is_string($element)){
            $serial = $element;
        }

        if(!empty($serial) && isset($this->elements[$serial])){
            $this->body = str_replace($this->elements[$serial]->getPlaceholder(), '', $this->body);
            $this->afterElementRemove($this->elements[$serial]);
            unset($this->elements[$serial]);
            $returnValue = true;
        }

        return $returnValue;
    }

    public function getIdentifiedElements(){

        $returnValue = new taoQTI_models_classes_QTI_IdentifierCollection();

        foreach($this->elements as $element){
            if($element instanceof taoQTI_models_classes_QTI_IdentifiedElementContainer){
                $returnValue->merge($element->getIdentifiedElements());
            }
            if($element instanceof taoQTI_models_classes_QTI_IdentifiedElement){
                $returnValue->add($element);
            }
        }
        
        return $returnValue;
    }

    /**
     * Short description of method toXHTML
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return string
     */
    public function toXHTML(){
        $returnValue = $this->getBody();

        foreach($this->elements as $element){
            $returnValue = str_replace($element->getPlaceholder(), $element->toXHTML(), $returnValue);
        }

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return string
     */
    public function toQTI(){
        $returnValue = $this->getBody();

        foreach($this->elements as $element){
            $returnValue = str_replace($element->getPlaceholder(), $element->toQTI(), $returnValue);
        }

        return (string) $returnValue;
    }

    /**
     * Serialize item object into json format
     * 
     * 
     */
    public function toArray(){

        $data = array(
            'serial' => $this->getSerial(),
            'body' => $this->getBody(),
            'elements' => array(),
        );
        foreach($this->getElements() as $element){
            $data['elements'][$element->getSerial()] = $element->toArray();
        }
        
        $data['debug'] = array('relatedItem' => is_null($this->getRelatedItem())?'':$this->getRelatedItem()->getSerial());
        
        return $data;
    }

}
/* end of abstract class taoQTI_models_classes_QTI_container_Container */