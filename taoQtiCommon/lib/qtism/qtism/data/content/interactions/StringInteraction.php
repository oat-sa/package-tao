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

namespace qtism\data\content\interactions;

use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * String interactions can be bound to numeric response variables, instead of strings, if desired.
 * 
 * If detailed information about a numeric response is required then the string interaction can be bound to a response variable with record cardinality. The resulting value contains the following fields:
 * 
 * * stringValue: the string, as typed by the candidate.
 * * floatValue: the numeric value of the string typed by the candidate, as a float
 * * integerValue: the numeric value of the string typed by the candidate if no fractional digits or exponent were specified, otherwise NULL. An integer.
 * * leftDigits: the number of digits to the left of the point. An integer.
 * * rightDigits: the number of digits to the right of the point. An integer.
 * * ndp: the number of fractional digits specified by the candidate. If no exponent was given this is the same as rightDigits. An integer.
 * * nsf: the number of significant digits specified by the candidate. An integer.
 * * exponent: the integer exponent given by the candidate or NULL if none was specified.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
interface StringInteraction {
    
    /**
     * Set the base attribute of the StringInteraction.
     * 
     * @param integer $base A positive (>= 0) integer.
     * @throws InvalidArgumentException If $base is not a positive integer.
     */
    public function setBase($base);
    
    /**
     * Get the base attribute of the StringInteraction.
     * 
     * @return integer A positive (>= 0) integer.
     */
    public function getBase();
    
    /**
     * Set the stringIdentifier attribute of the StringInteraction. If $stringIdentifier is
     * an empty string, it means no value is defined for the stringIdentifier attribute.
     * 
     * @param string $stringIdentifier The identifier of a response variable of baseType string.
     * @throws InvalidArgumentException If $stringIdentifier is not a valid QTI identifier nor an empty string.
     */
    public function setStringIdentifier($stringIdentifier);

    /**
     * Set the stringIdentifier attribute of the StringInteraction. If the returned value
     * is an empty string, it means no value is defined for the stringIdentifier attribute.
     * 
     * @return A QTI identifier or an empty string.
     */
    public function getStringIdentifier();
    
    /**
     * Whether a value for the stringIdentifier attribute is defined.
     * 
     * @return boolean
     */
    public function hasStringIdentifier();
    
    /**
     * Set the expectedLength attribute of the StringInteraction. If $expectedLength -1,
     * it means that no expectedLength is defined.
     * 
     * @param string $expectedLength A strictly positive integer (> 0) or -1 if no expectedLength is defined.
     * @throws InvalidArgumentException If $expectedLength is not a strictly positive (> 0) integer nor -1.
     */
    public function setExpectedLength($expectedLength);
    
    /**
     * Get the expectedLength attribute of the StringInteraction. If the returned value
     * is -1 it means that no expectedLength is defined.
     * 
     * @return integer A strictly positive (> 0) integer or -1.
     */
    public function getExpectedLength();
    
    /**
     * Whether a value is defined for the expectedLength attribute.
     * 
     * @return boolean
     */
    public function hasExpectedLength();
    
    /**
     * Set the patternMask attribute of the StringInteraction. If $patternMask
     * is an empty string, it means there is no patternMask defined.
     * 
     * @param string $patternMask An XML Schema 2 regular expression or an empty string.
     * @throws InvalidArgumentException If $patternMash is not a string value.
     */
    public function setPatternMask($patternMask);
    
    /**
     * Get the patternMask attribute of the StringInteraction. If the returned value
     * is an empty string, it means there is no patternMask defined.
     * 
     * @return string An XML Schema 2 regular expression or an empty string.
     */
    public function getPatternMask();
    
    /**
     * Whether a value for the patternMask attribute is defined.
     * 
     * @return boolean
     */
    public function hasPatternMask();
    
    /**
     * Set the placeholderText attribute of the StringInteraction. If $placeholderText is
     * an empty string, it means that there is not placeholderText defined.
     * 
     * @param string $placeholderText A placeholder text for the interaction.
     * @throws InvalidArgumentException If $placeholderText is not a string value.
     */
    public function setPlaceholderText($placeholderText);
    
    /**
     * Get the placeholderText attribute of the StringInteraction. If the returned value is
     * an empty string, it means that no placeholderText is defined.
     * 
     * @param string A placeholder text or an empty string.
     */
    public function getPlaceholderText();
    
    /**
     * Whether a value is defined for the placeholderText attribute.
     * 
     * @return boolean
     */
    public function hasPlaceholderText();
}