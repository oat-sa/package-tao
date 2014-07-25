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

use oat\taoQtiItem\model\qti\exception\UnsupportedQtiElement;
use oat\taoQtiItem\model\qti\exception\ParsingException;
use \common_exception_UserReadableException;
use \DOMElement;

/**
 * Exception thrown when an unsupported QTI class is found.
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
class UnsupportedQtiElement
    extends ParsingException
    implements common_exception_UserReadableException
{   
    
    /**
     * The element which represents the unsupported QTI class.
     * 
     * @var DOMElement
     */
    private $element;
    
    /**
     * Create a new UnsupportedQtiElement object.
     * 
     * @param DOMElement $element The element which represents the unsupported QTI class.
     */
    public function __construct(DOMElement $element) {
        parent::__construct('The QTI class "' . $this->type . '" is currently not supported.', $code);
        $this->setElement($element);
    }
    
    /**
     * Get the element which represents the unsupported QTI class.
     * 
     * @return DOMElement
     */
    public function getElement() {
        return $this->element;
    }
    
    /**
     * Set the element which represents the unsupported QTI class.
     * 
     * @param DOMElement $element
     */
    protected function setElement(DOMElement $element) {
        $this->element = $element;
    }
    
    /**
     * Returns a human-readable message describing the error that occured.
     * 
     * @return string
     */
    public function getUserMessage() {
        $name = $this->getElement()->localName;
        $line = $this->getElement()->getLineNo();
        return sprintf(__('The QTI class "%1$s" located at line "%2$d" is currently not supported.'), $name, $line);
    }
}