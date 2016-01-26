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

use qtism\common\datatypes\Integer;
use qtism\data\expressions\NumberResponded;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The NumberRespondedProcessor aims at processing NumberResponded
 * Outcome Processing only expressions.
 * 
 * From IMS QTI:
 * 
 * This expression, which can only be used in outcomes processing, calculates the number of 
 * items in a given sub-set that have been attempted (at least once) and for which a response 
 * was given. In other words, items for which at least one declared response has a value 
 * that differs from its declared default (typically NULL). The result is an integer with 
 * single cardinality.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class NumberRespondedProcessor extends ItemSubsetProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof NumberResponded) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The NumberRespondedProcessor class only accepts NumberResponded expressions to be processed.";
			throw new InvalidArgumentException($expression);
		}
	}
	
	/**
	 * Process the related NumberResponded expression.
	 * 
	 * @return integer The number of items in the given sub-set that been attempted (at least once) and for which a response was given.
	 * @throws ExpressionProcessingException
	 */
	public function process() {
	    $testSession = $this->getState();
	    $itemSubset = $this->getItemSubset();
	    $numberResponded = 0;
	    
	    foreach ($itemSubset as $item) {
	        
	        $itemSessions = $testSession->getAssessmentItemSessions($item->getIdentifier());
	        
	        foreach ($itemSessions as $itemSession) {
	            if ($itemSession->isResponded() === true) {
	                $numberResponded++;
	            }
	        }
	    }
	    
	    return new Integer($numberResponded);
	}
}