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
namespace oat\taoMediaManager\model\fileManagement;

use oat\oatbox\service\ConfigurableService;
use Slim\Http\Stream;

/**
 * File Managemenr relying on the tao 2.x file abstraction
 */
class TaoFileManagement extends ConfigurableService implements FileManagement
{

    protected function getFilesystem()
    {
        return new \core_kernel_fileSystem_FileSystem($this->getOption('uri'));
    }

    /**
     * (non-PHPdoc)
     * @see \oat\taoMediaManager\model\fileManagement\FileManagement::storeFile()
     */
    public function storeFile($filePath, $label)
    {
        $file = $this->getFilesystem()->spawnFile($filePath, $label);
        if (is_null($file)) {
            throw new \common_Exception('Unable to spawn file for ' . $filePath);
        }
        return $file->getUri();
    }
    
    /**
     * Old function to retrieve the local filepath
     * 
     * @param string $link
     * @return string
     * @deprecated
     */
    protected function getLocalPath($link)
    {
        $file = new \core_kernel_file_File($link);
        return $file->getAbsolutePath();
    }

    /**
     * (non-PHPdoc)
     * @see \oat\taoMediaManager\model\fileManagement\FileManagement::retrieveFile()
     */
    public function retrieveFile($link)
    {
        return $this->getLocalPath($link);
    }

    /**
     * (non-PHPdoc)
     * @see \oat\taoMediaManager\model\fileManagement\FileManagement::deleteFile()
     */
    public function deleteFile($link)
    {
        unlink($this->getLocalPath($link));
        $file = new \core_kernel_file_File($link);
        return $file->delete();
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoMediaManager\model\fileManagement\FileManagement::getFileSize()
     */
    public function getFileSize($link)
    {
        return filesize($this->getLocalPath($link));
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoMediaManager\model\fileManagement\FileManagement::getFileStream()
     */
    public function getFileStream($link)
    {
        $fh = fopen($this->getLocalPath($link), 'r');
        return new Stream($fh, ['size' => $this->getFileSize($link)]);
    }
}