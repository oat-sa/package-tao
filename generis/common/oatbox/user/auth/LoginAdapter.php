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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\oatbox\user\auth;

use common_user_auth_Adapter;

/**
 * Authentication adapter interface for login/password based authentication adapters
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 */
interface LoginAdapter extends common_user_auth_Adapter
{
    /**
     * Create an Adapter from a configuration
     * 
     * @param array $configuration
     */
    public function setOptions(array $options);
    
    /**
     * Adapter must be able to store the login and password of the potential user
     * 
     * @param string $login
     * @param string $password
     */
    public function setCredentials($login, $password);

}