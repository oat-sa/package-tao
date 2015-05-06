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
namespace qtism\runtime\common;

use \RuntimeException;
use \Exception;

/**
 * This Exception should be raised at runtime while processing something (e.g. an expression,
 * an outcomeCondition, ...).
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ProcessingException extends \RuntimeException {
	
	/**
	 * Code to use when the error of the nature is unknown.
	 *
	 * @var integer
	 */
	const UNKNOWN = 0;
	
	/**
	 * Code to use when a runtime error occcurs.
	 * 
	 * e.g. When a division by zero occurs, an overflow, ...
	 * 
	 * @var unknown_type
	 */
	const RUNTIME_ERROR = 1;
	
	/**
	 * Code to use when a requested variable does not exist or is not set.
	 *
	 * @var integer
	 */
	const NONEXISTENT_VARIABLE = 2;
	
	/**
	 * Code to use when a variable has not the expected type.
	 *
	 * e.g. If the correct processor retrieves a variable which is not
	 * a ResponseDeclaration.
	 *
	 * @var integer
	 */
	const WRONG_VARIABLE_TYPE = 3;
	
	/**
	 * Code to use when a variable has not the expected baseType.
	 *
	 * e.g. If the mapResponsePoint processor retrieves a variable with
	 * a baseType different than point.
	 *
	 * @var integer
	 */
	const WRONG_VARIABLE_BASETYPE = 4;
	
	/**
	 * Code to use when a variable is inconsistent.
	 *
	 * e.g. If the mapResponsePoint processor retrieves a variable with
	 * no areaMapping set.
	 *
	 * @var integer
	 */
	const INCONSISTENT_VARIABLE = 5;
	
	/**
	 * Code to use when a processor encounters an internal logic error.
	 *
	 * e.g. min >= max in the randomFloat processor.
	 *
	 * @var integer
	 */
	const LOGIC_ERROR = 6;
	
	private $source = null;
	
	/**
	 * Create a new ProcessingException.
	 * 
	 * @param string $msg A human-readable message describing the error.
	 * @param Processable $source A Processable object where the error occured.
	 * @param integer A code to characterize the error.
	 * @param Exception $previous An optional Exception object that caused the error.
	 */
	public function __construct($msg, Processable $source, $code = 0, Exception $previous = null) {
		
		parent::__construct($msg, $code, $previous);
		$this->setSource($source);
	}
	
	/**
	 * Set the source of the exception.
	 * 
	 * @param Processable $source The Processable object whithin the error occured.
	 */
	protected function setSource(Processable $source) {
		$this->source = $source;
	}
	
	/**
	 * Get the source of the exception.
	 * 
	 * @return Processable The Processable object within the error occured.
	 */
	public function getSource() {
		return $this->source;
	}
}