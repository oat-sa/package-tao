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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA 
 * 
 */

namespace oat\tao\helpers\data;

/**
 * Validation during preprocessing of a value that should be
 * assigned to a given property
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 */
class ValidationException extends \Exception implements \common_exception_UserReadableException
{
    /**
     * @param \core_kernel_classes_Property $property
     */
    private $property;

    /**
     * @var mixed
     */
    private $value;
    
    /**
     * Message that is save to display to user
     * 
     * @var string
     */
    private $userMessage;
    
    /**
     * 
     * @param \core_kernel_classes_Property $property
     * @param mixed $value
     * @param string $userMessage
     */
    public function __construct(\core_kernel_classes_Property $property, $value, $userMessage) {
        parent::__construct($userMessage.' '.$property->getUri().' '.$value);
        $this->property = $property;
        $this->value = $value;
        $this->userMessage = $userMessage;
    }
    
    /**
     * Returns the property the value should be assigned to
     * 
     * @return core_kernel_classes_Property
     */
    public function getProperty() {
        return $this->property;
    }
    
    /**
     * Returns the value that failed validation
     * 
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_exception_UserReadableException::getUserMessage()
     */
    public function getUserMessage() {
        return $this->userMessage;
    }
}
