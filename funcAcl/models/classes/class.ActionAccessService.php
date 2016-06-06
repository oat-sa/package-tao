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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */

/**
 * access operation for actions
 *
 * @access public
 * @author Jehan Bihin
 * @package tao
 * @since 2.2
 
 */
class funcAcl_models_classes_ActionAccessService
    extends funcAcl_models_classes_AccessService
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
		$uri = explode('#', $accessUri);
		list($type, $ext, $mod, $act) = explode('_', $uri[1]);
		
		$role = new core_kernel_classes_Resource($roleUri);
		$module = new core_kernel_classes_Resource($this->makeEMAUri($ext, $mod));
		$actionAccessProperty = new core_kernel_classes_Property(funcAcl_models_classes_AccessService::PROPERTY_ACL_GRANTACCESS);
		$moduleAccessProperty = new core_kernel_classes_Property(funcAcl_models_classes_AccessService::PROPERTY_ACL_GRANTACCESS);
		
		$values = $role->getPropertyValues($actionAccessProperty);
		if (!in_array($accessUri, $values)) {
		    $role->setPropertyValue($actionAccessProperty, $accessUri);
		    $controllerClassName = funcAcl_helpers_Map::getControllerFromUri($module->getUri());
		    funcAcl_helpers_Cache::flushControllerAccess($controllerClassName);
		} else {
		    common_Logger::w('Tried to regrant access for role '.$role->getUri().' to action '.$accessUri);
		}
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
        
		$uri = explode('#', $accessUri);
		list($type, $ext, $mod, $act) = explode('_', $uri[1]);
		
		$role = new core_kernel_classes_Class($roleUri);
		$actionAccessProperty = new core_kernel_classes_Property(funcAcl_models_classes_AccessService::PROPERTY_ACL_GRANTACCESS);
		
		$module = new core_kernel_classes_Resource($this->makeEMAUri($ext, $mod));
		$controllerClassName = funcAcl_helpers_Map::getControllerFromUri($module->getUri());
		
		// access via controller?
		$controllerAccess = funcAcl_helpers_Cache::getControllerAccess($controllerClassName);
		if (in_array($roleUri, $controllerAccess['module'])) {

		    // remove access to controller
		    funcAcl_models_classes_ModuleAccessService::singleton()->remove($roleUri, $module->getUri());
		
		    // add access to all other actions
		    foreach (funcAcl_helpers_Model::getActions($module) as $action) {
		        if ($action->getUri() != $accessUri) {
		            $this->add($roleUri, $action->getUri());
		        }
		    }
		    
		} elseif (isset($controllerAccess['actions'][$act]) && in_array($roleUri, $controllerAccess['actions'][$act])) {
		    // remove action only
		    $role->removePropertyValues($actionAccessProperty, array('pattern' => $accessUri));
            funcAcl_helpers_Cache::flushControllerAccess($controllerClassName);
		}
    }
}