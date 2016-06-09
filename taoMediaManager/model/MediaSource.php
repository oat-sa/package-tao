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
namespace oat\taoMediaManager\model;

use oat\oatbox\Configurable;
use oat\tao\model\media\MediaManagement;
use oat\taoMediaManager\model\fileManagement\FileManager;

class MediaSource extends Configurable implements MediaManagement
{

    private $lang;

    private $rootClassUri;

    /**
     * get the lang of the class in case we want to filter the media on language
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoMediaManager');
        $this->lang = (isset($options['lang'])) ? $options['lang'] : '';
        $this->rootClassUri = (isset($options['rootClass'])) ? $options['rootClass'] : MediaService::singleton()->getRootClass();
    }
    
    public function getRootClass()
    {
        return new \core_kernel_classes_Class($this->rootClassUri);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \oat\tao\model\media\MediaManagement::add
     */
    public function add($source, $fileName, $parent)
    {
        if (!file_exists($source)) {
            throw new \tao_models_classes_FileNotFoundException($source);
        }
        
        $clazz = $this->getOrCreatePath($parent);
        
        $service = MediaService::singleton();
        $instanceUri = $service->createMediaInstance($source, $clazz->getUri(), $this->lang, $fileName);
        
        return $this->getFileInfo($instanceUri);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \oat\tao\model\media\MediaManagement::delete
     */
    public function delete($link)
    {
        return MediaService::singleton()->deleteResource(new \core_kernel_classes_Resource(\tao_helpers_Uri::decode($link)));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \oat\tao\model\media\MediaBrowser::getDirectory
     */
    public function getDirectory($parentLink = '', $acceptableMime = array(), $depth = 1)
    {
        if ($parentLink == '') {
            $class = new \core_kernel_classes_Class($this->rootClassUri);
        } else {
            $class = new \core_kernel_classes_Class(\tao_helpers_Uri::decode($parentLink));
        }

        $data = array(
            'path' => 'taomedia://mediamanager/' . \tao_helpers_Uri::encode($class->getUri()),
            'label' => $class->getLabel()
        );

        if ($depth > 0) {
            $children = array();
            foreach ($class->getSubClasses() as $subclass) {
                $children[] = $this->getDirectory($subclass->getUri(), $acceptableMime, $depth - 1);
            }

            // add a filter for example on language (not for now)
            $filter = array();

            foreach ($class->searchInstances($filter) as $instance) {
                try{
                    $file = $this->getFileInfo($instance->getUri());
                    if (count($acceptableMime) == 0 || in_array($file['mime'], $acceptableMime)) {
                        $children[] = $file;
                    }
                }catch(\tao_models_classes_FileNotFoundException $e){
                    \common_Logger::e($e->getMessage());
                }
            }
            $data['children'] = $children;
        } else {
            $data['parent'] = $parentLink;
        }
        return $data;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \oat\tao\model\media\MediaBrowser::getFileInfo
     */
    public function getFileInfo($link)
    {
        // get the media link from the resource
        $resource = new \core_kernel_classes_Resource(\tao_helpers_Uri::decode($link));
        if ($resource->exists()) {
            $fileLink = $resource->getUniquePropertyValue(new \core_kernel_classes_Property(MEDIA_LINK));
            $fileLink = $fileLink instanceof \core_kernel_classes_Resource ? $fileLink->getUri() : (string)$fileLink;
            $file = null;
            $fileManagement = FileManager::getFileManagementModel();
            $mime = (string) $resource->getUniquePropertyValue(new \core_kernel_classes_Property(MEDIA_MIME_TYPE));

            // add the alt text to file array
            $altArray = $resource->getPropertyValues(new \core_kernel_classes_Property(MEDIA_ALT_TEXT));
            $alt = $resource->getLabel();
            if (count($altArray) > 0) {
                $alt = $altArray[0];
            }

            $file = array(
                'name' => $resource->getLabel(),
                'uri' => 'taomedia://mediamanager/' . \tao_helpers_Uri::encode($link),
                'mime' => $mime,
                'size' => $fileManagement->getFileSize($fileLink),
                'alt' => $alt,
                'link' => $fileLink
            );
            return $file;
        } else {
            throw new \tao_models_classes_FileNotFoundException($link);
        }
    }
    
    /**
     * 
     * @param string $link
     * @throws \tao_models_classes_FileNotFoundException
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getFileStream($link)
    {
        $resource = new \core_kernel_classes_Resource(\tao_helpers_Uri::decode($link));
        $fileLink = $resource->getOnePropertyValue(new \core_kernel_classes_Property(MEDIA_LINK));
        if (is_null($fileLink)) {
            throw new \tao_models_classes_FileNotFoundException($link);
        }
        $fileLink = $fileLink instanceof \core_kernel_classes_Resource ? $fileLink->getUri() : (string)$fileLink;
        $fileManagement = FileManager::getFileManagementModel();
        return $fileManagement->getFileStream($fileLink);
        
    }

    /**
     * (non-PHPdoc)
     *
     * @see \oat\tao\model\media\MediaBrowser::download
     * @deprecated
     */
    public function download($link)
    {
        \common_Logger::w('Deprecated, creates tmpfiles');
        $stream = $this->getFileStream($link);
        $filename = tempnam(sys_get_temp_dir(), 'media');
        $fh = fopen($filename, 'w');
        while (!$stream->eof()) {
            fwrite($fh, $stream->read(1048576));
        }
        fclose($fh);
        return $filename;
    }

    public function getBaseName($link)
    {
        $stream = $this->getFileStream($link);
        return basename($stream->getMetadata('uri'));
    }

    /**
     * Force the mime-type of a resource
     * 
     * @param string $link
     * @param string $mimeType
     * @return boolean
     */
    public function forceMimeType($link, $mimeType)
    {
        $resource = new \core_kernel_classes_Resource(\tao_helpers_Uri::decode($link));
        return $resource->editPropertyValues(new \core_kernel_classes_Property(MEDIA_MIME_TYPE), $mimeType);
    }
    
    /**
     * 
     * @param string $path
     * @return \core_kernel_classes_Class
     */
    private function getOrCreatePath($path) {
        if ($path === '') {
            $clazz = $this->getRootClass();
        } else {
            $clazz = new \core_kernel_classes_Class(\tao_helpers_uri::decode($path));
            if (!$clazz->isSubClassOf($this->getRootClass()) && !$clazz->equals($this->getRootClass()) && !$clazz->exists()) {
                // consider $path to be a label
                $found = false;
                foreach($this->getRootClass()->getSubClasses() as $subclass){
                    if($subclass->getLabel() === $path){
                        $found = true;
                        $clazz = $subclass;
                        break;
                    }
                }
                if (!$found) {
                    $clazz = $this->getRootClass()->createSubClass($path);
                }
            }
        }
        return $clazz;
    }

    /**
     * @param string $md5 representing the file md5
     * @param \core_kernel_classes_Class $parent parent to add the instance to
     * @return \core_kernel_classes_Resource instance if file exists or null
     * @throws \common_exception_Error
     */
    private function getInstanceFromFile($md5, $parent){
        \common_Logger::w('Not yet implemented');
    }
}
