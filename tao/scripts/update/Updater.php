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

namespace oat\tao\scripts\update;

use common_ext_ExtensionsManager;
use tao_helpers_data_GenerisAdapterRdf;
use common_Logger;
use oat\tao\model\ClientLibRegistry;
use oat\generis\model\kernel\persistence\file\FileModel;
use oat\generis\model\data\ModelManager;
use oat\tao\model\lock\implementation\OntoLock;
use oat\tao\model\lock\implementation\NoLock;
use oat\tao\model\lock\LockManager;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\websource\TokenWebSource;
use oat\tao\model\websource\WebsourceManager;
use oat\tao\model\websource\ActionWebSource;
use oat\tao\model\websource\DirectWebSource;

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
        $extensionManager = common_ext_ExtensionsManager::singleton();
        //migrate from 2.6 to 2.7.0
        if ($currentVersion == '2.6') {

            //create Js config  
            $ext = $extensionManager->getExtensionById('tao');
            $config = array(
                'timeout' => 30
            );
            $ext->setConfig('js', $config);

            $currentVersion = '2.7.0';
        }
        
        //migrate from 2.7.0 to 2.7.1
        if ($currentVersion == '2.7.0') {
        
            $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'indexation_2_7_1.rdf';
        
            $adapter = new tao_helpers_data_GenerisAdapterRdf();
            if ($adapter->import($file)) {
                $currentVersion = '2.7.1';
            } else{
                common_Logger::w('Import failed for '.$file);
            }
        }
        
        if ($currentVersion == '2.7.1') {
            foreach ($extensionManager->getInstalledExtensions() as $extension) {
                $extManifestConsts = $extension->getConstants();
                if (isset($extManifestConsts['BASE_WWW'])) {
                    
                    ClientLibRegistry::getRegistry()->register($extension->getId(), $extManifestConsts['BASE_WWW'] . 'js');
                    ClientLibRegistry::getRegistry()->register($extension->getId() . 'Css', $extManifestConsts['BASE_WWW'] . 'css');
                    
                }
            }
            
            $currentVersion = '2.7.2';
            
        }

        if ($currentVersion == '2.7.2') {
            // zendSearch Update only
            $currentVersion = '2.7.3';
        }
        
        if ($currentVersion == '2.7.3') {
        
            $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'indexation_2_7_4.rdf';
        
            $adapter = new tao_helpers_data_GenerisAdapterRdf();
            if ($adapter->import($file)) {
                $currentVersion = '2.7.4';
            } else{
                common_Logger::w('Import failed for '.$file);
            }
        }
        
        if ($currentVersion == '2.7.4') {
            $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'model_2_7_5.rdf';
            
            $adapter = new tao_helpers_data_GenerisAdapterRdf();
            if ($adapter->import($file)) {
                $currentVersion = '2.7.5';
            } else{
                common_Logger::w('Import failed for '.$file);
            }
        }
        
        if ($currentVersion == '2.7.5') {
            $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'index_type_2_7_6.rdf';
        
            $adapter = new tao_helpers_data_GenerisAdapterRdf();
            if ($adapter->import($file)) {
                $currentVersion = '2.7.6';
            } else{
                common_Logger::w('Import failed for '.$file);
            }
        }
        
        if ($currentVersion == '2.7.6') {
            
            $dir = FILES_PATH.'updates'.DIRECTORY_SEPARATOR.'pre_'.$currentVersion;
            if (!mkdir($dir, 0700, true)) {
                throw new \common_exception_Error('Unable to log update to '.$dir);
            }
            FileModel::toFile($dir.DIRECTORY_SEPARATOR.'backup.rdf', ModelManager::getModel()->getRdfInterface());
            
            OntologyUpdater::correctModelId(dirname(__FILE__).DIRECTORY_SEPARATOR.'indexation_2_7_1.rdf');
            OntologyUpdater::correctModelId(dirname(__FILE__).DIRECTORY_SEPARATOR.'indexation_2_7_4.rdf');
            OntologyUpdater::correctModelId(dirname(__FILE__).DIRECTORY_SEPARATOR.'model_2_7_5.rdf');
            OntologyUpdater::correctModelId(dirname(__FILE__).DIRECTORY_SEPARATOR.'index_type_2_7_6.rdf');
            
            // syncronise also adds translations to correct modelid
            OntologyUpdater::syncModels();
            
            // remove translations from model 1
            $persistence = \common_persistence_SqlPersistence::getPersistence('default');

            $result = $persistence->query("SELECT DISTINCT subject FROM statements WHERE NOT modelId = 1");
            $toCleanup = array();
            while ($row = $result->fetch()) {
                $toCleanup[] = $row['subject'];
            }
            
            $query = "DELETE from statements WHERE modelId = 1 AND subject = ? "
                    ."AND predicate IN ('".RDFS_LABEL."','".RDFS_COMMENT."') ";
            foreach ($toCleanup as $subject) {
                $persistence->exec($query,array($subject));
            }

            $currentVersion = '2.7.7';
        }
        
        if ($currentVersion == '2.7.7') {
            $lockImpl = (defined('ENABLE_LOCK') && ENABLE_LOCK)
                ? new OntoLock()
                : new NoLock();
            LockManager::setImplementation($lockImpl);
            AclProxy::applyRule(new AccessRule('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole', array('ext'=>'tao','mod' => 'Lock')));
            
            $currentVersion = '2.7.8';
        }

        if ($currentVersion == '2.7.8') {
            if ($this->migrateFsAccess()) {
                $currentVersion = '2.7.9';
            }
        }
        
        if ($currentVersion == '2.7.9') {
            // update role classes
            OntologyUpdater::syncModels();
            $currentVersion = '2.7.10';
        }
        
        if ($currentVersion == '2.7.10') {
            // correct access roles
            AclProxy::applyRule(new AccessRule('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole', array('act'=>'tao_actions_Lists@getListElements')));
            AclProxy::revokeRule(new AccessRule('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole', array('ext'=>'tao','mod' => 'Lock')));
            AclProxy::applyRule(new AccessRule('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole', array('act'=>'tao_actions_Lock@release')));
            AclProxy::applyRule(new AccessRule('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole', array('act'=>'tao_actions_Lock@locked')));
            AclProxy::applyRule(new AccessRule('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#LockManagerRole', array('act'=>'tao_actions_Lock@forceRelease')));
            $currentVersion = '2.7.11';
        }
        
        if ($currentVersion == '2.7.11') {
            // move session abstraction
            if (defined("PHP_SESSION_HANDLER") && class_exists(PHP_SESSION_HANDLER)) {
                if (PHP_SESSION_HANDLER == 'common_session_php_KeyValueSessionHandler') {
                    $sessionHandler = new \common_session_php_KeyValueSessionHandler(array(
                        \common_session_php_KeyValueSessionHandler::OPTION_PERSISTENCE => 'session'
                    ));
                } else {
                    $sessionHandler = new PHP_SESSION_HANDLER();  
                }
                $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
                $ext->setConfig(\Bootstrap::CONFIG_SESSION_HANDLER, $sessionHandler);
            }
            $currentVersion = '2.7.12';
        }
        
        if ($currentVersion == '2.7.12') {
            // add the property manager
            OntologyUpdater::syncModels();
            
            AclProxy::applyRule(new AccessRule('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#PropertyManagerRole', array('controller' => 'tao_actions_Lists')));
            AclProxy::applyRule(new AccessRule('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#PropertyManagerRole', array('controller' => 'tao_actions_PropertiesAuthoring')));
            $currentVersion = '2.7.13';
        }
        
        return $currentVersion;
    }
    
    private function migrateFsAccess() {
        $tao = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        $config = $tao->getConfig('filesystemAccess');
        if (is_array($config)) {
            foreach ($config as $id => $string) {
                list($class, $id, $fsUri, $jsconfig) = explode(' ', $string, 4);
                $config = json_decode($jsconfig, true);
                $options = array(
                    TokenWebSource::OPTION_ID => $id,
                    TokenWebSource::OPTION_FILESYSTEM_ID => $fsUri,
                );
                switch ($class) {
                	case 'tao_models_classes_fsAccess_TokenAccessProvider' :
                	    $fs = new \core_kernel_fileSystem_FileSystem($fsUri);
                        $options[TokenWebSource::OPTION_PATH] = $fs->getPath();
                	    $options[TokenWebSource::OPTION_SECRET] = $config['secret'];
                	    $options[TokenWebSource::OPTION_TTL] = (int) ini_get('session.gc_maxlifetime');
                	    $websource = new TokenWebSource($options);
                	    break;
                	case 'tao_models_classes_fsAccess_ActionAccessProvider' :
                	    $websource = new ActionWebSource($options);
                	    break;
                	case 'tao_models_classes_fsAccess_DirectAccessProvider' :
                	    $options[DirectWebSource::OPTION_URL] = $config['accessUrl'];
                	    $websource = new DirectWebSource($options);
                	    break;
                	default:
                	    throw \common_Exception('unknown implementation '.$class);
                }
                WebsourceManager::singleton()->addWebsource($websource);
            }
        } else {
            throw \common_Exception('Error reading former filesystem access configuration');
        }
        return true;
    }
}
