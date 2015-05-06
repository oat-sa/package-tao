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

use qtism\runtime\common\ResponseVariable;
use qtism\data\expressions\Expression;
use qtism\data\expressions\Correct;
use \InvalidArgumentException;

/**
 * The CorrectProcessor class aims at processing Correct Expression objects from the 
 * QTI Data Model.
 * 
 * FROM IMS QTI:
 * 
 * This expression looks up the declaration of a response variable and returns the
 * associated correctResponse or NULL if no correct value was declared. When used
 * in outcomes processing item identifier prefixing (see variable) may be used to
 * obtain the correct response from an individual item.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class CorrectProcessor extends ExpressionProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof Correct) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The CorrectProcessor can only process Correct Expression objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Returns the related correstResponse as a QTI Runtime compliant value.
	 * 
	 * * If no variable can be matched, null is returned.
	 * * If the target variable has no correctResponse, null is returned.
	 * 
	 * An ExpressionProcessingException is thrown if:
	 * 
	 * * The targeted variable is not a ResponseVariable.
	 * 
	 * @return mixed A QTI Runtime compliant value or null.
	 * @throws ExpressionProcessingException
	 */
	public function process() {
		$expr = $this->getExpression();
		$state = $this->getState();
		$identifier=  $expr->getIdentifier();
		
		$var = $state->getVariable($identifier);
		
		if (is_null($var)) {
			return null;
		}
		else if ($var instanceof ResponseVariable) {
			return $var->getCorrectResponse();
		}
		else {
			$msg = "The variable with identifier '${identifier}' is not a ResponseVariable object.";
			throw new ExpressionProcessingException($msg, $this, ExpressionProcessingException::WRONG_VARIABLE_TYPE);
		}
	}
}