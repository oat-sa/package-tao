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
 * Short description of class common_log_BaseAppender
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis

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

    /**
     * the prefix that will be added to each log message.
     *
     * @access protected
     * @var string
     */
    protected $prefix = '';

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

    	if ((1<<$item->getSeverity() & $this->mask) > 0
    		&& (empty($this->tags) || count(array_intersect($item->getTags(), $this->tags))) > 0) {
        	$this->doLog($item);
    	}

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


        $threshold = 0;
        while (($this->mask & 1<<$threshold) == 0){
        	$threshold++;
        }
        $returnValue = $threshold;


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

    	if (isset($configuration['prefix'])) {
    	    $this->prefix = $configuration['prefix'];
    	}
    	$returnValue = true;


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

}
