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
 * Short description of class common_log_UDPAppender
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis

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


    	if (isset($configuration['host'])) {
    		$this->host = $configuration['host'];
    	}

    	if (isset($configuration['port'])) {
    		$this->port = $configuration['port'];
    	}

    	$returnValue = parent::init($configuration);


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

        if (is_null($this->resource)) {
        	$this->resource  = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        	socket_set_nonblock($this->resource);
        }
        if ($this->resource !== false) {
        	$message = json_encode(array(
        		's' => $item->getSeverity(),
        		'd' => $item->getDescription(),
        		'p' => $this->prefix,
        		't' => $item->getTags(),
        		'f' => $item->getCallerFile(),
        		'l' => $item->getCallerLine(),
        		'b' => $item->getBacktrace()
        	));
        	@socket_sendto($this->resource, $message, strlen($message), 0, $this->host, $this->port);
        	//ignore errors, socket might already be closed because php is shutting down
        }

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

        // don't close since we might still need it
        /*
        if (!is_null($this->resource) && $this->resource !== false) {
    		socket_close($this->resource);
        }
        parent::__destruct();
        */

    }

}