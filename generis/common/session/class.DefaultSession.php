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
 * Represents a Session on Generis.
 *
 * @access private
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 * @subpackage common_session
 */
class common_session_DefaultSession implements common_session_StatefulSession
{
    /**
     * @var common_user_User
     */
    private $user;
    
    public function __construct(common_user_User $user) {
        $this->user = $user;
    }
    
    protected function getUser() {
        return $this->user;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_AbstractSession::getUserUri()
     */
    public function getUserUri() {
        return $this->user->getIdentifier();
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getUserLabel()
     */
    public function getUserLabel() {
        $label = '';
        $first = $this->user->getPropertyValues(PROPERTY_USER_FIRSTNAME);
        $label .= empty($first) ? '' : current($first);
        $last = $this->user->getPropertyValues(PROPERTY_USER_LASTNAME);
        $label .= empty($last) ? '' : ' '.current($last);
        $label = trim($label);
        if (empty($label)) {
            $login = $this->user->getPropertyValues(PROPERTY_USER_LOGIN);
            if (!empty($login)) {
                $label = current($login);
            }
        }
        if (empty($label)) {
            $rdflabel = $this->user->getPropertyValues(RDFS_LABEL);
            $label =  empty($rdflabel) ? __('user') : current($rdflabel);
        }
        return $label;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_AbstractSession::getUserRoles()
     */
    public function getUserRoles() {
        $returnValue = array();
        // We use a Depth First Search approach to flatten the Roles Graph.
        foreach ($this->user->getPropertyValues(PROPERTY_USER_ROLES) as $roleUri){
            $returnValue[$roleUri] = new core_kernel_classes_Resource($roleUri);
            foreach (core_kernel_users_Service::singleton()->getIncludedRoles($returnValue[$roleUri]) as $role) {
                $returnValue[$role->getUri()] = $role;
            }
        }
//        var_dump($returnValue);
        return $returnValue;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getDataLanguage()
     */
    public function getDataLanguage() {
        $lang = $this->user->getPropertyValues(PROPERTY_USER_DEFLG);
        return empty($lang) ? DEFAULT_LANG : current($lang);
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getInterfaceLanguage()
     */
    public function getInterfaceLanguage() {
        $lang = $this->user->getPropertyValues(PROPERTY_USER_UILG);
        return empty($lang) ? DEFAULT_LANG : current($lang);
    }
    
    public function refresh() {
        $this->user->refresh();
    }    
}