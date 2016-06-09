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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
namespace oat\oatbox\service;

use common_persistence_PhpFileDriver;
use common_Utils;
/**
 * A simplified config driver 
 */
class SimpleConfigDriver extends common_persistence_PhpFileDriver
{
    /**
     * Override the function to allow an additional header
     * 
     * (non-PHPdoc)
     * @see common_persistence_PhpFileDriver::getContent()
     */
    protected function getContent($key, $value)
    {
        return $this->getDefaultHeader($key).PHP_EOL."return ".common_Utils::toHumanReadablePhpString($value).";".PHP_EOL;
    }
    
    /**
     * Generates a default header
     * 
     * @param string $key
     * @return string
     */
    private function getDefaultHeader($key)
    {
        return '<?php'.PHP_EOL
            .'/**'.PHP_EOL
            .' * Default config header created during install'.PHP_EOL
            .' */'.PHP_EOL;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_persistence_PhpFileDriver::getPath()
     */
    protected function getPath($key)
    {
        $parts = explode('/', $key);
        $path = substr(parent::getPath(array_shift($parts)), 0, -4);
        foreach ($parts as $part) {
            $path .= DIRECTORY_SEPARATOR.$this->sanitizeReadableFileName($part);
        }
        return $path.'.conf.php';
    }
    
    
}