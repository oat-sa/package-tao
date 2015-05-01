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

namespace qtism\data\storage\php;

use qtism\data\storage\StorageException;
use \Exception;

/**
 * The Exception class to use when an error occurs while loading/saving
 * QTI data as PHP source code.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PhpStorageException extends StorageException {
    
    /**
     * Create a new PhpStorageException object.
     * 
     * @param string $message A human-readable message.
     * @param integer $code An error code.
     * @param Exception $previous A previously thrown and catched exception.
     */
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
}