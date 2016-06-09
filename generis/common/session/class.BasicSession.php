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
 
 */
use oat\oatbox\user\User;
use oat\oatbox\Refreshable;

class common_session_BasicSession implements common_session_Session
{
    /**
     * @var common_user_User
     */
    private $user;
    
    public function __construct(User $user) {
        $this->user = $user;
    }
    
    public function getUser() {
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
     * @param string $property
     * @return mixed
     */
    public function getUserPropertyValues($property) {
        return $this->user->getPropertyValues($property);
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
            $returnValue[$roleUri] = $roleUri;
            $role = new core_kernel_classes_Resource($roleUri);
            foreach (core_kernel_users_Service::singleton()->getIncludedRoles($role) as $incRole) {
                $returnValue[$incRole->getUri()] = $incRole->getUri();
            }
        }
        return $returnValue;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getDataLanguage()
     */
    public function getDataLanguage() {
        $lang = $this->user->getPropertyValues(PROPERTY_USER_DEFLG);
        return empty($lang) ? DEFAULT_LANG : (string)current($lang);
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getInterfaceLanguage()
     */
    public function getInterfaceLanguage() {
        $lang = $this->user->getPropertyValues(PROPERTY_USER_UILG);
        return empty($lang) ? DEFAULT_LANG : (string)current($lang);
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getTimeZone()
     */
    public function getTimeZone() {
        $tzs = $this->user->getPropertyValues(PROPERTY_USER_TIMEZONE);
        $tz = empty($tzs) ? '' : (string)current($tzs);
        return empty($tz) ? TIME_ZONE : $tz;
    }
    
    public function refresh() {
        if( $this->user instanceof Refreshable ){
            $this->user->refresh();
        }
    }
}