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
 *               2013 (update and modification) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Abstraction for the System Logger
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis
 * @subpackage common
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
     * The implementation of the Logger
     *
     * @access private
     * @var Appender
     */
    private $implementor = null;

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

        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004328 begin
		if (is_null(self::$instance)){
			self::$instance = new self();
        }
		$returnValue = self::$instance;
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004328 end

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
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004362 begin
		$this->implementor = common_log_Dispatcher::singleton();
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004362 end
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
        // section 10-30-1--78--3a1a6c41:13a1b46a114:-8000:0000000000001B4A begin
        
		// initialize the logger here to prevent fatal errors that occure if:
		// while autoloading any class, an error gets thrown
		// the logger initializes to handle this error,  and failes to autoload his files
        set_error_handler(array($this, 'handlePHPErrors'));
		register_shutdown_function(array($this, 'handlePHPShutdown'));
        // section 10-30-1--78--3a1a6c41:13a1b46a114:-8000:0000000000001B4A end
    }

    /**
     * Short description of method log
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int level
     * @param  string message
     * @param  array tags
     * @param  string errorFile
     * @param  int errorLine
     * @return mixed
     */
    public function log($level, $message, $tags, $errorFile = '', $errorLine = 0)
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000432A begin
		if ($this->enabled && $this->implementor->getLogThreshold() <= $level) {
			$this->disable();
			$stack = defined('DEBUG_BACKTRACE_IGNORE_ARGS')
                ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
                : debug_backtrace(FALSE);
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
			if(is_object($message) || is_array($message)){
				$message = print_r($message, true);
			}else{
				$message = (string) $message;
			}
			if(is_string($tags)){
				$tags = array($tags);
			}
			
			$this->implementor->log(new common_log_Item($message, $level, time(), $stack, $tags, $requestURI, $errorFile, $errorLine));
			$this->restore();
		};
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000432A end
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
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000432F begin
		$this->stateStack[] = self::singleton()->enabled;
		$this->enabled = true;
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000432F end
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
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004331 begin
		$this->stateStack[] = self::singleton()->enabled;
		$this->enabled = false;
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004331 end
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
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004333 begin
		if (count($this->stateStack) > 0) {
			$this->enabled = array_pop($this->stateStack);
		} else {
			self::e("Tried to restore Log state that was never changed");
		}
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004333 end
    }

    /**
     * trace logs finest-grained processes informations
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string message
     * @param  array tags
     * @return mixed
     */
    public static function t($message, $tags = array())
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004335 begin
		self::singleton()->log(self::TRACE_LEVEL, $message, $tags);
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004335 end
    }

    /**
     * debug logs fine grained informations for debugging
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string message
     * @param  array tags
     * @return mixed
     */
    public static function d($message, $tags = array())
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004337 begin
		self::singleton()->log(self::DEBUG_LEVEL, $message, $tags);
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004337 end
    }

	public static function dt($message, $tags = array()){
//		$stack = debug_backtrace();
//		array_shift($stack);
//		$keys = array_keys($stack);
//		$current = $stack[$keys[0]];
//		if ( (isset($current['file'])||isset($current['class'])) && isset($current['line'])) {
//			$errorLoc = isset($current['class'])?$current['class']:$current['file'];
//			$errorLine = $current['line'];
//			$errorFunc = isset($current['function'])?$current['function']:'body';
//			self::w($errorLoc.'::'.$errorFunc.' (line '.$errorLine.')', $tags);
//		}
		self::w($message,$tags);
	}

    /**
     * info logs high level system events
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string message
     * @param  array tags
     * @return mixed
     */
    public static function i($message, $tags = array())
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000433D begin
		self::singleton()->log(self::INFO_LEVEL, $message, $tags);
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000433D end
    }

    /**
     * warning logs events that represent potential problems
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string message
     * @param  array tags
     * @return mixed
     */
    public static function w($message, $tags = array())
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000433F begin
		self::singleton()->log(self::WARNING_LEVEL, $message, $tags);
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000433F end
    }

    /**
     * error logs events that allow the system to continue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string message
     * @param  array tags
     * @return mixed
     */
    public static function e($message, $tags = array())
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004341 begin
		self::singleton()->log(self::ERROR_LEVEL, $message, $tags);
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004341 end
    }

    /**
     * fatal logs very severe error events that prevent the system to continue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string message
     * @param  array tags
     * @return mixed
     */
    public static function f($message, $tags = array()
)
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:00000000000043A1 begin
		self::singleton()->log(self::FATAL_LEVEL, $message, $tags);
        // section 127-0-1-1--5509896f:133feddcac3:-8000:00000000000043A1 end
    }

    /**
     * Short description of method handleException
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Exception exception
     * @return mixed
     */
    public function handleException( Exception $exception)
    {
        // section 127-0-1-1-7b882644:1342260c2b6:-8000:000000000000186D begin
        $severity = method_exists($exception, 'getSeverity') ? $exception->getSeverity() : self::INFO_LEVEL;
        self::singleton()->log($severity, $exception->getMessage(), array(get_class($exception)), $exception->getFile(), $exception->getLine());
        // section 127-0-1-1-7b882644:1342260c2b6:-8000:000000000000186D end
    }

    /**
     * a handler for php errors, should never be called manually
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int errorNumber
     * @param  string errorString
     * @param  string errorFile
     * @param  mixed  errorLine
     * @param  array errorContext
     * @return boolean
     */
    public function handlePHPErrors($errorNumber, $errorString, $errorFile = null, $errorLine = null, $errorContext = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--209aa8b7:134195b5554:-8000:0000000000001848 begin
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
        // section 127-0-1-1--209aa8b7:134195b5554:-8000:0000000000001848 end

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
        // section 127-0-1-1-56e04748:1341d1d0e41:-8000:000000000000182B begin
    	$error = error_get_last();
    	if (($error['type'] & (E_COMPILE_ERROR | E_ERROR | E_PARSE | E_CORE_ERROR)) != 0) {
    		if (isset($error['file']) && isset($error['line'])) {
    			self::singleton()->log(self::FATAL_LEVEL, 'php error('.$error['type'].'): '.$error['message'], array('PHPERROR'), $error['file'], $error['line']);
    		} else {
    			self::singleton()->log(self::FATAL_LEVEL, 'php error('.$error['type'].'): '.$error['message'], array('PHPERROR'));
    		}
    	}
        // section 127-0-1-1-56e04748:1341d1d0e41:-8000:000000000000182B end
    }

}