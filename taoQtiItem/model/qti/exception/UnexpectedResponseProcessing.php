<?php
/*  
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

namespace oat\taoQtiItem\model\qti\exception;

use oat\taoQtiItem\model\qti\exception\UnexpectedResponseProcessing;
use oat\taoQtiItem\model\qti\exception\ParsingException;
use \common_exception_UserReadableException;
use \common_Logger;

/**
 * Exception to be thrown when an unknown/unexpected Response Processing
 * template is requested for use.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
class UnexpectedResponseProcessing
    extends ParsingException implements common_exception_UserReadableException {

    /**
     * The template URI to be dereferenced as a Response Processing template.
     * 
     * @var string
     */
    private $requestedUri;
    
    /**
     * Create a new UnexpectedResponseProcessing object.
     * 
     * @param string $message A message.
     * @param integer $code An optional code enabling the client code to react. 
     * @param string $requestedUri An invalid Response processing template URI.
     */
    public function __construct($message, $code = 0, $requestedUri = '') {
        parent::__construct($message, $code);
        $this->setRequestedUri($requestedUri);
    }
    
    /**
     * Get the serverity of the error.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return int
     */
    public function getSeverity() {
        return (int) common_Logger::TRACE_LEVEL;
    }

    /**
     * Set the URI that was requested for dereferencing.
     * 
     * @param string $requestedUri
     */
    protected function setRequestedUri($requestedUri) {
        $this->requestedUri = $requestedUri;
    }
    
    /**
     * Get the URI that was requested for dereferencing.
     * 
     * @return string
     */
    public function getRequestedUri() {
        return $this->requestedUri;
    }
    
    /**
     * Returns a human-readable message describing the error that occured.
     *
     * @return string
     */
    public function getUserMessage() {
        $requestedUri = $this->getRequestedUri();
        if (empty($requestedUri) === true) {
            return __('An unexpected error occured while dealing with Response Processing.');
        }
        else {
            return __('The Response Processing Template "%s" is not supported.', $this->getRequestedUri());
        }
        
    }
}