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
namespace qtism\runtime\expressions;

use qtism\common\datatypes\Scalar;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Float;
use qtism\common\Comparable;
use qtism\runtime\common\ResponseVariable;
use qtism\data\expressions\Expression;
use qtism\data\expressions\MapResponse;
use \InvalidArgumentException;

/**
 * The MapResponseProcessor class aims at processing MapResponse Expression objects.
 * 
 * FROM IMS QTI:
 * 
 * This expression looks up the value of a response variable and then transforms it using the 
 * associated mapping, which must have been declared. The result is a single float. If the 
 * response variable has single cardinality then the value returned is simply the mapped 
 * target value from the map. If the response variable has multiple or ordered cardinality 
 * then the value returned is the sum of the mapped target values. This expression cannot 
 * be applied to variables of record cardinality.
 * 
 * For example, if a mapping associates the identifiers {A,B,C,D} with the values {0,1,0.5,0} 
 * respectively then mapResponse will map the single value 'C' to the numeric value 0.5 and 
 * the set of values {C,B} to the value 1.5.
 * 
 * If a container contains multiple instances of the same value then that value is counted 
 * once only. To continue the example above {B,B,C} would still map to 1.5 and not 2.5.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class MapResponseProcessor extends ExpressionProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof MapResponse) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The MapResponseProcessor only accepts MapResponse Expression objects to be processed.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the MapResponse expression.
	 * 
	 * * An ExpressionProcessingException is thrown if the variable is not defined.
	 * * An ExpressionProcessingException is thrown if the variable has no mapping defined.
	 * * An ExpressionProcessingException is thrown if the variable is not a ResponseVariable.
	 * * An ExpressionProcessingException is thrown if the cardinality of the variable is RECORD.
	 * 
	 * @return a QTI float value.
	 * @throws ExpressionProcessingException
	 */
	public function process() {
		$expr = $this->getExpression();
		$state = $this->getState();
		$identifier = $expr->getIdentifier();
		$variable = $state->getVariable($identifier);
		
		if (!is_null($variable)) {
			
			if ($variable instanceof ResponseVariable) {
				
				$mapping = $variable->getMapping();
				
				if (!is_null($mapping)) {
					
					if ($variable->isSingle()) {
						
						foreach ($mapping->getMapEntries() as $mapEntry) {
							
							$val = $state[$identifier];
							$mapKey = $mapEntry->getMapKey();
							
							if ($val instanceof String && $mapEntry->isCaseSensitive() === false) {
								$val = mb_strtolower($val->getValue(), 'UTF-8');
								$mapKey = mb_strtolower($mapKey, 'UTF-8');
							}
							
							if ($val instanceof Comparable && $val->equals($mapKey) || $val === $mapKey) {
								// relevant mapping found.
								$mappedValue = $mapEntry->getMappedValue();
								return new Float($mappedValue);
							}
						}
						
						// No relevant mapping found, return mapping default.
						return new Float($mapping->getDefaultValue());
					}
					else if ($variable->isMultiple()) {
						
						$result = 0.0;
						
						if (!is_null($variable->getValue())) {
						    $mapped = array(); // already mapped keys.
						    $mapEntries = $mapping->getMapEntries();
						    
						    foreach ($variable->getValue() as $val) {
						        
						        for ($i = 0; $i < count($mapEntries); $i++) {
						            
						            $mapKey = $rawMapKey = $mapEntries[$i]->getMapKey();
						            
						            if ($val instanceof String && $mapEntries[$i]->isCaseSensitive() === false) {
						                $val = mb_strtolower($val->getValue(), 'UTF-8');
						                $mapKey = mb_strtolower($mapKey, 'UTF-8');
						            }
						            
                                    if (($val instanceof Comparable && $val->equals($mapKey) === true) || $val === $mapKey) {
						                if (in_array($rawMapKey, $mapped, true) === false) {
						                    $result += $mapEntries[$i]->getMappedValue();
						                    $mapped[] = $rawMapKey;
						                    
						                }
						                // else...
						                // This value has already been mapped.
						                
						                break;
						            }
						        }
						        
						        if ($i >= count($mapEntries)) {
						            // No explicit mapping found for source value $val.
						            $result += $mapping->getDefaultValue();
						        }
						    }
						    
						    // When mapping a container, try to apply lower or upper bound.
						    if ($mapping->hasLowerBound() && $result < $mapping->getLowerBound()) {
						        return new Float($mapping->getLowerBound());
						    }
						    else if ($mapping->hasUpperBound() && $result > $mapping->getUpperBound()) {
						        return new Float($mapping->getUpperBound());
						    }
						    else {
						        return new Float($result);
						    }
						}
						else {
						    // Returns a 0.0 result.
						    return new Float($result);
						}
						
						
					}
					else {
						$msg = "MapResponse cannot be applied on a RECORD container.";
						throw new ExpressionProcessingException($msg, $this, ExpressionProcessingException::WRONG_VARIABLE_BASETYPE);
					}
				}
				else {
					$msg = "The target variable has no mapping while processing MapResponse.";
					throw new ExpressionProcessingException($msg, $this, ExpressionProcessingException::INCONSISTENT_VARIABLE);
				}
			}
			else {
				$msg = "The target variable must be a ResponseVariable, OutcomeVariable given while processing MapResponse.";
				throw new ExpressionProcessingException($msg, $this, ExpressionProcessingException::WRONG_VARIABLE_TYPE);
			}
		}
		else {
			$msg = "No variable with identifier '${identifier}' could be found while processing MapResponse.";
			throw new ExpressionProcessingException($msg, $this, ExpressionProcessingException::NONEXISTENT_VARIABLE);
		}
	}
}