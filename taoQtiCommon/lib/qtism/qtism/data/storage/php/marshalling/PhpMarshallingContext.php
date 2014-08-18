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

namespace qtism\data\storage\php\marshalling;

use qtism\data\storage\php\PhpStreamAccess;
use qtism\data\storage\php\marshalling\Utils as PhpMarshallingUtils;
use \SplStack;
use \InvalidArgumentException;
use \RuntimeException;

/**
 * This class represents the running context of the marshalling process
 * of a QtiComponent into PHP source code.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PhpMarshallingContext {
    
    /**
     * An array to count variable name generations
     * for objects. The keys of the array are class names (fully qualified)
     * and the values the number of times a variable name was generated for this
     * class.
     * 
     * @var array
     */
    private $objectCount;
    
    /**
     * An array to count variable name generations for PHP datatypes. The datatypes
     * are 'integer', 'double', 'boolean', 'string', 'array' + 'null'. These datatypes
     * correspond to the keys of the array. The values are the number of times a variable
     * name was generated for this class. 
     * 
     * @var array
     */
    private $datatypeCount;
    
    /**
     * The stack of object variable names.
     * 
     * @var SplStack
     */
    private $variableStack;
    
    /**
     * Whether to format the output PHP source code.
     * 
     * @var boolean
     */
    private $formatOutput;
    
    /**
     * The stream where the output PHP source code must be written.
     * 
     * @var PhpStreamAccess
     */
    private $streamAccess;
    
    /**
     * Create a new MarshallingContext object.
     * 
     * @param PhpStreamAccess An access to a PHP source code stream for output.
     */
    public function __construct(PhpStreamAccess $streamAccess) {
        $this->setVariableStack(new SplStack());
        $this->setFormatOutput(false);
        $this->setStreamAccess($streamAccess);
        $this->setObjectCount(array());
        $this->setDatatypeCount(array('string' => 0, 'boolean' => 0, 'integer' => 0, 'double' => 0, 'array' => 0, 'null' => 0));
    }
    
    /**
     * Set the object count array.
     * 
     * @param array $objectCount
     */
    protected function setObjectCount(array $objectCount) {
        $this->objectCount = $objectCount;
    }
    
    /**
     * Get the object count array.
     * 
     * @return array
     */
    protected function getObjectCount() {
        return $this->objectCount;
    }
    
    /**
     * Set the datatype count array.
     * 
     * @param array $datatypeCount
     */
    protected function setDatatypeCount(array $datatypeCount) {
        $this->datatypeCount = $datatypeCount;
    }
    
    /**
     * Get the datatype count array.
     * 
     * @return array
     */
    protected function getDatatypeCount() {
        return $this->datatypeCount;
    }
    
    /**
     * Set the variables name stack.
     * 
     * @param SplStack $variableStack
     */
    protected function setVariableStack(SplStack $variableStack) {
        $this->variableStack = $variableStack;
    }
    
    /**
     * Get the variables name stack.
     * 
     * @return SplStack
     */
    protected function getVariableStack() {
        return $this->variableStack;
    }
    
    /**
     * Set whether to format the output PHP source code.
     * 
     * @param boolean $formatOutput
     */
    public function setFormatOutput($formatOutput) {
        $this->formatOutput = $formatOutput;
    }
    
    /**
     * Whether to format the output PHP source code.
     * 
     * @return boolean
     */
    public function mustFormatOutput() {
        return $this->formatOutput;
    }
    
    /**
     * Set the PHP source code stream access to be used at marshalling time.
     * 
     * @param PhpStreamAccess $streamAccess An access to a PHP source code stream.
     */
    protected function setStreamAccess(PhpStreamAccess $streamAccess) {
        $this->streamAccess = $streamAccess;
    }
    
    /**
     * Get the PHP source code stream access to be used at marshalling time for output.
     * 
     * @return PhpStreamAccess An access to a PHP source code stream.
     */
    public function getStreamAccess() {
        return $this->streamAccess;
    }
    
    /**
     * Push some value(s) on the variable names stack.
     * 
     * @param string|array $values A string or an array of strings to be pushed on the variable names stack.
     * @throws InvalidArgumentException If $value or an item of $value is not a non-empty string.
     */
    public function pushOnVariableStack($values) {
        if (is_array($values) === false) {
            $values = array($values);
        }
        
        foreach ($values as $value) {
            if (is_string($value) === false) {
                $msg = "The pushOnVariableStack method only accepts non-empty string values.";
                throw new InvalidArgumentException($msg);
            }
            
            $this->getVariableStack()->push($value);
        }
    }
    
    /**
     * Pop a given $quantity of values from the variable names stack.
     * 
     * @param integer $quantity
     * @return array An array of strings.
     * @throws RuntimeException If the the quantity of elements in the stack before popping is less than $quantity.
     * @throws InvalidArgumentException If $quantity < 1.
     */
    public function popFromVariableStack($quantity = 1) {
        
        $quantity = intval($quantity);
        if ($quantity < 1) {
            $msg = "The 'quantity' argument must be >= 1, '${quantity}' given.";
            throw new InvalidArgumentException($msg);
        }
        
        $stack = $this->getVariableStack();
        $stackCount = count($stack);
        
        if ($stackCount < $quantity) {
            $msg = "The number of elements in the variable names stack (${stackCount}) is lower than the requested quantity (${quantity}).";
            throw new RuntimeException($msg);
        }
        
        $values = array();
        for ($i = 0; $i < $quantity; $i++) {
            $values[] = $stack->pop();
        }
        
        return array_reverse($values);
    }
    
    /**
     * Generates a suitable variable name to be used for a given value.
     * 
     * @param mixed $value A value.
     * @return string A variable name without the leading dollar sign ('$').
     */
    public function generateVariableName($value) {
        
        $occurence = 0;
        
        if (is_object($value) === true) {
            $counter = $this->getObjectCount();
            $className = get_class($value);
            
            if (isset($counter[$className]) === false) {
                $occurence = 0;
                $counter[$className] = $occurence;
            }
            else {
                $occurence = $counter[$className];
            }
            
            $counter[$className] += 1;
            $this->setObjectCount($counter);
        }
        else {
            if (is_null($value) === true) {
                $type = 'null';
            }
            else {
                $type = gettype($value);
            }
            
            $counter = $this->getDatatypeCount();
            $occurence = $counter[$type];
            $counter[$type] += 1;
            
            $this->setDatatypeCount($counter);
        }
         
        return PhpMarshallingUtils::variableName($value, $occurence);
    }
}