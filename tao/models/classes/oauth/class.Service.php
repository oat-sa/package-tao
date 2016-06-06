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
 * Copyright (c) 2013 (original work) (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Includes the Oauth library 
 */
require_once dirname(__FILE__).'/../../../lib/oauth/OAuth.php';

/**
 * Oauth Services based on the TAO DataStore implementation
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_models_classes_oauth_Service
    implements common_http_SignatureService
{
    /**
     * Adds a signature to the request
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param $authorizationHeader Move the signature parameters into the Authorization header of the request
     */
    public function sign(common_http_Request $request, common_http_Credentials $credentials, $authorizationHeader = false) {
        
        if (!$credentials instanceof tao_models_classes_oauth_Credentials) {
            throw new tao_models_classes_oauth_Exception('Invalid credentals: '.gettype($credentials));
        }
        
        
        $oauthRequest = $this->getOauthRequest($request); 
        $dataStore = new tao_models_classes_oauth_DataStore();
        $consumer = $dataStore->getOauthConsumer($credentials);
        $token = $dataStore->new_request_token($consumer);

        $allInitialParameters = array();
        $allInitialParameters = array_merge($allInitialParameters, $request->getParams());
        $allInitialParameters = array_merge($allInitialParameters, $request->getHeaders());
        
        //oauth_body_hash is used for the signing computation
        if ($authorizationHeader) {
        $oauth_body_hash = base64_encode(sha1($request->getBody(), true));//the signature should be ciomputed from encoded versions
        $allInitialParameters = array_merge($allInitialParameters, array("oauth_body_hash" =>$oauth_body_hash));
        }

        //$authorizationHeader = self::buildAuthorizationHeader($signatureParameters);
        $signedRequest = OAuthRequest::from_consumer_and_token(
            $consumer,
            $token,
            $oauthRequest->get_normalized_http_method(),
            $oauthRequest->getUrl(),
            $allInitialParameters
        );
        $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
        //common_logger::d('Base string: '.$signedRequest->get_signature_base_string());
        $signedRequest->sign_request($signature_method, $consumer, $token);
        common_logger::d('Base string from TAO/Joel: '.$signedRequest->get_signature_base_string());

        if ($authorizationHeader) {
            $combinedParameters = $signedRequest->get_parameters();
            $signatureParameters = array_diff_assoc($combinedParameters, $allInitialParameters);
           
            $signatureParameters["oauth_body_hash"] = base64_encode(sha1($request->getBody(), true));
            $signatureHeaders = array("Authorization" => self::buildAuthorizationHeader($signatureParameters));
            $signedRequest = new common_http_Request(
                $signedRequest->getUrl(),
                $signedRequest->get_normalized_http_method(),
                $request->getParams(),
                array_merge($signatureHeaders, $request->getHeaders()),
                $request->getBody()
            );
        } else {
            $signedRequest =  new common_http_Request(
                $signedRequest->getUrl(),
                $signedRequest->get_normalized_http_method(),
                $signedRequest->get_parameters(),
                $request->getHeaders(),
                $request->getBody()
            );
        }

        return $signedRequest;
    }
    /**
     * As per the OAuth body hashing specification, all of the OAuth parameters must be sent as part of the Authorization header.
     *  In particular, OAuth parameters from the request URL and POST body will be ignored.
     * Return the Authorization header
     */
    static function buildAuthorizationHeader($signatureParameters) {
        $authorizationHeader = 'OAuth realm=""';
        
        foreach ($signatureParameters as $key=>$value) {
            $authorizationHeader.=','.$key."=".'"'.urlencode($value).'"';
        }
        return $authorizationHeader;
    }
    /**
     * Validates the signature of the current request
     *
     * @access protected
     * @author Joel Bout, <joel@taotesting.com>
     * @param  common_http_Request request
     * @throws common_Exception exception thrown if validation fails
    */
    public function validate(common_http_Request $request, common_http_Credentials $credentials = null) {
        $server = new OAuthServer(new tao_models_classes_oauth_DataStore());
		$method = new OAuthSignatureMethod_HMAC_SHA1();
        $server->add_signature_method($method);
        
        try {
            $oauthRequest = $this->getOauthRequest($request);
            $server->verify_request($oauthRequest);
        } catch (OAuthException $e) {
            throw new common_http_InvalidSignatureException('Validation failed: '.$e->getMessage());
        }
    }
    
    private function getOauthRequest(common_http_Request $request) {
        $params = array();
        
        $params = array_merge($params, $request->getParams());
        //$params = array_merge($params, $request->getHeaders());
        common_Logger::d("OAuth Request created:".$request->getUrl()." using ".$request->getMethod());
        $oauthRequest = new OAuthRequest($request->getMethod(), $request->getUrl(), $params);
        return $oauthRequest;
    }
}