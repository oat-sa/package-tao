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

use qtism\common\datatypes\Float;
use qtism\common\enums\BaseType;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\common\MultipleContainer;
use qtism\data\expressions\OutcomeMinimum;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The OutcomeMinimumProcessor aims at processing OutcomeMinimum
 * Outcome Processing only expressions.
 * 
 * From IMS QTI:
 * 
 * This expression, which can only be used in outcomes processing, simultaneously looks up 
 * the normalMinimum value of an outcome variable in a sub-set of the items referred to in a 
 * test. Only variables with single cardinality are considered. Items with no declared 
 * minimum are ignored. The result has cardinality multiple and base-type float.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OutcomeMinimumProcessor extends ItemSubsetProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof OutcomeMinimum) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The OutcomeMinimumProcessor class only accepts OutcomeMinimum expressions to be processed.";
			throw new InvalidArgumentException($expression);
		}
	}
	
	/**
	 * Process the related OutcomeMinimum expression.
	 * 
	 * @return MultipleContainer|null A MultipleContainer object with baseType float containing all the retrieved normalMinimum values or NULL if no declared minimum in the sub-set. 
	 * @throws ExpressionProcessingException
	 */
	public function process() {
	    $itemSubset = $this->getItemSubset();
	    $testSession = $this->getState();
	    $outcomeIdentifier = $this->getExpression()->getOutcomeIdentifier();
	    // If no weightIdentifier specified, its value is an empty string ('').
	    $weightIdentifier = $this->getExpression()->getWeightIdentifier();
	    $weight = (empty($weightIdentifier) === true) ? false : $testSession->getWeight($weightIdentifier);
	    $result = new MultipleContainer(BaseType::FLOAT);
	    
	    foreach ($itemSubset as $item) {
	        $itemSessions = $testSession->getAssessmentItemSessions($item->getIdentifier());
	        
	        foreach ($itemSessions as $itemSession) {
	            
	           // Variable mapping is in force.
	           $id = self::getMappedVariableIdentifier($itemSession->getAssessmentItem(), $outcomeIdentifier); 
	           if ($id === false) {
	               // Variable name conflict.
	               continue;
	           }
	           
	           if (isset($itemSession[$id]) && $itemSession->getVariable($id) instanceof OutcomeVariable) {
	               
	                $var = $itemSession->getVariable($id);
	                 
                    // Does this OutcomeVariable contain a value for normalMaximum?
                    if (($normalMinimum = $var->getNormalMinimum()) !== false) {
                        if ($weight === false) {
                            // No weight to be applied.
                            $result[] = new Float($normalMinimum);
                        }
                        else {
                            
                            // A weight has to be applied.
                            $result[] = new Float(floatval($normalMinimum *= $weight->getValue()));
                        }
                    }
                    // else ... items with no declared minimum are ignored.
	            }
	        }
	    }
	    
	    return $result;
	}
}