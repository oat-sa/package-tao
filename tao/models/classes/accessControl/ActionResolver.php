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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\tao\model\accessControl;

use oat\tao\model\routing\Resolver;
use common_http_Request;
use common_exception_Error;

/**
 * Wrap the {@link Resolver} for the needs of access controls in order to get controller and action from a url.
 * @author Betrrand Chevrier <bertrand@taotesting.com>
 */
class ActionResolver
{

    private $action;

    private $controller;

    public function __construct($url){
        $this->loadFromUrl($url);
    }

    /**
     * Build the helper from the controller className
     * @param string $extension 
     * @param string $shortname
     * @return ActionHelper 
     * @throws ResolverException
     */
    public static function getByControllerName($shortName, $extension) {
        $url = _url('index', $shortName, $extension);
        return new static($url);
    }

    /**
     * @throws ResolverException
     */
    private function loadFromUrl($url){
        $route = new Resolver(new common_http_Request($url));
        $this->controller   = $route->getControllerClass();
        $this->action       = $route->getMethodName();
    }

    public function getAction(){
        return $this->action;
    }

    public function getController(){
        return $this->controller;
    }
}
