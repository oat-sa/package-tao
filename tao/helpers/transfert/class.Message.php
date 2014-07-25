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
 * Generis Object Oriented API - tao/helpers/transfert/class.Message.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 13.07.2010, 12:37:06 with ArgoUML PHP module 
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
// section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000224D-includes begin
// section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000224D-includes end

/* user defined constants */
// section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000224D-constants begin
// section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000224D-constants end

/**
 * Short description of class tao_helpers_transfert_Message
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_transfert
 */
class tao_helpers_transfert_Message
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

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
     * Short description of attribute to
     *
     * @access protected
     * @var string
     */
    protected $to = '';

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
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:00000000000022D1 begin
        
    	$this->status = self::STATUS_WAITING;
    	
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:00000000000022D1 end
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

        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000226A begin
        
        $returnValue = $this->from;
        
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000226A end

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
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000226C begin
        
    	$this->from = $from;
    	
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000226C end
    }

    /**
     * Short description of method getTo
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getTo()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000226F begin
        
        $returnValue = $this->to;
        
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000226F end

        return (string) $returnValue;
    }

    /**
     * Short description of method setTo
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string to
     * @return mixed
     */
    public function setTo($to)
    {
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002271 begin
        
    	$this->to = $to;
    	
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002271 end
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

        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002274 begin
        
        $returnValue = $this->title;
        
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002274 end

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
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002276 begin
        
    	$this->title = $title;
    	
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002276 end
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

        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002279 begin
        
        $returnValue = $this->body;
        
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002279 end

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
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000227B begin
        
    	$this->body = $body;
    	
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000227B end
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

        // section 127-0-1-1-1609ec43:129caf00b07:-8000:00000000000022D6 begin
        
        $returnValue = $this->status;
        
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:00000000000022D6 end

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
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:00000000000022D3 begin
        
    	$this->status = $status;
    	
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:00000000000022D3 end
    }

} /* end of class tao_helpers_transfert_Message */

?>