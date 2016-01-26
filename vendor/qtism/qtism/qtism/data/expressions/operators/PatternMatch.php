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


namespace qtism\data\expressions\operators;

use qtism\data\expressions\ExpressionCollection;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The patternMatch operator takes a sub-expression which must have single cardinality
 * and a base-type of string. The result is a single boolean with a value of true if
 * the sub-expression matches the regular expression given by pattern and false if it
 * doesn't. If the sub-expression is NULL then the operator results in NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PatternMatch extends Operator {
	
	/**
	 * From IMS QTI:
	 * 
	 * The syntax for the regular expression language is as defined in 
	 * Appendix F of [XML_SCHEMA2].
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $pattern;
	
	/**
	 * Create a new PatternMatch object.
	 * 
	 * @param ExpressionCollection $expressions A collection of Expression objects.
	 * @param string $pattern A pattern to match or a variable reference.
	 * @throws InvalidArgumentException If $pattern is not a string value or if the $expressions count exceeds 1.
	 */
	public function __construct(ExpressionCollection $expressions, $pattern) {
		parent::__construct($expressions, 1, 1, array(OperatorCardinality::SINGLE), array(OperatorBaseType::STRING));
		$this->setPattern($pattern);
	}
	
	/**
	 * Set the pattern to match.
	 * 
	 * @param string $pattern A pattern or a variable reference.
	 * @throws InvalidArgumentException If $pattern is not a string value.
	 */
	public function setPattern($pattern) {
		if (gettype($pattern) === 'string') {
			$this->pattern = $pattern;
		}
		else {
			$msg = "The pattern argument must be a string or a variable reference, '" . $pattern . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the pattern to match.
	 * 
	 * @return string A pattern or a variable reference.
	 */
	public function getPattern() {
		return $this->pattern;
	}
	
	public function getQtiClassName() {
		return 'patternMatch';
	}
}
