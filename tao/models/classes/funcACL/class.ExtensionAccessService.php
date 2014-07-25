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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * access operation for extensions
 *
 * @author Jehan Bihin
 * @package tao
 * @since 2.2
 * @subpackage models_classes_funcACL
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * mother class for access operations
 *
 * @author Jehan Bihin
 * @since 2.2
 */
require_once('tao/models/classes/funcACL/class.AccessService.php');

/* user defined includes */
// section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A1C-includes begin
// section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A1C-includes end

/* user defined constants */
// section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A1C-constants begin
// section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A1C-constants end

/**
 * access operation for extensions
 *
 * @access public
 * @author Jehan Bihin
 * @package tao
 * @since 2.2
 * @subpackage models_classes_funcACL
 */
class tao_models_classes_funcACL_ExtensionAccessService
    extends tao_models_classes_funcACL_AccessService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method add
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param  string roleUri
     * @param  string accessUri
     * @return mixed
     */
    public function add($roleUri, $accessUri)
    {
        // section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A1E begin
		$uri = explode('#', $accessUri);
		list($type, $extId) = explode('_', $uri[1]);
		
		$extManager = common_ext_ExtensionsManager::singleton();
		$extension = $extManager->getExtensionById($extId);
		$role = new core_kernel_classes_Resource($roleUri);
		$modules = tao_helpers_funcACL_Model::getModules($extension->getID());
		$moduleAccessProperty = new core_kernel_classes_Property(PROPERTY_ACL_MODULE_GRANTACCESS);
		
		// Clean the role about this extension.
		$this->remove($role->getUri(), $accessUri);

		$role->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACL_GRANTACCESS), $accessUri);
		tao_helpers_funcACL_Cache::flushExtensionCache();
        // section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A1E end
    }

    /**
     * Short description of method remove
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param  string roleUri
     * @param  string accessUri
     * @return mixed
     */
    public function remove($roleUri, $accessUri)
    {
        // section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A22 begin
		$uri = explode('#', $accessUri);
		list($type, $extId) = explode('_', $uri[1]);
		
		// Remove the access to the module for this role.
		$extManager = common_ext_ExtensionsManager::singleton();
		$extension = $extManager->getExtensionById($extId);
		$role = new core_kernel_classes_Resource($roleUri);
		
		$role->removePropertyValues(new core_kernel_classes_Property(PROPERTY_ACL_GRANTACCESS), array('pattern' => $accessUri));
		
		$moduleAccessProperty = new core_kernel_classes_Property(PROPERTY_ACL_MODULE_GRANTACCESS);
		$moduleAccessService = tao_models_classes_funcACL_ModuleAccessService::singleton();
		$grantedModules = $role->getPropertyValues($moduleAccessProperty);
		
		foreach ($grantedModules as $gM){
			$gM = new core_kernel_classes_Resource($gM);
			$uri = explode('#', $gM->getUri());
			list($type, $ext) = explode('_', $uri[1]);
			
			if ($extId == $ext){
				$moduleAccessService->remove($role->getUri(), $gM->getUri());
			}
		}
		tao_helpers_funcACL_Cache::flushExtensionCache();
		// section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A22 end
    }

} /* end of class tao_models_classes_funcACL_ExtensionAccessService */

?>