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
 *               2013-2014 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\model\ClientLibRegistry;
use oat\tao\helpers\translation\rdf\RdfPack;
use oat\generis\model\data\ModelManager;
use oat\tao\model\extension\ExtensionModel;

/**
 * Specification of the Generis ExtensionInstaller class to add a new behavior:
 * the Modules and Actions in the Ontology at installation time.
 *
 * @access public
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @package tao
 * @since 2.4
 *       
 */
class tao_install_ExtensionInstaller extends common_ext_ExtensionInstaller
{
    // --- ASSOCIATIONS ---
    
    // --- ATTRIBUTES ---
    
    // --- OPERATIONS ---
    
    /**
     * Override the default model with the translated model
     * 
     * (non-PHPdoc)
     * @see common_ext_ExtensionInstaller::getExtensionModel()
     */
    public function getExtensionModel() {
        return new ExtensionModel($this->extension);
    }
    
    /**
     * Will create the model of Modules and Actions (MVC) in the persistent
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return void
     * @since 2.4
     */
    public function extendedInstall()
    {
        $this->installManagementRole();
        $this->applyAccessRules();
        $this->registerClientLib();
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
    public function installManagementRole()
    {
        
        // Try to get a Management Role described by the extension itself.
        // (this information comes actually from the Manifest of the extension)
        $roleUri = $this->extension->getManifest()->getManagementRoleUri();
        if (! empty($roleUri)) {
            
            $role = new core_kernel_classes_Resource($roleUri);
            $roleService = tao_models_classes_RoleService::singleton();
            if (! $role->exists()) {
                // Management role does not exist yet, so we create it
                $roleClass = new core_kernel_classes_Class(CLASS_MANAGEMENTROLE);
                $roleLabel = $this->extension->getId() . ' Manager';
                $role = $roleClass->createInstance($roleLabel, $roleLabel . ' Role', $role->getUri());
                $roleService->includeRole($role, new core_kernel_classes_Resource(INSTANCE_ROLE_BACKOFFICE));
            }
            
            // Take the Global Manager role and make it include
            // the Management role of the currently installed extension.
            if ($role->getUri() !== INSTANCE_ROLE_GLOBALMANAGER) {
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
    public function applyAccessRules()
    {
        foreach ($this->extension->getManifest()->getAclTable() as $tableEntry) {
            $rule = new AccessRule($tableEntry[0], $tableEntry[1], $tableEntry[2]);
            AclProxy::applyRule($rule);
        }
    }

    /**
     * Extension may declare client lib to add alias into requireJs
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function registerClientLib()
    {        
        $extManifestConsts = $this->extension->getConstants();
        if(isset($extManifestConsts['BASE_WWW'])){
            ClientLibRegistry::getRegistry()->register($this->extension->getId(),$extManifestConsts['BASE_WWW']. 'js');
            ClientLibRegistry::getRegistry()->register($this->extension->getId().'Css',$extManifestConsts['BASE_WWW']. 'css');
            
        }
    }
}
