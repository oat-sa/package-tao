<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package
 */

namespace qtism\data\content\xhtml;

use qtism\data\content\ObjectFlow;
use qtism\data\QtiComponent;
use qtism\data\QtiComponentCollection;
use \InvalidArgumentException;

/**
 * The QTI param class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Param extends QtiComponent implements ObjectFlow {
    
    /**
     * From IMS QTI:
     * 
     * The name of the parameter, as interpreted by the object.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $name;
    
    /**
     * From IMS QTI:
     * 
     * The value to pass to the object for the named parameter. This value is subject to 
     * template variable expansion. If the value is the name of a template variable that 
     * was declared with the paramVariable set to true then the template variable's value 
     * is passed to the object as the value for the given parameter.
     * 
     * When expanding a template variable as a parameter value, types other than identifiers, 
     * strings and uris must be converted to strings. Numeric types are converted to strings 
     * using the "%i" or "%G" formats as appropriate (see printedVariable for a discussion 
     * of numeric formatting). Values of base-type boolean are expanded to one of the strings 
     * "true" or "false". Values of base-type point are expanded to two space-separated integers 
     * in the order horizontal coordinate, vertical coordinate, using "%i" format. Values of 
     * base-type pair and directedPair are converted to a string consisting of the two identifiers, 
     * space separated. Values of base-type duration are converted using "%G" format. Values of 
     * base-type file cannot be used in parameter expansion.
     * 
     * If the valuetype is REF the template variable must be of base-type uri.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $value;
    
    /**
     * From IMS QTI:
     * 
     * This specification supports the use of DATA and REF but not OBJECT.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $valueType = ParamType::DATA;
    
    /**
     * From IMS QTI:
     * 
     * Used to provide a type for values valuetype REF.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $type = '';
    
    /**
     * Create a new Param object.
     * 
     * @param string $name The name of the parameter as interpreted by the object.
     * @param string $value The value to pass to the object named parameter.
     * @param integer $valueType A value from the ParamType enumeration.
     * @param string $type A mime-type for values valuetype REF.
     * @throws InvalidArgumentException If one of the arguments is invalid.
     */
    public function __construct($name, $value, $valueType = ParamType::DATA, $type = '') {
        $this->setName($name);
        $this->setValueType($valueType);
        $this->setValue($value);
        $this->setType($type);
    }
    
    /**
     * Set the name of the parameter, as interpreted by the object.
     * 
     * @param string $name A string.
     * @throws InvalidArgumentException If $name is not a string.
     */
    public function setName($name) {
        if (is_string($name) === true) {
            $this->name = $name;
        }
        else {
            $msg = "The 'name' argument must be a string, '" . gettype($name) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the name of the parameter, as interpreted by the object.
     * 
     * @return string A string.
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Set the value to pass to the object for the named parameter.
     * 
     * @param string $value A value as a string.
     * @throws InvalidArgumentException If $value is not a string.
     */
    public function setValue($value) {
        if (is_string($value) === true) {
            $this->value = $value;
        }
        else {
            $msg = "The 'value' argument must be a string, '" . gettype($value) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the value to pass to the object for the named parameter.
     * 
     * @return string A value as a string.
     */
    public function getValue() {
        return $this->value;
    }
    
    /**
     * Set the valueType attribute.
     * 
     * @param integer $valueType A value from the ParamType enumeration.
     * @throws InvalidArgumentException If $valueType is not a value from the ParamType enumeration.
     */
    public function setValueType($valueType) {
        if (in_array($valueType, ParamType::asArray()) === true) {
            $this->valueType = $valueType;
        }
        else {
            $msg = "The 'valueType' argument must be a value from the ParamType enumeration, '" . gettype($valueType) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the valueType attribute.
     * 
     * @return integer A value from the ParamType enumeration. 
     */
    public function getValueType() {
        return $this->valueType;
    }
    
    /**
     * Provide a type for values valuetype REF. Give an empty string
     * to indicate that no type is provided.
     * 
     * @param string $type A mime-type.
     * @throws InvalidArgumentException If the $type argument is not a string. 
     */
    public function setType($type) {
        if (is_string($type) === true) {
            $this->type = $type;
        }
        else {
            $msg = "The 'type' argument must be a string, '" . gettype($type) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the provided type for values valuetype REF. An empty string
     * indicates that no type is provided.
     * 
     * @return string A mime-type or an empty string if no type was provided.
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * Whether a type is provided for the param.
     * 
     * @return boolean
     */
    public function hasType() {
        return $this->getType() !== '';
    }
    
    public function getComponents() {
        return new QtiComponentCollection();
    }
    
    public function getQtiClassName() {
        return 'param';
    }
}