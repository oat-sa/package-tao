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
 */
namespace oat\taoDeliveryRdf\model;

use tao_models_classes_service_FileStorage;
/**
 * A wrapper of the filestorage that tracks added directories
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 
 */
 class TrackedStorage extends tao_models_classes_service_FileStorage
{
    private $ids = array();

    private $storage;
    
    public function __construct() {
        $this->storage = tao_models_classes_service_FileStorage::singleton();
    }
    
    /**
     * @param boolean $public
     * @return tao_models_classes_service_StorageDirectory
     */
    public function spawnDirectory($public = false) {
        $directory = $this->storage->spawnDirectory($public);
        $this->ids[] = $directory->getId();
        return $directory;
    }
    
    public function getSpawnedDirectoryIds() {
        return $this->ids;
    }
}