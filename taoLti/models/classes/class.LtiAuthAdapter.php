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
 * Authentication adapter interface to be implemented by authentication methodes
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoLti
 
 */
class taoLti_models_classes_LtiAuthAdapter
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
    	
        $service = new tao_models_classes_oauth_Service();
        try {
            $service->validate($this->request);
        	$ltiLaunchData = taoLti_models_classes_LtiLaunchData::fromRequest($this->request);
        	return new taoLti_models_classes_LtiUser($ltiLaunchData);
        } catch (common_http_InvalidSignatureException $e) {
            throw new taoLti_models_classes_LtiException('Invalid LTI signature');
        }
    }
}