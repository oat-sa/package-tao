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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */

namespace qtism\common\datatypes\files;

use \Exception;

/**
 * The exception class to be used when error occurs while dealing
 * with FileManager objects.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class FileManagerException extends Exception {
    
    /**
     * Error code to use when the constitution of the error
     * is unknown.
     * 
     * @var integer
     */
    const UNKNOWN = 0;
    
    /**
     * Create a new FileManagerException object.
     * 
     * @param string $message A human-readable error message.
     * @param integer $code A machine understandable error code (see class constants).
     * @param Exception $previous A possible previous Exception object.
     */
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}