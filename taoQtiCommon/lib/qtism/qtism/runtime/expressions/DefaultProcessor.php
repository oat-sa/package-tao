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

use qtism\data\expressions\DefaultVal;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The DefaultProcessor class aims at processing Default QTI Data Model Expressions.
 * 
 * From IMS QTI:
 * 
 * This expression looks up the declaration of an itemVariable and returns the associated
 * defaultValue or NULL if no default value was declared. When used in outcomes processing
 * item identifier prefixing (see variable) may be used to obtain the default value from an 
 * individual item.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class DefaultProcessor extends ExpressionProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof DefaultVal) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The DefaultProcessor class only accepts a Default Expression to be processed.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Returns the defaultValue of the current Expression to be processed. If no Variable
	 * with the given identifier is found, null is returned. If the Variable has no defaultValue,
	 * null is returned.
	 * 
	 * @return mixed A QTI Runtime compliant value.
	 */
	public function process() {
		$expr = $this->getExpression();
		$state = $this->getState();
		
		$var = $state->getVariable($expr->getIdentifier());
		return ($var === null) ? null : $var->getDefaultValue();
	}
}