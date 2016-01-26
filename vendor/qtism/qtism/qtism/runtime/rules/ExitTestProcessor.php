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
namespace qtism\runtime\rules;

use qtism\data\rules\ExitTest;
use qtism\data\rules\Rule;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The exit test rule terminates response processing immediately (for this 
 * invocation).
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ExitTestProcessor extends RuleProcessor {
	
	/**
	 * Set the ExitTest object to be processed.
	 * 
	 * @param Rule $rule An ExitTest object.
	 * @throws InvalidArgumentException If $rule is not an ExitTest object.
	 */
	public function setRule(Rule $rule) {
		if ($rule instanceof ExitTest) {
			parent::setRule($rule);
		}
		else {
			$msg = "The ExitTestProcessor only accepts ExitTest objects to be processed.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the ExitTest rule. It simply throws a RuleProcessingException with
	 * the special code RuleProcessingException::EXIT_TEST to simulate the test termination.
	 * 
	 * @throws RuleProcessingException with code = RuleProcessingException::EXIT_TEST In any case.
	 */
	public function process() {
		$msg = "Termination of Test.";
		throw new RuleProcessingException($msg, $this, RuleProcessingException::EXIT_TEST);
	}
}