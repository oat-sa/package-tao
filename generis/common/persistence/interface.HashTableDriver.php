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
 * @author Patrick Plichart  <patrick@taotesting.com>
 * @license GPLv2
 * @package generis
 *
 */

/**
 * Interface of drivers that provide hash possibilities 
 * 
 * @author Patrick Plichart <patrick@taotesting.com>
 * @author Joel Bout <joel@taotesting.com>
 */
interface common_persistence_HashTableDriver extends common_persistence_Driver
{
    /**
     * Sets multiple fields in a single operation
     * 
     * @param string $key
     * @param array $fields
     * @return boolean
     */
    public function hmSet($key, $fields);
    
    /**
     * Checks whenever a given field exists for a given key
     * 
     * @param string $key
     * @param string $field
     * @return boolean
     */
    public function hExists($key, $field);
    
    /**
     * Sets the value of a field
     * 
     * @param string $key
     * @param string $field
     * @param string $value
     * @return boolean
     */
    public function hSet($key, $field, $value);
    
    /**
     * gets the value of a field
     * return false if not found
     * 
     * @param string $key
     * @param string $field
     * @return string
     */
    public function hGet($key, $field);
    
    /**
     * Get all fields of the Hashtable
     * return false if not found
     * 
     * @param string $key
     * @return array
     */
    public function hGetAll($key);
}