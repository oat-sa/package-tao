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

/**
 * Short description of class common_log_Dispatcher
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis
 
 */
class common_log_Dispatcher
        implements common_log_Appender
{
    /**
     * Identifer of the configuration that stores the log configuration
     * 
     * @var string
     */
    const CONFIG_ID = 'log';
    
    // --- ATTRIBUTES ---

    /**
     * Short description of attribute appenders
     *
     * @access private
     * @var array
     */
    private $appenders = array();

    /**
     * Short description of attribute minLevel
     *
     * @access private
     * @var int
     */
    private $minLevel = null;

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var Dispatcher
     */
    private static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method log
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @return mixed
     */
    public function log( common_log_Item $item)
    {
        foreach ($this->appenders as $appender) {
        	$appender->log($item);
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

        
        $returnValue = $this->minLevel;
        

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

        
    	$this->appenders = array();
    	$this->minLevel = null;
    	foreach ($configuration as $appenderConfig) {
    		if (isset($appenderConfig['class'])) {
    			
    			$classname = $appenderConfig['class'];
    			if (!class_exists($classname)){
    				$classname = 'common_log_'.$classname;
                }
    			if (class_exists($classname) && is_subclass_of($classname, 'common_log_Appender')) {
    				$appender = new $classname();
    				if (!is_null($appender) && $appender->init($appenderConfig)) {
    					$this->addAppender($appender);
    				}
    			}
    		}
    	}
    	$returnValue = (count($this->appenders) > 0);
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return common_log_Dispatcher
     */
    public static function singleton()
    {
        $returnValue = null;

        
        if (is_null(self::$instance)) {
        	self::$instance = new common_log_Dispatcher();
        }
        $returnValue = self::$instance;
        

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    private function __construct()
    {
        // workaround to prevent errors during install
        if (defined('EXTENSION_PATH')) {
            $config = common_ext_ExtensionsManager::singleton()->getExtensionById('generis')->getConfig(self::CONFIG_ID);
            if (is_array($config)) {
                $this->init($config);
            }
        }
    }

    /**
     * Short description of method addAppender
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Appender appender
     * @return mixed
     */
    public function addAppender( common_log_Appender $appender)
    {
        
        $this->appenders[] = $appender;
        if (is_null($this->minLevel) || $this->minLevel > $appender->getLogThreshold()) {
        	$this->minLevel = $appender->getLogThreshold();
        }
        
    }

}