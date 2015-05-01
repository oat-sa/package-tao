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

use qtism\data\AssessmentItemRef;

use qtism\runtime\common\State;
use qtism\data\AssessmentItemRefCollection;
use qtism\common\collections\IdentifierCollection;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\data\expressions\ItemSubset;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The ItemSubsetProcessor class is the base class of Outcome Processing only
 * expression processors. These Outcome Processing only expressions are:
 * 
 * * testVariables
 * * outcomeMaximum
 * * outcomeMinimum
 * * numberCorrect
 * * numberIncorrect
 * * numberResponded
 * * numberPresented
 * * numberSelected
 * 
 * From IMS QTI:
 * 
 * This class defines the concept of a sub-set of the items selected in an assessmentTest.
 * The attributes define criteria that must be matched by all members of the sub-set. It is 
 * used to control a number of expressions in outcomeProcessing for returning information 
 * about the test as a whole, or abitrary subsets of it.
 * 
 * If specified, only variables from items in the assessmentSection with matching identifier 
 * are matched. Items in sub-sections are included in this definition.
 * 
 * If specified, only variables from items with a matching category are included.
 * 
 * If specified, only variables from items with no matching category are included.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class ItemSubsetProcessor extends ExpressionProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof ItemSubset) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The ItemSubsetProcessor class only accepts ItemSubset expressions to be processed.";
			throw new InvalidArgumentException($expression);
		}
	}
	
	/**
	 * A convenience method enabling you to get the sectionIdentifier attribute value
	 * of the ItemSubset expression to be processed.
	 * 
	 * @return string A section identifier. If no sectionIdentifier attribute was specified, an empty string ('') is returned.
	 */
	protected function getSectionIdentifier() {
	    return $this->getExpression()->getSectionIdentifier();
	}
	
	/**
	 * A convenience method enabling you to get the includeCategory attribute value
	 * of the ItemSubset expression to be processed.
	 * 
	 * @return IdentifierCollection A collection of category identifiers or NULL if no categories to be included were specified.
	 */
	protected function getIncludeCategories() {
	    $categories = $this->getExpression()->getIncludeCategories();
	    return (count($categories) === 0) ? null : $categories;
	}
	
	/**
	 * A convenience method enabling you to get the excludeCategory attribute value of the
	 * ItemSubset expression to be processed.
	 * 
	 * @return IdentifierCollection A collection of category identifiers or NULL if no categories to be excluded were specified.
	 */
	protected function getExcludeCategories() {
	    $categories = $this->getExpression()->getExcludeCategories();
	    return (count($categories) === 0) ? null : $categories;
	}
	
	/**
	 * Convenience method that returns the mapped variable identifier from $targetIdentifier.
	 * 
	 * @param AssessmentItemRef $assessmentItemRef An AssessmentItemRef object where variable mappings can be found.
	 * @param string $targetIdentifier A targetIdentifier to be replaced by a sourceIdentifier.
	 * @return string|false The mapped identifier or $targetIdentifier if no mapping could be established.
	 */
	protected static function getMappedVariableIdentifier(AssessmentItemRef $assessmentItemRef, $targetIdentifier) {
	    // return false if no mapping found.
	    $sourceIdentifier = $targetIdentifier;
	    
	    foreach ($assessmentItemRef->getVariableMappings() as $variableMapping) {
	        if ($variableMapping->getTarget() === $targetIdentifier) {
	            $sourceIdentifier = $variableMapping->getSource();
	            break;
	        }
	        else if ($variableMapping->getSource() === $targetIdentifier) {
	            // Conflict.
	            $sourceIdentifier = false;
	        }
	    }
	    
	    return $sourceIdentifier;
	}
	
	/**
	 * A convenience method enabling you to get the item subset corresponding to the ItemSubset expression to be processed.
	 * 
	 * @return AssessmentItemRefCollection A collection of AssessmentItemRef object that match the criteria expressed by the ItemSubset expression to be processed.
	 */
	protected function getItemSubset() {
	    $sectionIdentifier = $this->getSectionIdentifier();
	    $includeCategories = $this->getIncludeCategories();
	    $excludeCategories = $this->getExcludeCategories();
	    
	    return $this->getState()->getItemSubset($sectionIdentifier, $includeCategories, $excludeCategories);
	}
}