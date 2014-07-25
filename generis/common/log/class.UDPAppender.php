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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/log/class.UDPAppender.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 09.12.2011, 11:42:37 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_log_BaseAppender
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('common/log/class.BaseAppender.php');

/* user defined includes */
// section 127-0-1-1--508f6d44:1341e7d80d4:-8000:000000000000184C-includes begin
// section 127-0-1-1--508f6d44:1341e7d80d4:-8000:000000000000184C-includes end

/* user defined constants */
// section 127-0-1-1--508f6d44:1341e7d80d4:-8000:000000000000184C-constants begin
// section 127-0-1-1--508f6d44:1341e7d80d4:-8000:000000000000184C-constants end

/**
 * Short description of class common_log_UDPAppender
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */
class common_log_UDPAppender
    extends common_log_BaseAppender
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute host
     *
     * @access public
     * @var string
     */
    public $host = '127.0.0.1';

    /**
     * Short description of attribute port
     *
     * @access public
     * @var int
     */
    public $port = 5775;

    /**
     * Short description of attribute resource
     *
     * @access public
     * @var resource
     */
    public $resource = null;

    // --- OPERATIONS ---

    /**
     * Short description of method init
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array configuration
     * @return boolean
     */
    public function init($configuration)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--508f6d44:1341e7d80d4:-8000:0000000000001859 begin
    	if (isset($configuration['host'])) {
    		$this->host = $configuration['host'];
    	}
    	
    	if (isset($configuration['port'])) {
    		$this->port = $configuration['port'];
    	}
    	
    	$returnValue = parent::init($configuration);
        // section 127-0-1-1--508f6d44:1341e7d80d4:-8000:0000000000001859 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method doLog
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @return mixed
     */
    public function doLog( common_log_Item $item)
    {
        // section 127-0-1-1--508f6d44:1341e7d80d4:-8000:000000000000184D begin
        if (is_null($this->resource)) {
        	$this->resource  = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        	socket_set_nonblock($this->resource);
        }
        if ($this->resource !== false) {
        	$message = json_encode(array(
        		's' => $item->getSeverity(),
        		'd' => $item->getDescription(),
        		't' => $item->getTags(),
        		'f' => $item->getCallerFile(),
        		'l' => $item->getCallerLine(),
        		'b' => $item->getBacktrace()
        	));
        	@socket_sendto($this->resource, $message, strlen($message), 0, $this->host, $this->port);
        	//ignore errors, socket might already be closed because php is shutting down
        }
        // section 127-0-1-1--508f6d44:1341e7d80d4:-8000:000000000000184D end
    }

    /**
     * Short description of method __destruct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function __destruct()
    {
        // section 127-0-1-1--508f6d44:1341e7d80d4:-8000:000000000000184F begin
        // don't close since we might still need it
        /*
        if (!is_null($this->resource) && $this->resource !== false) {
    		socket_close($this->resource);
        }
        parent::__destruct();
        */
        // section 127-0-1-1--508f6d44:1341e7d80d4:-8000:000000000000184F end
    }

} /* end of class common_log_UDPAppender */

?>