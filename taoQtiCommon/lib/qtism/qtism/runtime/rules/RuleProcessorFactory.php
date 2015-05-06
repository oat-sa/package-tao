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

use qtism\data\QtiComponent;
use qtism\runtime\common\ProcessorFactory;
use qtism\runtime\common\Processable;
use qtism\data\rules\Rule;
use \RuntimeException;

/**
 * The RuleProcessorFactory class aims at providing a way to
 * build RuleProcessor objects on the demand given a given
 * Rule object.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RuleProcessorFactory implements ProcessorFactory {
	
	/**
	 * Create a new RuleProcessorFactory object.
	 * 
	 */
	public function __construct() {
		
	}
	
	/**
	 * Create the RuleProcessor object able to process the  given $rule.
	 * 
	 * @param QtiComponent $rule A Rule object you want to get the related processor.
	 * @return Processable The related RuleProcessor object.
	 * @throws RuntimeException If no RuleProcessor can be found for the given $rule.
	 */
	public function createProcessor(QtiComponent $rule) {
		$qtiClassName = ucfirst($rule->getQtiClassName());
		$nsPackage = 'qtism\\runtime\\rules\\';
		$className =  $nsPackage . $qtiClassName . 'Processor';
		
		if (class_exists($className) === true) {
			return new $className($rule);
		}
		
		$msg = "The QTI rule class '${qtiClassName}' has no dedicated RuleProcessor class.";
		throw new RuntimeException($msg);
	}
}