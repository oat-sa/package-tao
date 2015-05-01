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

/**
 * FlowController class
 * TODO FlowController class documentation.
 * 
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class FlowController
{

	public function __construct()
	{

	}
	
    /**
     * Forward routing.
     * The forward runs into tha same HTTP request unlike redirect.
     * @param string $action the name of the new action
     * @param string $controller the name of the new controller/module
     * @param string $extension the name of the new extension
     * @param array $params additional parameters
     */
	public function forward($action, $controller = null, $extension = null, $params = array())
	{
        $context = Context::getInstance();

        $tempExtensionName = $context->getExtensionName();
		$tempModuleName = $context->getModuleName();
		$tempActionName = $context->getActionName();

        if(is_null($extension)){ 
            $extension = $tempExtensionName;
        }

        if(is_null($controller)){ 
            $controller = $tempModuleName;
        }	

        $context->setExtensionName($extension);
        $context->setModuleName($controller);
		$context->setActionName($action);

        if(count($params) > 0){
            $context->getRequest()->addParameters($params);
        }

		$enforcer = new ActionEnforcer($extension, $controller, $action, $params);
		$enforcer->execute();
		
		throw new InterruptedActionException('Interrupted action after a forward',
											 $tempModuleName,
											 $tempActionName);
	}

    /**
     * Forward alias that use and parse an URL to extract extension/controller/actions?params 
     * @param string $url the url to forward to
     */
    public function forwardUrl($url){
		$context = Context::getInstance();

        $parsedUrl = parse_url($url);

        common_Logger::d($parsedUrl);

        $parts = explode('/', trim($parsedUrl['path'], '/'));
        if(count($parts) === 3){
            $params = array();
            if(strlen($parsedUrl['query']) > 0){
                parse_str($parsedUrl['query'], $params);
            }

            $this->forward($parts[2], $parts[1], $parts[0], $params);
        }

		throw new InterruptedActionException('Interrupted action after a forward',
											 $context->getModuleName(),
											 $context->getActionName());
                
    }
	
	// HTTP 303 : The response to the request can be found under a different URI
	public function redirect($url, $statusCode = 302)
	{
		$context = Context::getInstance();
		
		header(HTTPToolkit::statusCodeHeader($statusCode));
		header(HTTPToolkit::locationHeader($url));
		
		throw new InterruptedActionException('Interrupted action after a redirection', 
											 $context->getModuleName(),
											 $context->getActionName());
	}
}
