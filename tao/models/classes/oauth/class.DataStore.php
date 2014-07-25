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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *			   2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);\n *			   2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *             2013 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * include OAuth Classes
 *
 * @author Joel Bout, <joel@taotesting.com>
 */
require_once dirname(__FILE__).'/../../../lib/oauth/OAuth.php';

/**
 * Tao Implementation of an OAuthDatastore
 * Does not yet implement the nonce and request/access token
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_models_classes_oauth_DataStore
	extends OAuthDataStore
{

	/**
	 * Helper function to find the OauthConsumer RDF Resource
	 *
	 * @access public
	 * @author Joel Bout, <joel@taotesting.com>
	 * @param  string consumer_key
	 * @return core_kernel_classes_Resource
	 */
	public function findOauthConsumerResource($consumer_key)
	{
		$returnValue = null;

		$class = new core_kernel_classes_Class(CLASS_OAUTH_CONSUMER);
		$instances = $class->searchInstances(array(PROPERTY_OAUTH_KEY => $consumer_key), array('like' => false, 'recursive' => true));
		if (count($instances) == 0) {
			throw new tao_models_classes_oauth_Exception('No Credentials for consumer key '.$consumer_key);
		}
		if (count($instances) > 1) {
			throw new tao_models_classes_oauth_Exception('Multiple Credentials for consumer key '.$consumer_key);
		}
		$returnValue	= current($instances);

		return $returnValue;
	}
	
	public function getOauthConsumer(core_kernel_classes_Resource $consumer)
	{
	    $values = $consumer->getPropertiesValues(array(
	        PROPERTY_OAUTH_KEY,
	        PROPERTY_OAUTH_SECRET,
	        PROPERTY_OAUTH_CALLBACK
	    ));
	    if (empty($values[PROPERTY_OAUTH_KEY]) || empty($values[PROPERTY_OAUTH_SECRET])) {
	        throw new tao_models_classes_oauth_Exception('Incomplete oauth consumer definition for '.$consumer->getUri());
	    }
	    $consumer_key = (string)current($values[PROPERTY_OAUTH_KEY]);
	    $secret = (string)current($values[PROPERTY_OAUTH_SECRET]);
	    if (!empty($values[PROPERTY_OAUTH_CALLBACK])) {
	        $callbackUrl = (string)current($values[PROPERTY_OAUTH_CALLBACK]);
	        if (empty($callbackUrl)) {
	            $callbackUrl = null;
	        }
	    } else {
	        $callbackUrl = null;
	    }
        return new OAuthConsumer($consumer_key, $secret, $callbackUrl);
	}
	

	/**
	 * returns the OauthConsumer for the specified key
	 *
	 * @access public
	 * @author Joel Bout, <joel@taotesting.com>
	 * @param  consumer_key
	 * @return OAuthConsumer
	 */
	public function lookup_consumer($consumer_key)
	{
		$returnValue = null;

		$consumer = $this->findOauthConsumerResource($consumer_key);
		$secret			= (string)$consumer->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_OAUTH_SECRET));
		$callbackUrl	= null;
		
		$returnValue = new OAuthConsumer($consumer_key, $secret, $callbackUrl);

		return $returnValue;
	}

	/**
	 * Should verify if the token exists and return it
	 * Always returns an token with an empty secret for now
	 *
	 * @access public
	 * @author Joel Bout, <joel@taotesting.com>
	 * @param  consumer
	 * @param  token_type
	 * @param  token
	 * @return mixed
	 */
	public function lookup_token($consumer, $token_type, $token)
	{
  		common_Logger::d(__CLASS__.'::'.__FUNCTION__.' called for token '.$token.' of type '.$token_type);
		return new OAuthToken($consumer, "");
	}

	/**
	 * Should verify if a nonce has already been used
	 * always return NULL, meaning that nonces can be reused
	 *
	 * @access public
	 * @author Joel Bout, <joel@taotesting.com>
	 * @param  consumer
	 * @param  token
	 * @param  nonce
	 * @param  timestamp
	 * @return mixed
	 */
	public function lookup_nonce($consumer, $token, $nonce, $timestamp)
	{
		common_Logger::d(__CLASS__.'::'.__FUNCTION__.' called');
		return null;
	}

	/**
	 * Should create a new request token
	 * not implemented
	 *
	 * @access public
	 * @author Joel Bout, <joel@taotesting.com>
	 * @param  consumer
	 * @param  callback
	 * @return mixed
	 */
	function new_request_token($consumer, $callback = null)
	{
		common_Logger::d(__CLASS__.'::'.__FUNCTION__.' called');
		return null;
	}

	/**
	 * Should create a new access token
	 * not implemented
	 * 
	 * @access public
	 * @author Joel Bout, <joel@taotesting.com>
	 * @param  token
	 * @param  consumer
	 * @return mixed
	 */
	public function new_access_token($token, $consumer, $verifier = null)
	{
		common_Logger::d(__CLASS__.'::'.__FUNCTION__.' called');
		return null;
	}

}