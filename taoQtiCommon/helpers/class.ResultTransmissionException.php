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
 * A ResultTransmissionException must be raised when a result variable
 * cannot be transmitted correctly to a target Result Server.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiCommon_helpers_ResultTransmissionException extends common_Exception {
    
    /**
     * Error code to be used when the cause of the error
     * is unknown.
     * 
     * @var integer
     */
    const UNKNOWN = 0;
    
    /**
     * Error code to be used when the cause of the error
     * is a transmission malfunction when transmitting an
     * outcome variable to a target result server.
     * 
     * @var integer
     */
    const OUTCOME = 1;
    
    /**
     * Error code to be used when the cause of the error
     * is a transmission malfunction when transmitting
     * a response variable to a target result server.
     * 
     * @var integer
     */
    const RESPONSE = 2;
    
    /**
     * Create a new ResultTransmissionException object.
     * 
     * @param string $message A human-readable message describing the error.
     * @param integer $code A computer-understandable message describing the error.
     */
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code = 0);
    }
} 