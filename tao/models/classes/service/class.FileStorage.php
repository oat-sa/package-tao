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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

use oat\tao\model\websource\WebsourceManager;
/**
 * Represents the file storage used in services 
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_models_classes_service_FileStorage
{
    const CONFIG_KEY = 'ServiceFileStorage';
    
    /**
     * @var tao_models_classes_service_FileStorage
     */
    private static $instance;
    
    /**
     * @return tao_models_classes_service_FileStorage
     */
    public static function singleton() {
        if (is_null(self::$instance)) {
            $config = common_ext_ExtensionsManager::singleton()->getExtensionById('tao')->getConfig(self::CONFIG_KEY);
            $privateFs = new core_kernel_fileSystem_FileSystem($config['private']);
            $publicFs = new core_kernel_fileSystem_FileSystem($config['public']);
            $accessProvider = WebsourceManager::singleton()->getWebsource($config['provider']);
            self::$instance = new self($privateFs, $publicFs, $accessProvider);
        }
        
        return self::$instance;
    }
    
    /**
     * @var core_kernel_fileSystem_FileSystem
     */
    private $publicFs;
    
    private $privateFs;
    
    private $accessProvider;
    
    private function __construct(core_kernel_fileSystem_FileSystem $private, core_kernel_fileSystem_FileSystem $public, $provider) {
        $this->privateFs = $private;
        $this->publicFs = $public;
        $this->accessProvider = $provider;
    }
    
    /**
     * @param boolean $public
     * @return tao_models_classes_service_StorageDirectory
     */
    public function spawnDirectory($public = false) {
        $id = common_Utils::getNewUri().($public ? '+' : '-');
        $directory = $this->getDirectoryById($id);
        mkdir($directory->getPath(), 0700, true);
        return $directory;
    }

    /**
     * @param string $id
     * @return tao_models_classes_service_StorageDirectory
     */
    public function getDirectoryById($id) {
        $public = $id[strlen($id)-1] == '+';
        $fs = $public ? $this->publicFs : $this->privateFs;
        $path = $this->id2path($id);
        return new tao_models_classes_service_StorageDirectory($id, $fs, $path, $public ? $this->accessProvider : null);
    }
    
    public function import($id, $directoryPath) {
        $directory = $this->getDirectoryById($id);
        if (file_exists($directory->getPath())) {
            if(tao_helpers_File::isDirEmpty($directory->getPath())){
                common_Logger::d('Directory already found but content is empty');
                helpers_File::copy($directoryPath, $directory->getPath(), true);
                
            }else if (tao_helpers_File::isIdentical($directory->getPath(), $directoryPath)) {
                common_Logger::d('Directory already found but content is identical');
            } else {
                throw new common_Exception('Duplicate dir '.$id.' with different content');
            }
        } else {
            mkdir($directory->getPath(), 0700, true);
            helpers_File::copy($directoryPath, $directory->getPath(), true);
        }
    }
    
    private function id2path($id) {

        $encoded = md5($id);
        $returnValue = "";
        $len = strlen($encoded);
        for ($i = 0; $i < $len; $i++) {
            if ($i < 3) {
                $returnValue .= $encoded[$i].DIRECTORY_SEPARATOR;
            } else {
                $returnValue .= $encoded[$i];
            }
        }
        
        return $returnValue.DIRECTORY_SEPARATOR;
    }
    
    public static function configure(core_kernel_fileSystem_FileSystem $private, core_kernel_fileSystem_FileSystem $public, $provider) {
        common_ext_ExtensionsManager::singleton()->getExtensionById('tao')->setConfig(self::CONFIG_KEY, array(
        	'private' => $private->getUri(),
            'public' => $public->getUri(),
            'provider' => $provider->getId()
        ));
    }
}