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
 * 
 */

namespace oat\taoDevTools\actions;

/**
 * The Main Module of tao development tools
 *
 * @package taoDevTools
 * @subpackage actions
 * @license GPLv2 http://www.opensource.org/licenses/gpl-2.0.php
 *         
 */
class DataCreation extends \tao_actions_Main
{

    public function __construct()
    {
        parent::__construct();
    }

    public function createTesttakers()
    {
        $generationId = $this->generateRandomString(4);
        $count = $this->hasRequestParameter('count') ? $this->getRequestParameter('count') : 1000;
        
        set_time_limit($count);
        
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoGroups');
        
        $class = new \core_kernel_classes_Class(TAO_GROUP_CLASS);
        $group = $class->createInstanceWithProperties(array(
            RDFS_LABEL => 'Generation '.$generationId
        ));
        
        $topClass = new \core_kernel_classes_Class(TAO_SUBJECT_CLASS);
        $class = $topClass->createSubClass('Generation '.$generationId);
        for ($i = 0; $i < $count; $i++) {
            $tt = $class->createInstanceWithProperties(array(
            	RDFS_LABEL => 'Test taker '.$i,
                PROPERTY_USER_UILG	=> 'http://www.tao.lu/Ontologies/TAO.rdf#Langen-US',
                PROPERTY_USER_DEFLG => 'http://www.tao.lu/Ontologies/TAO.rdf#Langen-US',
                PROPERTY_USER_LOGIN	=> 'tt'.$i,
                PROPERTY_USER_PASSWORD => \core_kernel_users_AuthAdapter::getPasswordHash()->encrypt('pass'.$i),
                PROPERTY_USER_ROLES => 'http://www.tao.lu/Ontologies/TAO.rdf#DeliveryRole',
                PROPERTY_USER_FIRSTNAME => 'Testtaker '.$i,
                PROPERTY_USER_LASTNAME => 'Family '.$generationId
            ));
            $group->setPropertyValue(new \core_kernel_classes_Property(TAO_GROUP_MEMBERS_PROP), $tt);
        }
        echo 'created '.$count.' testakers';
    }
    
    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}