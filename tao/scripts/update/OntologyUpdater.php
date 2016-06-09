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

use AppendIterator;
use oat\generis\model\kernel\persistence\file\FileModel;
use oat\generis\model\data\ModelManager;
use helpers_RdfDiff;
use core_kernel_persistence_smoothsql_SmoothModel;
use common_persistence_SqlPersistence;
use common_ext_ExtensionsManager;
use core_kernel_persistence_smoothsql_SmoothIterator;
use oat\tao\model\extension\ExtensionModel;

class OntologyUpdater {
    
    static public function syncModels() {
        $currentModel = ModelManager::getModel();
        $modelIds = array_diff($currentModel->getReadableModels(),array('1'));
        
        $persistence = common_persistence_SqlPersistence::getPersistence('default');
        
        $smoothIterator = new core_kernel_persistence_smoothsql_SmoothIterator($persistence, $modelIds);
        
        $nominalModel = new AppendIterator();
        foreach (common_ext_ExtensionsManager::singleton()->getInstalledExtensions() as $ext) {
            $nominalModel->append(new ExtensionModel($ext));
        }
        
        $diff = helpers_RdfDiff::create($smoothIterator, $nominalModel);
        self::logDiff($diff);
        
        $diff->applyTo($currentModel);
    }
    
    static public function correctModelId($rdfFile) {
        $modelFile = new FileModel(array('file' => $rdfFile));
        $modelRdf = ModelManager::getModel()->getRdfInterface();
        foreach ($modelFile->getRdfInterface() as $triple) {
            $modelRdf->remove($triple);
            $modelRdf->add($triple);
        }
    }
    
    static protected function logDiff(\helpers_RdfDiff $diff) {
        $folder = FILES_PATH.'updates'.DIRECTORY_SEPARATOR;
        $updateId = time();
        while (file_exists($folder.$updateId)) {
            $count = isset($count) ? $count + 1 : 0;
            $updateId = time().'_'.$count;
        }
        $path = $folder.$updateId;
        if (!mkdir($path, 0700, true)) {
            throw new \common_exception_Error('Unable to log update to '.$path);
        }
        
        FileModel::toFile($path.DIRECTORY_SEPARATOR.'add.rdf', $diff->getTriplesToAdd());
        FileModel::toFile($path.DIRECTORY_SEPARATOR.'remove.rdf', $diff->getTriplesToRemove());
    }    
    
}
