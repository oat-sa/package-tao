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
?>
<?php

error_reporting(E_ALL);

/**
 * Specification of the Generis ExtensionInstaller class to add a new behavior:
 * the Modules and Actions in the Ontology at installation time.
 *
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @package tao
 * @since 2.4
 * @subpackage install
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_ext_ExtensionInstaller
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 */
require_once('common/ext/class.ExtensionInstaller.php');

/* user defined includes */
// section 10-13-1-85-74f9b31f:13c8ff1fd35:-8000:0000000000003C57-includes begin
// section 10-13-1-85-74f9b31f:13c8ff1fd35:-8000:0000000000003C57-includes end

/* user defined constants */
// section 10-13-1-85-74f9b31f:13c8ff1fd35:-8000:0000000000003C57-constants begin
// section 10-13-1-85-74f9b31f:13c8ff1fd35:-8000:0000000000003C57-constants end

/**
 * Specification of the Generis ExtensionInstaller class to add a new behavior:
 * the Modules and Actions in the Ontology at installation time.
 *
 * @access public
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @package tao
 * @since 2.4
 * @subpackage install
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
        // section 10-13-1-85-74f9b31f:13c8ff1fd35:-8000:0000000000003C58 begin
        $this->installManagementRole();
        // section 10-13-1-85-74f9b31f:13c8ff1fd35:-8000:0000000000003C58 end
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
    	
    	if (empty($role)){
    		// There is no Management role described by the Extension Manifest.
    		// We create a new one with the name of the extension
    		// currently installed.
    		$backOfficeRole = new core_kernel_classes_Resource(INSTANCE_ROLE_BACKOFFICE);
    		$roleLabel = $this->extension->getID() . ' Manager';
    		$roleClass = new core_kernel_classes_Class(CLASS_MANAGEMENTROLE);
    		$role = $roleService->addRole($roleLabel, $backOfficeRole, $roleClass);
    	}

    	// Take the Global Manager role and make it include
    	// the Management role of the currently installed extension.
    	if ($role->getUri() !== INSTANCE_ROLE_GLOBALMANAGER){
    		$globalManagerRole = new core_kernel_classes_Resource(INSTANCE_ROLE_GLOBALMANAGER);
    		$roleService->includeRole($globalManagerRole, $role);
    	}
    	
    	// Give the Management role access to all modules of the currently
    	// installed extension.
    	$extAccessService = tao_models_classes_funcACL_ExtensionAccessService::singleton();
    	$extAccessService->add($role->getUri(), $extAccessService->makeEMAUri($this->extension->getID()));
    	
    	common_Logger::i("Management Role " . $role->getUri() . " created for extension '" . $this->extension->getID() . "'.");
    }

} /* end of class tao_install_ExtensionInstaller */

?>