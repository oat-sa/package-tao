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

namespace qtism\runtime\rendering\markup;

use qtism\data\content\PrintedVariable;
use qtism\runtime\processing\PrintedVariableEngine;
use qtism\runtime\common\State;
use \InvalidArgumentException;
use \RuntimeException;
use \Exception;

/**
 * Utility class for the markup package.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Utils {
    
    /**
     * Helper method to be used in QTI template oriented rendering to produce the final
     * value of a qti:printedVariable component.
     * 
     * If an error occurs while printing the variable, the return value will contain the error message.
     * 
     * @param State $context A State object from where values will be retrieved prior to formatting, depending on the $identifier argument.
     * @param string $identifier The identifier of the variable to be printed.
     * @param string $format The ISO 9899 format string (see printf).
     * @param boolean $powerForm Render float values in 'e' or 'E' format.
     * @param integer|string $base The number base to use when formatting integer variables (can be a variable reference).
     * @param integer|string $index The index to use when displaying ordered cardinality variables (can be a variable reference).
     * @param integer $delimiter The delimiter to use between values when displaying ordered, multiple, or record cardinality variables.
     * @param string $field The field specifier to use when displaying variables of record cardinality.
     * @param string $mappingIndicator The mapping indicator to use between field name and field value when displaying record cardinality variables.
     * @return string The formatted variable or an error message.
     */
    static public function printVariable(State $context, $identifier, $format = '', $powerForm = false, $base = 10, $index = -1, $delimiter = ';', $field = '', $mappingIndicator = '=') {
        
        try {
            $printedVariable = new PrintedVariable($identifier);
            $printedVariable->setFormat($format);
            $printedVariable->setPowerForm($powerForm);
            $printedVariable->setBase($base);
            $printedVariable->setIndex($index);
            $printedVariable->setDelimiter($delimiter);
            $printedVariable->setField($field);
            $printedVariable->setMappingIndicator($mappingIndicator);
            
            $engine = new PrintedVariableEngine($printedVariable);
            $engine->setContext($context);
            return $engine->process();
        }
        catch (Exception $e) {
            $previous = $e->getMessage();
            return "qtism\\runtime\\rendering\\markup\\Utils::printVariable() method threw an unexpected exception:\n${previous}.";
        }
    }
    
}