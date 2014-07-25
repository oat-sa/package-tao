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

use \InvalidArgumentException;

abstract class PhpMarshaller {
    
    /**
     * Get the marshalling context.
     * 
     * @var PhpMarshallingContext
     */
    private $context;
    
    /**
     * The value to be marshalled.
     * 
     * @var mixed
     */
    private $toMarshall;
    
    /**
     * Create a new PhpMarshaller object.
     * 
     * @param PhpMarshallingContext $context A PhpMarshallingContext object.
     * @param mixed The value to be marshalled.
     * @throws InvalidArgumentException If $toMarshall cannot be handled by this PhpMarshaller implementation.
     */
    public function __construct(PhpMarshallingContext $context, $toMarshall) {
        $this->setContext($context);
        $this->setToMarshall($toMarshall);
    }
    
    /**
     * Set the value that has to be marshalled.
     * 
     * @param mixed $toMarshall The value to be marshalled.
     * @throws InvalidArgumentException If the value $toMarshall cannot be managed by this implementation.
     */
    public function setToMarshall($toMarshall) {
        if ($this->isMarshallable($toMarshall) === false) {
            $msg = "The value to marshall cannot be managed by this implementation.";
            throw new InvalidArgumentException($msg);
        }
        
        $this->toMarshall = $toMarshall;
    }
    
    /**
     * Get the value to be marshalled by the current PhpMarshaller implementation.
     * 
     * @return mixed A value to be marshalled.
     */
    protected function getToMarshall() {
        return $this->toMarshall;
    }
    
    /**
     * Set the marshalling context.
     * 
     * @param PhpMarshallingContext $context A PhpMarshallingContext object.
     */
    protected function setContext(PhpMarshallingContext $context) {
        $this->context = $context;
    }
    
    /**
     * Get the marshalling context.
     * 
     * @return PhpMarshallingContext A PhpMarshallingContext object.
     */
    protected function getContext() {
        return $this->context;
    }
    
    /**
     * Marshall the value that has to be marshalled. It is the responsibility
     * of the marshall method implementation to push all marshalled values on the
     * variable names stack.
     * 
     * @throws PhpMarshallingException If an error occurs during the marshalling process.
     */
    public abstract function marshall();
    
    /**
     * Implementations of this class must implement this method which states
     * whether or not the value $toMarshall can be handled or not.
     * 
     * @param mixed $toMarshall The value the current PhpMarshaller implementation has to deal with.
     * @return boolean Whether or not the value $toMarshall can be marshalled or not by this implementation. 
     */
    protected abstract function isMarshallable($toMarshall);
}