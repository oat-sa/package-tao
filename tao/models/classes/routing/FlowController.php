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
 * Copyright (c) 2006-2009 (original work) Public Research Centre Henri Tudor (under the project FP6-IST-PALETTE);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2014      (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\tao\model\routing;

use ActionEnforcingException;
use common_Logger;
use common_http_Request;
use common_ext_ExtensionsManager;
use HTTPToolkit;
use InterruptedActionException;
use HttpRequest;
use Context;
use FlowController as ClearFwFlowController;
use tao_helpers_Uri;

/**
 * The FlowController helps you to navigate through MVC actions.
 * 
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 * @author Bertrand Chevrier <Bertrand@taotestin.com>
 */
class FlowController extends ClearFwFlowController
{

    /**
     * This header is added to the response to inform the client a forward occurs
     */
    const FORWARD_HEADER = 'X-Tao-Forward';


    /**
     * Forward the action to execute reqarding a URL
     * The forward runs into tha same HTTP request unlike redirect.
     * @param string $url the url to forward to
     */
    public function forwardUrl($url){

        //get the current request
        $request = common_http_Request::currentRequest();
        $params = $request->getParams();

        //parse the given URL
        $parsedUrl = parse_url($url);

        //if new parameters are given, then merge them 
        if(isset($parsedUrl['query']) && strlen($parsedUrl['query']) > 0){
            $newParams = array();
            parse_str($parsedUrl['query'], $newParams);
            if(count($newParams) > 0){
                $params = array_merge($params, $newParams);
            }
        }

        //resolve the given URL for routing
        $resolver = new Resolver(new common_http_Request($parsedUrl['path'], $request->getMethod(), $params));
        
	    $context = Context::getInstance();

        // load the responsible extension
        common_ext_ExtensionsManager::singleton()->getExtensionById($resolver->getExtensionId());
   
        //update the context to the new route
        $context->setExtensionName($resolver->getExtensionId());
        $context->setModuleName($resolver->getControllerShortName());
		$context->setActionName($resolver->getMethodName());
        if(count($params) > 0){
            $context->getRequest()->addParameters($params);
        }

        //add a custom header so the client knows where the route ends
        header(self::FORWARD_HEADER . ': ' . $resolver->getExtensionId() . '/' .  $resolver->getControllerShortName() . '/' . $resolver->getMethodName());

        //execite the new action
        $enforcer = new ActionEnforcer($resolver->getExtensionId(), $resolver->getControllerClass(), $resolver->getMethodName(), $params);
        $enforcer->execute();

        //should not be reached
        throw new InterruptedActionException('Interrupted action after a forward',
                                             $context->getModuleName(),
                                             $context->getActionName());
                
    }

    /**
     * Forward routing.

     * @param string $action the name of the new action
     * @param string $controller the name of the new controller/module
     * @param string $extension the name of the new extension
     * @param array $params additional parameters
     */
	public function forward($action, $controller = null, $extension = null, $params = array())
	{
        //as we use a route resolver, it's easier to rebuild the URL to resolve it 
        $this->forwardUrl(\tao_helpers_Uri::url($action, $controller, $extension, $params));
	}
	
}
