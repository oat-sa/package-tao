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

use oat\tao\helpers\Template;
use oat\tao\model\routing\FlowController;
use oat\oatbox\service\ServiceManager;

/**
 * Top level controller
 * All children extenions module should extends the CommonModule to access the shared data
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2 http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 *         
 */
abstract class tao_actions_CommonModule extends Module
{

    /**
     * The Modules access the models throught the service instance
     * 
     * @var tao_models_classes_Service
     */
    protected $service = null;

    /**
     * empty constuctor
     */
    public function __construct()
    {
    }

    /**
     *
     * @see Module::setView()
     * @param string $path
     *            view identifier
     * @param string $extensionID
     *            use the views in the specified extension instead of the current extension
     */
    public function setView($path, $extensionID = null)
    {
        parent::setView(Template::getTemplate($path, $extensionID));
    }

    /**
     * Retrieve the data from the url and make the base initialization
     * 
     * @return void
     */
    protected function defaultData()
    {
        $context = Context::getInstance();
        
        $this->setData('extension', context::getInstance()->getExtensionName());
        $this->setData('module', $context->getModuleName());
        $this->setData('action', $context->getActionName());
        
        if ($this->hasRequestParameter('uri')) {
            
            // @todo stop using session to manage uri/classUri
            $this->setSessionAttribute('uri', $this->getRequestParameter('uri'));
            
            // inform the client of new classUri
            $this->setData('uri', $this->getRequestParameter('uri'));
        }
        if ($this->hasRequestParameter('classUri')) {
            
            // @todo stop using session to manage uri/classUri
            $this->setSessionAttribute('classUri', $this->getRequestParameter('classUri'));
            if (! $this->hasRequestParameter('uri')) {
                $this->removeSessionAttribute('uri');
            }
            
            // inform the client of new classUri
            $this->setData('uri', $this->getRequestParameter('classUri'));
        }
        
        if ($this->getRequestParameter('message')) {
            $this->setData('message', $this->getRequestParameter('message'));
        }
        if ($this->getRequestParameter('errorMessage')) {
            $this->setData('errorMessage', $this->getRequestParameter('errorMessage'));
        }

        $this->setData('client_timeout', $this->getClientTimeout());
        $this->setData('client_config_url', $this->getClientConfigUrl());
    }
	
    /**
     * Function to return an user readable error
     * Does not work with ajax Requests yet
     * 
     * @param string $description error to show
     * @param boolean $returnLink whenever or not to add a return link
     */
    protected function returnError($description, $returnLink = true) {
        if (tao_helpers_Request::isAjax()) {
            common_Logger::w('Called '.__FUNCTION__.' in an unsupported AJAX context');
            throw new common_Exception($description); 
        } else {
            $this->setData('message', $description);
            $this->setData('returnLink', $returnLink);
            $this->setView('error/user_error.tpl', 'tao');
        }
    }

    /**
     * Returns the absolute path to the specified template
     * 
     * @param string $identifier
     * @param string $extensionID
     * @return string
     */
    protected static function getTemplatePath($identifier, $extensionID = null)
    {
    	if ($extensionID === true) {
			$extensionID = 'tao';
			common_Logger::d('Deprecated use of setView() using a boolean');
		}
    	if(is_null($extensionID) || empty($extensionID)) {
    		$extensionID = Context::getInstance()->getExtensionName();
    	}
    	$ext = common_ext_ExtensionsManager::singleton()->getExtensionById($extensionID);
    	return $ext->getConstant('DIR_VIEWS').'templates'.DIRECTORY_SEPARATOR.$identifier;
    }
   
     
    /**
     * Helps you to add the URL of the client side config file
     * 
     * @param array $extraParameters additionnal parameters to append to the URL 
     * @return string the URL
     */
    protected function getClientConfigUrl($extraParameters = array()){
        $context = Context::getInstance();
        $clientConfigParams = array(
            'extension'         => $context->getExtensionName(),
            'module'            => $context->getModuleName(),
            'action'            => $context->getActionName()
        );
        
        return _url('config', 'ClientConfig', 'tao', array_merge($clientConfigParams, $extraParameters));
    }


    /**
     * Get the client timeout value from the config.
     * 
     * @return int the timeout value in seconds
     */
    protected function getClientTimeout(){
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        $config = $ext->getConfig('js');
        if($config != null && isset($config['timeout'])){
            return (int)$config['timeout'];
        } 
        return 30;
    }
    
    protected function returnJson($data, $httpStatus = 200) {
        header(HTTPToolkit::statusCodeHeader($httpStatus));
        Context::getInstance()->getResponse()->setContentHeader('application/json');
        echo json_encode($data);
    }
    
    /**
     * Returns a report
     * 
     * @param common_report_Report $report
     */
    protected function returnReport(common_report_Report $report, $refresh = true) {
        if ($refresh) {
            $data = $report->getdata();
            if ($report->getType() == common_report_Report::TYPE_SUCCESS &&
                !is_null($data) && $data instanceof \core_kernel_classes_Resource) {
                $this->setData('message', $report->getMessage());
                $this->setData('selectNode', tao_helpers_Uri::encode($data->getUri()));
                $this->setData('reload', true);
                return $this->setView('form.tpl', 'tao');
            }
        }
        
        $this->setData('report', $report);
        $this->setView('report.tpl', 'tao');
    }


    /**
     * Forward using the TAO FlowController implementation
     * @see {@link oat\model\routing\FlowController}
     */
	public function forward($action, $controller = null, $extension = null, $params = array())
    {
        $flow = new FlowController();
        $flow->forward($action, $controller, $extension, $params);
    }

    /**
     * Forward using the TAO FlowController implementation
     * @see {@link oat\model\routing\FlowController}
     */
    public function forwardUrl($url)
    {
        $flow = new FlowController();
        $flow->forwardUrl($url);
    }

    /**
     * Redirect using the TAO FlowController implementation
     * @see {@link oat\model\routing\FlowController}
     */
	public function redirect($url, $statusCode = 302)
    {
        $flow = new FlowController();
        $flow->redirect($url, $statusCode);
    }
    
    /**
     * Placeholder function until controllers properly support service manager
     * 
     * @return \oat\oatbox\service\ServiceManager
     */
    protected function getServiceManager()
    {
        return ServiceManager::getServiceManager();
    }
}
