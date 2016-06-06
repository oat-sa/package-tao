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
use League\Flysystem\Filesystem;
use Slim\Http\Stream;
use Psr\Http\Message\StreamInterface;
use oat\oatbox\filesystem\FileSystemService;

class FlySystemManagement extends ConfigurableService implements FileManagement
{
    const OPTION_FS = 'fs';
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoMediaManager\model\fileManagement\FileManagement::storeFile()
     */
    public function storeFile($filePath, $label)
    {
        $filesystem = $this->getFileSystem();
        $filename = $this->getUniqueFilename(basename($filePath));
        
        $stream = fopen($filePath, 'r+');
        $filesystem->writeStream($filename, $stream);
        fclose($stream);
        
        return $filename;
    }
    
    public function getFileSize($link)
    {
        return $this->getFilesystem()->getSize($link);
    }

    /**
     * 
     * @param string $link
     * @return StreamInterface
     */
    public function getFileStream($link)
    {
        $resource = $this->getFilesystem()->readStream($link); 
        return new Stream($resource); 
    }
    
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoMediaManager\model\fileManagement\FileManagement::retrieveFile()
     */
    public function retrieveFile($link)
    {
        \common_Logger::w('Deprecated');
        return null;
    }

    /**
     * (non-PHPdoc)
     * @see \oat\taoMediaManager\model\fileManagement\FileManagement::deleteFile()
     */
    public function deleteFile($link)
    {
        return $this->getFilesystem()->delete($link);
    }
    
    /**
     * @return Filesystem
     */
    protected function getFilesystem()
    {
        $fs = $this->getServiceManager()->get(FileSystemService::SERVICE_ID);
        return $fs->getFileSystem($this->getOption(self::OPTION_FS));
    }
    
    /**
     * Create a new unique filename based on an existing filename
     * 
     * @param string $fileName
     * @return string
     */
    protected function getUniqueFilename($fileName)
    {
        $returnValue = uniqid(hash('crc32', $fileName));
    
        $ext = @pathinfo($fileName, PATHINFO_EXTENSION);
        if (!empty($ext)){
            $returnValue .= '.' . $ext;
        }
    
        return $returnValue;
    }
}
