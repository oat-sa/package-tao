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
 * Represents a Session.
 *
 * @access private
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 */
interface common_session_Session
{

    /**
     * Get the user of the session
     * 
     * Returns null if there is no user
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return oat\oatbox\user\User
     */
    public function getUser();
    
    /**
     * Get the URI identifying the currently authenticated user in persistent memory.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function getUserUri();

    /**
     * A string representation of the current user. Might not be unique
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function getUserLabel();
    
    /**
     * returns the roles of the current user
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return array An array of strings
     */
    public function getUserRoles();

    /**
     * returns the language identifier to use for data
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function getDataLanguage();
    
    /**
     * returns the language identifier to use for the interface
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function getInterfaceLanguage();
    
    /**
     * returns the timezone to use for times
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function getTimeZone();
    
    /**
     * Generic information retrieval of user data
     * 
     * @param string $property
     * @return array
     */
    public function getUserPropertyValues($property);
    
    /**
     * refreshes the information stored in the current session
     */
    public function refresh();
    
}