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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * Represents  direxctory for file storage 
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_models_classes_service_StorageDirectory
{
    private $id;
    private $fs;
    private $relPath;
    private $accessProvider;
    
    public function __construct($id, $fs, $path, $provider) {
        $this->id = $id;
        $this->fs = $fs;
        $this->relPath = $path;
        $this->accessProvider = $provider;
    }
    
    /**
     * Returns the absolute path to this directory
     * 
     * @return string
     */
    public function getPath() {
        return $this->fs->getPath().$this->relPath;
    }

    /**
     * Returns the relative path of this directory
     * @return string
     */
    public function getRelativePath() {
        return $this->relPath;
    }
    
    /**
     * Returns the identifier of this directory
     * @return string
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Returns whenever or not this directory is public
     * @return boolean
     */
    public function isPublic() {
        return !is_null($this->accessProvider);
    }
    
    public function getPublicAccessUrl() {
        if (is_null($this->accessProvider)) {
            common_Logger::e('accessss');
            throw new common_Exception('Tried obtaining access to private directory with ID '.$this->id);
        }
        return $this->accessProvider->getAccessUrl($this->relPath);
    }
    
}