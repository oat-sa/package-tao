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
 * This is the base class of the Access Providers
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
abstract class tao_models_classes_fsAccess_AccessProvider
{
	/**
	 * Filesystem that is being made available
	 * 
	 * @var core_kernel_fileSystem_FileSystem
	 */
	private $fileSystem = null;
	
	/**
	 * Identifier of the Access Provider 
	 * 
	 * @var string
	 */
	private $id;
	
	/**
	 * Used to instantiate new AccessProviders
	 * 
	 * @param core_kernel_fileSystem_FileSystem $fileSystem
	 * @param unknown $customConfig
	 * @return tao_models_classes_fsAccess_AccessProvider
	 */
	protected static function spawn(core_kernel_fileSystem_FileSystem $fileSystem, $customConfig = array()) {
	    $id = tao_models_classes_fsAccess_Manager::singleton()->reserveId();
	    $provider = new static($id, $fileSystem);
	    $provider->restoreConfig($customConfig);
	    tao_models_classes_fsAccess_Manager::singleton()->addProvider($provider);
	    return $provider;
	}
	
	/**
	 * Restore an AccessProvider from a configurationString, called by Manager
	 * 
	 * @param string $string
	 * @return tao_models_classes_fsAccess_AccessProvider
	 */
	public static function restoreFromString($string) {
	     list($class, $id, $fsUri, $config) = explode(' ', $string, 4);
	     $provider = new $class($id, new core_kernel_fileSystem_FileSystem($fsUri));
	     $provider->restoreConfig(json_decode($config, true));
	     return $provider;
	}
	
	/**
	 * Private constructor in order to prevent direct instantiation, please us
	 * spawn() to instantiate new instances
	 * 
	 * @param string $id
	 * @param core_kernel_fileSystem_FileSystem $fileSystem
	 */
	private function __construct($id, core_kernel_fileSystem_FileSystem $fileSystem) {
	    $this->id = $id;
	    $this->fileSystem = $fileSystem;
	}

	/**
	 * Filesystem made available by this Access Provider
	 * 
	 * @return core_kernel_fileSystem_FileSystem
	 */
	public function getFileSystem() {
	    return $this->fileSystem;
	}

	/**
	 * Return the identifer of the AccessProvider
	 * 
	 * @return string
	 */
	public function getId() {
	    return $this->id;
	}
	
	/**
	 * Delete an AccessProvider and remove it from the manager
	 */
	public function delete() {
	    tao_models_classes_fsAccess_Manager::singleton()->removeProvider($this);
	}
	
	
	/**
	 * Serialize the AccessProvider to a string
	 * 
	 * @return string
	 */
	public function serializeToString() {
	    // does not use php serialize anymore to prevent configuration files being interpreted as binary (NULL bytes) 
	    return get_class($this).' '.$this->getId().' '.$this->getFileSystem()->getUri().' '.json_encode($this->getConfig());
	}
	
	/**
	 * Returns a configuration array that can be used to restore the Access Provider 
	 * used by serializeToString()
	 * 
	 * @return array
	 */
	protected abstract function getConfig();

	/**
	 * Restores an Access Provider from a configuration array provided by getConfig()
	 * 
	 * @param array $config
	 */
	protected abstract function restoreConfig($config);
	
	/**
	 * Returns an URL that can be used to acces the resource specified by relativePath
	 * 
	 * @param string $relativePath
	 * @return string URL to the resource
	 */
	public abstract function getAccessUrl($relativePath);
	
}