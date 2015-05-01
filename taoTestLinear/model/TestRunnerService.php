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
 *
 */

namespace oat\taoTestLinear\model;


use core_kernel_classes_Class;

/**
 * TestRunner service to get data of the test
 *
 * @access public
 * @author Antoine Robin, <antoine.robin@vesperiagroup.com>
 * @package taoTestLinear
 */
class TestRunnerService extends \tao_models_classes_ClassService{

    //volatile
    private $itemDataCache = null;

    private $previousCache = null;

    public function getItemData($compilationId) {
        if (!isset($this->itemDataCache[$compilationId])) {
            $filePath = \tao_models_classes_service_FileStorage::singleton()->getDirectoryById($compilationId)->getPath().'data.json';
            $json = file_get_contents($filePath);
            $items = json_decode($json, true);
            if (!is_array($items)) {
                throw new \common_exception_Error('Unable to load compilation data for '.$compilationId);
            }

            if(isset($items['items'])){
                $items = $items['items'];
            }

            $this->itemDataCache[$compilationId] = $items;
        }
        return $this->itemDataCache[$compilationId];
    }

    public function getPrevious($compilationId){
        if(!isset($this->previousCache[$compilationId])){
            $previous = false;

            $filePath = \tao_models_classes_service_FileStorage::singleton()->getDirectoryById($compilationId)->getPath().'data.json';
            $json = file_get_contents($filePath);
            $config = json_decode($json, true);
            if (!is_array($config)) {
                throw new \common_exception_Error('Unable to load compilation data for '.$compilationId);
            }
            if(isset($config['previous'])){
                $previous = $config['previous'];
            }

            $this->previousCache[$compilationId] = $previous;
        }
        return $this->previousCache[$compilationId];
    }


    /**
     * Returns the root class of this service
     *
     * @return core_kernel_classes_Class
     */
    public function getRootClass()
    {
        return new core_kernel_classes_Class(CLASS_SIMPLE_DELIVERYCONTENT);
    }
}