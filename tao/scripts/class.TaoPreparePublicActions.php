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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * The taoPrepareActions script aims at updating the Extension model in the Ontology
 * depending on Action classes found in the extension at the file system level.
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 * @subpackage scripts
 */
class tao_scripts_TaoPreparePublicActions
    extends tao_scripts_Runner
{

    public function preRun()
    {

    }

    /**
     * Main script logic.
     * 
     * * Recreate extension model.
     * * Grant access for the extension to the dedicated management role.
     *
     */
    public function run()
    {
    	
    	// We get all the management roles and the extension they belong to.
    	$managementRoleClass = new core_kernel_classes_Class(CLASS_MANAGEMENTROLE);
    	$foundManagementRoles = $managementRoleClass->getInstances(true);
    	$managementRolesByExtension = array();
    	
    	foreach (common_ext_ExtensionsManager::singleton()->getInstalledExtensions() as $extension) {
	    	$managementRole = $extension->getManagementRole();
	    		
	    	if (empty($managementRole)) {
	    		// try to discover it.
	    		foreach ($foundManagementRoles as $mR) {
	    			$moduleURIs = $mR->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ACL_MODULE_GRANTACCESS));
	    			
	    			foreach ($moduleURIs as $moduleURI) {
	    				$uri = explode('#', $moduleURI);
	    				list($type, $extId) = explode('_', $uri[1]);
	    				
	    				if ($extId == $extension->getID()) {
	    					$managementRole = $mR;
	    					break 2;
	    				}
	    			}
	    		}
	    	}
	    	
	    	if (!empty($managementRole)) {
	    		$managementRolesByExtension[$extension->getID()] = $managementRole;
	    	}
    	}
    	
        // delete old Instances
		$moduleClass = new core_kernel_classes_Class(CLASS_ACL_MODULE);
    	foreach ($moduleClass->getInstances() as $res) {
    		$res->delete();
    	}
    	
    	$actionClass = new core_kernel_classes_Class(CLASS_ACL_ACTION);
    	foreach ($actionClass->getInstances() as $res) {
    		$res->delete();
    	}
    	
    	tao_helpers_funcACL_Cache::flush();
    	
    	foreach (common_ext_ExtensionsManager::singleton()->getInstalledExtensions() as $extension) {
    		if ($extension->getID() != 'generis') {
	    		// 1. Create the Extension Model.
				// All action classes of this module will be reflected to get an equivalent in the ontology.
				tao_helpers_funcACL_Model::spawnExtensionModel($extension);
    		}
		}
		
		foreach (common_ext_ExtensionsManager::singleton()->getInstalledExtensions() as $extension) {
			if ($extension->getID() != 'generis') {
				// 2. Grant access to Management Role.
				if (!empty($managementRolesByExtension[$extension->getID()])) {
					$extAccessService = tao_models_classes_funcACL_ExtensionAccessService::singleton();
					$extAccessService->add($managementRolesByExtension[$extension->getID()]->getUri(), $extAccessService->makeEMAUri($extension->getID()));
				}
				else {
					common_Logger::i('Management Role not found for extension ' . $extension->getID());
				}
			}
		}
    }

    public function postRun()
    {

    }
}

?>