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
namespace oat\taoDeliveryRdf\install\update;

use oat\tao\scripts\update\OntologyUpdater;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\model\accessControl\func\AccessRule;
use oat\taoDeliveryRdf\model\GroupAssignment;
use oat\taoDelivery\model\AssignmentService;
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
        
        $currentVersion = $initialVersion;
        
        //migrate ACL
        if ($currentVersion == '0.1') {

            $MngrRole = new \core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryManagerRole');
            $accessService = \funcAcl_models_classes_AccessService::singleton();
            $accessService->grantModuleAccess($MngrRole, 'taoDeliveryRdf', 'DeliveryMgmt');
            $currentVersion = '0.2';
            $this->setVersion($currentVersion);
        }
        
        if ($this->isVersion('0.2')) {
            OntologyUpdater::syncModels();
            AclProxy::applyRule(new AccessRule(
                'grant',
                'http://www.tao.lu/Ontologies/generis.rdf#AnonymousRole',
                array('controller'=>'oat\\taoDeliveryRdf\\controller\\Guest')));
            
            $currentService = $this->safeLoadService(AssignmentService::CONFIG_ID);
            if (class_exists('taoDelivery_models_classes_AssignmentService', false)
                && $currentService instanceof \taoDelivery_models_classes_AssignmentService) {
            
                    $assignmentService = new GroupAssignment();
                    $this->getServiceManager()->register(AssignmentService::CONFIG_ID, $assignmentService);
            }
            
            $this->setVersion('1.0.0');
        }

        if ($this->isVersion('1.0.0')){
            $this->setVersion('1.0.1');
        }
        
        if ($this->isVersion('1.0.1')) {
            OntologyUpdater::syncModels();
            $this->setVersion('1.1.0');
        }
        
        $this->skip('1.1.0', '1.2.0');
    }
}
