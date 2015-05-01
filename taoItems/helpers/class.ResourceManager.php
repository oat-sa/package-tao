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
use oat\tao\model\media\MediaBrowser;
use oat\tao\model\media\MediaManagement;
use \oat\tao\helpers\FileUploadException;

/**
 * This helper class aims at formating the item content folder description
 *
 */
class taoItems_helpers_ResourceManager implements MediaBrowser, MediaManagement
{

    private $item;
    private $lang;

    public function __construct($data){
        $this->item = (isset($data['item'])) ? $data['item'] : null;
        $this->lang = (isset($data['lang'])) ? $data['lang'] : '';

    }

    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaBrowser::getDirectory
     */
    public function getDirectory($parentLink = '/', $acceptableMime = array(), $depth = 1) {
        $sysPath = $this->getSysPath($parentLink);

        $label = substr($parentLink,strrpos($parentLink, '/') + 1);
        if(!$label){
            $label = 'local';
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
                common_Logger::w('"'.$sysPath.'" is not a directory');
            }
            $data['children'] = $children;
        }
        else{
                $data['url'] = _url('files', 'ItemContent', 'taoItems', array('uri' => $this->item->getUri(),'lang' => $this->lang, 'path' => $parentLink));
        }
        return $data;
    }


    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaBrowser::getFileInfo
     */
    public function getFileInfo($link) {
        $file = null;

        $filename = basename($link);
        $dir = ltrim(dirname($link),'/');

        $sysPath = $this->getSysPath($dir.'/'.$filename);

        $mime = tao_helpers_File::getMimeType($sysPath);
        if(file_exists($sysPath)){
            $file = array(
                'name' => basename($sysPath),
                'mime' => $mime,
                'size' => filesize($sysPath),
            );
        }
        return $file;
    }

    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaBrowser::download
     */
    public function download($filename){

        $sysPath = $this->getSysPath($filename);
        tao_helpers_Http::returnFile($sysPath);
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
            throw new common_exception_Error('Unable to move file '.$source);
        }

        $fileData = $this->getFileInfo('/'.$parent.$fileName, array());
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
    private function getSysPath($parentLink){
        $baseDir = taoItems_models_classes_ItemsService::singleton()->getItemFolder($this->item, $this->lang);

        $sysPath = $baseDir.ltrim($parentLink, '/');
        if(!tao_helpers_File::securityCheck($sysPath)){
            throw new common_exception_Error(__('Your path contains error'));
        }

        return $sysPath;
    }
}
