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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoDacSimple\scripts\update;

use oat\taoDacSimple\model\PermissionProvider;
use oat\taoDacSimple\model\AdminService;
use oat\taoBackOffice\model\menuStructure\ClassActionRegistry;
use oat\generis\model\data\permission\PermissionManager;
use oat\taoDacSimple\model\action\AdminAction;
use \core_kernel_classes_Class;
/**
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class Updater extends \common_ext_ExtensionUpdater {
    
    /**
     * 
     * @param string $currentVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion) {
        
        
        if ($this->isVersion('1.0')) {
            $impl = new PermissionProvider();
            
            // add read access to Items
            $class = new \core_kernel_classes_Class(TAO_ITEM_CLASS);
            AdminService::addPermissionToClass($class, INSTANCE_ROLE_BACKOFFICE, array('READ'));
            
            // add backoffice user rights to Tests
            $class = new \core_kernel_classes_Class(TAO_TEST_CLASS);
            AdminService::addPermissionToClass($class, INSTANCE_ROLE_BACKOFFICE, $impl->getSupportedRights());

            $this->setVersion('1.0.1');
        }
        if ($this->isVersion('1.0.1')) {
            $this->setVersion('1.0.2');
        }
        if ($this->isVersion( '1.0.2')) {
            $taoClass = new \core_kernel_classes_Class(TAO_OBJECT_CLASS);
            $classAdmin = new AdminAction();
            ClassActionRegistry::getRegistry()->registerAction($taoClass, $classAdmin);
            
            $this->setVersion('1.1');
        }
        if ($this->isVersion('1.1')) {
            $classesToAdd = array(
                new \core_kernel_classes_Class(CLASS_GENERIS_USER),
                new \core_kernel_classes_Class(CLASS_ROLE)
            );
            
            // add admin to new instances
            $classAdmin = new AdminAction();
            foreach ($classesToAdd as $class) {
                ClassActionRegistry::getRegistry()->registerAction($class, $classAdmin);
            }
            
            // add base permissions to new classes
            $taoClass = new \core_kernel_classes_Class(TAO_OBJECT_CLASS);
            foreach ($taoClass->getSubClasses(false) as $class) {
                if (!in_array($class->getUri(), array(TAO_ITEM_CLASS,TAO_TEST_CLASS))) {
                    $classesToAdd[] = $class;
                }
            }
            $rights = PermissionManager::getPermissionModel()->getSupportedRights();
            foreach ($classesToAdd as $class) {
                if (count(AdminService::getUsersPermissions($class->getUri())) == 0) {
                    AdminService::addPermissionToClass($class, INSTANCE_ROLE_BACKOFFICE, $rights);
                } else {
                    \common_Logger::w('Unexpected rights present for '.$class->getUri());
                }
            }
            $this->setVersion('1.2.0');
        }

        $this->skip('1.2.0','1.2.2');
        return null;
    }
}