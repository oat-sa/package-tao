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
 */

namespace oat\taoQtiTest\models;

/**
 * Manage the flagged items
 */
class ExtendedStateService
{
    const STORAGE_PREFIX = 'extra_';
    
    private $cache = null;

    /**
     * Retrieve extended state informations
     * @param string $testSessionId
     * @throws \common_Exception
     * @return array
     */
    protected function getExtra($testSessionId)
    {
        if (!isset($this->cache[$testSessionId])) {
            $storageService = \tao_models_classes_service_StateStorage::singleton();
            $userUri = \common_session_SessionManager::getSession()->getUserUri();
        
            $data = $storageService->get($userUri, self::STORAGE_PREFIX.$testSessionId);
            if ($data) {
                $data = json_decode($data, true);
                if (is_null($data)) {
                    throw new \common_exception_InconsistentData('Unable to decode extra for test session '.$testSessionId);
                }
            } else {
                $data = array(
                	'review' => array()
                );
            }
            $this->cache[$testSessionId] = $data;
        }
        return $this->cache[$testSessionId];
    }
    
    /**
     * Store extended state informations
     * @param string $testSessionId
     * @param array $extra
     */
    protected function saveExtra($testSessionId, $extra)
    {
        $this->cache[$testSessionId] = $extra;
        $storageService = \tao_models_classes_service_StateStorage::singleton();
        $userUri = \common_session_SessionManager::getSession()->getUserUri();
    
        $storageService->set($userUri, self::STORAGE_PREFIX.$testSessionId, json_encode($extra));
    }
    
    /**
     * Set the marked for review state of an item
     * @param string $sessionId
     * @param string $itemRef
     * @param boolean $flag
     */
    public function setItemFlag($sessionId, $itemRef, $flag) {
        $extra = $this->getExtra($sessionId);
        $extra['review'][$itemRef] = $flag;
        $this->saveExtra($sessionId, $extra);
    }
    
    /**
     * Gets the marked for review state of an item
     * @param string $session
     * @param string $itemRef
     * @return bool
     * @throws common_exception_InconsistentData
     */
    public function getItemFlag($testSessionId, $itemRef) {
        $extra = $this->getExtra($testSessionId);
        return isset($extra['review'][$itemRef])
            ? $extra['review'][$itemRef]
            : false;
    }
}