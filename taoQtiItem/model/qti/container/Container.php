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

namespace oat\taoQtiItem\model\qti\container;

use oat\taoQtiItem\model\qti\Element;
use oat\taoQtiItem\model\qti\IdentifiedElementContainer;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\IdentifiedElement;
use oat\taoQtiItem\model\qti\exception\QtiModelException;
use oat\taoQtiItem\model\qti\IdentifierCollection;
use \InvalidArgumentException;
use \common_Logger;

/**
 * The QTI_Container object represents the generic element container
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
abstract class Container extends Element implements IdentifiedElementContainer
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
    public function __construct($body = '', Item $relatedItem = null, $serial = ''){
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
    public function setElement(Element $qtiElement, $body = '', $integrityCheck = true, $requiredPlaceholder = true){
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
                    if($qtiElement instanceof IdentifiedElement){
                        $qtiElement->getIdentifier();//generate one
                    }
                }
                $this->elements[$qtiElement->getSerial()] = $qtiElement;
                $this->afterElementSet($qtiElement);
            }else{
                throw new QtiModelException('The container '.get_class($this).' cannot contain element of type '.get_class($qtiElement));
            }
        }

        $this->edit($body);

        return true;
    }
    
    public function afterElementSet(Element $qtiElement){

        if($qtiElement instanceof IdentifiedElement){
            //check ids
        }
    }

    public function afterElementRemove(Element $qtiElement){
        
    }

    public function getBody(){
        return $this->body;
    }

    /**
     * modify the content of the body
     * 
     * @param string $body
     * @param bool $integrityCheck
     * @return bool
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
     * Converts <foo/> to <foo></foo> unless foo is a proper void element such as img etc.
     *
     * @param $html
     * @return mixed
     */
    public function fixNonvoidTags($html) {
        return preg_replace_callback('~(<([\w]+)[^>]*?)(\s*/>)~u', function ($matches) {
            // something went wrong
            if(empty($matches[2])) {
                // do nothing
                return $matches[0];
            }
            // regular void elements
            if(in_array($matches[2], array('area','base','br','col','embed','hr','img','input','keygen','link','meta','param','source','track','wbr'))) {
                // do nothing
                return $matches[0];
            }
            // correctly closed element
            return trim(mb_substr($matches[0], 0, -2), 'UTF-8') . '></' . $matches[2] . '>';
        }, $html);
    }

    public function isValidElement(Element $element){
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
     * @return oat\taoQtiItem\model\qti\Element
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
        if($element instanceof Element){
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

        $returnValue = new IdentifierCollection();

        foreach($this->elements as $element){
            if($element instanceof IdentifiedElementContainer){
                $returnValue->merge($element->getIdentifiedElements());
            }
            if($element instanceof IdentifiedElement){
                $returnValue->add($element);
            }
        }
        
        return $returnValue;
    }

    /**
     * Export the data to QTI XML format
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * Get the array representation of the Qti Element.
     * Particularly helpful for data transformation, e.g. json
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return array
     */
    public function toArray($filterVariableContent = false, &$filtered = array()){

        $data = array(
            'serial' => $this->getSerial(),
            'body' => $this->getBody(),
            'elements' => $this->getArraySerializedElementCollection($this->getElements(), $filterVariableContent, $filtered),
        );
        
        if(DEBUG_MODE){
            //in debug mode, add debug data, such as the related item
            $data['debug'] = array('relatedItem' => is_null($this->getRelatedItem())?'':$this->getRelatedItem()->getSerial());
        }   
        
        return $data;
    }

}