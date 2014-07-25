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
 * Generis Object Oriented API - common/log/class.BaseAppender.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 19.06.2012, 10:30:40 with ArgoUML PHP module 
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
 * include common_log_Appender
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('common/log/interface.Appender.php');

/* user defined includes */
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:000000000000185C-includes begin
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:000000000000185C-includes end

/* user defined constants */
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:000000000000185C-constants begin
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:000000000000185C-constants end

/**
 * Short description of class common_log_BaseAppender
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */
abstract class common_log_BaseAppender
        implements common_log_Appender
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The message mask, where the least significant bit corresponds to the
     * important severity (trace)
     *
     * @access private
     * @var Integer
     */
    private $mask = null;

    /**
     * an array of tags of which one must be present
     * for the logItem to be logged
     *
     * @access public
     * @var array
     */
    public $tags = array();

    // --- OPERATIONS ---

    /**
     * decides whenever the Item should be logged by doLog
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @return mixed
     * @see doLog
     */
    public function log( common_log_Item $item)
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000435D begin
    	if ((1<<$item->getSeverity() & $this->mask) > 0
    		&& (empty($this->tags) || count(array_intersect($item->getTags(), $this->tags))) > 0) {
        	$this->doLog($item);
    	}
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000435D end
    }

    /**
     * Short description of method getLogThreshold
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return int
     */
    public function getLogThreshold()
    {
        $returnValue = (int) 0;

        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017C6 begin
        $threshold = 0;
        while (($this->mask & 1<<$threshold) == 0){
        	$threshold++;
        }
        $returnValue = $threshold;
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017C6 end

        return (int) $returnValue;
    }

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

        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:000000000000183B begin
    	if (isset($configuration['mask']) && is_numeric($configuration['mask'])) {
    		// take over the mask
    		$this->mask = intval($configuration['mask']);
    	} elseif (isset($configuration['threshold']) && is_numeric($configuration['threshold'])) {
    		// map the threshold to a mask
    		$this->mask = max(0,(1<<common_Logger::FATAL_LEVEL +1) - (1<<$configuration['threshold']));
    	} else {
    		// log everything
    		$this->mask = (1<<common_Logger::FATAL_LEVEL + 1) - 1;
    	}
    	
    	if (isset($configuration['tags'])) {
    		$this->tags = is_array($configuration['tags']) ? $configuration['tags'] : array($configuration['tags']);
    	}
    	$returnValue = true;
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:000000000000183B end

        return (bool) $returnValue;
    }

    /**
     * Logs the item
     *
     * @abstract
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @return mixed
     */
    public abstract function doLog( common_log_Item $item);

} /* end of abstract class common_log_BaseAppender */

?>