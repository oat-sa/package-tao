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

use qtism\data\expressions\NumberPresented;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The NumberPresentedProcessor aims at processing NumberPresented
 * Outcome Processing only expressions.
 * 
 * From IMS QTI:
 * 
 * This expression, which can only be used in outcomes processing, calculates the number 
 * of items in a given sub-set that have been attempted (at least once). In other words, 
 * items with which the user has interacted, whether or not they provided a response. The 
 * result is an integer with single cardinality.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class NumberPresentedProcessor extends ItemSubsetProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof NumberPresented) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The NumberPresentedProcessor class only accepts NumberPresented expressions to be processed.";
			throw new InvalidArgumentException($expression);
		}
	}
	
	/**
	 * Process the related NumberPresented expression.
	 * 
	 * @return integer The number of items in the given item sub-set that have been attempted (at least once).
	 * @throws ExpressionProcessingException
	 */
	public function process() {
	    $testSession = $this->getState();
	    $itemSubset = $this->getItemSubset();
	    $numberPresented = 0;
	    
	    foreach ($itemSubset as $item) {
	        
	        $itemSessions = $testSession->getAssessmentItemSessions($item->getIdentifier());
	        
	        foreach ($itemSessions as $itemSession) {
	            
	            if ($itemSession->isPresented() === true) {
	                
	                $numberPresented++;
	            }
	        }
	    }
	    
	    return new Integer($numberPresented);
	}
}