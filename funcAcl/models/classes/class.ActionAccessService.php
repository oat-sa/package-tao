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
        
		//$rba = funcAcl_helpers_funcACL::getRolesByActions();
		$uri = explode('#', $accessUri);
		list($type, $ext, $mod, $act) = explode('_', $uri[1]);
		
		$role = new core_kernel_classes_Resource($roleUri);
		$module = new core_kernel_classes_Resource($this->makeEMAUri($ext, $mod));
		$actionAccessProperty = new core_kernel_classes_Property(PROPERTY_ACL_ACTION_GRANTACCESS);
		$moduleAccessProperty = new core_kernel_classes_Property(PROPERTY_ACL_MODULE_GRANTACCESS);
		
		// clean access.
		$this->remove($role->getUri(), $accessUri);
		
		// give access.
		$role->setPropertyValue($actionAccessProperty, $accessUri);
		
		// Remove global access to the entire module if granted.
		$grantedModules = $role->getPropertyValues($moduleAccessProperty);

		if (in_array($module->getUri(), $grantedModules)){
			$role->removePropertyValues($moduleAccessProperty, array('like' => false, 'pattern' => $module->getUri()));
		}
		
		funcAcl_helpers_Cache::cacheModule($module);
        
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
		$module = new core_kernel_classes_Resource($this->makeEMAUri($ext, $mod));
		$actionAccessProperty = new core_kernel_classes_Property(PROPERTY_ACL_ACTION_GRANTACCESS);
		
		$role->removePropertyValues($actionAccessProperty, array('pattern' => $accessUri));
		
		funcAcl_helpers_Cache::cacheModule($module);
        
    }

    /**
     * Short description of method moduleToActionAccess
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param  string roleUri
     * @param  string accessUri
     * @return mixed
     */
    public function moduleToActionAccess($roleUri, $accessUri)
    {
        
		$uri = explode('#', $accessUri);
		list($type, $ext, $mod, $act) = explode('_', $uri[1]);
		$module = new core_kernel_classes_Resource($this->makeEMAUri($ext, $mod));
		$roler = new core_kernel_classes_Class($roleUri);
		$propa = new core_kernel_classes_Property(PROPERTY_ACL_ACTION_GRANTACCESS);
		$roler->removePropertyValues(new core_kernel_classes_Property(PROPERTY_ACL_MODULE_GRANTACCESS), array('pattern' => $this->makeEMAUri($ext, $mod)));
		foreach (funcAcl_helpers_Model::getActions($module) as $action) {
			if ($act != $action->getLabel()) {
			    $roler->setPropertyValue($propa, $this->makeEMAUri($ext, $mod, $action->getLabel()));
			}
		}
		
		$module = new core_kernel_classes_Resource($this->makeEMAUri($ext, $mod));
		funcAcl_helpers_Cache::cacheModule($module);
        
    }

    /**
     * Short description of method moduleToActionsAccess
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param  string roleUri
     * @param  string accessUri
     * @return mixed
     */
    public function moduleToActionsAccess($roleUri, $accessUri)
    {
        
		$uri = explode('#', $accessUri);
		list($type, $ext, $mod) = explode('_', $uri[1]);
		$module = new core_kernel_classes_Resource($accessUri);
		$roler = new core_kernel_classes_Class($roleUri);
		$propa = new core_kernel_classes_Property(PROPERTY_ACL_ACTION_GRANTACCESS);
		$roler->removePropertyValues(new core_kernel_classes_Property(PROPERTY_ACL_MODULE_GRANTACCESS), array('pattern' => $this->makeEMAUri($ext, $mod)));
		foreach (funcAcl_helpers_Model::getActions($module) as $action) {
			$roler->setPropertyValue($propa, $this->makeEMAUri($ext, $mod, $action->getLabel()));
		}
		
		$module = new core_kernel_classes_Resource($this->makeEMAUri($ext, $mod));
		funcAcl_helpers_Cache::cacheModule($module);
        
    }

} /* end of class funcAcl_models_classes_ActionAccessService */

?>