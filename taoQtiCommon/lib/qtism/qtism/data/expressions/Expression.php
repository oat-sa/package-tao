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


namespace qtism\data\expressions;

use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use \InvalidArgumentException;

/**
 * The base class for all QTI expressions.
 * 
 * From IMS QTI:
 * 
 * Expressions are used to assign values to item variables and to control conditional 
 * actions in response and template processing.
 * 
 * An expression can be a simple reference to the value of an itemVariable, a 
 * constant value from one of the value sets defined by baseTypes or a hierarchical 
 * expression operator. Like itemVariables, each expression can also have the special value NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class Expression extends QtiComponent {
	
	private static $expressionClassNames = array('and', 'anyN', 'baseValue', 'containerSize', 'contains', 'correct', 'customOperator', 'default', 
			'delete', 'divide', 'durationGTE', 'durationLT', 'equal', 'equalRounded', 'fieldValue', 'gcd', 'lcm', 'repeat', 'gt', 'gte', 'index', 
			'inside', 'integerDivide', 'integerModulus', 'integerToFloat', 'isNull', 'lt', 'lte', 'mapResponse', 'mapResponsePoint', 'match', 
			'mathOperator', 'mathConstant', 'max', 'min', 'member', 'multiple', 'not', 'null', 'numberCorrect', 'numberIncorrect', 'numberPresented', 
			'numberResponded', 'numberSelected', 'or', 'ordered', 'outcomeMaximum', 'outcomeMinimum', 'patternMatch', 'power', 'product', 'random', 
			'randomFloat', 'randomInteger', 'round', 'roundTo', 'statsOperator', 'stringMatch', 'substring', 'subtract', 'sum', 'testVariables', 
			'truncate', 'variable');
	
	/**
	 * Returns an array of string which are all the class names that 
	 * are sub classes of the 'expression' QTI class.
	 * 
	 * @return array An array of string values.
	 */
	public static function getExpressionClassNames() {
		return self::$expressionClassNames;
	}
	
	public function getComponents() {
		return new QtiComponentCollection();
	}
}
