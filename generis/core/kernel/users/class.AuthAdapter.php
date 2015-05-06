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
class core_kernel_users_AuthAdapter
	implements common_user_auth_Adapter
{
    CONST LEGACY_ALGORITHM = 'md5';
    CONST LEGACY_SALT_LENGTH = 0;

    /**
     * Returns the hashing algorithm defined in generis configuration
     * 
     * @return helpers_PasswordHash
     */
    public static function getPasswordHash() {
        return new helpers_PasswordHash(
            defined('PASSWORD_HASH_ALGORITHM') ? PASSWORD_HASH_ALGORITHM : self::LEGACY_ALGORITHM,
            defined('PASSWORD_HASH_SALT_LENGTH') ? PASSWORD_HASH_SALT_LENGTH : self::LEGACY_SALT_LENGTH
        );
    }    
    
    private $username;
	private $password;
	
	/**
	 * 
	 * @param string $username
	 * @param string $password
	 */
	public function __construct($username, $password) {
		$this->username = $username;
		$this->password = $password;
	}
	
	/**
     * (non-PHPdoc)
     * @see common_user_auth_Adapter::authenticate()
     */
    public function authenticate() {
    	
    	$userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
    	$filters = array(PROPERTY_USER_LOGIN => $this->username);
    	$options = array('like' => false, 'recursive' => true);
    	$users = $userClass->searchInstances($filters, $options);
    	
    	
    	if (count($users) > 1){
    		// Multiple users matching
    		throw new common_exception_InconsistentData("Multiple Users found with the same login '".$this->username."'.");
    	}
        if (empty($users)){
            // fake code execution to prevent timing attacks
            $label = new core_kernel_classes_Property(RDFS_LABEL);
            $hash = $label->getUniquePropertyValue($label);
            if (!self::getPasswordHash()->verify($this->password, $hash)) {
                throw new core_kernel_users_InvalidLoginException();
            }
            // should never happen, added for integrity
    		throw new core_kernel_users_InvalidLoginException();
    	}
    	
	    $userResource = current($users);
	    $hash = $userResource->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_PASSWORD));
	    if (!self::getPasswordHash()->verify($this->password, $hash)) {
	        throw new core_kernel_users_InvalidLoginException();
	    }
    	
    	return new core_kernel_users_GenerisUser($userResource);
    }
}