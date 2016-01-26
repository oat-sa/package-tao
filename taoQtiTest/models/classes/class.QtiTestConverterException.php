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
 * QTI Test Converter Exception
 * 
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * 
 * @access public
 * @package taoQtiTest
 
 */
class taoQtiTest_models_classes_QtiTestConverterException extends common_Exception implements common_exception_UserReadableException {
	
	/**
	 * Create a new QtiTestServiceException object.
	 * 
	 * @param string $message A technical infiormation message.
	 * @param integer $code A code to explicitely identify the nature of the error.
	 */
	public function __construct($message) {
		parent::__construct($message);
	}
	
	/**
	 * Returns a translated human-readable message destinated to the end-user.
	 *
	 * @return string A human-readable message.
	 */
	public function getUserMessage() {
		return $this->message;
	}
}