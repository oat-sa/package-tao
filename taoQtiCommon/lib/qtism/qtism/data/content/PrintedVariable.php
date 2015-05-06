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

namespace qtism\data\content;

use qtism\data\QtiComponentCollection;

use qtism\common\utils\Format;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * If the variable's value is NULL then the element is ignored.
 * 
 * Variables of baseType string are treated as simple runs of text.
 * 
 * Variables of baseType integer or float are converted to runs of text (strings) using the 
 * formatting rules described below. Float values should only be formatted in the 
 * e, E, f, g, G, r or R styles.
 * 
 * Variables of baseType duration are treated as floats, representing the duration 
 * in seconds.
 * 
 * Variables of baseType file are rendered using a control that enables the user to 
 * open the file. The control should display the name associated with the file, if any.
 * 
 * Variables of baseType uri are rendered using a control that enables the user to 
 * open the identified resource, for example, by following a hypertext link in the 
 * case of a URL.
 * 
 * For variables of single cardinality, the value of the variable is printed.
 * 
 * For variables of ordered cardinality, if the attribute index is set, the single
 * value corresponding to the indexed member is printed, otherwise an ordered list 
 * of the values within the container is printed, delimited by the string value 
 * of the delimiter attribute.
 * 
 * For variables of multiple cardinality, a list of the values within the container 
 * is printed, delimited by the string value of the delimiter attribute.
 * 
 * For variables of record cardinality, if the attribute field is set, the value 
 * corresponding to the specified field is printed, otherwise a list of the field 
 * names and corresponding field values within the variable is printed, delimited 
 * by the string value of the delimiter attribute and with the correspondence 
 * between them indicated by the string value of the mappingIndicator attribute.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PrintedVariable extends BodyElement implements FlowStatic, InlineStatic, TextOrVariable {
    
    /**
     * The base URI of the PrintedVariable.
     *
     * @var string
     * @qtism-bean-property
     */
    private $xmlBase = '';
    
    /**
     * From IMS QTI:
     * 
     * The outcome variable or template variable must have been defined. 
     * The values of response variables cannot be printed directly as their 
     * values are implicitly known to the candidate through the interactions 
     * they are bound to; if necessary, their values can be assigned to outcomes 
     * during responseProcessing and displayed to the candidate as part of a 
     * bodyElement visible only in the appropriate feedback states.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $identifier;
    
    /**
     * From IMS QTI:
     * 
     * The format conversion specifier to use when converting numerical values 
     * to strings. See Number Formatting Rules for details.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $format = '';
    
    /**
     * From IMS QTI:
     * 
     * If the variable value is a float and powerForm is set to 'false', the variable 
     * will be rendered using the 'e' or 'E' format. If the powerform is set to 'true', 
     * the form "e+n" is changed to "x 10n".
     * 
     * @var boolean
     * @qtism-bean-property
     */
    private $powerForm = false;
    
    /**
     * From IMS QTI:
     * 
     * The number base to use when converting integer variables to strings 
     * with the i conversion type code.
     * 
     * Note: qti:integerOrVariableRef
     * 
     * @var integer|string
     * @qtism-bean-property
     */
    private $base = 10;
    
    /**
     * From IMS QTI:
     * 
     * The index to use when displaying a variable of ordered cardinality. If a variable 
     * of ordered cardinality is to be displayed and index is not set, all the values 
     * in the container are displayed.
     * 
     * Note: qti:integerOrVariableRef, an empty string indicates nothing was declared
     * for the index attribute.
     * 
     * @var integer|string
     * @qtism-bean-property
     */
    private $index = -1;
    
    /**
     * From IMS QTI:
     * 
     * The delimiter to use between values when displaying variables of ordered, multiple or 
     * record cardinality. ";" is default when delimiter is not declared. Implementations 
     * can override this default with personal preferences or locale settings.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $delimiter = ';';
    
    /**
     * From IMS QTI:
     * 
     * The field specifier to use when displaying variables of record cardinality.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $field = '';
    
    /**
     * From IMS QTI:
     * 
     * The mapping indicator to use between field name and field value when displaying 
     * variables of record cardinality. "=" is default when mappingIndicator is not declared.
     * Implementations can override this default with personal preferences or locale settings.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $mappingIndicator = '=';
    
    /**
     * Create a new PrintedVariable object.
     * 
     * @param string $identifier The identifier of the outcome/template variable to print.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If one of the arguments is invalid.
     */
    public function __construct($identifier, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setIdentifier($identifier);
    }
    
    /**
     * Set the outcome or template variable identifier of the printedVariable.
     * 
     * @param string $identifier A valid QTI identifier.
     * @throws InvalidArgumentException If the given $identifier is invalid.
     */
    public function setIdentifier($identifier) {
        if (Format::isIdentifier($identifier, false) === true) {
            $this->identifier = $identifier;
        }
        else {
            $msg = "The 'identifier' argument must be a valid QTI identifier, '" . $identifier . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the outcome or template variable identifier of the printedVariable.
     * 
     * @return string A valid QTI identifier.
     */
    public function getIdentifier() {
        return $this->identifier;
    }
    
    /**
     * Set the format conversion to use when converting numerical values
     * to strings.
     * 
     * @param string $format See Number Formatting Rules for details.
     * @throws InvalidArgumentException If $format is not a string with at most 256 characters.
     * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#section10074
     */
    public function setFormat($format) {
        if (Format::isString256($format) === true) {
            $this->format = $format;
        }
        else {
            $msg = "The 'format' argument must be a string with at most 256 characters, '" . $format . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the format conversion to use when converting numerical values
     * to strings.
     * 
     * @return string A string with at most 256 characters.
     * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#section10074
     */
    public function getFormat() {
        return $this->format;
    }
    
    /**
     * Whether a format attribute value is defined.
     * 
     * @return boolean
     */
    public function hasFormat() {
        return $this->getFormat() !== '';
    }
    
    /**
     * Set whether the variable value (when it's a float) must use the 'e' format
     * for display.
     * 
     * @param boolean $powerForm
     * @throws InvalidArgumentException
     */
    public function setPowerForm($powerForm) {
        if (is_bool($powerForm) === true) {
            $this->powerForm = $powerForm;
        }
        else {
            $msg = "The 'powerForm' argument must be a boolean value, '" . gettype($powerForm) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Whether the power form 'e' format must be used to display
     * float values.
     * 
     * @return boolean
     */
    public function mustPowerForm() {
        return $this->powerForm;
    }
    
    /**
     * Set the number base to use when converting integer variables to strings.
     * 
     * @param integer|string $base A base to use for conversion as an integer or a variable reference.
     * @throws InvalidArgumentException If $base is not an integer nor a variable reference.
     */
    public function setBase($base) {
        if (is_int($base) === true || Format::isVariableRef($base) === true) {
            $this->base = $base;
        }
        else {
            $msg = "The 'base' argument must be an integer or a variable reference, '" . $base . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the number base to use when converting integer variables to strings.
     * 
     * @return integer|string An integer or a variable reference.
     */
    public function getBase() {
        return $this->base;
    }
    
    /**
     * Set the index to use when displaying a variable of ordered cardinality. Give a negative integer
     * if there is no index indicated.
     * 
     * @param integer|string $index An integer or variable reference.
     * @throws InvalidArgumentException If $index is not an integer nor a variable reference.
     */
    public function setIndex($index) {
        if (is_int($index) === true || Format::isVariableRef($index) === true) {
            $this->index = $index;
        }
        else {
            $msg = "The 'index' argument must be an integer or a variable reference, '" . $index . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the index to use when displaying a variable of ordered cardinality. A negative integer
     * will be returned if there is no index indicated.
     * 
     * @return integer|string An integer or a variable reference.
     */
    public function getIndex() {
        return $this->index;
    }
    
    /**
     * Whether or not an index is defined for the printedVariable.
     * 
     * @return boolean
     */
    public function hasIndex() {
        return is_string($this->index) || $this->index >= 0;
    }
    
    /**
     * Set the delimiter to use between values when displaying variables of ordered, multiple or record
     * cardinality.
     * 
     * @param string $delimiter A non-empty string with at most 256 characters.
     * @throws InvalidArgumentException If $delimiter is not a non-empty string with at most 256 characters.
     */
    public function setDelimiter($delimiter) {
        if (empty($delimiter) === false && Format::isString256($delimiter) === true) {
            $this->delimiter = $delimiter;
        }
        else {
            $msg = "The 'delimiter' argument must be a non-empty string, '" . gettype($delimiter) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the delimiter to use between values when displaying variables of ordered,
     * multiple or record cardinality.
     * 
     * @return string A non-empty string with at least 256 characters.
     */
    public function getDelimiter() {
        return $this->delimiter;
    }
    
    /**
     * Set the field specifier to use when displaying variables of record cardinality. Give an
     * empty string if no field is indicated.
     * 
     * @param string $field A string with at most 256 characters.
     * @throws InvalidArgumentException If $field is not a string with at most 256 characters.
     */
    public function setField($field) {
        if (Format::isString256($field) === true) {
            $this->field = $field;
        }
        else {
            $msg = "The 'field' argument must be a non-empty string, '" . gettype($field) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the field specifier to use when displaying variables of record cardinality. Returns an
     * empty string if no field is specified.
     * 
     * @return string A string with at most 256 characters.
     */
    public function getField() {
        return $this->field;
    }
    
    /**
     * Whether or not the printedVariable has a value for the field attribute.
     * 
     * @return boolean
     */
    public function hasField() {
        $field = $this->getField();
        return empty($field) === false;
    }
    
    /**
     * Set the mapping indicator to use between field name and field value when
     * displaying variables of record cardinality.
     * 
     * @param string $mappingIndicator A non-empty string with at most 256 characters.
     * @throws InvalidArgumentException If $mappingIndicator is not a non-empty string with at most 256 characters.
     */
    public function setMappingIndicator($mappingIndicator) {
        if (empty($mappingIndicator) === false && Format::isString256($mappingIndicator) === true) {
            $this->mappingIndicator = $mappingIndicator;
        }
        else {
            $msg = "The 'mappingIndicator' argument must be a non-empty string with at most 256 characters, '" . gettype($mappingIndicator) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the mapping indicator to use between field name and field value
     * when displaying variables of record cardinality.
     * 
     * @return string A non-empty string with at most 256 characters.
     */
    public function getMappingIndicator() {
        return $this->mappingIndicator;
    }
    
    /**
     * Set the base URI of the PrintedVariable.
     *
     * @param string $xmlBase A URI.
     * @throws InvalidArgumentException if $base is not a valid URI nor an empty string.
     */
    public function setXmlBase($xmlBase = '') {
        if (is_string($xmlBase) && (empty($xmlBase) || Format::isUri($xmlBase))) {
            $this->xmlBase = $xmlBase;
        }
        else {
            $msg = "The 'xmlBase' argument must be an empty string or a valid URI, '" . $xmlBase . "' given";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the base URI of the PrintedVariable.
     *
     * @return string An empty string or a URI.
     */
    public function getXmlBase() {
        return $this->xmlBase;
    }
    
    public function hasXmlBase() {
        return $this->getXmlBase() !== '';
    }
    
    public function getComponents() {
        return new QtiComponentCollection();
    }
    
    public function getQtiClassName() {
        return 'printedVariable';
    }
}