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
 *
 */
namespace oat\tao\model\routing;

use common_http_Request;
use tao_helpers_Request;
use common_ext_ExtensionsManager;

/**
 * Resolves a http request to a controller and method
 * using the provided routers
 * 
 * @author Joel Bout, <joel@taotesting.com>
 */
class Resolver
{
    const DEFAULT_EXTENSION = 'tao';
    
    /**
     * Request to be resolved
     * 
     * @var common_http_Request
     */
    private $request;
    
    private $extensionId;
    
    private $controller;
    
    private $action;
    
    /**
     * Resolves a request to a method
     * 
     * @param HttpRequest $pRequest
     * @return string
     */
    public function __construct(common_http_Request $request) {
       $this->request = $request;
    }
    
    public function getExtensionId() {
        if (is_null($this->extensionId)) {
            $this->resolve();
        }
        return $this->extensionId;
    }
    
    public function getControllerClass() {
        if (is_null($this->controller)) {
            $this->resolve();
        }
        return $this->controller;
    }

    public function getMethodName() {
        if (is_null($this->action)) {
            $this->resolve();
        }
        return $this->action;
    }
   
    /**
     * Get the controller short name as used into the URL
     * @return string the name
     */ 
    public function getControllerShortName() {
        $relativeUrl = tao_helpers_Request::getRelativeUrl($this->request->getUrl());
        $parts = explode('/', trim($relativeUrl, '/'));
        if(count($parts) == 3){
            return $parts[1];
        }
        return null;
    }

    /**
     * Tries to resolve the current request using the routes first
     * and then falls back to the legacy controllers
     */
    protected function resolve() {
        $relativeUrl = tao_helpers_Request::getRelativeUrl($this->request->getUrl());
        foreach ($this->getRouteMap() as $entry) {
            $route = $entry['route'];
            $called = $route->resolve($relativeUrl);
            if (!is_null($called)) {
                list($controller, $action) = explode('@', $called);
                $this->controller = $controller;
                $this->action = $action;
                $this->extensionId = $entry['extId'];
                return true;
            }
        }
        throw new \ResolverException('Unable to resolve '.$this->request->getUrl());
    }
    
    private function getRoutes(\common_ext_Extension $extension) {
        $routes = array();
        foreach ($extension->getManifest()->getRoutes() as $routeId => $routeData) {
            $class = is_array($routeData) && isset($routeData['class'])
                ? $routeData['class']
                : 'oat\tao\model\routing\NamespaceRoute';
            $routes[] = new $class($extension, trim($routeId, '/'), $routeData);
        }
        if (empty($routes)) {
            $routes[] = new LegacyRoute($extension, $extension->getName(), array());
        }
        return $routes;
    }
    
    private function getRouteMap() {
        $routes = array();
        foreach (\common_ext_ExtensionsManager::singleton()->getInstalledExtensions() as $extension) {
            foreach ($this->getRoutes($extension) as $route) {
                $routes[] = array(
                	'extId' => $extension->getId(),
                    'route' => $route
                );
            }
        }
        return $routes;
    }
}
