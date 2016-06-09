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
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013- (update and modification) Open Assessment Technologies SA;
 *
 */
namespace oat\tao\model\mvc;

use oat\oatbox\service\ServiceManager;
use oat\tao\helpers\Template;
use oat\tao\model\asset\AssetService;
use oat\tao\model\routing\TaoFrontController;
use common_Profiler;
use common_Logger;
use common_ext_ExtensionsManager;
use common_session_SessionManager;
use common_AjaxResponse;
use common_report_Report as Report;
use tao_helpers_Context;
use tao_helpers_Request;
use tao_helpers_Uri;
use Request;
use HTTPToolkit;

use Exception;
use oat\oatbox\service\ServiceNotFoundException;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\action\ActionResolver;
use oat\oatbox\action\ResolutionException;

/**
 * The Bootstrap Class enables you to drive the application flow for a given extenstion.
 * A bootstrap instance initialize the context and starts all the services:
 * 	- session
 *  - database
 *  - user
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
	 * Initialize the context
	 * @param string $configFile
	 * @param array $options
	 */
	public function __construct($configFile, $options = array())
	{
	    
	    require_once $configFile;
	    
	    common_Profiler::singleton()->register();

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
			$this->setDefaultTimezone();
			$this->registerErrorhandler();
			self::$isStarted = true;
		}
		common_Profiler::stop('start');
	}
	
	protected function dispatchHttp()
	{
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
	    
	                throw new \common_exception_SystemUnderMaintenance();
	            }
	        }
	    }
	    catch(Exception $e){
	        $this->catchError($e);
	    }
	    
	    // explicitly close session
	    session_write_close();
	}
	
	protected function dispatchCli()
	{
	    $params = $_SERVER['argv'];
	    $file = array_shift($params);
	    if (count($params) < 1) {
	        $report = new Report(Report::TYPE_ERROR, __('No action specified'));
	    } else {
	        try {
    	        $resolver = new ActionResolver();
    	        $resolver->setServiceManager($this->getServiceManager());
    	        $actionIdentifier = array_shift($params);
    	        $invocable = $resolver->resolve($actionIdentifier);
    	        try {
    	            $report = call_user_func($invocable, $params);
    	        } catch (\Exception $e) {
    	            $report = new Report(Report::TYPE_ERROR, __('An exception occured while running "%s"', $actionIdentifier));
    	            $report->add(new Report(Report::TYPE_ERROR, $e->getMessage()));
    	        }
	        } catch (ResolutionException $e) {
	            $report = new Report(Report::TYPE_ERROR, __('Action "%s" not found.', $actionIdentifier));
	        }
	    }
	     
	    echo \tao_helpers_report_Rendering::renderToCommandline($report);
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
		    if (PHP_SAPI == 'cli') {
		        $this->dispatchCli();
		    } else {
                $this->dispatchHttp();
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
    	catch (\ActionEnforcingException $ae){
    		common_Logger::w("Called module ".$ae->getModuleName().', action '.$ae->getActionName().' not found.', array('TAO', 'BOOT'));
    		
    		$message  = "Called module: ".$ae->getModuleName()."\n";
    		$message .= "Called action: ".$ae->getActionName()."\n";
    		
    		$this->dispatchError($ae, 404, $message);
    	}
        catch (\tao_models_classes_AccessDeniedException $ue){
    		common_Logger::i('Access denied', array('TAO', 'BOOT'));
            if (!tao_helpers_Request::isAjax()
                && common_session_SessionManager::isAnonymous()
    		    && \tao_models_classes_accessControl_AclProxy::hasAccess('login', 'Main', 'tao')
    		) {
                header(HTTPToolkit::statusCodeHeader(302));
                header(HTTPToolkit::locationHeader(_url('login', 'Main', 'tao', array(
                    'redirect' => $ue->getDeniedRequest()->getRequestURI(),
                    'msg' => $ue->getUserMessage()
                ))));
            } else {
                $this->dispatchError($ue, 403, $ue->getUserMessage());
            }
    	}
    	catch (\tao_models_classes_UserException $ue){
    		$this->dispatchError($ue, 403);
    	}
    	catch (\tao_models_classes_FileNotFoundException $e){
    		$this->dispatchError($e, 404);
    	}
    	catch (\common_exception_UserReadableException $e) {
    		$this->dispatchError($e, 500, $e->getUserMessage());
    	}
    	catch (\ResolverException $e) {
    	    common_Logger::singleton()->handleException($e);
            if (!tao_helpers_Request::isAjax()
    		    && \tao_models_classes_accessControl_AclProxy::hasAccess('login', 'Main', 'tao')
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
	 * Set Timezone quickfix
	 */
	protected function setDefaultTimezone()
	{
	    if(function_exists("date_default_timezone_set") && defined('TIME_ZONE')){
	        date_default_timezone_set(TIME_ZONE);
	    }
	}

	/**
	 *  Start the MVC Loop from the ClearFW
	 *  @throws ActionEnforcingException in case of wrong module or action
	 *  @throws tao_models_classes_UserException when a request try to acces a protected area
	 */
    protected function mvc()
    {
        $re = \common_http_Request::currentRequest();
        $fc = new TaoFrontController();
        $fc->legacy($re);
    }

	/**
	 * Load external resources for the current context
	 * @see tao_helpers_Scriptloader
	 */
	protected function scripts()
	{
	    $assetService = $this->getServiceManager()->get(AssetService::SERVICE_ID);
        $cssFiles = array(
			$assetService->getJsBaseWww('tao') . 'css/layout.css',
			$assetService->getJsBaseWww('tao') . 'css/tao-main-style.css',
			$assetService->getJsBaseWww('tao') . 'css/tao-3.css'
        );

        //stylesheets to load
        \tao_helpers_Scriptloader::addCssFiles($cssFiles);

        if(\common_session_SessionManager::isAnonymous()) {
            \tao_helpers_Scriptloader::addCssFile(
				$assetService->getJsBaseWww('tao') . 'css/portal.css'
            );
        }

        //ajax file upload works only without HTTP_AUTH
        if(!USE_HTTP_AUTH){
            \tao_helpers_Scriptloader::addCssFile(
                TAOBASE_WWW . 'js/lib/jquery.uploadify/uploadify.css'
            );
        }
    }

	private function getServiceManager()
	{
	    return ServiceManager::getServiceManager();
	}
}
