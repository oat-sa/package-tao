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
class common_persistence_KeyValuePersistence extends common_persistence_Persistence
{
    
    public function set($key, $value, $ttl = null)
    {
        return $this->getDriver()->set($key, $value, $ttl);
    }
    
    /**
     * Returns the value stored for the identifier $key
     * or FALSE if nothing found
     * 
     * @param string $key
     */
    public function get($key) {
        return $this->getDriver()->get($key);
    }
    
    public function exists($key) {
        return $this->getDriver()->exists($key);
    }
    
    public function del($key) {
        return $this->getDriver()->del($key);
    }
    
    public function purge() {
        if ($this->getDriver() instanceof common_persistence_Purgable) {
            return $this->getDriver()->purge();
        } else {
            throw new common_exception_NotImplemented("purge not implemented ");
        }
    }
    
}