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
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

use oat\tao\helpers\Template;
use oat\tao\model\routing\TaoFrontController;

/*
 * The generis extension loader is included there ONCE!
 *  1. Load and initialize the API and so the database
 *  2. Initialize the autoloaders
 *  3. Initialize the extension manager
 */
require_once dirname(__FILE__) . '/../../generis/common/inc.extension.php';

/**
 * The Bootstrap Class enables you to drive the application flow for a given extenstion.
 * A bootstrap instance initialize the context and starts all the services:
 * 	- session
 *  - database
 *  - user
 *  - i18n
 *
 * And it's used to disptach the Control Loop
 *  - control the platform status (redirect to the maintenance page if it is required)
 *  - dispatch to the convenient action
 *  - control code exceptions
 *
 * @author Bertrand CHEVRIER <bertrand.chevrier@tudor.lu>
 * @package tao
 * @example
 * <code>
 *  $bootStrap = new BootStrap('tao');	//create the Bootstrap instance
 *  $bootStrap->start();				//start all the services
 *  $bootStrap->dispatch();				//dispatch the http request into the control loop
 * </code>
 */
class Bootstrap {
    
    const CONFIG_SESSION_HANDLER = 'session';

	/**
	 * @var boolean if the context has been started
	 */
	protected static $isStarted = false;

	/**
	 * @var boolean if the context has been dispatched
	 */
	protected static $isDispatched = false;

	/**
	 * @var common_ext_Extension
	 */
	protected $extension = null;

	/**
	 * Initialize the context
	 * @param string $extension
	 * @param array $options
	 */
	public function __construct($extension, $options = array())
	{
	    common_Profiler::singleton()->register();

		$this->extension = common_ext_ExtensionsManager::singleton()->getExtensionById($extension);

		if(PHP_SAPI == 'cli'){
			tao_helpers_Context::load('SCRIPT_MODE');
		}
		else{
			tao_helpers_Context::load('APP_MODE');
		}

	}

	/**
	 * Check if the current context has been started
	 * @return boolean
	 */
	public static function isStarted()
	{
		return self::$isStarted;
	}

	/**
	 * Check if the current context has been dispatched
	 * @return boolean
	 */
	public static function isDispatched()
	{
		return self::$isDispatched;
	}

    /**
     * Check if the application is ready
     * @return {boolean} Return true if the application is ready
     */
    protected function isReady()
    {
        return defined('SYS_READY') ? SYS_READY : true;
    }

	/**
	 * Start all the services:
	 *  1. Start the session
	 *  2. Update the include path
	 *  3. Include the global helpers
	 *  4. Connect the current user to the generis API
	 *  5. Initialize the internationalization
	 *  6. Check the application' state
	 */
	public function start()
	{
		if(!self::$isStarted){
			$this->session();
			$this->includePath();
			$this->registerErrorhandler();
			$this->globalHelpers();
			$this->i18n();
			self::$isStarted = true;
		}
		common_Profiler::stop('start');
	}

	/**
	 * Dispatch the current http request into the control loop:
	 *  1. Load the ressources
	 *  2. Start the MVC Loop from the ClearFW
     *  manage Exception:
	 */
	public function dispatch()
	{
		common_Profiler::start('dispatch');
		if(!self::$isDispatched){
            $isAjax = tao_helpers_Request::isAjax();

			if(tao_helpers_Context::check('APP_MODE')){
				if(!$isAjax){
					$this->scripts();
				}
			}

            //Catch all exceptions
            try{
                //the app is ready
                if($this->isReady()){
                    $this->mvc();
                }
                //the app is not ready
                else{
                    //the request is not an ajax request, redirect the user to the maintenance page
                    if(!$isAjax){
                        require_once Template::getTemplate('error/maintenance.tpl', 'tao');
                    //else throw an exception, this exception will be send to the client properly
                    }
                    else{
                        
                        throw new common_exception_SystemUnderMaintenance();
                    }
                }
            }
            catch(Exception $e){
                $this->catchError($e);
            }
			self::$isDispatched = true;
		}
		common_Profiler::stop('dispatch');
	}

    /**
     * Catch any errors
     * If the request is an ajax request, return to the client a formated object.
     *
     * @param Exception $exception
     */
    private function catchError(Exception $exception)
    {
    	try {
    		// Rethrow for a direct clean catch...
    		throw $exception;
    	}
    	catch (ActionEnforcingException $ae){
    		common_Logger::w("Called module ".$ae->getModuleName().', action '.$ae->getActionName().' not found.', array('TAO', 'BOOT'));
    		
    		$message  = "Called module: ".$ae->getModuleName()."\n";
    		$message .= "Called action: ".$ae->getActionName()."\n";
    		
    		$this->dispatchError($ae, 404, $message);
    	}
        catch (tao_models_classes_AccessDeniedException $ue){
    		common_Logger::i('Access denied', array('TAO', 'BOOT'));
            if (!tao_helpers_Request::isAjax()
                && common_session_SessionManager::isAnonymous()
    		    && tao_models_classes_accessControl_AclProxy::hasAccess('login', 'Main', 'tao')
    		) {
                header(HTTPToolkit::statusCodeHeader(302));
                header(HTTPToolkit::locationHeader(_url('login', 'Main', 'tao', array(
                    'redirect' => $ue->getDeniedRequest()->getRequestURI(),
                    'msg' => $ue->getUserMessage()
                ))));
            } else {
                $this->dispatchError($ue, 403);
            }
    	}
    	catch (tao_models_classes_UserException $ue){
    		$this->dispatchError($ue, 403);
    	}
    	catch (tao_models_classes_FileNotFoundException $e){
    		$this->dispatchError($e, 404);
    	}
    	catch (common_exception_UserReadableException $e) {
    		$this->dispatchError($e, 500, $e->getUserMessage());
    	}
    	catch (ResolverException $e) {
    	    common_Logger::singleton()->handleException($e);
            if (!tao_helpers_Request::isAjax()
    		    && tao_models_classes_accessControl_AclProxy::hasAccess('login', 'Main', 'tao')
    		) {
                header(HTTPToolkit::statusCodeHeader(302));
                header(HTTPToolkit::locationHeader(_url('login', 'Main', 'tao')));
            } else {
                $this->dispatchError($e, 403);
            }
    	}
    	catch (Exception $e) {
    		// Last resort.
    		$msg = "System Error: uncaught exception (";
    		$msg .= get_class($e) . ") in (" . $e->getFile() . ")";
    		$msg .= " at line " . $e->getLine() . ": " . $e->getMessage();

    		$previous = $e->getPrevious();
    		
    		while ($previous !== null) {
    		    $msg .= "\n\ncaused by:\n\n";
    		    $msg .= "(" . get_class($previous) . ") in (" . $previous->getFile() . ")";
    		    $msg .= " at line " . $previous->getLine() . ": " . $previous->getMessage();
    		    
    		    $previous = $previous->getPrevious();
    		}
    		
    		common_Logger::e($msg);
    		
    		$message = $e->getMessage();
    		$trace = $e->getTraceAsString();
    		
    		$this->dispatchError($e, 500, $message, $trace);
    	}
    }
    
    private function dispatchError(Exception $e, $httpStatus, $message = '', $trace = '')
    {
        
        // Set relevant HTTP header.
        header(HTTPToolkit::statusCodeHeader($httpStatus));
        
        if (tao_helpers_Request::isAjax()) {
            new common_AjaxResponse(array(
                "success" => false,
                "type" => 'Exception',
                "data" => array(
                    'ExceptionType' => get_class($e)
                ),
                "message" => $message
            ));
        } else {
            require_once Template::getTemplate("error/error${httpStatus}.tpl", 'tao');
        }
    }

    /**
     * Start the session
     */
    protected function session()
    {
        if (tao_helpers_Context::check('APP_MODE')) {
            // Set a specific ID to the session.
            $request = new Request();
            if ($request->hasParameter('session_id')) {
                session_id($request->getParameter('session_id'));
            }
        }
        
        // set the session cookie to HTTP only.
        
        $this->configureSessionHandler();
  
        $sessionParams = session_get_cookie_params();
        $cookieDomain = ((true == tao_helpers_Uri::isValidAsCookieDomain(ROOT_URL)) ? tao_helpers_Uri::getDomain(ROOT_URL) : $sessionParams['domain']);
        session_set_cookie_params($sessionParams['lifetime'], tao_helpers_Uri::getPath(ROOT_URL), $cookieDomain, $sessionParams['secure'], TRUE);
        session_name(GENERIS_SESSION_NAME);
        
        if (isset($_COOKIE[GENERIS_SESSION_NAME])) {
            
            // Resume the session
            session_start();
            
            //cookie keep alive, if lifetime is not 0
            if ($sessionParams['lifetime'] !== 0) {
                $expiryTime = $sessionParams['lifetime'] + time();
                setcookie(session_name(), session_id(), $expiryTime, tao_helpers_Uri::getPath(ROOT_URL), $cookieDomain, $sessionParams['secure'], true);
            }
        }
	}
	
    private function configureSessionHandler() {
        $sessionHandler = common_ext_ExtensionsManager::singleton()->getExtensionById('tao')->getConfig(self::CONFIG_SESSION_HANDLER);
        if ($sessionHandler !== false) {
            session_set_save_handler(
                array($sessionHandler, 'open'),
                array($sessionHandler, 'close'),
                array($sessionHandler, 'read'),
                array($sessionHandler, 'write'),
                array($sessionHandler, 'destroy'),
                array($sessionHandler, 'gc')
            );
        }
    }
    
	/**
	 * register a custom Errorhandler
	 */
	protected function registerErrorhandler()
	{
		// register the logger as erorhandler
		common_Logger::singleton()->register();
	}

	/**
	 * Update the include path
	 */
	protected function includePath()
	{
		set_include_path(get_include_path() . PATH_SEPARATOR . ROOT_PATH);
	}

	/**
	 * Include the global helpers
	 * because of the shortcuts function like
	 * _url() or _dh()
	 * that are not loaded with the autoloader
	 */
	protected function globalHelpers()
	{
		require_once 'tao/helpers/class.Uri.php';
		require_once 'tao/helpers/class.Display.php';
	}

	/**
	 *  Start the MVC Loop from the ClearFW
	 *  @throws ActionEnforcingException in case of wrong module or action
	 *  @throws tao_models_classes_UserException when a request try to acces a protected area
	 */
	protected function mvc()
	{	
		$re		= new HttpRequest();
		$fc		= new TaoFrontController($re);
		$fc->loadModule();
	}

	/**
	 * Initialize the internationalization
	 * @see tao_helpers_I18n
	 */
	protected function i18n()
	{
		$uiLang = \common_session_SessionManager::getSession()->getInterfaceLanguage();
		tao_helpers_I18n::init($this->extension, $uiLang);
	}

	/**
	 * Load external resources for the current context
	 * @see tao_helpers_Scriptloader
	 */
	protected function scripts()
	{
        $cssFiles = array(
            TAOBASE_WWW . 'css/layout.css',
            TAOBASE_WWW . 'css/tao-main-style.css',
            TAOBASE_WWW . 'css/tao-3.css'
        );

        //stylesheets to load
        \tao_helpers_Scriptloader::addCssFiles($cssFiles);

        if(\common_session_SessionManager::isAnonymous()) {
            tao_helpers_Scriptloader::addCssFile(
                TAOBASE_WWW . 'css/portal.css'
            );
        }

        //ajax file upload works only without HTTP_AUTH
        if(!USE_HTTP_AUTH){
            tao_helpers_Scriptloader::addCssFile(
                TAOBASE_WWW . 'js/lib/jquery.uploadify/uploadify.css'
            );
        }
    }
}
?>
