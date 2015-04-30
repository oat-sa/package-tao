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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @license GPLv2
 * @package taoDevTools
 *
 */
namespace oat\taoDevTools\helper;

use oat\taoQtiItem\model\qti\ImportService;
use helpers_TimeOutHelper;
use oat\taoTestTaker\models\TestTakerService;

class DataGenerator
{
    public static function generateItems($count = 100) {
        // load QTI constants
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem');
        
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoDevTools');
        
        $generationId = NameGenerator::generateRandomString(4);
        
        $topClass = new \core_kernel_classes_Class(TAO_ITEM_CLASS);
        $class = $topClass->createSubClass('Generation '.$generationId);
        $fileClass = new \core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#File');
        
        $sampleFile = $ext->getDir().'data/items/sampleItem.xml';
        
        helpers_TimeOutHelper::setTimeOutLimit(helpers_TimeOutHelper::LONG);
        for ($i = 0; $i < $count; $i++) {
        
            $report = ImportService::singleton()->importQTIFile($sampleFile, $class, false);
            $item = $report->getData();
            $item->setLabel(NameGenerator::generateTitle());
        }
        helpers_TimeOutHelper::reset();
        
        return $class;
    }
    
    public static function generateGlobalManager($count = 100) {
        $topClass = new \core_kernel_classes_Class(CLASS_TAO_USER);
        $role = new \core_kernel_classes_Resource(INSTANCE_ROLE_GLOBALMANAGER);
        $class = self::generateUsers($count, $topClass, $role, 'Backoffice user', 'user');
        
        return $class;
    }
    
    public static function generateTesttakers($count = 1000) {
        
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoGroups');
        
        
        $topClass = new \core_kernel_classes_Class(TAO_SUBJECT_CLASS);
        $role = new \core_kernel_classes_Resource(INSTANCE_ROLE_DELIVERY);
        $class = self::generateUsers($count, $topClass, $role, 'Test-Taker ', 'tt');
        
        $groupClass = new \core_kernel_classes_Class(TAO_GROUP_CLASS);
        $group = $groupClass->createInstanceWithProperties(array(
            RDFS_LABEL => $class->getLabel(),
            TAO_GROUP_MEMBERS_PROP => $class->getInstances()
        ));
        
        return $class;
    }
    
    protected static function generateUsers($count, $class, $role, $label, $prefix) {
        
        $userExists = \tao_models_classes_UserService::singleton()->loginExists($prefix.'0');
        if ($userExists) {
            throw new \common_exception_Error($label.' 0 already exists, Generator already run?');
        }
        
        $generationId = NameGenerator::generateRandomString(4);
        $subClass = $class->createSubClass('Generation '.$generationId);
        
        helpers_TimeOutHelper::setTimeOutLimit(helpers_TimeOutHelper::LONG);
        for ($i = 0; $i < $count; $i++) {
            $tt = $subClass->createInstanceWithProperties(array(
                RDFS_LABEL => $label.' '.$i,
                PROPERTY_USER_UILG	=> 'http://www.tao.lu/Ontologies/TAO.rdf#Langen-US',
                PROPERTY_USER_DEFLG => 'http://www.tao.lu/Ontologies/TAO.rdf#Langen-US',
                PROPERTY_USER_LOGIN	=> $prefix.$i,
                PROPERTY_USER_PASSWORD => \core_kernel_users_Service::getPasswordHash()->encrypt('pass'.$i),
                PROPERTY_USER_ROLES => $role,
                PROPERTY_USER_FIRSTNAME => $label.' '.$i,
                PROPERTY_USER_LASTNAME => 'Family '.$generationId
            ));
        }
        
        helpers_TimeOutHelper::reset();
        return $subClass;
    }
}
