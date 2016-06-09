<?php
use oat\oatbox\user\LoginService;
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
 * 
 */

/**
 * HTTP Authentication implementation of RFC 2617 (http://tools.ietf.org/html/rfc2617)
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_models_classes_HttpBasicAuthAdapter
	implements common_user_auth_Adapter
{
    /**
     * 
     * @var common_http_Request
     */
	private $request;
	
	/**
	 * Creates an Authentication adapter from an OAuth Request
	 * 
	 * @param common_http_Request $request
	 */
	public function __construct(common_http_Request $request) {
	    $this->request = $request;
	}
	
	/**
     * (non-PHPdoc)
     * @see common_user_auth_Adapter::authenticate()
     */
    public function authenticate() {
    	
        //$headers = $this->request->getHeaders();
        if (!(isset($_SERVER['PHP_AUTH_USER'])) or ($_SERVER['PHP_AUTH_USER']=="")){
            throw new \oat\oatbox\user\LoginFailedException(array('Rest (Basic) login failed for user (missing login/password)'));
        }
        
        return LoginService::authenticate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
    }
}