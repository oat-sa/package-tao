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
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */

namespace qtism\runtime\pci\json;

use \Exception;

/**
 * Exception to be thrown when a Marshalling error occurs while
 * dealing with JSON Data.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class MarshallingException extends Exception {
    
    const UNKNOWN = 0;
    
    const NOT_SUPPORTED = 1;
    
    /**
     * Create a new MarshallingException object.
     * 
     * @param string $message A human-readable message describing the error.
     * @param integer $code A machine-understandable (see class constants) error code.
     * @param Exception $previous An eventual previous Exception.
     */
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}