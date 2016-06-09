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

use common_ext_Extension;

/**
 * Interface of a router, that based on a relative Url
 * and its configuration provided as $routeData
 * decides which methode of which controller should be executed
 * 
 * @author Joel Bout, <joel@taotesting.com>
 */
abstract class Route
{
    /**
     * Owner of the route
     * 
     * @var common_ext_Extension
     */
    private $extension;
    
    /**
     * Id of the route
     *
     * @var string
     */
    private $id;
    
    /**
     * Data the route requires to resolve
     *
     * @var mixed
     */
    private $config;
    
    /**
     * 
     * @param common_ext_Extension $extension
     * @param string $routeId
     * @param mixed $routeConfig
     */
    public function __construct(common_ext_Extension $extension, $routeId, $routeConfig) {
        $this->extension = $extension;
        $this->id = $routeId;
        $this->config = $routeConfig;
    }
    
    /**
     * 
     * @return common_ext_Extension
     */
    protected function getExtension() {
        return $this->extension;
    }
    
    /**
     * 
     * @return mixed
     */
    protected function getConfig() {
        return $this->config;
    }
    
    /**
     * 
     * @return string
     */
    protected function getId() {
        return $this->id;
    }
    
    
    /**
     * Returns the name of the controller and action to call
     * or null if it doesn't apply
     * 
     * @return string
     */
    public abstract function resolve($relativeUrl);
    
}