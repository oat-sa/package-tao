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

/**
 * 
 * RestrictedSession allows a user to reduce his own access roles
 * This can be used to test the interface
 *
 * @access private
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 
 */
class common_session_RestrictedSession implements common_session_StatefulSession
{
    /**
     * real user session that will be restricted
     * 
     * @var common_session_Session
     */
    private $internalSession;
    
    /**
     * filter on the users roles
     * 
     * @var array
     */
    private $filter;
    
    /**
     * 
     * @param common_session_Session $session
     * @param array $filter
     */
    public function __construct(common_session_Session $session, $filter) {
        $this->internalSession = $session;
        $this->filter = $filter;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getUser()
     */
    public function getUser() {
        return $this->internalSession->getUser();
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_AbstractSession::getUserUri()
     */
    public function getUserUri() {
        return $this->internalSession->getUserUri();
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getUserLabel()
     */
    public function getUserLabel() {
        return $this->internalSession->getUserLabel();
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_AbstractSession::getUserRoles()
     */
    public function getUserRoles() {
        $returnValue = $this->internalSession->getUserRoles();
        foreach (array_keys($returnValue) as $key) {
            $role = $returnValue[$key];
            if (!in_array($role, $this->filter)) {
                unset($returnValue[$key]);
            }
        }
        return $returnValue;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getDataLanguage()
     */
    public function getDataLanguage() {
        return $this->internalSession->getDataLanguage();
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getInterfaceLanguage()
     */
    public function getInterfaceLanguage() {
        return $this->internalSession->getInterfaceLanguage();
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getUserPropertyValues()
     */
    public function getUserPropertyValues($property) {
        return $this->internalSession->getUserPropertyValues($property);
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getTimeZone()
     */
    public function getTimeZone() {
        return $this->internalSession->getTimeZone();
    }
    
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::refresh()
     */
    public function refresh() {
        $this->internalSession->refresh();
    }
    
    /**
     * Revert back to the original Session
     */
    public function restoreOriginal() {
        common_session_SessionManager::startSession($this->internalSession);
    }
}