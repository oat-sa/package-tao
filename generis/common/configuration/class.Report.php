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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class common_configuration_Report
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
class common_configuration_Report
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute status
     *
     * @access private
     * @var int
     */
    private $status = 0;

    /**
     * Short description of attribute VALID
     *
     * @access public
     * @var int
     */
    const VALID = 0;

    /**
     * Short description of attribute INVALID
     *
     * @access public
     * @var int
     */
    const INVALID = 1;

    /**
     * Short description of attribute UNKNOWN
     *
     * @access public
     * @var int
     */
    const UNKNOWN = 2;

    /**
     * Short description of attribute message
     *
     * @access private
     * @var string
     */
    private $message = '';

    /**
     * Short description of attribute component
     *
     * @access private
     * @var Component
     */
    private $component = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int status
     * @param  string message
     * @param  Component component
     * @return mixed
     */
    public function __construct($status, $message,  common_configuration_Component $component = null)
    {
        
        $this->setStatus($status);
        $this->setMessage($message);
        
        if (!empty($component)){
            $this->setComponent($component);    
        }
        
    }

    /**
     * Short description of method getStatus
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return int
     */
    public function getStatus()
    {
        $returnValue = (int) 0;

        
        $returnValue = $this->status;
        

        return (int) $returnValue;
    }

    /**
     * Short description of method setStatus
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int status
     * @return void
     */
    public function setStatus($status)
    {
        
        $this->status = $status;
        
    }

    /**
     * Short description of method getStatusAsString
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getStatusAsString()
    {
        $returnValue = (string) '';

        
        switch ($this->getStatus()){
            case self::INVALID:
                $returnValue = 'invalid';
            break;
            
            case self::UNKNOWN:
                $returnValue = 'unknown';
            break;
            
            case self::VALID:
                $returnValue = 'valid';
            break;
        }
        

        return (string) $returnValue;
    }

    /**
     * Short description of method getMessage
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getMessage()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->message;
        

        return (string) $returnValue;
    }

    /**
     * Short description of method setMessage
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string message
     * @return void
     */
    public function setMessage($message)
    {
        
        $this->message = $message;
        
    }

    /**
     * Short description of method getComponent
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_configuration_Component
     */
    public function getComponent()
    {
        $returnValue = null;

        
        $returnValue = $this->component;
        

        return $returnValue;
    }

    /**
     * Short description of method setComponent
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Component component
     * @return void
     */
    protected function setComponent( common_configuration_Component $component)
    {
        
        $this->component = $component;
        
    }

} 