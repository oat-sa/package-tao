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
 * 
 */


/**
 * The QtiTestServiceException is thrown when an error occurs
 * at the QtiTestService level, in order to report an appropriate
 * Exception to the client side.
 * 
 * Error codes:
 * 
 * * 0: Unknown error.
 * * 1: The QTI-XML item involved in a test cannot be read.
 * * 2: The QTI-XML item involved in a test cannot be written.
 * * 3: The QTI-XML test cannot be read.
 * * 4: The QTI-XML test cannot be written. 
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @package taoQtiTest
 
 */
class taoQtiTest_models_classes_QtiTestServiceException extends common_Exception implements common_exception_UserReadableException {
	
        const TEST_WRITE_ERROR = 0;
        const ITEM_READ_ERROR = 1;
        const ITEM_WRITE_ERROR = 2;
        const TEST_READ_ERROR = 3;
        
	/**
	 * Create a new QtiTestServiceException object.
	 * 
	 * @param string $message A technical infiormation message.
	 * @param integer $code A code to explicitely identify the nature of the error.
	 */
	public function __construct($message, $code = 0) {
		parent::__construct($message, $code);
	}
	
	/**
	 * Returns a translated human-readable message destinated to the end-user.
	 *
	 * @return string A human-readable message.
	 */
	public function getUserMessage() {
		switch ($this->getCode()) {
                        case 3:
				return __("The QTI test could not be retrieved correctly.");
				break;
                        case 2:
				return __("An item involved in the test cannot be written.");
				break;
			case 1:
				return __("An item involved in the test cannot be read or is not QTI compliant.");
				break;
                        case 0:		
			default:
				return __("The QTI-XML test could not be written correctly.");
				break;
		}
	}
}