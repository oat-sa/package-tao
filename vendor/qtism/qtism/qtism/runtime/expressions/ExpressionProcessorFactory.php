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

use qtism\data\QtiComponent;
use qtism\runtime\common\ProcessorFactory;
use qtism\runtime\common\Processable;
use qtism\runtime\expressions\operators\OperatorProcessor;
use qtism\data\expressions\Expression;
use \RuntimeException;

/**
 * The ExpressionProcessorFactory class provides a way to build
 * an appropriate ExpressionProcessor on basis of QTI Data Model Expression
 * objects.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ExpressionProcessorFactory implements ProcessorFactory {
	
	/**
	 * Create a new ExpressionProcessorFactory object.
	 * 
	 */
	public function __construct() {
		
	}
	
	/**
	 * Create the ExpressionProcessor object able to process the 
	 * given $expression.
	 * 
	 * @param QtiComponent $expression An Expression object you want to get the related processor.
	 * @return Processable The related ExpressionProcessor object.
	 * @throws RuntimeException If no ExpressionProcessor can be found for the given $expression.
	 */
	public function createProcessor(QtiComponent $expression) {
		$qtiClassName = ucfirst($expression->getQtiClassName());
		$nsPackage = 'qtism\\runtime\\expressions\\';
		$className =  $nsPackage . $qtiClassName . 'Processor';
		
		if (class_exists($className) === true) {
			// This is a simple expression to be processed.
			return new $className($expression);
		}
		
		$msg = "The QTI expression class '${qtiClassName}' has no dedicated ExpressionProcessor class.";
		throw new RuntimeException($msg);
	}
}