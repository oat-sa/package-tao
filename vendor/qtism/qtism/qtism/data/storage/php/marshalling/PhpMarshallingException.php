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

namespace qtism\data\storage\php\marshalling;

use \Exception;

/**
 * The exception class to use when exception occurs during PHP marshalling time.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PhpMarshallingException extends Exception {
    
    /**
     * Error code to use when the error is unknown.
     * 
     * @var integer
     */
    const UNKNOWN = 0;
    
    /**
     * Error code to use when a runtime error occurs
     * at marshalling time.
     * 
     * @var integer
     */
    const RUNTIME = 1;
    
    /**
     * Error code to use while dealing with the stream where
     * the code has to be put into.
     * 
     * @var integer
     */
    const STREAM = 2;
    
    /**
     * Create a new PhpMarshallingException object.
     * 
     * @param string $message A human-readable message.
     * @param integer $code An error code.
     * @param Exception $previous A previously thrown exception.
     */
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
}