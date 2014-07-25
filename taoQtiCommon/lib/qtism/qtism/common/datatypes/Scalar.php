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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */

namespace qtism\common\datatypes;

use qtism\common\Comparable;

/**
 * The base class for all Scalar wrappers.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class Scalar implements Comparable {
    
    /**
     * The value of the Scalar object.
     * 
     * @var mixed
     */
    private $value;
    
    /**
     * Create a new Scalar object with a given $value as its content.
     * 
     * @param mixed $value
     * @throws InvalidArgumentException If $value is not compliant with the Scalar wrapper.
     */
    public function __construct($value) {
        $this->setValue($value);
    }
    
    /**
     * Set the PHP value to be encapsulated witin the Scalar object.
     * 
     * @param mixed $value
     * @throws InvalidArgumentException If $value is not compliant with the Scalar wrapper.
     */
    public function setValue($value) {
        $this->checkType($value);
        $this->value = $value;
    }
    
    /**
     * Get the encapsulated value from the Scalar object, representing its intrinsic value.
     * 
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }
    
    public function isNull() {
        return $this->getValue() === null;
    }
    
    public function equals($obj) {
        if ($obj instanceof Scalar) {
            return $obj->getValue() === $this->getValue();
        }
        else {
            return $this->getValue() === $obj;
        }
    }
    
    /**
     * Checks if $value has the correct PHP datatype to
     * be encapsulated withing the Scalar object.
     * 
     * @param mixed $value A value to be encapsulated whithin the Scalar object.
     * @throws InvalidArgumentException If $value has a not compliant PHP datatype.
     */
    abstract protected function checkType($value);
}