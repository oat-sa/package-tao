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
use oat\oatbox\Configurable;

/**
 * Session implementation as a Key Value storage and using the persistence
 * 
 * @author Joel Bout <joel@taotesting.com>
 * @package generis
 */
class common_session_php_KeyValueSessionHandler extends Configurable
    implements common_session_php_SessionHandler
{
    const OPTION_PERSISTENCE = 'persistence'; 
    
    const KEY_NAMESPACE = "generis:session:";
    
    /**
     * @var common_persistence_KeyValuePersistence
     */
    private $sessionPersistence = null;
    
    protected function getPersistence() {
        if (is_null($this->sessionPersistence)) {
            $this->sessionPersistence = common_persistence_KeyValuePersistence::getPersistence($this->getOption(self::OPTION_PERSISTENCE));
        }
        return $this->sessionPersistence;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::open()
     */
    public function open($savePath, $sessionName){
           return true;
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::close()
     */
    public function close()
    {
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::read()
     */
    public function read($id)
    {
        return $this->getPersistence()->get(self::KEY_NAMESPACE.$id);
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::write()
     */
    public function write($id, $data)
    {  
        return $this->getPersistence()->set(self::KEY_NAMESPACE.$id, $data, (int) ini_get('session.gc_maxlifetime'));
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::destroy()
     */
    public function destroy($id){
        $this->getPersistence()->del(self::KEY_NAMESPACE.$id);
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::gc()
     */
    public function gc($maxlifetime)
    { 
        //
        //problem here either 
        // solution 1 : do two explicit handlers for each specific persistence (Redis, SQL) 
        // solution 2 : Check if the eprsistence is capable of autonomous garbage  
        //
    }
}