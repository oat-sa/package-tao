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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */
namespace qtism\runtime\storage\binary;

use qtism\common\datatypes\files\FileSystemFile;
use qtism\common\datatypes\File;

class QtiBinaryStreamAccessFsFile extends AbstractQtiBinaryStreamAccess {
    
    public function writeFile(File $file) {
        try {
            $this->writeString($file->getPath());
        }
        catch (QtiBinaryStreamAccessException $e) {
            $msg = "An error occured while reading a QTI File.";
            throw new QtiBinaryStreamAccessException($msg, $this, QtiBinaryStreamAccessException::FILE, $e);
        }
    }
    
    public function readFile() {
        try {
            $path = $this->readString();
            
            return new FileSystemFile($path);
        }
        catch (\Exception $e) {
            $msg = "An error occured while writing a QTI File.";
            throw new QtiBinaryStreamAccessException($msg, $this, QtiBinaryStreamAccessException::FILE, $e);
        }
    }
}