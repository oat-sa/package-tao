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
 * access operation for modules
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
// section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A26-includes begin
// section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A26-includes end

/* user defined constants */
// section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A26-constants begin
// section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A26-constants end

/**
 * access operation for modules
 *
 * @access public
 * @author Jehan Bihin
 * @package tao
 * @since 2.2
 * @subpackage models_classes_funcACL
 */
class tao_models_classes_funcACL_ModuleAccessService
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
        // section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A31 begin
		$module = new core_kernel_classes_Resource($accessUri);
		$role = new core_kernel_classes_Resource($roleUri);
		$moduleAccessProperty = new core_kernel_classes_Property(PROPERTY_ACL_MODULE_GRANTACCESS);
		
		// Clean role about this module.
		$this->remove($role->getUri(), $module->getUri());
		
		// Grant access to the module for this role.
		$role->setPropertyValue($moduleAccessProperty, $module->getUri());
		
		tao_helpers_funcACL_Cache::cacheModule($module);
        // section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A31 end
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
        // section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A35 begin
		$module = new core_kernel_classes_Resource($accessUri);
		$role = new core_kernel_classes_Class($roleUri);
		$moduleAccessProperty = new core_kernel_classes_Property(PROPERTY_ACL_MODULE_GRANTACCESS);
		$actionAccessProperty = new core_kernel_classes_Property(PROPERTY_ACL_ACTION_GRANTACCESS);
		
		// Retrieve the module ID.
		$uri = explode('#', $module->getUri());
		list($type, $extId, $modId) = explode('_', $uri[1]);
		
		// Remove the access to the module for this role.
		$role->removePropertyValues($moduleAccessProperty, array('pattern' => $module->getUri()));
		
		// Remove the access to the actions corresponding to the module for this role.
		$actionAccessService = tao_models_classes_funcACL_ActionAccessService::singleton();
		$grantedActions = $role->getPropertyValues($actionAccessProperty);
		
		foreach ($grantedActions as $gA){
			$gA = new core_kernel_classes_Resource($gA);
			
			$uri = explode('#', $gA->getUri());
			list($type, $ext, $mod) = explode('_', $uri[1]);
			
			if ($modId == $mod){
				$actionAccessService->remove($role->getUri(), $gA->getUri());
			}
		}
		
		tao_helpers_funcACL_Cache::cacheModule($module);
        // section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A35 end
    }

    /**
     * Short description of method actionsToModuleAccess
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param  string roleUri
     * @param  string accessUri
     * @return mixed
     */
    public function actionsToModuleAccess($roleUri, $accessUri)
    {
        // section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A39 begin
		$uri = explode('#', $accessUri);
		list($type, $ext, $mod) = explode('_', $uri[1]);
		$module = new core_kernel_classes_Resource($accessUri);
		$roler = new core_kernel_classes_Class($roleUri);
		$propa = new core_kernel_classes_Property(PROPERTY_ACL_ACTION_GRANTACCESS);
		$roler->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACL_MODULE_GRANTACCESS), $this->makeEMAUri($ext, $mod));
		foreach (tao_helpers_funcACL_Model::getActions($module) as $action) {
			$roler->removePropertyValues($propa, array('pattern' => $this->makeEMAUri($ext, $mod, $action->getLabel())));
		}
		
		$module = new core_kernel_classes_Resource($this->makeEMAUri($ext, $mod));
		tao_helpers_funcACL_Cache::cacheModule($module);
        // section 127-0-1-1--43b2a85f:1372be1e0be:-8000:0000000000003A39 end
    }

} /* end of class tao_models_classes_funcACL_ModuleAccessService */

?>