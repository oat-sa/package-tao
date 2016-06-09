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
namespace oat\taoDelivery\scripts\update;

use oat\oatbox\service\ServiceNotFoundException;
use oat\tao\scripts\update\OntologyUpdater;
use oat\tao\model\entryPoint\EntryPointService;

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
        
        //migrate from 2.6 to 2.6.1
        if ($currentVersion == '2.6') {

            //data upgrade
            OntologyUpdater::syncModels();
            $currentVersion = '2.6.1';
        }
        
        if ($currentVersion == '2.6.1') {
            $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
            $className = $ext->getConfig(\taoDelivery_models_classes_execution_ServiceProxy::CONFIG_KEY);
            if (is_string($className)) {
                $impl = null;
                switch ($className) {
                	case 'taoDelivery_models_classes_execution_OntologyService' :
                	    $impl = new \taoDelivery_models_classes_execution_OntologyService();
                	    break;
                	case 'taoDelivery_models_classes_execution_KeyValueService' :
                	    $impl = new \taoDelivery_models_classes_execution_KeyValueService(array(
                    	    \taoDelivery_models_classes_execution_KeyValueService::OPTION_PERSISTENCE => 'deliveryExecution'
                	    ));
                	    break;
                	default :
                	    \common_Logger::w('Unable to migrate custom execution service');
                }
                if (!is_null($impl)) {
                    $proxy = \taoDelivery_models_classes_execution_ServiceProxy::singleton();
                    $proxy->setImplementation($impl);
                    $currentVersion = '2.6.2';
                }
            }
        }
        if ($currentVersion == '2.6.2') {
             $currentVersion = '2.6.3';
        }

        if ($currentVersion == '2.6.3') {
        
            //data upgrade
            OntologyUpdater::syncModels();
            $currentVersion = '2.7.0';
        }
        

        if ($currentVersion == '2.7.0') {
            EntryPointService::getRegistry()->registerEntryPoint(new \taoDelivery_models_classes_entrypoint_FrontOfficeEntryPoint());
            $currentVersion = '2.7.1';
        }
        
        if ($currentVersion == '2.7.1' || $currentVersion == '2.8') {
            $currentVersion = '2.9';
        }

        if( $currentVersion == '2.9'){
            OntologyUpdater::syncModels();

            //grant access to anonymous user
            $anonymousRole = new \core_kernel_classes_Resource(INSTANCE_ROLE_ANONYMOUS);
            $accessService = \funcAcl_models_classes_AccessService::singleton();
            $accessService->grantActionAccess($anonymousRole, 'taoDelivery', 'DeliveryServer', 'guest');

            $currentVersion = '2.9.1';
        }

        if( $currentVersion == '2.9.1'){
            OntologyUpdater::syncModels();
            $currentVersion = '2.9.2';
        }

        if ($currentVersion == '2.9.2') {
            //$assignmentService = new \taoDelivery_models_classes_AssignmentService();
            //$this->getServiceManager()->register('taoDelivery/assignment', $assignmentService);
            $currentVersion = '2.9.3';
        }

        if ($currentVersion == '2.9.3') {
            try{
                $currentConfig = $this->getServiceManager()->get(\taoDelivery_models_classes_DeliveryServerService::CONFIG_ID);
                if (is_array($currentConfig)) {
                    $deliveryServerService = new \taoDelivery_models_classes_DeliveryServerService($currentConfig);
                } else {
                    $deliveryServerService = new \taoDelivery_models_classes_DeliveryServerService();
                }
            }catch(ServiceNotFoundException $e){
                $deliveryServerService = new \taoDelivery_models_classes_DeliveryServerService();
            }
            $this->getServiceManager()->register(\taoDelivery_models_classes_DeliveryServerService::CONFIG_ID, $deliveryServerService);
            $currentVersion = '2.9.4';
        }

        $this->setVersion($currentVersion);
        
        if ($this->isVersion('2.9.4')) {
            OntologyUpdater::syncModels();
            $this->setVersion('3.0.0');
        }
        
        if ($this->isBetween('3.0.0','3.1.0')) {
            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
            $config = $extension->getConfig('deliveryServer');
            $config->setOption('deliveryContainer', 'oat\\taoDelivery\\helper\\container\\DeliveryServiceContainer');
            $extension->setConfig('deliveryServer', $config);
            $this->setVersion('3.1.0');
        }
        
        $this->skip('3.1.0','3.2.0');

        if ($this->isVersion('3.2.0')) {
            // set the test runner controller
            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
            $config = $extension->getConfig('testRunner');
            $config['serviceController'] = 'Runner';
            $config['serviceExtension'] = 'taoQtiTest';
            $extension->setConfig('testRunner', $config);

            $this->setVersion('3.3.0');
        }

        $this->skip('3.3.0','3.3.2');
    }
}
