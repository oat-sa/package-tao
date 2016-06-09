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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\oatbox\filesystem;

use oat\oatbox\service\ConfigurableService;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use common_exception_Error;
 /**
 * A service to reference and retrieve filesystems
 */
class FileSystemService extends ConfigurableService
{
    const SERVICE_ID = 'generis/filesystem';
    
    const OPTION_FILE_PATH = 'filesPath';
    
    const OPTION_ADAPTERS = 'adapters';
    
    const FLYSYSTEM_ADAPTER_NS = '\\League\\Flysystem\\Adapter\\';
    
    const FLYSYSTEM_LOCAL_ADAPTER = 'Local';
    
    private $filesystems = array();
    
    /**
     * 
     * @param unknown $id
     * @return Filesystem
     */
    public function getFileSystem($id)
    {
        if (!isset($this->filesystems[$id])) {
            $this->filesystems[$id] = new Filesystem($this->getFlysystemAdapter($id));
        }
        return $this->filesystems[$id];
    }
    
    /**
     * Create a new local file system
     * 
     * @param string $id
     * @return Filesystem
     */
    public function createLocalFileSystem($id)
    {
        $path = $this->getOption(self::OPTION_FILE_PATH).\helpers_File::sanitizeInjectively($id);
        $this->registerLocalFileSystem($id, $path);
        return $this->getFileSystem($id);
    }
    
    /**
     * Registers a local file system, used for transition
     * 
     * @param string $id
     * @param string $path
     * @return boolean
     */
    public function registerLocalFileSystem($id, $path)
    {
        $adapters = $this->hasOption(self::OPTION_ADAPTERS) ? $this->getOption(self::OPTION_ADAPTERS) : array();
        $adapters[$id] = array(
            'class' => self::FLYSYSTEM_LOCAL_ADAPTER,
            'options' => array('root' => $path)
        );
        $this->setOption(self::OPTION_ADAPTERS, $adapters);
        return true;
    }

    /**
     * Remove a filesystem adapter
     * 
     * @param string $id
     * @return boolean
     */
    public function unregisterFileSystem($id)
    {
        $adapters = $this->getOption(self::OPTION_ADAPTERS);
        if (isset($adapters[$id])) {
            unset($adapters[$id]);
            if (isset($this->filesystems[$id])) {
                unset($this->filesystems[$id]);
            }
            $this->setOption(self::OPTION_ADAPTERS, $adapters);
            return true;
        } else {
            return false;
        }
    }

    /**
     * inspired by burzum/storage-factory
     * 
     * @param string $id
     * @throws common_Exception
     * @return AdapterInterface
     */
    protected function getFlysystemAdapter($id)
    {
        $fsConfig = $this->getOption(self::OPTION_ADAPTERS);
        if (!isset($fsConfig[$id])) {
            throw new common_exception_Error('Undefined filesystem "'.$id.'"');
        }
        $adapterConfig = $fsConfig[$id];
        // alias?
        while (is_string($adapterConfig)) {
            $adapterConfig = $fsConfig[$adapterConfig];
        }
        $class = $adapterConfig['class'];
        $options = isset($adapterConfig['options']) ? $adapterConfig['options'] : array();
        
        if (!class_exists($class)) {
            if (class_exists(self::FLYSYSTEM_ADAPTER_NS.$class)) {
                $class = self::FLYSYSTEM_ADAPTER_NS.$class;
            } elseif (class_exists(self::FLYSYSTEM_ADAPTER_NS.$class.'\\'.$class.'Adapter')) {
                $class = self::FLYSYSTEM_ADAPTER_NS.$class.'\\'.$class.'Adapter';
            } else {
                throw new common_exception_Error('Unknown Flysystem adapter "'.$class.'"');
            }
        }
        
        if (!is_subclass_of($class, 'League\Flysystem\AdapterInterface')) {
            throw new common_exception_Error('"'.$class.'" is not a flysystem adapter');
        }
        return (new \ReflectionClass($class))->newInstanceArgs($options);
    }
}
