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
 * It is the top class of every attributes used in QTI
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @subpackage models_classes_QTI
 */
abstract class taoQTI_models_classes_QTI_attribute_Attribute
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
     * The class of datatype (a subclass of taoQTI_models_classes_QTI_datatype_Datatype)
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
     * @var taoQTI_models_classes_QTI_datatype_Datatype 
     */
    protected $value = null;
    protected $version = self::QTI_v2p1;

    /**
     * Instantiate the attribute object
     * 
     * @param mixed $value
     * @throws taoQTI_models_classes_QTI_attribute_AttributeException
     */
    public function __construct($value = null, $version = self::QTI_v2p1){

        $this->version = $version;

        if(empty(static::$name) || empty(static::$type)){
            throw new taoQTI_models_classes_QTI_attribute_AttributeException('Fail to extend QTI_attribute_Attribute class properly: wrong QTI Attribute property definition: "'.__CLASS__.'"');
        }

        if(class_exists(static::$type) && is_subclass_of(static::$type, 'taoQTI_models_classes_QTI_datatype_Datatype')){
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
            throw new taoQTI_models_classes_QTI_attribute_AttributeException('Fail to extend QTI_attribute_Attribute class properly: the attribute type class does not exist: "'.static::$type.'"');
        }
    }

    public function __toString(){
        return $this->isNull() ? '' : (string) $this->value->getValue();
    }

    public function isRequired(){
        return (bool) static::$required;
    }

    public function isNull(){
        return is_null($this->value);
    }
    
    public function setNull(){
        return $this->value = null;
    }
    /**
     * Check if the attribute is valid in terms of value
     * @param mixed $value
     * @return boolean
     */
    public function validateValue($value){
        return call_user_func(array(static::$type, 'validate'), $value);
    }

    /**
     * Check if the cardinality of the attribute value is correct
     * @return boolean
     */
    public function validateCardinality(){
        return $this->isRequired() ? !$this->isNull() : true;
    }

    public function getName(){
        return static::$name;
    }

    public function getType(){
        return static::$type;
    }

    public function getDefault(){
        return static::$defaultValue;
    }

    /**
     * Return the value of the attribute in base type int, string, null)
     * @return mixed
     */
    public function getValue($returnObject = false){

        $returnValue = null;

        if(!is_null($this->value)){
            $returnValue = ($returnObject) ? $this->value : $this->value->getValue(); //return mixed
        }

        return $returnValue;
    }

    public function setValue($value){

        $returnValue = false;

        if(!is_null($value)){
            try{
                $value = new static::$type($value);
                if(!is_null($value)){
                    $this->value = $value;
                    $returnValue = true;
                }
            }catch(taoQTI_models_classes_QTI_datatype_DatatypeException $de){
                $type = '('.gettype($value).')';
                if($type == 'object'){
                    $type .= '('.get_class($value).')';
                }
                throw new taoQTI_models_classes_QTI_attribute_AttributeException('Cannot assign the value to attribute: '.static::$name.' -> '.$type.' '.$value);
            }
        }

        return $returnValue;
    }

}
/* end of abstract class taoQTI_models_classes_QTI_attribute_Attribute */