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

use oat\tao\scripts\update\OntologyUpdater;

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
        
        return $currentVersion;
    }
}
