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

namespace oat\taoMediaManager\scripts\update;

use oat\tao\model\media\MediaService as TaoMediaService;
use oat\tao\scripts\update\OntologyUpdater;
use oat\taoMediaManager\model\fileManagement\FileManager;
use oat\taoMediaManager\model\fileManagement\SimpleFileManagement;
use oat\taoMediaManager\model\MediaService;
use oat\taoMediaManager\model\MediaSource;
use oat\taoMediaManager\model\SharedStimulusImporter;

class Updater extends \common_ext_ExtensionUpdater
{

    /**
     *
     * @param string $initialVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion)
    {

        $currentVersion = $initialVersion;

        //migrate from 0.1 to 0.1.1
        if ($currentVersion == '0.1') {
            // mediaSources set in 0.2
            $currentVersion = '0.1.1';
        }
        if ($currentVersion == '0.1.1') {

            FileManager::setFileManagementModel(new SimpleFileManagement());
            // mediaSources unset in 0.2

            $currentVersion = '0.1.2';
        }
        if ($currentVersion == '0.1.2') {

            //add alt text to media manager
            $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'alt_text.rdf';

            $adapter = new \tao_helpers_data_GenerisAdapterRdf();
            if ($adapter->import($file)) {
                $currentVersion = '0.1.3';
            } else {
                \common_Logger::w('Import failed for ' . $file);
            }
        }


        if ($currentVersion == '0.1.3') {

            OntologyUpdater::correctModelId(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'alt_text.rdf');
            $currentVersion = '0.1.4';

        }

        if ($currentVersion == '0.1.4') {

            //modify config files due to the new interfaces relation
            $tao = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
            $tao->unsetConfig('mediaManagementSources');
            $tao->unsetConfig('mediaBrowserSources');

            TaoMediaService::singleton()->addMediaSource(new MediaSource());

            //modify links in item content
            $service = \taoItems_models_classes_ItemsService::singleton();
            $items = $service->getAllByModel('http://www.tao.lu/Ontologies/TAOItem.rdf#QTI');

            foreach ($items as $item) {
                $itemContent = $service->getItemContent($item);
                $itemContent = preg_replace_callback('/src="mediamanager\/([^"]+)"/', function ($matches) {
                    $mediaClass = MediaService::singleton()->getRootClass();
                    $medias = $mediaClass->searchInstances(array(
                        MEDIA_LINK => $matches[1]
                    ), array('recursive' => true));
                    $media = array_pop($medias);
                    $uri = '';
                    if (!is_null($media) && $media->exists()) {
                        $uri = \tao_helpers_Uri::encode($media->getUri());
                    }
                    return 'src="taomedia://mediamanager/' . $uri . '"';


                }, $itemContent);

                $itemContent = preg_replace_callback('/src="local\/([^"]+)"/', function ($matches) {
                    return 'src="' . $matches[1] . '"';

                }, $itemContent);

                $service->setItemContent($item, $itemContent);

            }

            $currentVersion = '0.2.0';

        }

        if ($currentVersion === '0.2.0') {
            $accessService = \funcAcl_models_classes_AccessService::singleton();

            //revoke access right to back office
            $backOffice = new \core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole');
            $accessService->revokeExtensionAccess($backOffice, 'taoMediaManager');

            //grant access right to media manager
            $mediaManager = new \core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOMedia.rdf#MediaManagerRole');
            $accessService->grantExtensionAccess($mediaManager, 'taoMediaManager');

            $currentVersion = '0.2.1';
        }

        if ($currentVersion === '0.2.1') {
            //include mediamanager into globalmanager
            $mediaManager = new \core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOMedia.rdf#MediaManagerRole');
            $globalManager = new \core_kernel_Classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#GlobalManagerRole');
            \tao_models_classes_RoleService::singleton()->includeRole($globalManager, $mediaManager);
            $currentVersion = '0.2.2';
        }

        if($currentVersion === '0.2.2'){
            //copy file from /media to data/taoMediaManager/media and delete /media
            $dataPath = FILES_PATH . 'taoMediaManager' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR;
            $dir = dirname(dirname(__DIR__)) . '/media';

            if(file_exists($dir)){
                if(\tao_helpers_File::copy($dir, $dataPath)){
                    $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
                    $files = new \RecursiveIteratorIterator($it,
                        \RecursiveIteratorIterator::CHILD_FIRST);
                    foreach($files as $file) {
                        if ($file->isDir()){
                            rmdir($file->getRealPath());
                        } else {
                            unlink($file->getRealPath());
                        }
                    }
                    rmdir($dir);
                    $currentVersion = '0.2.3';
                }
            }
            else{
                $currentVersion = '0.2.3';
            }

        }

        if ($currentVersion === '0.2.3') {
            $accessService = \funcAcl_models_classes_AccessService::singleton();
            //grant access to item author
            $itemAuthor = new \core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOItem.rdf#ItemAuthor');
            $accessService->grantExtensionAccess($itemAuthor, 'taoMediaManager');
            $currentVersion = '0.2.4';
        }

        if ($currentVersion === '0.2.4') {
            $mediaClass = MediaService::singleton()->getRootClass();
            $fileManager = FileManager::getFileManagementModel();
            foreach($mediaClass->getInstances(true) as $media){
                $fileLink = $media->getUniquePropertyValue(new \core_kernel_classes_Property(MEDIA_LINK));
                $fileLink = $fileLink instanceof \core_kernel_classes_Resource ? $fileLink->getUri() : (string)$fileLink;
                $filePath = $fileManager->retrieveFile($fileLink);
                $mimeType = \tao_helpers_File::getMimeType($filePath);
                $mimeType = ($mimeType === 'application/xhtml+xml') ? 'application/qti+xml' : $mimeType;
                $media->setPropertyValue(new \core_kernel_classes_Property(MEDIA_MIME_TYPE), $mimeType);
            }
            $currentVersion = '0.2.5';
        }

        if ($currentVersion === '0.2.5') {
            $fileManager = FileManager::getFileManagementModel();
            $iterator = new \core_kernel_classes_ResourceIterator(array(MediaService::singleton()->getRootClass()));
            foreach ($iterator as $media) {
                $fileLink = $media->getUniquePropertyValue(new \core_kernel_classes_Property(MEDIA_LINK));
                $fileLink = $fileLink instanceof \core_kernel_classes_Resource ? $fileLink->getUri() : (string)$fileLink;
                $filePath = $fileManager->retrieveFile($fileLink);
                try {
                    SharedStimulusImporter::isValidSharedStimulus($filePath);
                    $media->editPropertyValues(new \core_kernel_classes_Property(MEDIA_MIME_TYPE), 'application/qti+xml');
                } catch (\Exception $e) {
                    $mimeType = \tao_helpers_File::getMimeType($filePath);
                    $media->editPropertyValues(new \core_kernel_classes_Property(MEDIA_MIME_TYPE), $mimeType);
                } 
            }
            $currentVersion = '0.3.0';
        }
        
        $this->skip('0.3.0','0.4.3');
        
        return null;
    }
}
