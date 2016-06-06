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

use qtism\common\utils\Reflection;

use qtism\data\expressions\Expression;

use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;
use \InvalidArgumentException;

/**
 * Utility class for Processors.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Utils {
	
	/**
	 * Removes trailing and ending braces ('{' and '}') from a variableRef.
	 * 
	 * @return string A sanitized variableRef.
	 */
	public static function sanitizeVariableRef($variableRef) {
		if (gettype($variableRef) === 'string') {
			return trim($variableRef, '{}');
		}
		else {
			$msg = "The Utils::sanitizeVariableRef method only accepts a string argument, '" . gettype($variableRef) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Returns a processing error reporting message in the following format:
	 * 
	 * [ExpressionClassName] My message...
	 * 
	 * @param Expression $expression A given expression that failed to be processed.
	 * @param string $message A formatted error reporting message.
	 * @return string
	 */
	public static function errorReporting(Expression $expression, $message) {
	    $shortClassName = Reflection::shortClassName($expression);
	    return "[${shortClassName}] ${message}";
	}
}