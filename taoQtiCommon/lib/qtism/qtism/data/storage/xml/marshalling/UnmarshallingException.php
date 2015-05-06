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


namespace qtism\data\storage\xml\marshalling;

use \Exception;
use \DOMElement;

/**
 * Exception to be thrown when an error occurs during the unmarshalling process
 * of a DOMElement object.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class UnmarshallingException extends Exception {
	
	/**
	 * The DOMElement object that caused the exception to be thrown.
	 * 
	 * @var DOMElement
	 */
	private $DOMElement;
	
	/**
	 * Create a new instance of UnmarshallingException.
	 * 
	 * @param string $message A human-readable message which describe the exception.
	 * @param DOMElement $element The DOMElement object that caused the exception to be thrown.
	 * @param Exception $previous A previous Exception that caused the exception to be thrown.
	 */
	public function __construct($message, DOMElement $element, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
		$this->setDOMElement($element);
	}
	
	/**
	 * Get the DOMElement object that caused the exception to be thrown.
	 * 
	 * @return DOMElement A DOMElement object.
	 */
	public function getDOMElement() {
		return $this->DOMElement;
	}
	
	/**
	 * Set the DOMElement object that caused the exception to be thrown.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 */
	protected function setDOMElement(DOMElement $element) {
		$this->DOMElement = $element;
	}
}
