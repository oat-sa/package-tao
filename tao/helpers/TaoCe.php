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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */

namespace oat\tao\helpers;

use common_session_SessionManager;
use core_kernel_classes_Resource;
use core_kernel_classes_Property;

/**
 * Helper for TaoCe related operations
 *
 * @author Joel Bout <joel@taotesting.com>
 * @package taoCe
 * @license GPL-2.0
 *
 */
class TaoCe {

    /**
     * Check whether the current user has already been connected to the TAO backend.
     *  
     * @return boolean true if this is the first time
     */
    public static function isFirstTimeInTao() {
        $firstTime = common_session_SessionManager::getSession()->getUserPropertyValues(PROPERTY_USER_FIRSTTIME);

        //for compatibility purpose we assume previous users are veterans 
        return in_array(GENERIS_TRUE, $firstTime);
	}
    
    /**
     * The user knows TAO, he's now a veteran, the PROPERTY_USER_FIRSTTIME property can be false (except if $notYet is true).
     *  
     * @param core_kernel_classes_Resource $user a user or the current user if null/not set
     * @param boolean $notYet our veteran want to be still considered as a noob...
     */
    public static function becomeVeteran() {
        $success = false;
        
        $userUri = common_session_SessionManager::getSession()->getUserUri();
        if (!empty($userUri)) {
            $user = new \core_kernel_classes_Resource($userUri);
            if ($user->exists()) {
                // user in ontology
                $success = $user->editPropertyValues(
                    new core_kernel_classes_Property(PROPERTY_USER_FIRSTTIME),
                    new core_kernel_classes_Resource(GENERIS_FALSE)
                );
            } // else we fail;
        }
        return $success;
	}
	

	/**
	 * Get the URL of the last visited extension
	 * @param core_kernel_classes_Resource $user a user or the current user if null/not set (optional)
	 * @return string the url or null
	 */
	public static function getLastVisitedUrl() {
	    $urls = common_session_SessionManager::getSession()->getUserPropertyValues(PROPERTY_USER_LASTEXTENSION);
	    if (!empty($urls)) {
	        $lastUrl = current($urls);
	        return ROOT_URL.$lastUrl;
	    } else {
	        return null;
	    }
	}
	
	/**
	 * Set the URL of the last visited extension to a user.
	 * @param string $url a non empty URL where the user was the last time
	 * @param core_kernel_classes_Resource $user a user or the current user if null/not set (optional)
	 * @throws common_Exception
	 */
	public static function setLastVisitedUrl($url){
	    if(empty($url)){
	        throw new common_Exception('Cannot register an empty URL for the last visited extension');
	    }
        $success = false;
	    
	    $userUri = common_session_SessionManager::getSession()->getUserUri();
	    if (!empty($userUri)) {
	        $user = new \core_kernel_classes_Resource($userUri);
    	    $user = new core_kernel_classes_Resource($userUri);
    	    if ($user->exists()) {
    	        // user in ontology
    	        
    	        //clean up what's stored
    	        $url = str_replace(ROOT_URL, '', $url);
    	        $success = $user->editPropertyValues(new core_kernel_classes_Property(PROPERTY_USER_LASTEXTENSION), $url);
    	    } // else we fail;
	    }
	    return $success;
	}
}