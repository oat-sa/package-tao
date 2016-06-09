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
 *               2013 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
namespace oat\tao\model\messaging\transportStrategy;

use oat\tao\model\messaging\Message;
/**
 * Short description of class tao_helpers_transfert_Adapter
 *
 * @abstract
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 *         
 */
abstract class AbstractAdapter
{

    /**
     * Short description of method send
     *
     * @abstract
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return int
     */
    public abstract function send();

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
        
        $returnValue = $this->messages;
        
        return (array) $returnValue;
    }

    /**
     * Short description of method setMessages
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param
     *            array messages
     * @return mixed
     */
    public function setMessages($messages)
    {
        $this->messages = (array) $messages;
    }

    /**
     * Short description of method addMessage
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param
     *            Message message
     * @return mixed
     */
    public function addMessage(Message $message)
    {
        $this->messages[] = $message;
    }
}
