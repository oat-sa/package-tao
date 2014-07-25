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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use qtism\data\IAssessmentItem;
use qtism\common\datatypes\Point;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Identifier;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\common\datatypes\Boolean;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\common\enums\Cardinality;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\DirectedPair;
use qtism\common\enums\BaseType;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\common\Variable;

/**
 * The VariableFiller provides a way to fill a QtiSm Runtime Variable with
 * a value coming from the client side. These values are always transmitted as
 * plain strings. However, we need a way to transform these string values as 
 * QtiSm compliant variable values. This is the goal of this class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiCommon_helpers_LegacyVariableFiller extends taoQtiCommon_helpers_AbstractVariableFiller {
    
    /**
     * Create a new LegacyVariableFiller object.
     * 
     * @param IAssessmentItem $itemRef The item the variables you want to fill belong to.
     */
    public function __construct(IAssessmentItem $itemRef) {
        parent::__construct($itemRef);
    }
    
    /**
     * Fille the variable $variableName with a correctly transformed
     * $clientSideValue.
     * 
     * @param string $variableName The variable identifier you want to fill.
     * @param string $clientSideValue The value received from the client-side.
     * @return Variable A Variable object filled with a correctly transformed $clientSideValue.
     * @throws OutOfBoundsException If no variable with $variableName is described in the item.
     * @throws OutOfRangeException If the $clientSideValue does not fit the target variable's baseType.
     */
    public function fill($variableName, $clientSideValue) {
        $variable = null;
        
        $outcomeDeclarations = $this->getItemRef()->getOutcomeDeclarations();
        $responseDeclarations = $this->getItemRef()->getResponseDeclarations();
        
        if (isset($responseDeclarations[$variableName]) === true) {
            $variable = ResponseVariable::createFromDataModel($responseDeclarations[$variableName]);
        }
        else if (isset($outcomeDeclarations[$variableName]) === true) {
            $variable = OutcomeVariable::createFromDataModel($outcomeDeclarations[$variableName]);
        }
        
        if (empty($variable) === true) {
            $itemId = $this->getItemRef()->getIdentifier();
            $msg = "No variable declaration '${variableName}' found in '${itemId}'.";
            throw new OutOfBoundsException($msg);
        }
        
        common_Logger::d("Filling variable '" . $variable->getIdentifier() . "'.");
        
        if (is_array($clientSideValue) === false) {
            $clientSideValue = array($clientSideValue);
        }
        
        try {
            $finalValues = array();
            foreach ($clientSideValue as $val) {
                $finalValues[] = self::transform($variable->getBaseType(), $val);
            }
            
            if ($variable->getCardinality() === Cardinality::SINGLE) {
                $variable->setValue($finalValues[0]);
            }
            else if ($variable->getCardinality() === Cardinality::MULTIPLE) {
                $variable->setValue(new MultipleContainer($variable->getBaseType(), $finalValues));
            }
            else {
                // Cardinality is ORDERED.
                $variable->setValue(new OrderedContainer($variable->getBaseType(), $finalValues));
            }
            
            return $variable;
        }
        catch (InvalidArgumentException $e) {
            if (is_array($clientSideValue)) {
                $clientSideValue = implode(',', $clientSideValue);
            }
            
            $msg = "An error occured while filling variable '${variableName}' with client-side value '${clientSideValue}':" . $e->getMessage();
            throw new InvalidArgumentException($msg, 0, $e); 
        }
    }
    
    /**
     * Transform $value in a $baseType datatype.
     * 
     * @param integer $baseType
     * @param mixed $value
     * @return mixed
     * @throws InvalidArgumentException If $baseType is unknown.
     * @throws OutOfRangeException If $value cannot be transformed into $baseType datatype.
     */
    protected static function transform($baseType, $value) {
        
        switch ($baseType) {
            case BaseType::BOOLEAN:
                if ($value === '') {
                    return null;
                }
                else {
                    return ($value === 'true') ? true : false;
                }
            break;
            
            case BaseType::DIRECTED_PAIR:
                if (is_array($value) === false) {
                    $msg = "Cannot transform a value into a QTI directedPair, it is not an array, '" . gettype($value) . "' given";
                    throw new OutOfRangeException($msg);
                }
                else if (empty($value[0])) {
                    $msg = "Cannot transform a value into a QTI directedPair, the first identifier was not given.";
                    throw new OutOfRangeException($msg);
                }
                else if (empty($value[1])) {
                    $msg = "Cannot transform a value into a QTI directedPair, the second identifier was not given.";
                    throw new OutOfRangeException($msg);
                }
                else {
                    return new DirectedPair($value[0], $value[1]);
                }
            break;
            
            case BaseType::PAIR:
                if (is_array($value) === false) {
                    $msg = "Cannot transform a value into a QTI pair, it is not an array, '" . gettype($value) . "' given";    
                    throw new OutOfRangeException($msg);
                }
                else if (empty($value[0])) {
                    $msg = "Cannot transform a value into a QTI pair, the first identifier was not given.";
                    throw new OutOfRangeException($msg);
                }
                else if (empty($value[1])) {
                    $msg = "Cannot transform a value into a QTI pair, the second identifier was not given.";
                    throw new OutOfRangeException($msg);
                }
                else {
                    return new Pair($value[0], $value[1]);
                }
            break;
            
            case BaseType::STRING:
                return new String($value);
            break;
            case BaseType::IDENTIFIER:
                return ($value !== '') ? new Identifier($value) : null;
            break;
            
            case BaseType::INTEGER:
                return ($value !== '') ? new Integer(intval($value)) : null;
            break;
            
            case BaseType::FLOAT:
                return ($value !== '') ? new Float(floatval($value)) : null;
            break;
            
            case BaseType::POINT:
                if (is_array($value) === false) {
                    $msg = "Cannot transform a value into a QTI point, it is not an array, '" . gettype($value) . "' given";
                    throw new OutOfRangeException($msg);
                }
                else if (empty($value[0])) {
                    $msg = "Cannot transform a value into a QTI point, X was not given.";
                    throw new OutOfRangeException($msg);
                }
                else if (empty($value[1])) {
                    $msg = "Cannot transform a value into a QTI point, Y was not given.";
                    throw new OutOfRangeException($msg);
                }
                else {
                    return new Point(intval($value[0]), intval($value[1]));
                }
            break;
            
            default:
                $msg = "Not supported baseType constant '${baseType}'.";
                throw new InvalidArgumentException($msg);
            break;
        }
    }
}