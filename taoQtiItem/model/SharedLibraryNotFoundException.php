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
 */
namespace oat\taoQtiItem\model;

use \common_Exception;
use Exception;

/**
 * Exception to be thrown when a PCI/PIC shared library cannot be found.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @see http://www.imsglobal.org/assessment/PCI_Change_Request_v1pd.html The Pacific Metrics PCI Change Proposal introducing the notion of Shared Libraries.
 */
class SharedLibraryNotFoundException extends common_Exception
{
    private $id;
    
    /**
     * Set the name of the library that could not be found.
     * 
     * @param string $id A library name.
     */
    protected function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     * Get the name of the library that could not be found.
     * 
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Create a new SharedLibraryNotFound exception object.
     * 
     * @param string $message The message of the exception.
     * @param string $id The name of the shared library that could not be found.
     * @param Exception $previous An optional previous exception that led to this one.
     */
    public function __construct($message, $id, Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->setId($id);
    }
}