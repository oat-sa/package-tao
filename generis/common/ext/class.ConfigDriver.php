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
    const DEFAULT_HEADER = '';
    
    /**
     * 
     * @param common_ext_Extension $extension
     * @return common_persistence_KeyValuePersistence
     */
    public static function getPersistence(common_ext_Extension $extension) {
        $driver = new self($extension);
        return $driver->connect('config_'.$extension->getId(), array(
            'dir' => CONFIG_PATH.$extension->getId(),
            'humanReadable' => true
        ));
    }
    
    private $extension;
    
    private function __construct($extension) {
        $this->extension = $extension;
    }
    
    /**
     * @return common_ext_Extension
     */
    protected function getExtension() {
        return $this->extension;
    }
    
    /**
     * Override the function to allow an additional header
     * 
     * (non-PHPdoc)
     * @see common_persistence_PhpFileDriver::getContent()
     */
    protected function getContent($key, $value) {
        $headerPath = $this->getExtension()->getDir().'config/header/'.$key.'.conf.php';
        $header = file_exists($headerPath)
            ? file_get_contents($headerPath)
            : $this->getDefaultHeader($key);
            
        return $header.PHP_EOL."return ".common_Utils::toHumanReadablePhpString($value).";".PHP_EOL;
    }
    
    private function getDefaultHeader($key) {
        return '<?php'.PHP_EOL
            .'/**'.PHP_EOL
            .' * Default config header'.PHP_EOL
            .' *'.PHP_EOL
            .' * To replace this add a file '.basename($this->getExtension()->getDir()).'/conf/header/'.$key.'.conf.php'.PHP_EOL
            .' */'.PHP_EOL;
    }
    
    protected function getPath($key) {
        return substr(parent::getPath($key), 0, -4).'.conf.php';
    }
}