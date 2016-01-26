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

namespace qtism\common\beans;

use \Exception;

/**
 * The exception class representing exceptions thrown by the classes
 * of the qtism\common\beans package.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class BeanException extends Exception {
    
    /**
     * Error code to use when the error is unknown.
     * 
     * @var integer
     */
    const UNKNOWN = 0;
    
    /**
     * Error code to use when a class method does not exist.
     * 
     * @var integer
     */
    const NO_METHOD = 1;
    
    /**
     * Error code to use when a class property does not exist.
     * 
     * @var integer
     */
    const NO_PROPERTY = 2;
    
    /**
     * Error code to use when a method parameter does not exist.
     * 
     * @var integer
     */
    const NO_PARAMETER = 3;
    
    /**
     * Error code to use when an expected bean annotation cannot be found.
     * 
     * @var integer
     */
    const NO_ANNOTATION = 4;
    
    /**
     * Error code to use when the bean has no constructor.
     * 
     * @var integer
     */
    const NO_CONSTRUCTOR = 5;
    
    /**
     * Error code to use when a bean is not a strict bean.
     * 
     * @var integer
     */
    const NOT_STRICT = 6;
    
    /**
     * Create a new BeanException object.
     * 
     * @param string $message A human-readable message.
     * @param integer $code An error code from the BeanException class constants.
     * @param Exception $previous An optional previous exception.
     */
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}