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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - tao/helpers/transfert/class.Adapter.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 13.07.2010, 11:08:04 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_transfert
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002245-includes begin
// section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002245-includes end

/* user defined constants */
// section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002245-constants begin
// section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002245-constants end

/**
 * Short description of class tao_helpers_transfert_Adapter
 *
 * @abstract
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_transfert
 */
abstract class tao_helpers_transfert_Adapter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute messages
     *
     * @access protected
     * @var array
     */
    protected $messages = array();

    // --- OPERATIONS ---

    /**
     * Short description of method getMessages
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getMessages()
    {
        $returnValue = array();

        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000227E begin
        
        $returnValue = $this->messages;
        
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000227E end

        return (array) $returnValue;
    }

    /**
     * Short description of method setMessages
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array messages
     * @return mixed
     */
    public function setMessages($messages)
    {
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002280 begin
        
    	$this->messages = (array)$messages;
    	
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002280 end
    }

    /**
     * Short description of method addMessage
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Message message
     * @return mixed
     */
    public function addMessage( tao_helpers_transfert_Message $message)
    {
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002283 begin
        
    	$this->messages[] = $message;
    	
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002283 end
    }

    /**
     * Short description of method send
     *
     * @abstract
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return int
     */
    public abstract function send();

} /* end of abstract class tao_helpers_transfert_Adapter */

?>