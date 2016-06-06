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
 */



/**
 * Short description of class common_ext_Extension
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package generis
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 
 */
class common_ext_ConfigDriver extends common_persistence_PhpFileDriver
{
    private static $singleton = null;
    
    public static function singleton()
    {
        if (is_null(self::$singleton)) {
            $driver = new self();
            return $driver->connect('config', array(
                'dir' => CONFIG_PATH,
                'humanReadable' => true
            ));
        }
        return self::$singleton;
    }
    
    /**
     * Override the function to allow an additional header
     * 
     * (non-PHPdoc)
     * @see common_persistence_PhpFileDriver::getContent()
     */
    protected function getContent($key, $value)
    {
        $headerPath = $this->getHeaderPath($key);
        $header = !is_null($headerPath) && file_exists($headerPath)
            ? file_get_contents($headerPath)
            : $this->getDefaultHeader($key);
            
        return $header.PHP_EOL."return ".common_Utils::toHumanReadablePhpString($value).";".PHP_EOL;
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
            .' * Default config header'.PHP_EOL
            .' *'.PHP_EOL
            .' * To replace this add a file '.$this->getHeaderPath($key).PHP_EOL
            .' */'.PHP_EOL;
    }
    
    /**
     * Returns the path to the expected header file
     * 
     * @param string $key
     * @return string|NULL
     */
    private function getHeaderPath($key)
    {
        $parts = explode('/', $key, 2);
        if (count($parts) >= 2) {
            list($extId, $configId) = $parts;
            $ext = common_ext_ExtensionsManager::singleton()->getExtensionById($extId);
            return $ext->getDir().'config/header/'.$configId.'.conf.php';
        } else {
            return null;
        }
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