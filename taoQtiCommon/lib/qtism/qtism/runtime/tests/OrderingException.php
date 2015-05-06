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
 * The OrderingException must be thrown when an error occurs while
 * ordering child elements of an AssessmentSection.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OrderingException extends Exception {
    
    /**
     * Error code to use when the nature
     * of the error is unknown.
     * 
     * @var integer
     */
    const UNKNOWN = 0;
    
    /**
     * Error code to use when the error comes
     * from a lack of logic.
     * 
     * @var integer
     */
    const LOGIC_ERROR = 1;
    
    /**
     * Create a new OrderingException exception object.
     * 
     * @param string $message A human-readable message describing the error while ordering child elements of an AssessmentSection.
     * @param integer $code The code that enables client-code to identify the nature of the error efficiently.
     * @param Exception $previous An optional previous Exception object.
     */
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}