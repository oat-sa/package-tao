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
namespace oat\tao\model\messaging;

use oat\oatbox\user\User;
/**
 * Message to be send to an user
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 */
class Message
{

    /**
     * Short description of attribute STATUS_WAITING
     *
     * @access public
     * @var int
     */
    const STATUS_WAITING = 2;

    /**
     * Short description of attribute STATUS_SENT
     *
     * @access public
     * @var int
     */
    const STATUS_SENT = 3;

    /**
     * Short description of attribute STATUS_ERROR
     *
     * @access public
     * @var int
     */
    const STATUS_ERROR = 4;

    /**
     * Short description of attribute from
     *
     * @access protected
     * @var string
     */
    protected $from = '';

    /**
     * @var User
     */
    protected $to = null;

    /**
     * Short description of attribute title
     *
     * @access protected
     * @var string
     */
    protected $title = '';

    /**
     * Short description of attribute body
     *
     * @access protected
     * @var string
     */
    protected $body = '';

    /**
     * Short description of attribute status
     *
     * @access protected
     * @var int
     */
    protected $status = 0;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        
        
    	$this->status = self::STATUS_WAITING;
    	
        
    }

    /**
     * Short description of method getFrom
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getFrom()
    {
        $returnValue = (string) '';

        
        
        $returnValue = $this->from;
        
        

        return (string) $returnValue;
    }

    /**
     * Short description of method setFrom
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string from
     * @return mixed
     */
    public function setFrom($from)
    {
        
        
    	$this->from = $from;
    	
        
    }

    /**
     * User the message is to be send to
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return User
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Short description of method setTo
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string to
     */
    public function setTo(User $to)
    {
    	$this->to = $to;
    }

    /**
     * Short description of method getTitle
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getTitle()
    {
        $returnValue = (string) '';

        
        
        $returnValue = $this->title;
        
        

        return (string) $returnValue;
    }

    /**
     * Short description of method setTitle
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string title
     */
    public function setTitle($title)
    {
        
        
    	$this->title = $title;
    	
        
    }

    /**
     * Short description of method getBody
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getBody()
    {
        $returnValue = (string) '';

        
        
        $returnValue = $this->body;
        
        

        return (string) $returnValue;
    }

    /**
     * Short description of method setBody
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string body
     * @return mixed
     */
    public function setBody($body)
    {
        
        
    	$this->body = $body;
    	
        
    }

    /**
     * Short description of method getStatus
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  int status
     * @return mixed
     */
    public function setStatus($status)
    {
        
        
    	$this->status = $status;
    	
        
    }

}

?>