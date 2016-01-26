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

class taoLti_models_classes_LtiLaunchData
{
    const OAUTH_CONSUMER_KEY               = 'oauth_consumer_key';
    const RESOURCE_LINK_ID                 = 'resource_link_id';
    const RESOURCE_LINK_TITLE              = 'resource_link_title';
    
    const USER_ID                          = 'user_id';
    const ROLES                            = 'roles';
    const LIS_PERSON_NAME_GIVEN            = 'lis_person_name_given';
    const LIS_PERSON_NAME_FAMILY           = 'lis_person_name_family';
    const LIS_PERSON_NAME_FULL             = 'lis_person_name_full';
    const LIS_PERSON_CONTACT_EMAIL_PRIMARY = 'lis_person_contact_email_primary';
    
    const LAUNCH_PRESENTATION_LOCALE       = 'launch_presentation_locale';
    const LAUNCH_PRESENTATION_RETURN_URL   = 'launch_presentation_return_url';
    
    const TOOL_CONSUMER_INSTANCE_NAME           = 'tool_consumer_instance_name';
    const TOOL_CONSUMER_INSTANCE_DESCRIPTION    = 'tool_consumer_instance_description';

    /**
     * LTI variables
     * 
     * @var array
     */
    private $variables;
    
    /**
     * Custom parameters of the LTI call
     * 
     * @var array
     */
    private $customParams;
    
    
    /**
     * 
     * @param common_http_Request $request
     * @return taoLti_models_classes_LtiLaunchData
     */
    public static function fromRequest(common_http_Request $request) {
        $extra = self::getParametersFromUrl($request->getUrl());
        return new self($request->getParams(), $extra);
    }
    
    private static function getParametersFromUrl($url)
    {
        $returnValue = array();
        
        // get parameters
        parse_str(parse_url($url,PHP_URL_QUERY), $returnValue);
    
        // encoded in url
        $parts = explode('/', tao_helpers_Request::getRelativeUrl($url), 4);
        if (count($parts) == 4) {
            list ($extension, $module, $action, $codedUri) = $parts;
            $base64String = base64_decode($codedUri);
            if ($base64String !== false) {
                // old serialised url
                if (substr($base64String, 0, strlen('a:')) == 'a:') {
                    $additionalParams = unserialize($base64String);
                } else {
                    $additionalParams = json_decode($base64String, true);
                }
                if ($additionalParams !== false && is_array($additionalParams)) {
                    foreach ($additionalParams as $key => $value) {
                        $returnValue[$key] = $value;
                    }
                }
            }
        }
        return $returnValue;
    }
    
    /**
     * Spawns an LtiSession
     * 
     * @param array $ltiVariables
     */
    private function __construct($ltiVariables, $customParameters) {
        $this->variables    = $ltiVariables;
        $this->customParams = $customParameters;
    }
    
    public function hasVariable($key) {
        return isset($this->variables[$key]);
    }
    
    public function getVariable($key) {
        if (isset($this->variables[$key])) {
            return $this->variables[$key];
        } else {
            throw new taoLti_models_classes_LtiException('Undefined LTI variable '.$key);
        }
    }
    
    public function getCustomParameter($key) {
        return isset($this->customParams[$key]) ? $this->customParams[$key] : null; 
    }    

    // simpler access
    
    public function getOauthKey() {
        return $this->getVariable(self::OAUTH_CONSUMER_KEY);
    }
    
    public function getResourceLinkID() {
        return $this->getVariable(self::RESOURCE_LINK_ID);
    }
    
    public function getResourceLinkTitle() {
        if ($this->hasVariable(self::RESOURCE_LINK_TITLE)) {
            return $this->getVariable(self::RESOURCE_LINK_TITLE);
        } else {
            return __('link');
        }
    }
    
    public function getUserID() {
        return $this->getVariable(self::USER_ID);
    }
    
    public function getUserGivenName() {
        return $this->getVariable(self::LIS_PERSON_NAME_GIVEN);
    }
    
    public function getUserFamilyName() {
        return $this->getVariable(self::LIS_PERSON_NAME_FAMILY);
    }
    
    public function getUserFullName() {
        if ($this->hasVariable(self::LIS_PERSON_NAME_FULL)) {
            return $this->getVariable(self::LIS_PERSON_NAME_FULL);
        }
    }
    
    public function getUserEmail() {
        return $this->getVariable(self::LIS_PERSON_CONTACT_EMAIL_PRIMARY);
    }
    
    public function getUserRoles() {
        return explode(',',$this->getVariable(self::ROLES));
    }
    
    public function hasLaunchLanguage() {
        return $this->hasVariable(self::LAUNCH_PRESENTATION_LOCALE);
    }
        
    public function getLaunchLanguage() {
        return $this->getVariable(self::LAUNCH_PRESENTATION_LOCALE);
    }
    
    /**
     * Tries to return the tool consumer name
     * 
     * Returns null if no name found
     * 
     * @return string
     */
    public function getToolConsumerName() {
        return $this->hasVariable(self::TOOL_CONSUMER_INSTANCE_NAME) 
            ? $this->getVariable(self::TOOL_CONSUMER_INSTANCE_NAME)
            : $this->hasVariable(self::TOOL_CONSUMER_INSTANCE_DESCRIPTION)
                ? $this->getVariable(self::TOOL_CONSUMER_INSTANCE_DESCRIPTION)
                : null;
    }
    
    /**
     * Return the returnUrl to the tool consumer
     * 
     * @return string
     * @throws taoLti_models_classes_LtiException
     */
    public function getReturnUrl() {
        return $this->getVariable(self::LAUNCH_PRESENTATION_RETURN_URL);
    }
}