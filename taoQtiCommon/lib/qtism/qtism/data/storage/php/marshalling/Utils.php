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

use \ReflectionObject;
use \InvalidArgumentException;

/**
 * Utility class aiming at providing utility methods for the PHP Marshalling
 * package.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Utils {
    
    /**
     * Generate a variable name for a given object. 
     * 
     * 
     * * If $value is an object, the generated variable name
     * will be [$object-class-short-name]_$occurence in lower case e.g. 'point_0',
     * 'assessmenttest_3', ... 
     * 
     * * If $value is a PHP scalar value (not including the null value), the generated
     * variable name will be [gettype($value)]_$occurence e.g. 'string_1', 'boolean_0', ...
     * 
     * * If $value is an array, the generated variable name will be array_$occurence such as
     * 'array_0', 'array_2', ...
     * 
     * * If $value is the null value, the generated variable name will be nullvalue_$occurence
     * such as 'nullvalue_3'.
     * 
     * * Finally, if the $value cannot be handled by this method, an InvalidArgumentException
     * is thrown.
     * 
     * @param mixed $value A value.
     * @param integer $occurence An occurence number.
     * @return string A variable name.
     * @throws InvalidArgumentException If $occurence is not a positive integer or if $value cannot be handled by this method.
     */
    public static function variableName($value, $occurence = 0) {
        
        if (is_int($occurence) === false || $occurence < 0) {
            $msg = "The 'occurence' argument must be a positive integer (>= 0).";
            throw new InvalidArgumentException($msg);
        }
        
        if (is_object($value) === true) {
            $object = new ReflectionObject($value);
            $className = mb_strtolower($object->getShortName(), 'UTF-8');
            return "${className}_${occurence}";
        }
        else {
            // Is it a PHP scalar value?
            if (is_scalar($value) === true) {
                return gettype($value) . '_' . $occurence;
            }
            else if (is_array($value) === true) {
                return 'array_' . $occurence;
            }
            // null value?
            else if (is_null($value) === true) {
                return 'nullvalue_' . $occurence;
            }
            else {
                $msg = "Cannot handle the given value.";
                throw new InvalidArgumentException($msg);
            }
        }
    }
}