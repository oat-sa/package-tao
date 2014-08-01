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
 * The AssessmentTestSessionException must be thrown when an error occurs
 * in an AssessmentTestSession.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentTestSessionException extends Exception {
    
    /**
     * Code to use when the origin of the error is unknown.
     * 
     * @var integer
     */
    const UNKNOWN = 0;
    
    /**
     * Code to use when a state violation occurs e.g. while trying
     * to skip the current item but the test session is closed.
     * 
     * @var integer
     */
    const STATE_VIOLATION = 1;
    
    /**
     * Code to use when a navigation mode violation occurs e.g. while
     * trying to move to the next item but the navigation is LINEAR.
     * 
     * @var integer
     */
    const NAVIGATION_MODE_VIOLATION = 2;
    
    /**
     * Code to use when an error occurs while running the outcome processing
     * relate to the AssessmentTest.
     * 
     * @var int
     */
    const OUTCOME_PROCESSING_ERROR = 3;
    
    /**
     * Code to use when an error occurs while running the response processing
     * related to a postponed response submission.
     * 
     * @var integer
     */
    const RESPONSE_PROCESSING_ERROR = 4;
    
    /**
     * Code to use when an error occurs while transmitting item/test results.
     * 
     * @var integer
     */
    const RESULT_SUBMISSION_ERROR = 5;
    
    /**
     * Error code to use when a logic error is done.
     * 
     * @var integer
     */
    const LOGIC_ERROR = 6;
    
    /**
     * Error code to use when a jump is performed outside the current
     * TestPart.
     *
     * @var integer
     */
    const FORBIDDEN_JUMP = 7;
    
    /**
     * Error code to use when the maximum duration of a testPart
     * is reached.
     * 
     * @var integer
     */
    const TEST_PART_DURATION_OVERFLOW = 8;
    
    /**
     * Error code to use when the maximum duration of an assessmentSection
     * is reached.
     * 
     * @var integer
     */
    const ASSESSMENT_SECTION_DURATION_OVERFLOW = 9;
    
    /**
     * Error code to use when the minimum duration of a testPart is not
     * reached.
     * 
     * @var integer
     */
    const TEST_PART_DURATION_UNDERFLOW = 10;
    
    /**
     * Error code to use when the minimum duration of an assessmentSection is not
     * reached.
     * 
     * @var integer
     */
    const ASSESSMENT_SECTION_DURATION_UNDERFLOW = 11;
    
    /**
     * Error code to use when the maximum duration of an assessmentItem is reached.
     * 
     * @var integer
     */
    const ASSESSMENT_ITEM_DURATION_OVERFLOW = 12;
    
    /**
     * Error code to use when the minimum duration of an assessmentItem is not reached.
     * 
     * @var integer
     */
    const ASSESSMENT_ITEM_DURATION_UNDERFLOW = 13;
    
    /**
     * Error code to use when the maximum duration of an assessmentTest is reached.
     * 
     * @var integer
     */
    const ASSESSMENT_TEST_DURATION_OVERFLOW = 14;
    
    /**
     * Error code to use when the minimum duration of an assessmentTest is not reached.
     * 
     * @var integer
     */
    const ASSESSMENT_TEST_DURATION_UNDERFLOW = 15;
    
    /**
     * Error code to use when the maximum number of attempts on the current assessment item
     * is reached.
     * 
     * @var integer
     */
    const ASSESSMENT_ITEM_ATTEMPTS_OVERFLOW = 16;
    
    /**
     * Error code to use when an invalid response is submitted for the current
     * assessment item while itemSessionControl->validateResponse is in force.
     * 
     * @var integer
     */
    const ASSESSMENT_ITEM_INVALID_RESPONSE = 17;
    
    /**
     * Error code to use when trying to skip the current item while
     * it is not allowed to skip it.
     * 
     * @var integer
     */
    const ASSESSMENT_ITEM_SKIPPING_FORBIDDEN = 18;
    
    /**
     * Create a nex AssessmentTestSessionException.
     * 
     * @param string $message A human-readable message describing the error.
     * @param integer $code A code to enable client-code to identify the error programatically.
     * @param Exception $previous An optional previous exception.
     */
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
}