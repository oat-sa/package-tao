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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 */
namespace oat\tao\model\routing;

use FrontController;
use HttpRequest;
use Context;
use InterruptedActionException;
use common_ext_ExtensionsManager;
use common_http_Request;

/**
 * A simple controller to replace the ClearFw controller
 * 
 * @author Joel Bout, <joel@taotesting.com>
 */
class TaoFrontController implements FrontController
{
    /**
     * @var common_http_Request
     */
    private $httpRequest;
    
    /**
     * 
     * @param HttpRequest $pRequest
     */
    public function __construct( HttpRequest $pRequest ) {
        // ignore deprecated request class
        $this->httpRequest = common_http_Request::currentRequest();
    }
    
    /**
     * Returns the request to be executed
     * 
     * @return common_http_Request
     */
    protected function getRequest() {
        return $this->httpRequest;
    }
    
    /**
     * (non-PHPdoc)
     * @see FrontController::loadModule()
     */
    public function loadModule() {
        $resolver = new Resolver($this->getRequest());

        // load the responsible extension
        common_ext_ExtensionsManager::singleton()->getExtensionById($resolver->getExtensionId());
        \Context::getInstance()->setExtensionName($resolver->getExtensionId());


        //if the controller is a rest controller we try to authenticate the user
        $controllerClass = $resolver->getControllerClass();

        if(is_subclass_of($controllerClass,'tao_actions_CommonRestModule')){
            $authAdapter = new \tao_models_classes_HttpBasicAuthAdapter(common_http_Request::currentRequest());
            try {
                $user = $authAdapter->authenticate();
                $session = new \common_session_RestSession($user);
                \common_session_SessionManager::startSession($session);
            } catch (\common_user_auth_AuthFailedException $e) {
                $class = new $controllerClass();
                $class->requireLogin();
            }
        }


        try
        {
            $enforcer = new ActionEnforcer($resolver->getExtensionId(), $resolver->getControllerClass(), $resolver->getMethodName(), $this->getRequest()->getParams());
            $enforcer->execute();
        }
        catch (InterruptedActionException $iE)
        {
            // Nothing to do here.
        }
    }
}
