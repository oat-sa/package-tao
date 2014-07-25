<?php
/*  
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */


/**
 * A specific implementation of tao_models_classes_UserService. It restricts
 * the authentication to users that have the 'Test Taker' role.
 *
 * @access public
 * @author @author Somsack Sipasseuth, <sam@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_UserService
    extends tao_models_classes_UserService
{
	
    /**
     * Overrides tao_models_classes_UserService to limit authentication
     * to users that have the 'Test Taker' role.
     *
     * @access public
     * @author Somsack Sipasseuth, <sam@taotesting.com>
     * @return array An array of core_kernel_classes_Resource
     */
    public function getAllowedRoles(){
		return array(INSTANCE_ROLE_DELIVERY => new core_kernel_classes_Resource(INSTANCE_ROLE_DELIVERY));
    }
    
    /**
     * Overrides tao_models_classes_UserService to specify which class to use to instanciate
     * new users.
     */
    public function getRootClass(){
    	$subjectsExt = common_ext_ExtensionsManager::singleton()->getExtensionById('taoSubjects');
    	$const = $subjectsExt->getConstant('TAO_CLASS_SUBJECT');
    	return new core_kernel_classes_Class($const);
    }

}

?>