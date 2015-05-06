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
 * 
 */

/**
 * HTTP Authentication implementation of RFC 2617 (http://tools.ietf.org/html/rfc2617)
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_models_classes_HttpDigestAuthAdapter
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
    	
        throw new common_exception_NotImplemented();
        
        $digest = tao_helpers_Http::getDigest();
        $data = tao_helpers_Http::parseDigest($digest);
        //store the hash A1 as a property to be updated on register/changepassword
        $trialLogin = 'admin'; $trialPassword = 'admin';
        $A1 = md5($trialLogin . ':' . $this::realm . ':' . $trialPassword);
        $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
        $valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);
    }
    
    
}