<?php
/**
 * 
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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * filesystem access provider
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItemRunner
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class tao_models_classes_fsAccess_Manager
{	
    const CONFIG_KEY = 'filesystemAccess';

    private static $instance = null;
    
    public static function singleton() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private $providers = array();
    
    private function __construct() {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        if ($ext->hasConfig(self::CONFIG_KEY)) {
            foreach ($ext->getConfig(self::CONFIG_KEY) as $serialized) {
                $provider = tao_models_classes_fsAccess_AccessProvider::restoreFromString($serialized);
                $this->providers[$provider->getId()] = $provider; 
            }
        }
    }
    
    public function getProvider($key) {
        if (!isset($this->providers[$key])) {
            throw new common_Exception('Undefined provider '.$key);
        }
        return $this->providers[$key];
    }
    
    public function addProvider($provider) {
        if (!is_null($this->providers[$provider->getId()])) {
            throw new common_Exception('Attempting to add unreserved Id '.$provider->getId());
        }
        $this->providers[$provider->getId()] = $provider;
        $this->saveconfig();
    }
    
    public function removeProvider($provider) {
        if (!isset($this->providers[$provider->getId()])) {
            throw new common_Exception('Attempting to remove inexistent '.$provider->getId());
        }
        unset($this->providers[$provider->getId()]);
        $this->saveconfig();
    }
    
    private function saveconfig() {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        $data = array();
        foreach ($this->providers as $provider) {
            if (is_null($provider)) {
                throw new common_Exception('Reserved provider missing');
            }
            $data[] = $provider->serializeToString();
        }
        $config = $ext->setConfig(self::CONFIG_KEY, $data);
        
    }
    
    public function reserveId() {
        $id = empty($this->providers) ? 0 : max(array_keys($this->providers)) + 1;
        $this->providers[$id] = null;
        return $id;
    }
}