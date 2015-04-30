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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */
namespace qtism\runtime\processing;

use qtism\common\datatypes\Integer;
use qtism\runtime\common\Variable;
use qtism\data\QtiComponent;
use qtism\common\utils\Format;
use qtism\runtime\common\RecordContainer;
use qtism\runtime\common\Utils;
use qtism\common\enums\BaseType;
use qtism\runtime\common\Container;
use qtism\common\enums\Cardinality;
use qtism\data\content\TextRun;
use qtism\data\content\PrintedVariable;
use qtism\runtime\common\AbstractEngine;
use \RuntimeException;

/**
 * From IMS QTI:
 * 
 * The outcome variable or template variable must have been defined. The values of response variables 
 * cannot be printed directly as their values are implicitly known to the candidate through the 
 * interactions they are bound to; if necessary, their values can be assigned to outcomes during 
 * responseProcessing and displayed to the candidate as part of a bodyElement visible only in 
 * the appropriate feedback states.
 * 
 * If the variable's value is NULL then the element is ignored.
 * 
 * Variables of baseType string are treated as simple runs of text.
 * 
 * Variables of baseType integer or float are converted to runs of text (strings) using the 
 * formatting rules described below. Float values should only be formatted in the e, E, f, 
 * g, G, r or R styles.
 * 
 * Variables of baseType duration are treated as floats, representing the duration in seconds.
 * 
 * Variables of baseType file are rendered using a control that enables the user to open the file. 
 * The control should display the name associated with the file, if any.
 * 
 * Variables of baseType uri are rendered using a control that enables the user to open the 
 * identified resource, for example, by following a hypertext link in the case of a URL.
 * 
 * For variables of single cardinality, the value of the variable is printed.
 * 
 * For variables of ordered cardinality, if the attribute index is set, the single value corresponding 
 * to the indexed member is printed, otherwise an ordered list of the values within the container is 
 * printed, delimited by the string value of the delimiter attribute.
 * 
 * For variables of multiple cardinality, a list of the values within the container is printed, 
 * delimited by the string value of the delimiter attribute.
 * 
 * For variables of record cardinality, if the attribute field is set, the value corresponding 
 * to the specified field is printed, otherwise a list of the field names and corresponding field 
 * values within the variable is printed, delimited by the string value of the delimiter attribute 
 * and with the correspondence between them indicated by the string value of the mappingIndicator 
 * attribute.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PrintedVariableEngine extends AbstractEngine {
    
    /**
     * Set the PrintedVariable object to be executed by the engine depending
     * on the current context.
     *
     * @param QtiComponent $printedVariable A PrintedVariable object.
     * @throws InvalidArgumentException If $printedVariable is not a PrintedVariable object.
     */
    public function setComponent(QtiComponent $printedVariable) {
        if ($printedVariable instanceof PrintedVariable) {
            parent::setComponent($printedVariable);
        }
        else {
            $msg = "The PrintedVariableEngine class only accepts PrintedVariable objects to be executed.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Processes the encapsulated PrintedVariable object into a formatted
     * string, depending on the PrintedVariable object and the current context.
     * 
     * * If the value to format is an OrderedContainer, and no 'index' attribue value is given, the whole container is displayed.
     * * If no specific precision is given for a float display, the precision will be by default 6.
     * 
     * @return TextRun A processed PrintedVariable as a TextRun object or the NULL value if the variable's value is NULL.
     * @throws PrintedVariableProcessingException If an error occurs while processing the PrintedVariable object into a TextRun object.
     */
    public function process() {
        
        $printedVariable = $this->getComponent();
        $identifier = $printedVariable->getIdentifier();
        $state = $this->getContext();
        
        if ($state[$identifier] === null) {
            return '';
        }
        
        $variable = $state->getVariable($identifier);
        $value = $variable->getValue();
        
        if ($value === null || $value === '' || ($value instanceof Container && $value->isNull() === true)) {
            return '';
        }
        
        if ($variable->getCardinality() === Cardinality::SINGLE) {
            return $this->processValue($variable->getBaseType(), $value);
        }
        else if ($variable->getCardinality() === Cardinality::ORDERED) {
            $index = $printedVariable->getIndex();
            
            // $index might be a variable reference.
            if (is_string($index) === true && $state[$index] !== null) {
                $refIndex = $state[$index];
                
                if ($refIndex instanceof Integer) {
                    // $index references an integer, that's correct.
                    $index = $refIndex->getValue();
                }
                else {
                    // $index references something else than an integer.
                    // This is not correct. We consider then we have
                    // no index given.
                    $index = -1;
                }
            }
            
            if (is_int($index) && $index >= 0 && isset($value[$index])) {
                return $this->processValue($variable->getBaseType(), $value[$index]);
            }
            else {
                // Display all values.
                return $this->processOrderedMultiple($variable);
            }
        }
        else if ($variable->getCardinality() === Cardinality::MULTIPLE) {
            // Display all values.
            return $this->processOrderedMultiple($variable);
        }
        else {
            // This is a record.
            $field = $printedVariable->getField();
            
            if (empty($field) === false && isset($value[$field])) {
                $v = $value[$field];
                return $this->processValue(Utils::inferBaseType($v), $v);
            }
            else {
                // Display all values.
                return $this->processRecord($variable);
            }
        }
    }
    
    /**
     * Processes all values of an ordered/multiple container and merge
     * them into a single string.
     * 
     * @param Variable $variable The ordered/multiple container Variable to process.
     * @return string All the values delimited by printedVariable->delimiter.
     */
    private function processOrderedMultiple(Variable $variable) {
        $processedValues = array();
        $baseType = $variable->getBaseType();
        
        foreach ($variable->getValue() as $v) {
            $processedValues[] = $this->processValue($baseType, $v);
        }
        
        return implode($this->getComponent()->getDelimiter(), $processedValues);
    }
    
    /**
     * Processes all values of a record container and merge them into
     * a single string.
     * 
     * @param RecordContainer $variable The record to process.
     * @return string All the key/values delimited by printedVariable->delimiter. Indicator between keys and values is defined by printedVariable->mappingIndicator.
     */
    private function processRecord(Variable $variable) {
        $processedValues = array();
        $baseType = $variable->getBaseType();
        $mappingIndicator = $this->getComponent()->getMappingIndicator();
        
        foreach ($variable->getValue() as $k => $v) {
            $processedValues[] = "${k}${mappingIndicator}" . $this->processValue(Utils::inferBaseType($v), $v);
        }
        
        return implode($this->getComponent()->getDelimiter(), $processedValues);
    }
    
    /**
     * Process a $value depending on its $baseType.
     * 
     * @param integer $baseType The baseType of the value to process.
     * @param mixed $value A QTI Runtime compliant value.
     * @throws PrintedVariableProcessingException If the baseType is unknown.
     * @return string
     */
    private function processValue($baseType, $value) {
        $printedVariable = $this->getComponent();
        
        if ($value === null) {
            return 'null';
        }
        
        if ($baseType === BaseType::INT_OR_IDENTIFIER) {
            $baseType = (is_int($value) === true) ? BaseType::INTEGER : BaseType::STRING;
        }
        else if ($baseType === BaseType::IDENTIFIER || $baseType === BaseType::URI) {
            $baseType = BaseType::STRING;
        }
        else if ($baseType === BaseType::FILE) {
            // @todo support baseType::FILE in PrintedVariable.
            $msg = "the 'file' BaseType is not supported yet by PrintedVariableEngine implementation.";
            throw new PrintedVariableProcessingException($msg, $this, PrintedVariableProcessingException::RUNTIME_ERROR);
        }
        
        if ($baseType === BaseType::STRING) {
            return $value->getValue();
        }
        else if ($baseType === BaseType::INTEGER || $baseType === BaseType::FLOAT) {
            $format = $printedVariable->getFormat();
            
            if (empty($format) === false) {
               $format = Format::printfFormatIsoToPhp($format);
               return sprintf($format, $value->getValue());
            }
            else if ($baseType === BaseType::FLOAT && $printedVariable->mustPowerForm() === true) {
                return Format::scale10($value->getValue(), 'x');
            }
            else if ($baseType === BaseType::FLOAT) {
                return sprintf('%e', $value->getValue());
            }
            else {
                // integer to string
                return '' . $value->getValue();
            }
        }
        else if ($baseType === BaseType::DURATION) {
            return '' . $value->getSeconds(true);
        }
        else if ($baseType === BaseType::BOOLEAN) {
            return ($value->getValue() === true) ? 'true' : 'false';
        }
        else if ($baseType === BaseType::POINT || $baseType === BaseType::PAIR || $baseType === BaseType::DIRECTED_PAIR) {
            return $value->__toString();
        }
        else {
            $msg = "Unknown value type.";
            throw new PrintedVariableProcessingException($msg, $this, PrintedVariableProcessingException::RUNTIME_ERROR);
        }
    }
}