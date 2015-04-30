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
 * @author Patrick Plichart <patrick@taotesting.com>
 * @license GPLv2
 * @package generis
 
 *
 */
class common_persistence_AdvKeyValuePersistence extends common_persistence_KeyValuePersistence
{
    
    //O(N) where N is the number of fields being set.
    public function hmSet($key, $fields) {
        return $this->getDriver()->hmSet($key, $fields);
    }
    
    //Time complexity: O(1)
    public function hExists($key, $field){
        return (bool) $this->getDriver()->hExists($key, $field);
    }
    //Time complexity: O(1)
    public function hSet($key, $field, $value){
        return $this->getDriver()->hSet($key, $field, $value);
    }
    //Time complexity: O(1)
    public function hGet($key, $field){
        return $this->getDriver()->hGet($key, $field);
    }
    //Time complexity: O(N) where N is the size of the hash.
    public function hGetAll($key){
        return $this->getDriver()->hGetAll($key);
    }
    //o(n)
    public function keys($pattern) {
        return $this->getDriver()->keys($pattern);
    }

    public function incr($key) {
       return $this->getDriver()->incr($key); 
    }
    
    
}
