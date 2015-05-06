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
use qtism\data\expressions\MathEnumeration;
use qtism\data\expressions\Expression;
use qtism\data\expressions\MathConstant;
use \InvalidArgumentException;

/**
 * The MathConstant processor aims at processing QTI Data Model MathConstant expressions.
 * 
 * From IMS QTI:
 * 
 * The result is a mathematical constant returned as a single float, e.g. π and e.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class MathConstantProcessor extends ExpressionProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof MathConstant) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The MathConstantProcessor class only processes MathConstant QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the MathConstant Expression. 
	 * 
	 * @return float A float value (e or pi).
	 */
	public function process() {
		$expr = $this->getExpression();
		if ($expr->getName() === MathEnumeration::E) {
			return new Float(M_E);
		}
		else {
			return new Float(M_PI);
		}
	}
}