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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */



/**
 * Any exception related to extensions should inherit this class.
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package generis
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 
 */
class common_ext_ExtensionException extends common_Exception implements common_log_SeverityLevel
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The extension ID related to the exception.
     *
     * @access private
     * @var Integer
     */
    private $extensionId = null;

    // --- OPERATIONS ---

    /**
     * Sets the extension ID related to the exception.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string extensionId An extension ID.
     * @return mixed
     */
    public function setExtensionId($extensionId)
    {
        
        $this->extensionId = $extensionId;
        
    }

    /**
     * Get the extension ID related to the exception
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getExtensionId()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->extensionId;
        

        return (string) $returnValue;
    }

    /**
     * Creates a new instance of ExtensionException.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string message
     * @param  string extensionId
     * @return mixed
     */
    public function __construct($message, $extensionId = 'unknown')
    {
        
        parent::__construct($message);
        $this->setExtensionId($extensionId);
        
    }
    
    /**
     * Get the severity of the error.
     *
     * @access public
     * @return int
     */
    public function getSeverity()
    {
        return common_Logger::ERROR_LEVEL;
    }

}