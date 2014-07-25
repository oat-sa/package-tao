<?php
/**
 * 
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
 * Grants direct Access to compiled data
 * This is the fastest implementation but
 * allows anyone access that guesses the path
 * access to the compiled delivery
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItemRunner
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class tao_models_classes_fsAccess_ActionAccessProvider
	extends tao_models_classes_fsAccess_AccessProvider
{
    /**
     *
     * @param core_kernel_fileSystem_FileSystem $fileSystem
     * @param string $accessUrl
     * @return unknown
     */
    public static function spawnProvider(core_kernel_fileSystem_FileSystem $fileSystem) {
        return self::spawn($fileSystem);
    }

    protected function getConfig() {
        return array();
    }
    
    protected function restoreConfig($config) {
    }
    
    public function getAccessUrl($relativePath) {
	    $fsUri = $this->getFileSystem()->getUri();
	    $trailingSlash = substr($relativePath, -1) == DIRECTORY_SEPARATOR ? '/' : '';
		return _url('accessFile/'.base64_encode($this->getId().' '.trim($relativePath, DIRECTORY_SEPARATOR)).'/','File', 'tao');
    }
}