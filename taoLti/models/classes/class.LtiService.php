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
 * Basic service to handle everything LTI
 * 
 * @author Joel Bout, <joel@taotesting.com>
 */
class taoLti_models_classes_LtiService extends tao_models_classes_Service 
{
	const LIS_CONTEXT_ROLE_NAMESPACE = 'urn:lti:role:ims/lis/';

	const LTICONTEXT_SESSION_KEY	= 'LTICONTEXT';
	
	protected function __construct() {
	}
	
	/**
	 * start a session from the provided OAuth Request
	 * 
	 * @param common_http_Request $request
	 * @throws common_user_auth_AuthFailedException
	 */
	public function startLtiSession(common_http_Request $request) {
        $adapter = new taoLti_models_classes_LtiAuthAdapter($request);
        $user = $adapter->authenticate();
        $session = new taoLti_models_classes_TaoLtiSession($user);
        common_session_SessionManager::startSession($session);
	}
	
	/**
	 * Returns the current LTI session
	 * @return taoLti_models_classes_TaoLtiSession 
	 */
	public function getLtiSession() {
	    $session = common_session_SessionManager::getSession();
	    if (!$session instanceof taoLti_models_classes_TaoLtiSession) {
	        throw new taoLti_models_classes_LtiException(__FUNCTION__.' called on a non LTI session');
	    }
	    return $session;
	}
	
	public function getCredential($key) {
		$class = new core_kernel_classes_Class(CLASS_LTI_CONSUMER);
		$instances = $class->searchInstances(array(PROPERTY_OAUTH_KEY => $key), array('like' => false));
		if (count($instances) == 0) {
			throw new taoLti_models_classes_LtiException('No Credentials for consumer key '.$key);
		}
		if (count($instances) > 1) {
			throw new taoLti_models_classes_LtiException('Multiple Credentials for consumer key '.$key);
		}
		return current($instances);
	}
	
	/**
	 * Returns the LTI Consumer resource associated to this lti session
	 *
	 * @access public
	 * @author Joel Bout, <joel@taotesting.com>
	 * @return core_kernel_classes_Resource resource of LtiConsumer
	 * @throws tao_models_classes_oauth_Exception thrown if no Consumer found for key
	 */
	public function getLtiConsumerResource($launchData)
	{
        $dataStore = new tao_models_classes_oauth_DataStore();
        return $dataStore->findOauthConsumerResource($launchData->getOauthKey());
	}
		
	/**
	 * Returns the existing tao User that corresponds to
	 * the LTI request or spawns it
	 * 
	 * @param taoLti_models_classes_LtiLaunchData $ltiContext
	 * @throws taoLti_models_classes_LtiException
	 * @return core_kernel_classes_Resource
	 */
	public function findOrSpwanUser(taoLti_models_classes_LtiLaunchData $launchData) {
	    $taoUser = $this->findUser($launchData);
	    if (is_null($taoUser)) {
	        $taoUser = $this->spawnUser($launchData);
	    }
	    return $taoUser;
	}
	
	/**
	 * Searches if this user was already created in TAO
	 * 
	 * @param taoLti_models_classes_LtiLaunchData $ltiContext
	 * @throws taoLti_models_classes_LtiException
	 * @return core_kernel_classes_Resource
	 */
	public function findUser(taoLti_models_classes_LtiLaunchData $ltiContext) {
		$class = new core_kernel_classes_Class(CLASS_LTI_USER);
		$instances = $class->searchInstances(array(
			PROPERTY_USER_LTIKEY		=> $ltiContext->getUserID(),
			PROPERTY_USER_LTICONSUMER	=> $this->getLtiConsumerResource($ltiContext)
		), array(
			'like'	=> false
		));
		if (count($instances) > 1) {
			throw new taoLti_models_classes_LtiException('Multiple user accounts found for user key \''.$ltiContext->getUserID().'\'');
		}
		return count($instances) == 1 ? current($instances) : null;
	}
	
	/**
	 * Creates a new LTI User with the absolute minimum of required informations
	 * 
	 * @param taoLti_models_classes_LtiLaunchData $ltiContext
	 * @return core_kernel_classes_Resource
	 */
	public function spawnUser(taoLti_models_classes_LtiLaunchData $ltiContext) {
		$class = new core_kernel_classes_Class(CLASS_LTI_USER);
		//$lang = tao_models_classes_LanguageService::singleton()->getLanguageByCode(DEFAULT_LANG);
                
		$props = array(
			PROPERTY_USER_LTIKEY		=> $ltiContext->getUserID(),
			PROPERTY_USER_LTICONSUMER	=> $this->getLtiConsumerResource($ltiContext),
		    /*
			PROPERTY_USER_UILG			=> $lang,
			PROPERTY_USER_DEFLG			=> $lang,
			*/
			
		);
                
        if ($ltiContext->hasVariable(taoLti_models_classes_LtiLaunchData::LIS_PERSON_NAME_FULL)) {
			$props[RDFS_LABEL] = $ltiContext->getUserFullName();
		}
                
		if ($ltiContext->hasVariable(taoLti_models_classes_LtiLaunchData::LIS_PERSON_NAME_GIVEN)) {
			$props[PROPERTY_USER_FIRSTNAME] = $ltiContext->getUserGivenName();
		}
		if ($ltiContext->hasVariable(taoLti_models_classes_LtiLaunchData::LIS_PERSON_NAME_FAMILY)) {
			$props[PROPERTY_USER_LASTNAME] = $ltiContext->getUserFamilyName();
		}
		if ($ltiContext->hasVariable(taoLti_models_classes_LtiLaunchData::LIS_PERSON_CONTACT_EMAIL_PRIMARY)) {
			$props[PROPERTY_USER_MAIL] = $ltiContext->getUserEmail();
		}
		$user = $class->createInstanceWithProperties($props);
		common_Logger::i('added User '.$user->getLabel());

		return $user;
	}
}