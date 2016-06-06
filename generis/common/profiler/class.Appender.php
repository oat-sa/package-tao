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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * 
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package generis
 
 */
abstract class common_profiler_Appender
{
	
	protected $options = array();
	
	/**
     * Short description of method init
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  array configuration
     * @return boolean
     */
    public function init($configuration){
		
		$returnValue = (bool) false;
		 
		foreach ($configuration as $logger => $options) {
			
			$loggerOptions = array();
			
			switch ($logger) {
				case 'context': {
					$loggerOptions = array(
						'active' =>	isset($options['active']) ? (boolean) $options['active'] : false
					);
					break;
				}
				case 'timer': {
					$loggerOptions = array(
						'active' =>	isset($options['active']) ? (boolean) $options['active'] : false,
						'flags' => (isset($options['flags']) && is_array($options['flags'])) ? (array) $options['flags'] : array()
					);
					break;
				}
				case 'memoryPeak': {
					$loggerOptions = array(
						'active' =>	isset($options['active']) ? (boolean) $options['active'] : false
					);
					break;
				}
				case 'countQueries': {
					$loggerOptions = array(
						'active' =>	isset($options['active']) ? (boolean) $options['active'] : false
					);
					break;
				}
				case 'slowQueries': {
					$loggerOptions = array(
						'active' =>	isset($options['active']) ? (boolean) $options['active'] : false,
						'threshold' => isset($options['threshold']) ? (int) $options['threshold'] : 1000
					);
					break;
				}
				case 'slowestQueries': {
					$loggerOptions = array(
						'active' =>	isset($options['active']) ? (boolean) $options['active'] : false,
						'count' => isset($options['slowest']) ? (int) $options['slowest'] : 0
					);
					break;
				}
				case 'queries': {
					$loggerOptions = array(
						'active' =>	isset($options['active']) ? (boolean) $options['active'] : false,
						'count' => isset($options['count']) ? (int) $options['count'] : 10
					);
					break;
				}
			}
			
			if(!empty($loggerOptions)){
				$this->options[strtolower($logger)] = $loggerOptions;
			}
			
		}
		
		$returnValue = true;

        return (bool) $returnValue;
	}
	
	/**
     * Log current context of execution
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  common_profiler_Context count
     * @return boolean
     */
	public abstract function logContext(common_profiler_Context $context);
	
	/**
     * Log a specific event identified by a unique flag
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  string flag
     * @param  int duration
     * @param  int total
     * @return boolean
     */
	public abstract function logTimer($flag, $duration, $total);
	
	/**
     * Log current context of execution
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  int memPeak
     * @param  int memMax
     * @return boolean
     */
	public abstract function logMemoryPeak($memPeak, $memMax);
	
	/**
     * Log the number of queries
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  integer count
     * @return boolean
     */
	public abstract function logQueriesCount($count);
	
	/**
     * Log slow queries and their parameters
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  array slowQueries
     * @return boolean
     */
	public abstract function logQueriesSlow($slowQueries);
	
	/**
     * Log the slowest queries and their parameters
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  array slowQueries
     * @return boolean
     */
	public abstract function logQueriesSlowest($slowestQueries);
	
	/**
     * Log stats on all queries
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  array slowQueries
     * @return boolean
     */
	public abstract function logQueriesStat($queries);
	
	/**
     * Finalize profiler data logging, the data bulk ready for processing or storage
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  array slowQueries
     * @return boolean
     */
	public abstract function flush();
	
	public function isEnabled($logger){
		return (bool) $this->getConfigOption($logger, 'active');
	}
	
	public function enable($logger){
		$this->setConfigOption($logger, 'active', true);
	}
	
	public function getConfigOption($logger, $option){
		
		$returnValue = null;
		
		$logger = strtolower($logger);
		if(isset($this->options[$logger]) && isset($this->options[$logger][$option])){
			$returnValue = $this->options[$logger][$option];
		}
		
		return $returnValue;
	}
	
	public function setConfigOption($logger, $option, $value){
		
		$logger = strtolower($logger);
		if(!isset($this->options[$logger])){
			$this->options[$logger] = array();
		}
		$this->options[$logger][$option] = $value;
		
	}
} /* end of interface common_profiler_Appender */
	