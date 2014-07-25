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

namespace qtism\data\storage\php;

use \InvalidArgumentException;

/**
 * Represents a PHP variable with a given name.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PhpVariable {
    
    /**
     * The name of the variable without the leading dollar sign ('$').
     * 
     * @var string
     */
    private $name;
    
    /**
     * Create a new PhpVariable object.
     * 
     * @param string $name The name of the variable without the leading dollar sign ('$').
     * @throws InvalidArgumentException If $name is not a string value.
     */
    public function __construct($name) {
        $this->setName($name);
    }
    
    /**
     * Set the name of the variable.
     * 
     * @param string $name The name of the variable without the leading dollar sign ('$').
     * @throws InvalidArgumentException If $name is not a string value.
     */
    public function setName($name) {
        if (is_string($name) === false) {
            $msg = "The 'name' argument must be a string value, '" . gettype($name) . "' given.";
            throw new InvalidArgumentException($msg);
        }
        
        $this->name = $name;
    }
    
    /**
     * Get the name of the variable.
     * 
     * @return string A variable name without the leading dollar sign ('$').
     */
    public function getName() {
        return $this->name;
    }
}