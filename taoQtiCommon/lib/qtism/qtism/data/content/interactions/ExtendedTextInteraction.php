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

use qtism\common\utils\Format;
use qtism\data\QtiComponentCollection;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * An extended text interaction is a blockInteraction that allows the candidate to enter 
 * an extended amount of text.
 * 
 * The extendedTextInteraction must be bound to a response variable with baseType of string, 
 * integer or float. When bound to response variable with single cardinality a single string 
 * of text is required from the candidate. When bound to a response variable with multiple or 
 * ordered cardinality several separate text strings may be required, see maxStrings below.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ExtendedTextInteraction extends BlockInteraction implements StringInteraction {
    
    /**
     * From IMS QTI:
     *
     * If the string interaction is bound to a numeric response variable then the base attribute
     * must be used to set the number base in which to interpret the value entered by the candidate.
     *
     * @var integer
     * @qtism-bean-property
     */
    private $base = 10;
    
    /**
     * From IMS QTI:
     *
     * If the string interaction is bound to a numeric response variable then the actual string
     * entered by the candidate can also be captured by binding the interaction to a second
     * response variable (of base-type string).
     *
     * @var string
     * @qtism-bean-property
     */
    private $stringIdentifier = '';
    
    /**
     * From IMS QTI:
     *
     * The expectedLength attribute provides a hint to the candidate as to the expected overall
     * length of the desired response measured in number of characters. A Delivery Engine should
     * use the value of this attribute to set the size of the response box, where applicable.
     * This is not a validity constraint.
     *
     * @var integer
     * @qtism-bean-property
     */
    private $expectedLength = -1;
    
    /**
     * From IMS QTI:
     *
     * If given, the pattern mask specifies a regular expression that the candidate's response
     * must match in order to be considered valid. The regular expression language used is
     * defined in Appendix F of [XML_SCHEMA2]. Care is needed to ensure that the format of
     * the required input is clear to the candidate, especially when validity checking of
     * responses is required for progression through a test. This could be done by providing
     * an illustrative sample response in the prompt, for example.
     *
     * @var string
     * @qtism-bean-property
     */
    private $patternMask = '';
    
    /**
     * From IMS QTI:
     *
     * In visual environments, string interactions are typically represented by empty boxes
     * into which the candidate writes or types. However, in speech based environments it
     * is helpful to have some placeholder text that can be used to vocalize the interaction.
     * Delivery engines should use the value of this attribute (if provided) instead of their
     * default placeholder text when this is required. Implementors should be aware of the
     * issues concerning the use of default values described in the section on Response Variables.
     *
     * @var string
     * @qtism-bean-property
     */
    private $placeholderText = '';
    
    /**
     * From IMS QTI:
     * 
     * The maxStrings attribute is required when the interaction is bound to a response 
     * variable that is a container. A Delivery Engine must use the value of this attribute 
     * to control the maximum number of separate strings accepted from the candidate. When
     * multiple strings are accepted, expectedLength applies to each string.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $maxStrings = -1;
    
    /**
     * From IMS QTI:
     * 
     * The minStrings attribute specifies the minimum number separate (non-empty) strings required
     * from the candidate to form a valid response. If minStrings is 0 then the candidate is not 
     * required to enter any strings at all. minStrings must be less than or equal to the limit 
     * imposed by maxStrings. If the interaction is not bound to a container then there is a 
     * special case in which minStrings may be 1. In this case the candidate must enter a 
     * non-empty string to form a valid response. More complex constraints on the form of 
     * the string can be controlled with the patternMask attribute.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $minStrings = 0;
    
    /**
     * From IMS QTI:
     * 
     * The expectedLines attribute provides a hint to the candidate as to the expected number 
     * of lines of input required. A line is expected to have about 72 characters. A Delivery 
     * Engine should use the value of this attribute to set the size of the response box, 
     * where applicable. This is not a validity constraint.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $expectedLines = -1;
    
    /**
     * From IMS QTI:
     * 
     * Used to control the format of the text entered by the candidate. See textFormat below.
     * This attribute affects the way the value of the associated response variable should be
     * interpreted by response processing engines and also controls the way it should be 
     * captured in the delivery engine.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $format = TextFormat::PLAIN;
    
    /**
     * Create a new ExtendedTextInteraction object.
     * 
     * @param string $responseIdentifier The identifier of the associated response variable.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The lang of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If any of the arguments is invalid.
     */
    public function __construct($responseIdentifier, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $id, $class, $lang, $label);
    }
    
    /**
     * If the interaction is bound to a numeric response variable, get the number base in which
     * to interpret the value entered by the candidate.
     *
     * @param integer $base A positive (>= 0) integer.
     * @throws InvalidArgumentException If $base is not a positive integer.
     */
    public function setBase($base) {
        if (is_int($base) === true && $base >= 0) {
            $this->base = $base;
        }
        else {
            $msg = "The 'base' argument must be a positive (>= 0) integer value, '" . gettype($base) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * If the interaction is bound to a numeric response variable, get the number base in which
     * to interpret the value entered by the candidate.
     *
     * @return integer A positive (>= 0) integer.
     */
    public function getBase() {
        return $this->base;
    }
    
    /**
     * If the interaction is bound to a numeric response variable, set the identifier of the response variable where the
     * plain text entered by the candidate will be stored. If $stringIdentifier is an empty string, it means that
     * there is no value for the stringIdentifier attribute.
     *
     * @param string $stringIdentifier A QTI Identifier or an empty string.
     * @throws InvalidArgumentException If $stringIdentifier is not a valid QTIIdentifier nor an empty string.
     */
    public function setStringIdentifier($stringIdentifier) {
        if (Format::isIdentifier($stringIdentifier, false) === true || (is_string($stringIdentifier) && empty($stringIdentifier) === true)) {
            $this->stringIdentifier = $stringIdentifier;
        }
        else {
            $msg = "The 'stringIdentifier' argument must be a valid QTI identifier or an empty string, '" . $stringIdentifier . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * If the interaction is bound to a numeric response variable, get the identifier of the response variable where the
     * plain text entered by the candidate will be stored. If the returned value is an empty string, it means that there
     * is no value defined for the stringIdentifier attribute.
     *
     * @return string A QTI identifier or an empty string.
     */
    public function getStringIdentifier() {
        return $this->stringIdentifier;
    }
    
    /**
     * Whether a value is defined for the stringIdentifier attribute.
     * 
     * @return boolean
     */
    public function hasStringIdentifier() {
        return $this->getStringIdentifier() !== '';
    }
    
    /**
     * Set the hint to the candidate about the expected overall length of its response. If $expectedLength
     * is -1, it means that no value is defined for the expectedLength attribute.
     *
     * @param integer A strictly positive (> 0) integer or -1.
     * @throws InvalidArgumentException If $expectedLength is not a strictly positive integer nor -1.
     */
    public function setExpectedLength($expectedLength) {
        if (is_int($expectedLength) && ($expectedLength > 0 || $expectedLength === -1)) {
            $this->expectedLength = $expectedLength;
        }
        else {
            $msg = "The 'expectedLength' argument must be a strictly positive (> 0) integer or -1, '" . gettype($expectedLength) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the hint to the candidate about the expected overall length of its response. If the returned
     * value is -1, it means that no value is defined for the expectedLength attribute.
     *
     * @return A strictly positive (> 0) integer or -1 if undefined.
     */
    public function getExpectedLength() {
        return $this->expectedLength;
    }
    
    /**
     * Whether a value is defined for the expectedLength attribute.
     * 
     * @return boolean
     */
    public function hasExpectedLength() {
        return $this->getExpectedLength() !== -1;
    }
    
    /**
     * Set the pattern mask specifying an XML Schema 2 regular expression that the candidate response must
     * match with. If $patternMask is an empty string, it means that there is no value defined for patternMask.
     *
     * @param string $patternMask An XML Schema 2 regular expression or an empty string.
     * @throws InvalidArgumentException If $patternMask is not a string value.
     */
    public function setPatternMask($patternMask) {
        if (is_string($patternMask) === true) {
            $this->patternMask = $patternMask;
        }
        else {
            $msg = "The 'patternMask' argument must be a string value, '" . gettype($patternMask) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the pattern mask specifying an XML Schema 2 regular expression that the candidate response must
     * match with. If the returned value is an empty string, it means that there is no value defined
     * for patternMask.
     *
     * @return string An XML Schema 2 regular expression or an empty string.
     */
    public function getPatternMask() {
        return $this->patternMask;
    }
    
    /**
     * Whether a value is defined for the patternMask attribute.
     * 
     * @return boolean
     */
    public function hasPatternMask() {
        return $this->getPatternMask() !== '';
    }
    
    /**
     * Set a placeholder text. If $placeholderText is an empty string, it means that no value is defined
     * for the placeholderText attribute.
     *
     * @param string $placeholderText A placeholder text or an empty string.
     * @throws InvalidArgumentException If $placeholderText is not a string value.
     */
    public function setPlaceholderText($placeholderText) {
        if (is_string($placeholderText) === true) {
            $this->placeholderText = $placeholderText;
        }
        else {
            $msg = "The 'placeholderText' argument must be a string value, '" . gettype($placeholderText) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the placeholder text. If the returned value is an empty string, it means that no value
     * is defined for the placeholderText attribute.
     *
     * @return string A placeholder text or an empty string.
     */
    public function getPlaceholderText() {
        return $this->placeholderText;
    }
    
    /**
     * Whether a value is defined for the placeholderText attribute.
     * 
     * @return boolean
     */
    public function hasPlaceholderText() {
        return $this->getPlaceholderText() !== '';
    }
    
    /**
     * If the interaction is bound to a numeric response variable, get the number of separate strings
     * accepted from the candidate. If $maxStrings is -1, it means no value is defined for the attribute.
     * 
     * @param integer $maxStrings A strictly positive (> 0) integer or -1.
     * @throws InvalidArgumentException If $maxStrings is not a strictly positive integer nor -1.
     */
    public function setMaxStrings($maxStrings) {
        if (is_int($maxStrings) === true && ($maxStrings > 0 || $maxStrings === -1)) {
            $this->maxStrings = $maxStrings;
        }
        else {
            $msg = "The 'maxStrings' argument must be a strictly positive (> 0) integer or -1, '" . gettype($maxStrings) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * If the interaction is bound to a numeric response variable, get the number of separate strings
     * accepted from the candidate. If the returned value is -1, it means no value is defined for the attribute.
     * 
     * @return integer A strictly positive (> 0) integer or -1.
     */
    public function getMaxStrings() {
        return $this->maxStrings;    
    }
    
    /**
     * Whether a value for the maxStrings attribute is defined.
     * 
     * @return boolean
     */
    public function hasMaxStrings() {
        return $this->getMaxStrings() !== -1;
    }
    
    /**
     * Set the minimum separate (non-empty) strings required from the candidate.
     * 
     * @param string $minStrings A positive (>= 0) integer.
     * @throws InvalidArgumentException If $minStrings is not a positive integer.
     */
    public function setMinStrings($minStrings) {
        if (is_int($minStrings) && $minStrings >= 0) {
            $this->minStrings = $minStrings;
        }
        else {
            $msg = "The 'minStrings' argument must be a positive (>= 0) integer, '" . gettype($minStrings) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the minimum separate (non-empty) strings required from the candidate.
     * 
     * @return integer A positive (>= 0) integer.
     */
    public function getMinStrings() {
        return $this->minStrings;
    }
    
    /**
     * Set the hint to the candidate as to the expected number of lines of input required. If
     * $expectedLines is -1, it means that no value is defined for the expectedLines attribute.
     * 
     * @param integer $expectedLines A strictly positive (> 0) integer or -1.
     * @throws InvalidArgumentException If $expectedLines is not a strictly positive integer nor -1.
     */
    public function setExpectedLines($expectedLines) {
        if (is_int($expectedLines) && ($expectedLines > 0 || $expectedLines === -1)) {
            $this->expectedLines = $expectedLines;
        }
        else {
            $msg = "The 'expectedLines' argument must be a strictly positive (> 0) intege or -1, '" . gettype($expectedLines) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the hint to the candidate as to the expected number of lines of input required. If
     * the returned value is -1, it means that no value is defined for the expectedLines attribute.
     * 
     * @return integer A strictly positive (> 0) integer or -1.
     */
    public function getExpectedLines() {
        return $this->expectedLines;
    }
    
    /**
     * Whether a value for the expectedLines attribute is defined.
     * 
     * @return boolean
     */
    public function hasExpectedLines() {
        return $this->getExpectedLines() !== -1;
    }
    
    /**
     * Set the format of the text entered by the candidate.
     * 
     * @param integer $format A value from the TextFormat enumeration.
     * @throws InvalidArgumentException If $format is not a value from the TextFormat enumeration.
     */
    public function setFormat($format) {
        if (in_array($format, TextFormat::asArray()) === true) {
            $this->format = $format;
        }
        else {
            $msg = "The 'format' argument must be a value from the TextFormat enumeration, '" . gettype($format) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the format of the text entered by the candidate.
     * 
     * @return integer A value from the TextFormat enumeration.
     */
    public function getFormat() {
        return $this->format;
    }
    
    public function getComponents() {
        return parent::getComponents();
    }
    
    public function getQtiClassName() {
        return 'extendedTextInteraction';
    }
}