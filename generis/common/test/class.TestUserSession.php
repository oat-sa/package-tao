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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * @author CRP Henri Tudor - TAO Team
 * @license GPLv2
 *
 */

class common_test_TestUserSession implements common_session_StatelessSession
{
    /**
     * Code of the current data language to use
     * 
     * @var string
     */
    private $dataLanguage = DEFAULT_LANG;
    
    private $uiLanguage = DEFAULT_LANG;
    
    public function __construct() {
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_AbstractSession::getUserUri()
     */
    public function getUserUri() {
        return LOCAL_NAMESPACE.'virtualTestUser';
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getUserLabel()
     */
    public function getUserLabel() {
        return 'Virtual Test User';
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_AbstractSession::getUserRoles()
     */
    public function getUserRoles() {
        return array();
    }
    
    public function setDataLanguage($languageCode) {
        $this->dataLanguage = $languageCode;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getDataLanguage()
     */
    public function getDataLanguage() {
        return $this->dataLanguage;
    }
    
    public function setInterfaceLanguage($languageCode) {
        $this->uiLanguage = $languageCode;
    }
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getInterfaceLanguage()
     */
    public function getInterfaceLanguage() {
        $lang = $this->uiLanguage;
    }
    
    public function refresh() {
        // nothign to do
    }
}