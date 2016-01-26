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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\oatbox\service;

use common_ext_ExtensionsManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
/**
 * The simple placeholder ServiceManager
 * @author Joel Bout <joel@taotesting.com>
 */
class ServiceManager implements ServiceLocatorInterface
{
    private static $instance;
    
    public static function getServiceManager()
    {
        if (is_null(self::$instance)) {
            self::$instance = new ServiceManager(\common_ext_ConfigDriver::singleton());
        }
        return self::$instance;
    }
    
    private $services = array();
    
    private $configService;
    
    public function __construct($configService)
    {
        $this->configService = $configService;
    }
    
    /**
     * Returns the service configured for the serviceKey
     * or throws a ServiceNotFoundException
     * 
     * @param string $serviceKey
     * @throws \common_Exception
     * @throws ServiceNotFoundException
     */
    public function get($serviceKey)
    {
        if (!isset($this->services[$serviceKey])) {
            $parts = explode('/', $serviceKey, 2);
            if (count($parts) < 2) {
                throw new ServiceNotFoundException($serviceKey, 'Invalid servicekey');
            }
            list($extId, $configId) = $parts;
            $extension = common_ext_ExtensionsManager::singleton()->getExtensionById($extId);
            $service = $extension->getConfig($configId);
            
            if ($service === false) {
                throw new ServiceNotFoundException($serviceKey);
            }
            if ($service instanceof ServiceLocatorAwareInterface) {
                $service->setServiceLocator($this);
            }
            
            $this->services[$serviceKey] = $service;
        }
        return $this->services[$serviceKey];
    }
    
    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\ServiceLocatorInterface::has()
     */
    public function has($serviceKey)
    {
        if (isset($this->services[$serviceKey])) {
            return true;
        }
        $parts = explode('/', $serviceKey, 2);
        if (count($parts) < 2) {
            return false;
        }
        list($extId, $configId) = $parts;
        $extension = common_ext_ExtensionsManager::singleton()->getExtensionById($extId);
        return $extension->hasConfig($configId);
    }

    /**
     * Registers a service, overwritting a potentially already
     * existing service.
     * 
     * @param string $serviceKey
     * @param ConfigurableService $service
     * @throws \common_Exception
     */
    public function register($serviceKey, ConfigurableService $service)
    {
        $parts = explode('/', $serviceKey, 2);
        if (count($parts) < 2) {
            throw new \common_Exception('Invalid servicekey '.$serviceKey);
        }
        $this->services[$serviceKey] = $service;
        $this->getConfig()->set($serviceKey, $service);
    }

    /**
     * @return \common_persistence_KeyValuePersistence
     */
    protected function getConfig()
    {
        return $this->configService;
    }
}
