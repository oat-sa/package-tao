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

/**
 * This class provides utility methods dedicated to PHP data storage.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Utils {
    
    /**
     * Whether a given $value is considered to be scalar.
     * 
     * A value will be considered scalar if it is a PHP scalar
     * value or the null value.
     * 
     * @return boolean
     */
    static public function isScalar($value) {
        return is_scalar($value) === true || is_null($value) === true;
    }
    
    /**
     * Whether a given $string represents a variable reference e.g. '$foobar'.
     * 
     * @return boolean
     */
    static public function isVariableReference($string) {
        return is_string($string) === true && mb_strpos($string, '$') === 0 && mb_strlen($string, 'UTF-8') > 1;
    }
    
    /**
     * Returns a PHP style double quoted string.
     * 
     * @param string $string The string to be quoted (e.g. blabla).
     * @return string The quoted string (e.g. "blabla").
     */
    static public function doubleQuotedPhpString($string) {
        $escapes = array("\\", '"', "\n", "\t", "\v", "\r", "\f", "$");
        $replace = array("\\\\", '\\"', "\\n", "\\t", "\\v", "\\r", "\\f", "\\$");
        
        return "\"" . str_replace($escapes, $replace, $string) . "\""; // UTF-8 safe.
    }
}