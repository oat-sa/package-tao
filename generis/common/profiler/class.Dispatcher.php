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
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package generis
 
 */
class common_profiler_Dispatcher
        extends common_profiler_Appender
{

    /**
     * Short description of attribute appenders
     *
     * @access private
     * @var array
     */
    private $appenders = array();

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var Dispatcher
     */
    private static $instance = null;

    /**
     * Init profiler appenders according to config
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  array configuration
     * @return boolean
     */
    public function init($configuration)
    {
        $returnValue = (bool) false;

    	$this->appenders = array();
    	foreach ($configuration as $appenderConfig){
    		if(isset($appenderConfig['class'])){
    			
    			$classname = $appenderConfig['class'];
    			if (!class_exists($classname)){
    				$classname = 'common_profiler_'.$classname;
                }
    			if (class_exists($classname) && is_subclass_of($classname, 'common_profiler_Appender')) {
    				$appender = new $classname();
    				if (!is_null($appender) && $appender->init($appenderConfig)) {
    					
						//add appender
    					$this->addAppender($appender);
						
						foreach ($appenderConfig as $logger => $options) {
							//set global config:
							$appenderName = strtolower($logger);
							$enable = isset($options['active']) ? (boolean) $options['active'] : false;
							if($enable){
								switch ($appenderName) {
									case 'context': {
										if(!$this->isEnabled($appenderName)){
											$this->enable($appenderName);
										}
										break;
									}
									case 'timer': {
										if(!$this->isEnabled($appenderName)){
											$this->enable($appenderName);
										}

										$flags = (array) $this->getConfigOption($appenderName, 'flags');
										$newFlags = (isset($options['flags']) && is_array($options['flags'])) ? (array) $options['flags'] : array();
										if(!empty($newFlags)){
											$flags = array_merge($flags, $newFlags);
											$this->setConfigOption($appenderName, 'flags', $flags);
										}
										break;
									}
									case 'memorypeak': {
										if(!$this->isEnabled($appenderName)){
											$this->enable($appenderName);
										}
										break;
									}
									case 'countqueries': {
										if(!$this->isEnabled($appenderName)){
											$this->enable($appenderName);
										}
										break;
									}
									case 'slowestqueries': {
										if(!$this->isEnabled($appenderName)){
											$this->enable($appenderName);
										}
										
										$slowest = $this->getConfigOption($appenderName, 'count');
										$newSlowest = isset($options['count']) ? (int) $options['count'] : 0;
										if(is_null($newSlowest) || $newSlowest > $slowest){
											$this->setConfigOption($appenderName, 'count', $newSlowest);
										}
										break;
									}
									case 'slowqueries': {
										if(!$this->isEnabled($appenderName)){
											$this->enable($appenderName);
										}

										$threshold = $this->getConfigOption($appenderName, 'threshold');
										$newThreshold = isset($options['threshold']) ? (int) $options['threshold'] : 1000;
										if(is_null($threshold) || $newThreshold < $threshold){
											$this->setConfigOption($appenderName, 'threshold', $newThreshold);
										}
										break;
									}
									case 'queries': {
										if(!$this->isEnabled($appenderName)){
											$this->enable($appenderName);
										}

										$count = $this->getConfigOption($appenderName, 'count');
										$newCount = isset($options['slowest']) ? (int) $options['count'] : 10;
										if(is_null($count) || $newCount > $count){
											$this->setConfigOption($appenderName, 'count', $newCount);
										}
										break;
									}
								}
							}	
						}//end of foreach on appender config options
    				}
    			}
    		}
    	}//end of foreach loop
    	$returnValue = (count($this->appenders) > 0);

//		common_Logger::d($this, 'PROFILER');exit;
		
        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return common_log_Dispatcher
     */
    public static function singleton()
    {
        $returnValue = null;

        if (is_null(self::$instance)) {
        	self::$instance = new common_profiler_Dispatcher();
        }
        $returnValue = self::$instance;

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Sam, <sam@taotesting.com>
     * @return mixed
     */
    private function __construct()
    {
        $config = common_ext_ExtensionsManager::singleton()->getExtensionById('generis')->getConfig('profiler');
        $this->init($config === false ? array() : $config);
    }

    /**
     * Short description of method addAppender
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  Appender appender
     * @return mixed
     */
    public function addAppender(common_profiler_Appender $appender){
        $this->appenders[] = $appender;
    }

	public function hasAppender(){
		return (bool) count($this->appenders);
	}
	
	public function logContext(common_profiler_Context $context){
		foreach ($this->appenders as $appender){
			if($appender->isEnabled('context')){
				$appender->logContext($context);
			}
		}
	}
	
	public function logTimer($flag, $duration, $total){
		foreach($this->appenders as $appender){
			$flags = $appender->getConfigOption('timer', 'flags');
			if($appender->isEnabled('timer') && (is_array($flags)||in_array($flag, $flags))){
				$appender->logTimer($flag, $duration, $total);
			}
		}
	}
	
	public function logMemoryPeak($memPeak, $memMax){
		foreach($this->appenders as $appender){
			if($appender->isEnabled('memoryPeak')){
				$appender->logMemoryPeak($memPeak, $memMax);
			}
		}
	}
	
	public function logQueriesCount($count){
		foreach($this->appenders as $appender){
			if($appender->isEnabled('countQueries')){
				$appender->logQueriesCount($count);
			}
		}
	}
	
	public function logQueriesSlow($slowQueries){
		foreach($this->appenders as $appender){
			if($appender->isEnabled('slowQueries')){
				$appender->logQueriesSlow($slowQueries);
			}
		}
	}
	
	public function logQueriesSlowest($slowestQueries){
		foreach($this->appenders as $appender){
			if($appender->isEnabled('slowestQueries')){
				$appender->logQueriesSlowest($slowestQueries);
			}
		}
	}
	
	public function logQueriesStat($queries){
		foreach ($this->appenders as $appender) {
			if ($appender->isEnabled('queries')) {
				$appender->logQueriesStat($queries);
			}
		}
	}
	
	public function flush(){
		foreach($this->appenders as $appender){
			$appender->flush();
		}
	}

}