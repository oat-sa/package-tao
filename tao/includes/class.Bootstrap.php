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
?>
<?php
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
 * @subpackage includes
 * @example
 * <code>
 *  $bootStrap = new BootStrap('tao');	//create the Bootstrap instance
 *  $bootStrap->start();				//start all the services
 *  $bootStrap->dispatch();				//dispatch the http request into the control loop
 * </code>
 */
class Bootstrap{

	/**
	 * @var string the contextual path
	 */
	protected $ctxPath = "";

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

		common_ext_ClassLoader::singleton()->register();
		
		$this->ctxPath = ROOT_PATH . '/' . $extension;
		$this->extension = common_ext_ExtensionsManager::singleton()->getExtensionById($extension);
		
		$extraConstants = isset($options['constants']) ? $options['constants'] : array();
		$extraConstants = is_string($extraConstants) ? array($extraConstants) : $extraConstants;
		
		$extensionLoader = new common_ext_ExtensionLoader($this->extension);
		$extensionLoader->load($extraConstants);

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
                        require_once TAO_TPL_PATH . 'error/maintenance.tpl';
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
    	catch (tao_models_classes_UserException $ue){
    		common_Logger::i('Access forbidden', array('TAO', 'BOOT'));
    		
    		$this->dispatchError($ue, 403);
    	}
    	catch (tao_models_classes_FileNotFoundException $e){
    		$this->dispatchError($e, 404);
    	}
    	catch (common_exception_UserReadableException $e) {
    		$this->dispatchError($e, 500, $e->getUserMessage());
    	}
    	catch (Exception $e) {
    		// Last resort.
    		$msg = "System Error: uncaught exception (";
    		$msg.= get_class($e) . ") in (".$e->getFile(). ")";
    		$msg.= "at line ".$e->getLine().": ".$e->getMessage();

    		common_Logger::e($msg);
    		
    		$message = $e->getMessage();
    		$trace = $e->getTraceAsString();
    		
    		$this->dispatchError($e, 500, $message, $trace);
    	}
    }
    
    private function dispatchError(Exception $e, $httpStatus, $message = '', $trace = ''){
    	
    	// Set relevant HTTP header.
    	header(HTTPToolkit::statusCodeHeader($httpStatus));
    	
    	if (tao_helpers_Request::isAjax()){
    		new common_AjaxResponse(array(
    				"success"   => false, 
    				"type"		=> 'Exception',
    				"data"		=> array('ExceptionType' => get_class($e)),
    				"message" 	=> $message
    		));
    	}
    	else{
    		require_once TAO_TPL_PATH . "error/error${httpStatus}.tpl";
    	}
    }

	/**
	 * Start the session
	 */
	protected function session()
	{
		if(tao_helpers_Context::check('APP_MODE')){
			// Set a specific ID to the session.
			$request = new Request();
			if($request->hasParameter('session_id')){
			 	session_id($request->getParameter('session_id'));
			}
		}

		// set the session cookie to HTTP only. 
		$sessionParams = session_get_cookie_params();
		$cookieDomain = ((true == tao_helpers_Uri::isValidAsCookieDomain(ROOT_URL)) ? tao_helpers_Uri::getDomain(ROOT_URL) : $sessionParams['domain']);
		session_set_cookie_params($sessionParams['lifetime'], tao_helpers_Uri::getPath(ROOT_URL), $cookieDomain, $sessionParams['secure'], TRUE);
		
		// Start the session with a specific name.
		session_name(GENERIS_SESSION_NAME);
		session_start();
		
		common_Logger::t("Session with name '" . GENERIS_SESSION_NAME ."' started.");
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
		$fc		= new AdvancedFC($re);
		$fc->loadModule();
	}

	/**
	 * Initialize the internationalization
	 * @see tao_helpers_I18n
	 */
	protected function i18n()
	{
		$uiLang = core_kernel_classes_Session::singleton()->getInterfaceLanguage();
		tao_helpers_I18n::init($uiLang);
	}

	/**
	 * Load external resources for the current context
	 * @see tao_helpers_Scriptloader
	 */
	protected function scripts()
	{
		switch ($this->extension->getID()){
			case 'filemanager':
				tao_helpers_Scriptloader::addCssFiles(array(
						TAOBASE_WWW . 'css/custom-theme/jquery-ui-1.8.22.custom.css',
						BASE_WWW . 'js/jqueryFileTree/jqueryFileTree.css',
						BASE_WWW . 'css/style.css'
					)
				);

				tao_helpers_Scriptloader::addJsFiles(array(
						TAOBASE_WWW . 'js/jquery-1.8.0.min.js',
						TAOBASE_WWW . 'js/jquery-ui-1.8.23.custom.min.js',
						BASE_WWW . 'js/jquery.easing.1.3.js',
						BASE_WWW . 'js/jqueryFileTree/jqueryFileTree.js',
						BASE_WWW . 'js/filemanager.js'
					)
				);
			break;

			default:
				//stylesheets to load
				tao_helpers_Scriptloader::addCssFiles(
				    array(
		    			TAOBASE_WWW . 'css/custom-theme/jquery-ui-1.8.22.custom.css',
		    			TAOBASE_WWW . 'js/jwysiwyg/jquery.wysiwyg.css',
		    			TAOBASE_WWW . 'js/jquery.jqGrid-4.4.0/css/ui.jqgrid.css',
		    			TAOBASE_WWW . 'css/style.css',
		    			TAOBASE_WWW . 'css/layout.css',
		    			TAOBASE_WWW . 'css/form.css',
		    			TAOBASE_WWW . 'css/grid.css',
		    			TAOBASE_WWW . 'css/widgets.css'
		    		)
				);

				$lang = strtolower(tao_helpers_I18n::getLangCode());
				$gridi18nFile = 'js/jquery.jqGrid-4.4.0/js/i18n/grid.locale-'.$lang.'.js';
				if (!file_exists(BASE_PATH. '/views' . $gridi18nFile)) {
					//$gridi18nFile = 'js/jquery.jqGrid-4.4.0/js/i18n/grid.locale-en.js';
					$lang = 'en';
				}

				//js golbal vars to export
				tao_helpers_Scriptloader::addJsVars(array(
					'root_url'		=> ROOT_URL,				// -> the app URL (http://www.domain.com or (http://www.domain.com/app)
					'base_url'		=> BASE_URL,				// -> the current extension URL (http://www.domain.com/tao, http://www.domain.com/taoItems)
					'taobase_www'	=> TAOBASE_WWW,			// -> the resources URL of meta extension tao (http://www.domain.com/tao/views/)
					'base_www'		=> BASE_WWW,				// -> the resources URL of the current extension (http://www.domain.com/taoItems/views/)
					'base_lang'		=> $lang
				));

				//ajax file upload works only without HTTP_AUTH
				if(!USE_HTTP_AUTH){
					tao_helpers_Scriptloader::addCssFile(
						TAOBASE_WWW . 'js/jquery.uploadify/uploadify.css'
					);
				}
			break;
		}
	}
}
?>