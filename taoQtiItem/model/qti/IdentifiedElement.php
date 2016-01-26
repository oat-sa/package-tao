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

namespace oat\taoQtiItem\model\qti;

use oat\taoQtiItem\model\qti\exception\QtiModelException;
use \common_Logger;

/**
 * The QTI_Element class represent the abstract model for all the QTI objects.
 * It contains all the attributes of the different kind of QTI objects.
 * It manages the identifiers and serial creation.
 * It provides the serialisation and persistence methods.
 * And give the interface for the rendering.
 *
 * @abstract
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
abstract class IdentifiedElement extends Element
{

    /**
     * It represents the  QTI  identifier.
     * It must be unique string within an item.
     * It will generated if it hasn't been set.
     *
     * @access protected
     * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10541
     * @var string
     */
    protected $identifier = '';

    public function setAttribute($name, $value){
        if($name == 'identifier'){
            $this->setIdentifier($value); //manage identifier separately, as it is too complicate an attribute
        }else{
            parent::setAttribute($name, $value);
        }
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){
        $data = array();
        $data['identifier'] = $this->getIdentifier();
        $data = array_merge($data, parent::toArray($filterVariableContent, $filtered));

        return $data;
    }

    /**
     * get the identifier
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return string
     */
    public function getIdentifier($generate = true){
        if(empty($this->identifier) && $generate){
            //try generating an identifier
            $relatedItem = $this->getRelatedItem();
            if(!is_null($relatedItem)){
                $this->identifier = $this->generateIdentifier();
            }
        }
        return (string) $this->identifier;
    }

    public function getAttributeValue($name){

        $returnValue = null;

        if($name == 'identifier'){
            $returnValue = $this->getIdentifier();
        }else{
            $returnValue = parent::getAttributeValue($name);
        }

        return $returnValue;
    }

    public function getAttributeValues($filterNull = true){

        $returnValue = array('identifier' => $this->getIdentifier());
        $returnValue = array_merge($returnValue, parent::getAttributeValues($filterNull));

        return $returnValue;
    }

    /**
     * Set a unique identifier.
     * If the identifier is already given to another qti element in the same item an InvalidArgumentException is thrown.
     * The option collisionFree allows to ensure that a new identifier is generated if a collision happens
     * 
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  string id
     * @return boolean
     */
    public function setIdentifier($identifier, $collisionFree = false){

        $returnValue = false;
        if(empty($identifier) || is_null($identifier)){
            common_Logger::w('ss');
            throw new \InvalidArgumentException("Id must not be empty");
        }

        if($this->isIdentifierAvailable($identifier)){
            $returnValue = true;
        }else{
            if($collisionFree){
                $identifier = $this->generateIdentifier($identifier);
                $returnValue = true;
            }else{
                $relatedItem = $this->getRelatedItem();
                if(!is_null($relatedItem)){
                    $identifiedElements = $relatedItem->getIdentifiedElements();
                }
                common_Logger::w("Tried to set non unique identifier ".$identifier, array('TAOITEMS', 'QTI'));
                throw new \InvalidArgumentException("The identifier \"{$identifier}\" is already in use");
            }
        }

        if($returnValue){
            $this->identifier = $identifier;
        }

        return $returnValue;
    }

    /**
     * Validate if the current identifier of the qti element does not collide with another one's
     * CollisionFree option allows to ensure that no collision subsides after the fonction call.
     * It indeed ensures that that the identifier becomes unique if it collides with another one's.
     * 
     * @param boolean $collisionFree
     * @return boolean
     */
    public function validateCurrentIdentifier($collisionFree = false){

        $returnValue = false;

        if(empty($this->identifier)){
            //empty identifier, nothing to check
            $returnValue = true;
        }else{
            $returnValue = $this->setIdentifier($this->identifier, $collisionFree);
        }

        return $returnValue;
    }

    /**
     * Check if the given new identifier is valid in the current state of the qti element
     * 
     * @param string $newIdentifier
     * @return boolean
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
                $idCollection = $relatedItem->getIdentifiedElements();
                $returnValue = !$idCollection->exists($newIdentifier);
            }
        }

        return $returnValue;
    }

    /**
     * Create a unique identifier, based on the class if the qti element.
     *
     * @access protected
     * @author Sam, <sam@taotesting.com>
     *
     * @param string $prefix
     *
     * @return mixed
     * @throws QtiModelException
     */
    protected function generateIdentifier($prefix = ''){

        $relatedItem = $this->getRelatedItem();
        if(is_null($relatedItem)){
            throw new QtiModelException('cannot generate the identifier because the element does not belong to any item');
        }
        $identifiedElementsCollection = $relatedItem->getIdentifiedElements();
        
        $index = 1;
        $suffix = '';

        if(empty($prefix)){
            $clazz = get_class($this);
            if(preg_match('/[A-Z]{1}[a-z]*$/', $clazz, $matches)){
                $prefix = $matches[0];
            }else{
                $prefix = substr($clazz, strripos($clazz, '_')+1);
            }
            $suffix = '_'.$index;
        }else{
            $prefix = preg_replace('/_[0-9]+$/', '_', $prefix); //detect incremental id of type choice_12, response_3, etc.
            $prefix = preg_replace('/[^a-zA-Z0-9_]/', '_', $prefix);
            $prefix = preg_replace('/(_)+/', '_', $prefix);
        }

        do{
            $exist = false;
            $id = $prefix.$suffix;
            if($identifiedElementsCollection->exists($id)){
                $exist = true;
                $suffix = '_'.$index;
                $index++;
            }
        } while($exist);
        
        return $id;
    }

}