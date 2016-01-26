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
namespace qtism\runtime\tests;

use \Exception;

/**
 * The AssessmentItemSessionException class must be used to raise errors
 * related to an AssessmentItemSession. Information about the nature
 * of the error is indicated in the exception code. Please see related
 * class constants for more information about the error codes and their
 * signification.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentItemSessionException extends Exception {
	
    /**
     * Error code to use when the nature of the error is unknown.
     * 
     * @var integer
     */
	const UNKNOWN = 0;
	
	/**
	 * Error code to use when timelimits are in force and the
	 * maximum duration is exceeded at 'endAttempt' time.
	 * 
	 * @var integer
	 */
	const DURATION_OVERFLOW = 1;
	
	/**
	 * Error code to use when timelimits are in force and
	 * the minimum duration is not exceeded at 'endAttempt' time.
	 * 
	 * @var integer
	 */
	const DURATION_UNDERFLOW = 2;
	
	/**
	 * Error code to use when the maximum amount attempts for a non-adaptive
	 * item is exceeded.
	 * 
	 * @var integer
	 */
	const ATTEMPTS_OVERFLOW = 3;
	
	/**
	 * Error code to use when a runtime error that could not be corrected
	 * occurs during the assessment item session lifecycle.
	 * 
	 * @var integer
	 */
	const RUNTIME_ERROR = 4;
	
	/**
	 * Error code to return when itemSessionControl.validateResponses is in force
	 * but a provided response is incorrect.
	 * 
	 * @var integer
	 */
	const INVALID_RESPONSE = 5;
	
	/**
	 * Error code to use when itemSessionControl.allowSkipping is not in force
	 * but a request to skip the item is performed.
	 * 
	 * @var integer
	 */
	const SKIPPING_FORBIDDEN = 6;
	
	/**
	 * Error code to use when a sequence of states is violated.
	 * 
	 * @var integer
	 */
	const STATE_VIOLATION = 7;
	
	/**
	 * The AssessmentItemSession object which threw the error.
	 * 
	 * @var AssessmentItemSession
	 */
	private $source;
	
	/**
	 * Create a new AssessmentItemSessionException object.
	 * 
	 * @param string $message A human-readable message describing the nature of the exception.
	 * @param AssessmentItemSession $source The AssessmentItemSession object from where the error occured.
	 * @param integer $code A numeric error code. The accepted error codes are described in the constants of this class. 
	 * @param Exception $previous An optional previous Exception object that was previously thrown and led to this Exception.
	 */
	public function __construct($message, AssessmentItemSession $source, $code = AssessmentItemSessionException::UNKNOWN, Exception $previous = null) {
	    parent::__construct($message, $code, $previous);
	    $this->setSource($source);
	}
	
	/**
	 * Set the AssessmentItemSource object the exception comes from.
	 * 
	 * @param AssessmentItemSession $source An AssessmentItemSession object.
	 */
	public function setSource(AssessmentItemSession $source) {
	    $this->source = $source;
	}
	
	/**
	 * Get the AssessmentItemSource object the exception comes from.
	 * 
	 * @return AssessmentItemSession An AssessmentItemSession object.
	 */
	public function getSource() {
	    return $this->source;
	}
}