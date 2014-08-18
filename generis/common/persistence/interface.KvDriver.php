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
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 * @package generis
 
 *
 */

/**
 * Interface of drivers that provide the Kay Value Persistence 
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
interface common_persistence_KvDriver extends common_persistence_Driver
{

    /**
     * Stores a value, implementing time to live is optional
     * 
     * @param string $id
     * @param string $value
     * @param string $ttl
     * @return boolean
     */
    public function set($id, $value, $ttl = null);

    /**
     * Returns a value from storage
     * or false if not found
     * 
     * @param string $id
     * @return string
     */
    public function get($id);
    
    /**
     * test whenever or not an entry exists
     * 
     * @param string $id
     * @return boolean
     */
    public function exists($id);

    /**
     * Remove an  entry from storage
     * 
     * @param string $id
     * @return boolean
     */
    public function del($id);

}