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

namespace oat\taoQtiItem\model\qti\attribute;

use oat\taoQtiItem\model\qti\datatype\DatatypeException;
/**
 * It is the top class of every attributes used in QTI
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
abstract class Attribute
{

    const QTI_v2p0 = '2.0';
    const QTI_v2p1 = '2.1';

    /**
     * The name of the attribute defined in the QTI standard
     * 
     * @var string
     */
    static protected $name = '';

    /**
     * The class of datatype (a subclass of oat\taoQtiItem\model\qti\datatype\Datatype)
     * 
     * @var string
     */
    static protected $type = '';

    /**
     * Define if this attribute is required or not
     * 
     * @var boolean
     */
    static protected $required = false;

    /**
     * Define the default value of the attribute
     * 
     * @var mixed 
     */
    static protected $defaultValue = null;
    
    /**
     * Define the default value of the attribute
     * 
     * @var mixed 
     */
    static protected $taoDefaultValue = null;
    
    /**
     * The object holding the value of the attribute
     * 
     * @var \oat\taoQtiItem\model\qti\datatype\Datatype
     */
    protected $value = null;
    protected $version = self::QTI_v2p1;

    /**
     * Instantiate the attribute object
     * 
     * @param mixed $value
     * @throws \oat\taoQtiItem\model\qti\attribute\AttributeException
     */
    public function __construct($value = null, $version = self::QTI_v2p1){

        $this->version = $version;

        if(empty(static::$name) || empty(static::$type)){
            throw new AttributeException('Fail to extend QTI_attribute_Attribute class properly: wrong QTI Attribute property definition: "'.__CLASS__.'"');
        }

        if(class_exists(static::$type) && is_subclass_of(static::$type, 'oat\\taoQtiItem\\model\\qti\\datatype\\Datatype')){
            if(!is_null($value)){
                $this->value = new static::$type($value);
            }elseif(!is_null(static::$defaultValue)){
                $this->value = new static::$type(static::$defaultValue);
            }elseif(!is_null(static::$taoDefaultValue)){
                $this->value = new static::$type(static::$taoDefaultValue);
            }else{
                $this->setNull();
            }
        }else{
            throw new AttributeException('Fail to extend QTI_attribute_Attribute class properly: the attribute type class does not exist: "'.static::$type.'"');
        }
    }

    public function __toString(){
        return $this->isNull() ? '' : (string) $this->value->getValue();
    }

    /**
     * Check if this attribute is required
     * 
     * @return boolean
     */
    public function isRequired(){
        return (bool) static::$required;
    }
    
    /**
     * Check if a value has been set to this attribute
     * 
     * @return boolean
     */
    public function isNull(){
        return is_null($this->value);
    }
    
    /**
     * Clear, empty, nullify the value of the attribute
     * 
     * @return null
     */
    public function setNull(){
        return $this->value = null;
    }
    /**
     * Check if the attribute is valid in terms of value
     * 
     * @param mixed $value
     * @return boolean
     */
    public function validateValue($value){
        return call_user_func(array(static::$type, 'validate'), $value);
    }

    /**
     * Check if the cardinality of the attribute value is correct
     * 
     * @return boolean
     */
    public function validateCardinality(){
        return $this->isRequired() ? !$this->isNull() : true;
    }

    /**
     * Get the attribute name
     * 
     * @return string
     */
    public function getName(){
        return static::$name;
    }
    
    /**
     * Get the Qti BaseType class
     * 
     * @return string
     */
    public function getType(){
        return static::$type;
    }

    /**
     * Get the default value of the attribute defined in standard QTI 2.1
     * 
     * @return mixed
     */
    public function getDefault(){
        return static::$defaultValue;
    }

    /**
     * Return the value of the attribute in base type int, string, null
     * 
     * @return mixed
     */
    public function getValue($returnObject = false){

        $returnValue = null;

        if(!is_null($this->value)){
            $returnValue = ($returnObject) ? $this->value : $this->value->getValue(); //return mixed
        }

        return $returnValue;
    }

    /**
     * Set the attribute value
     * 
     * @param mixed $value
     * @return boolean
     * @throws \oat\taoQtiItem\model\qti\attribute\AttributeException
     */
    public function setValue($value){

        $returnValue = false;

        if(!is_null($value)){
            try{
                $value = new static::$type($value);
                if(!is_null($value)){
                    $this->value = $value;
                    $returnValue = true;
                }
            }catch(DatatypeException $de){
                $type = '('.gettype($value).')';
                if($type == 'object'){
                    $type .= '('.get_class($value).')';
                }
                throw new AttributeException('Cannot assign the value to attribute: '.static::$name.' -> '.$type.' '.$value);
            }
        }

        return $returnValue;
    }

}