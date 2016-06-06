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

/**
 * Proof of concept File Manager, do not use
 * @deprecated
 */
class SimpleFileManagement implements FileManagement
{

    /** @var string */
    private $baseDir = '';

    /**
     * @return string
     */
    private function getBaseDir()
    {
        if ($this->baseDir === '') {
            $this->baseDir = FILES_PATH . 'taoMediaManager' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR;
        }
        return $this->baseDir;
    }


    /**
     * store the file and provide a link to retrieve it
     * @param string $filePath the relative path to the file
     * @return string $link to the file
     * @throws \common_exception_Error
     */
    public function storeFile($filePath, $label)
    {
        $path = $this->getBaseDir();
        // create media folder if doesn't exist
        if (!is_dir($path)) {
            mkdir($path);
        }

        if (!is_dir($path . $label)) {
            if (!@copy($filePath, $path . $label)) {
                throw new \common_exception_Error('Unable to move uploaded file');
            }
            return $label;
        }
        throw new \common_exception_Error('Unable to move uploaded file');
    }

    /**
     * get the link and return the file that match it
     * @param string $link the link provided by storeFile
     * @return string $filename the file that match the link
     * @throws \common_exception_Error
     */
    public function retrieveFile($link)
    {
        if (!\tao_helpers_File::securityCheck($link)) {
            throw new \common_exception_Error('Unsecure file link found');
        }
        return $this->getBaseDir() . $link;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoMediaManager\model\fileManagement\FileManagement::getFileSize()
     */
    public function getFileSize($link)
    {
        return filesize($this->getBaseDir() . $link);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoMediaManager\model\fileManagement\FileManagement::getFileStream()
     */
    public function getFileStream($link)
    {
        $fh = fopen($this->getBaseDir() . $link, 'r');
        return new Stream($fh, ['size' => $this->getFileSize($link)]);
    }

    /**
     * @param $link
     * @return boolean if the deletion was successful or not
     */
    public function deleteFile($link)
    {
        return @unlink($this->getBaseDir() . $link);
    }
}