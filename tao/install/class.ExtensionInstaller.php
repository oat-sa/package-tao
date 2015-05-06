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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Specification of the Generis ExtensionInstaller class to add a new behavior:
 * the Modules and Actions in the Ontology at installation time.
 *
 * @access public
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @package tao
 * @since 2.4
 
 */
class tao_install_ExtensionInstaller
    extends common_ext_ExtensionInstaller
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Will create the model of Modules and Actions (MVC) in the persistent
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return void
     * @since 2.4
     */
    public function extendedInstall() {
        
        $this->installManagementRole();
        
        $this->applyAccessRules();
        
    }
    
    /**
     * Will make the Global Manager include the Management Role of the extension
     * to install (if it exists).
     * 
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return void
     * @since 2.4
     */
    public function installManagementRole() {
    	// Try to get a Management Role described by the extension itself.
    	// (this information comes actually from the Manifest of the extension)
    	$role = $this->extension->getManagementRole();
    	
    	$roleService = tao_models_classes_RoleService::singleton();

    	if (!empty($role)){

        	if (!$role->exists()) {
        	    // Management role does not exist yet, so we create it
        	    $roleClass = new core_kernel_classes_Class(CLASS_MANAGEMENTROLE);
        	    $roleLabel = $this->extension->getId() . ' Manager';
        	    $role = $roleClass->createInstance($roleLabel, $roleLabel.' Role', $role->getUri());
        	    $roleService->includeRole($role, new core_kernel_classes_Resource(INSTANCE_ROLE_BACKOFFICE));
        	}
    
        	// Take the Global Manager role and make it include
        	// the Management role of the currently installed extension.
        	if ($role->getUri() !== INSTANCE_ROLE_GLOBALMANAGER){
        		$globalManagerRole = new core_kernel_classes_Resource(INSTANCE_ROLE_GLOBALMANAGER);
        		$roleService->includeRole($globalManagerRole, $role);
        	}
        	
        	common_Logger::d("Management Role " . $role->getUri() . " created for extension '" . $this->extension->getId() . "'.");
        } else {
            // There is no Management role described by the Extension Manifest.
            common_Logger::i("No management role for extension '" . $this->extension->getId() . "'.");
        }
    }
    
    /**
     * Will make the Global Manager include the Management Role of the extension
     * to install (if it exists).
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return void
     * @since 2.4
     */
    public function applyAccessRules() {
        foreach ($this->extension->getManifest()->getAclTable() as $tableEntry) {
            $rule = new tao_models_classes_accessControl_AccessRule($tableEntry[0], $tableEntry[1], $tableEntry[2]);
            tao_models_classes_accessControl_AclProxy::applyRule($rule);
        }
    }

} /* end of class tao_install_ExtensionInstaller */

?>