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
 * @package generis
 
 */
class core_kernel_users_GenerisUser extends common_user_User
{

    private $userResource;

    private $cache;

    private $cachedProperties = array(
        PROPERTY_USER_DEFLG,
        PROPERTY_USER_ROLES,
        PROPERTY_USER_UILG,
        PROPERTY_USER_FIRSTNAME,
        PROPERTY_USER_LASTNAME,
        PROPERTY_USER_LOGIN
    );

    public function __construct(core_kernel_classes_Resource $user)
    {
        $this->userResource = $user;
        // load datalanguage to prevent cycle later on
        $this->getPropertyValues(PROPERTY_USER_DEFLG);
    }

    public function getIdentifier()
    {
        return $this->userResource->getUri();
    }
    
    // private $cache = array();
    private function getUserResource()
    {
        return new core_kernel_classes_Resource($this->getIdentifier());
    }

    public function getPropertyValues($property)
    {
        if (!in_array($property, $this->cachedProperties)) {
            return $this->getUncached($property);
        } elseif (!isset($this->cache[$property])) {
            $this->cache[$property] = $this->getUncached($property);
        }

        return $this->cache[$property];

    }
    
    private function getUncached($property)
    {
        $value = array();
        switch ($property) {
            case PROPERTY_USER_DEFLG:
            case PROPERTY_USER_UILG:
                $resource = $this->getUserResource()->getOnePropertyValue(new core_kernel_classes_Property($property));
	    	    if (!is_null($resource)) {
	    	        if ($resource instanceof core_kernel_classes_Resource) {
                        return array($resource->getUniquePropertyValue(new core_kernel_classes_Property(RDF_VALUE)));
	    	        } else {
	    	            common_Logger::w('Language '.$resource.' is not a resource');
	    	            return array(DEFAULT_LANG);
	    	        }
	    	    } else {
	    	        return array(DEFAULT_LANG);
	    	    }
	    	    break;
	    	default:
	    	    return $this->getUserResource()->getPropertyValues(new core_kernel_classes_Property($property));
	    }
	}
	
	public function refresh() {
	    $this->roles = false;
	    $this->cache = array(
	        PROPERTY_USER_DEFLG => $this->getUncached(PROPERTY_USER_DEFLG)
	    );
	    return true;
	}	
	
}