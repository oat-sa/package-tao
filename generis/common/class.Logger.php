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
 *               2013 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Abstraction for the System Logger
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis
 */
class common_Logger
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * whenever or not the Logger is enabled
     *
     * @access private
     * @var boolean
     */
    private $enabled = true;

    /**
     * a history of past states, to allow a restoration of the previous state
     *
     * @access private
     * @var array
     */
    private $stateStack = array();

    /**
     * instance of the class Logger, to implement the singleton pattern
     *
     * @access private
     * @var Logger
     */
    private static $instance = null;

    /**
     * The dispatcher of the Logger
     *
     * @access private
     * @var Appender
     */
    private $dispatcher = null;

    /**
     * the lowest level of events representing the finest-grained processes
     *
     * @access public
     * @var int
     */
    const TRACE_LEVEL = 0;

    /**
     * the level of events representing fine grained informations for debugging
     *
     * @access public
     * @var int
     */
    const DEBUG_LEVEL = 1;

    /**
     * the level of information events that represent high level system events
     *
     * @access public
     * @var int
     */
    const INFO_LEVEL = 2;

    /**
     * the level of warning events that represent potential problems
     *
     * @access public
     * @var int
     */
    const WARNING_LEVEL = 3;

    /**
     * the level of error events that allow the system to continue
     *
     * @access public
     * @var int
     */
    const ERROR_LEVEL = 4;

    /**
     * the level of very severe error events that prevent the system to continue
     *
     * @access public
     * @var int
     */
    const FATAL_LEVEL = 5;

    /**
     * Warnings that are acceptable in our projects
     * invoked by the way generis/tao use abstract functions
     *
     * @access private
     * @var array
     */
    private $ACCEPTABLE_WARNINGS = array();

    // --- OPERATIONS ---

    /**
     * returns the existing Logger instance or instantiates a new one
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return common_Logger
     */
    public static function singleton()
    {
        $returnValue = null;

        
		if (is_null(self::$instance)){
			self::$instance = new self();
        }
		$returnValue = self::$instance;
        

        return $returnValue;
    }

    /**
     * Private constructor for singleton pattern
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    private function __construct()
    {
    }
    
    /**
     * Returns the dispatcher 
     * 
     * @return Appender
     */
    private function getDispatcher() {
        if (is_null($this->dispatcher)) {
            $this->disable();
            $this->dispatcher = common_log_Dispatcher::singleton();
            $this->restore();
        }
        return $this->dispatcher;
    }

    /**
     * register the logger as errorhandler
     * and shutdown function
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function register()
    {
		// initialize the logger here to prevent fatal errors that occure if:
		// while autoloading any class, an error gets thrown
		// the logger initializes to handle this error,  and failes to autoload his files
        set_error_handler(array($this, 'handlePHPErrors'));
		register_shutdown_function(array($this, 'handlePHPShutdown'));
        
    }

    /**
     * Short description of method log
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int $level
     * @param  string $message
     * @param  array $tags
     * @param  string $errorFile
     * @param  int $errorLine
     * @return mixed
     */
    public function log($level, $message, $tags, $errorFile = '', $errorLine = 0)
    {
        
		if ($this->enabled && $this->getDispatcher()->getLogThreshold() <= $level) {
			$this->disable();
			$stack = defined('DEBUG_BACKTRACE_IGNORE_ARGS')
                ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
                : debug_backtrace(false);
			array_shift($stack);
			// retrieving the user can be a complex procedure, leading to missing log informations
			$user = null;
			if ($errorFile == '') {
				$keys = array_keys($stack);
				$current = $stack[$keys[0]];
				if (isset($current['file']) && isset($current['line'])) {
					$errorFile = $current['file'];
					$errorLine = $current['line'];
				}
			}
			if(PHP_SAPI != 'cli'){
				$requestURI = $_SERVER['REQUEST_URI'];
			}else{
				$requestURI = implode(' ', $_SERVER['argv']);
			}
			
			//reformat input
			if(is_object($message)){
				$message = 'Message is object of type ' . gettype($message);

                //show content of logged object only from debug level
                if($level <= self::DEBUG_LEVEL){
                    $message .= ' : ' . PHP_EOL . var_export($message, true);
                }
            //same for arrays
		    }else if (is_array($message) && $level <= self::DEBUG_LEVEL){
                
				$message = 'Message is an array : ' . PHP_EOL . var_export($message, true);
			}else{
				$message = (string) $message;
			}
			if(is_string($tags)){
				$tags = array($tags);
			}
			
			$this->getDispatcher()->log(new common_log_Item($message, $level, time(), $stack, $tags, $requestURI, $errorFile, $errorLine));
			$this->restore();
		};
        
    }

    /**
     * enables the logger, should not be used to restore a previous logger state
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function enable()
    {
        
		$this->stateStack[] = self::singleton()->enabled;
		$this->enabled = true;
        
    }

    /**
     * disables the logger, should not be used to restore a previous logger
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function disable()
    {
        
		$this->stateStack[] = self::singleton()->enabled;
		$this->enabled = false;
        
    }

    /**
     * restores the logger after its state was modified by enable() or disable()
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function restore()
    {
        
		if (count($this->stateStack) > 0) {
			$this->enabled = array_pop($this->stateStack);
		} else {
			self::e("Tried to restore Log state that was never changed");
		}
        
    }

    /**
     * trace logs finest-grained processes informations
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string $message
     * @param  array $tags
     * @return mixed
     */
    public static function t($message, $tags = array())
    {
        
		self::singleton()->log(self::TRACE_LEVEL, $message, $tags);
        
    }

    /**
     * debug logs fine grained informations for debugging
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string $message
     * @param  array $tags
     * @return mixed
     */
    public static function d($message, $tags = array())
    {
        
		self::singleton()->log(self::DEBUG_LEVEL, $message, $tags);
        
    }



    /**
     * info logs high level system events
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string $message
     * @param  array $tags
     * @return mixed
     */
    public static function i($message, $tags = array())
    {
        
		self::singleton()->log(self::INFO_LEVEL, $message, $tags);
        
    }

    /**
     * warning logs events that represent potential problems
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string $message
     * @param  array $tags
     * @return mixed
     */
    public static function w($message, $tags = array())
    {
        
		self::singleton()->log(self::WARNING_LEVEL, $message, $tags);
        
    }

    /**
     * error logs events that allow the system to continue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string $message
     * @param  array $tags
     * @return mixed
     */
    public static function e($message, $tags = array())
    {
        
		self::singleton()->log(self::ERROR_LEVEL, $message, $tags);
        
    }

    /**
     * fatal logs very severe error events that prevent the system to continue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string $message
     * @param  array $tags
     * @return mixed
     */
    public static function f($message, $tags = array()
)
    {
        
		self::singleton()->log(self::FATAL_LEVEL, $message, $tags);
        
    }

    /**
     * Short description of method handleException
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Exception $exception
     * @return mixed
     */
    public function handleException( Exception $exception)
    {
        
        $severity = method_exists($exception, 'getSeverity') ? $exception->getSeverity() : self::INFO_LEVEL;
        self::singleton()->log($severity, $exception->getMessage(), array(get_class($exception)), $exception->getFile(), $exception->getLine());
        
    }

    /**
     * a handler for php errors, should never be called manually
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int $errorNumber
     * @param  string $errorString
     * @param  string $errorFile
     * @param  mixed  $errorLine
     * @param  array $errorContext
     * @return boolean
     */
    public function handlePHPErrors($errorNumber, $errorString, $errorFile = null, $errorLine = null, $errorContext = array())
    {
        $returnValue = (bool) false;

        
        if (error_reporting() != 0){
        	if ($errorNumber == E_STRICT) {
        		foreach ($this->ACCEPTABLE_WARNINGS as $pattern) {
        			if (preg_match($pattern, $errorString) > 0){
        				return false;
        			}
        		}
        	}
        		
        	switch ($errorNumber) {
        		case E_USER_ERROR :
        		case E_RECOVERABLE_ERROR :
        			$severity = self::FATAL_LEVEL;
        			break;
        		case E_WARNING :
        		case E_USER_WARNING :
        			$severity = self::ERROR_LEVEL;
        			break;
        		case E_NOTICE :
        		case E_USER_NOTICE:
        			$severity = self::WARNING_LEVEL;
        			break;
        		case E_DEPRECATED :
        		case E_USER_DEPRECATED :
        		case E_STRICT:
        			$severity = self::DEBUG_LEVEL;
        			break;
        		default :
        			self::d('Unsuported PHP error type: '.$errorNumber, 'common_Logger');
        			$severity = self::ERROR_LEVEL;
        			break;
        	}
        	self::singleton()->log($severity, 'php error('.$errorNumber.'): '.$errorString, array('PHPERROR'), $errorFile, $errorLine);
        }
        

        return (bool) $returnValue;
    }

    /**
     * a workaround to catch fatal errors by handling the php shutdown,
     * should never be called manually
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function handlePHPShutdown()
    {
        
    	$error = error_get_last();
    	if (($error['type'] & (E_COMPILE_ERROR | E_ERROR | E_PARSE | E_CORE_ERROR)) != 0) {
    		if (isset($error['file']) && isset($error['line'])) {
    			self::singleton()->log(self::FATAL_LEVEL, 'php error('.$error['type'].'): '.$error['message'], array('PHPERROR'), $error['file'], $error['line']);
    		} else {
    			self::singleton()->log(self::FATAL_LEVEL, 'php error('.$error['type'].'): '.$error['message'], array('PHPERROR'));
    		}
    	}
        
    }

}
