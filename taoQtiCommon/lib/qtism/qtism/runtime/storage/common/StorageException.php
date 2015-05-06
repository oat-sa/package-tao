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
namespace qtism\runtime\storage\common;

use \Exception;

/**
 * The StorageException class represents exceptions that AssessmentTestSession
 * Storage Services encounter an error.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class StorageException extends Exception {
    
    /**
     * The error code to be used when the nature of the error
     * is unknown. Should be used in absolute necessity. Otherwise,
     * use the appropriate error code.
     * 
     * @var integer
     */
    const UNKNOWN = 0;
    
    /**
     * Error code to be used when an error occurs while
     * instantiating an AssessmentTestSession.
     * 
     * @var integer
     */
    const INSTANTIATION = 1;
    
    /**
     * Error code to use when an error occurs while
     * persisting an AssessmentTestSession.
     * 
     * @var integer
     */
    const PERSITANCE = 2;
    
    /**
     * Error code to use when an error occurs while
     * retrieving an AssessmentTestSession.
     * 
     * @var integer
     */
    const RETRIEVAL = 3;
    
    /**
     * Create a new StorageException instance.
     * 
     * @param string $message A human-readable message describing the encountered error.
     * @param integer $code A code enabling client-code to identify the cause of the error.
     * @param Exception $previous An optional previous Exception that was thrown and catched.
     */
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
}