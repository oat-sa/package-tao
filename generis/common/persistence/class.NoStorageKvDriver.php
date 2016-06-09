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
* @author Lionel Lecaque  <lionel@taotesting.com>
* @license GPLv2
*
*/

class common_persistence_NoStorageKvDriver implements common_persistence_KvDriver, common_persistence_Purgable
{
    /**
     * 
     * @see common_persistence_Driver::connect()
     */
    function connect($id, array $params){
        common_Logger::t('Init NoStorageKvPersistence');
        return new common_persistence_KeyValuePersistence($params, $this);
    }
    /**
     * 
     * @see common_persistence_KvDriver::set()
     */
    public function set($id, $value, $ttl = null){
        common_Logger::t('NoStorageKvPersistence : set id=' . $id);
        return false;
    }
    
    /**
     * 
     * @see common_persistence_KvDriver::get()
     */
    public function get($id){
        common_Logger::t('NoStorageKvPersistence : get id=' . $id );
        return false;
    }
    
    /**
     * 
     * @see common_persistence_KvDriver::exists()
     */
    public function exists($id){
        return false;
        
    }
    /**
     * 
     * @see common_persistence_KvDriver::del()
     */
    public function del($id){
        common_Logger::t('NoStorageKvPersistence : delete id=' . $id );
        return true;
        
    }
    /**
     * 
     * @see common_persistence_Purgable::purge()
     */
    public function purge(){
        common_Logger::t('NoStorageKvPersistence is purged');
        return true;
        
    }
}

?>