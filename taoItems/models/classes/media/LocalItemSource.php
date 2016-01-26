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
namespace oat\taoItems\model\media;

use common_exception_Error;
use oat\tao\model\media\MediaManagement;
use tao_helpers_File;
use taoItems_models_classes_ItemsService;
use DirectoryIterator;
/**
 * This media source gives access to files that are part of the item
 * and are addressed in a relative way
 */
class LocalItemSource implements MediaManagement
{

    /**
     * @return \core_kernel_classes_Resource
     */
    private $item;
    
    private $lang;

    public function __construct($data){
        $this->item = (isset($data['item'])) ? $data['item'] : null;
        $this->lang = (isset($data['lang'])) ? $data['lang'] : '';

    }

    /**
     * @return \core_kernel_classes_Resource
     */
    public function getItem()
    {
        return $this->item;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaBrowser::getDirectory
     */
    public function getDirectory($parentLink = '', $acceptableMime = array(), $depth = 1) {
        $sysPath = $this->getSysPath($parentLink);

        $label = rtrim($parentLink,'/');
        if(strrpos($parentLink, '/') !== false && substr($parentLink, -1) !== '/'){
            $label = substr($parentLink,strrpos($parentLink, '/') + 1);
            $parentLink = $parentLink.'/';
        }

        if(in_array($parentLink,array('','/'))){
            $label = $this->item->getLabel();
            $parentLink = '/';
        }

        $data = array(
            'path' => $parentLink,
            'label' => $label
        );

        if ($depth > 0 ) {
            $children = array();
            if (is_dir($sysPath)) {
                foreach (new DirectoryIterator($sysPath) as $fileinfo) {
                    if (!$fileinfo->isDot()) {
                        $subPath = rtrim($parentLink, '/').'/'.$fileinfo->getFilename();
                        if ($fileinfo->isDir()) {
                            $children[] = $this->getDirectory($subPath, $acceptableMime, $depth - 1);
                        } else {
                            $file = $this->getFileInfo($subPath, $acceptableMime);
                            if(!is_null($file) && (count($acceptableMime) == 0 || in_array($file['mime'], $acceptableMime))){
                                $children[] = $file;
                            }
                        }
                    }
                }
            } else {
                \common_Logger::w('"'.$sysPath.'" is not a directory');
            }
            $data['children'] = $children;
        }
        else{
                $data['parent'] = $parentLink;
        }
        return $data;
    }


    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaBrowser::getFileInfo
     */
    public function getFileInfo($link) {

        $sysPath = $this->getSysPath($link);
        if(file_exists($sysPath)){
            $file = array(
                'name' => basename($link),
                'uri' => $link,
                'mime' => tao_helpers_File::getMimeType($sysPath),
                'filePath' => $link,
                'size' => filesize($sysPath),
            );
        } else {
            throw new \tao_models_classes_FileNotFoundException($link);
        }
        return $file;
    }

    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaBrowser::download
     */
    public function download($filename){

        $sysPath = $this->getSysPath($filename);
        if(!file_exists($sysPath)) {
            if (file_exists($sysPath.'.js')) {
                $sysPath = $sysPath.'.js';
            } else {
                throw new \tao_models_classes_FileNotFoundException($filename);
            }
        }
        return $sysPath;
    }


    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaManagement::add
     */
    public function add($source, $fileName, $parent)
    {
        if (!\tao_helpers_File::securityCheck($fileName, true)) {
            throw new \common_Exception('Unsecured filename "'.$fileName.'"');
        }         

        $sysPath = $this->getSysPath($parent.$fileName);

        if(!tao_helpers_File::copy($source, $sysPath)){
            throw new \common_exception_Error('Unable to move file '.$source);
        }

        $fileData = $this->getFileInfo('/'.ltrim($parent, '/').$fileName, array());
        return $fileData;

    }

    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaManagement::delete
     */
    public function delete($filename)
    {
        $deleted = false;

        $sysPath = $this->getSysPath($filename);
        if(is_file($sysPath) && !is_dir($sysPath)){
            $deleted = unlink($sysPath);
        }

        return $deleted;
    }

    /**
     * @param $parentLink
     * @return string
     * @throws common_exception_Error
     */
    private function getSysPath($parentLink)
    {
        $baseDir = taoItems_models_classes_ItemsService::singleton()->getItemFolder($this->item, $this->lang);

        $sysPath = $baseDir.ltrim($parentLink, '/');
        if(!tao_helpers_File::securityCheck($sysPath)){
            throw new common_exception_Error(__('Your path contains error'));
        }

        return $sysPath;
    }
}
