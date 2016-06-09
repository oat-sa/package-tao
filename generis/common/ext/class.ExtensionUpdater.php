<?php
use oat\oatbox\service\ServiceManager;
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *			   2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class common_ext_ExtensionInstaller
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package generis
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 
 */
abstract class common_ext_ExtensionUpdater
	extends common_ext_ExtensionHandler
{
    /**
     * 
     * @param string $currentVersion
     * @return string $versionUpdatedTo
     */
    public abstract function update($initialVersion);
    
    /**
     * Temporary helper untill the service manager
     * gets properly injected into the update scripts
     * 
     * @return \oat\oatbox\service\ServiceManager
     */
    public function getServiceManager()
    {
        return ServiceManager::getServiceManager();
    }
    
    /**
     * Update the current version of the extension to the provided version
     * Ensures that a successfull update doesn't get executed twice
     * 
     * @param string $version
     */
    public function setVersion($version)
    {
        common_ext_ExtensionsManager::singleton()->updateVersion($this->getExtension(), $version);
    }
    
    /**
     * Test if $version is the current version
     * 
     * @param string $version
     * @return boolean
     */
    public function isVersion($version)
    {
        return $version == common_ext_ExtensionsManager::singleton()->getInstalledVersion($this->getExtension()->getId());
    }
    
    /**
     * Please use "skip" instead of inBetween.
     * 
     * @param string $minVersion
     * @param string $maxVersion
     * @return boolean
     * @deprecated
     */
    public function isBetween($minVersion, $maxVersion)
    {
        $current = common_ext_ExtensionsManager::singleton()->getInstalledVersion($this->getExtension()->getId());
        return version_compare($minVersion, $current, '<=') && version_compare($current, $maxVersion, '<=');
    }
    
    /**
     * Skip from version FROM to version TO without additional required actions
     * 
     * @param string $from
     * @param string $to
     */
    public function skip($from, $to)
    {
        $current = common_ext_ExtensionsManager::singleton()->getInstalledVersion($this->getExtension()->getId());
        if (version_compare($from, $current, '<=') && version_compare($current, $to, '<')) {
            $this->setVersion($to);
        }
    }
    
    /**
     * Loads a service in a "safe" way, trying to convert
     * unknown classes to abstract services
     * 
     * @param string $configId
     * @return 
     */
    public function safeLoadService($configId)
    {
        /**
         * Inline autoloader that will construct a new class based on ConfigurableService
         * @param string $class_name
         */
        $missingClasses = array();
        
        $fallbackAutoload = function($class_name) use (&$missingClasses) {
            $missingClasses[] = $class_name;
            $split = strrpos($class_name, '\\');
            if ($split == false) {
                $result = eval('class '.$class_name.' extends oat\\oatbox\\service\\ConfigurableService {}');
            } else {
                $namespace = substr($class_name, 0, $split);
                $class = substr($class_name, $split+1);
                eval('namespace '.$namespace.'; '.'class '.$class.' extends \\oat\\oatbox\\service\\ConfigurableService {}');
            }
        };
        $serviceManager = $this->getServiceManager();
        spl_autoload_register($fallbackAutoload);
        $service = $serviceManager->get($configId);
        spl_autoload_unregister($fallbackAutoload);
        
        return $service;
    }
}
