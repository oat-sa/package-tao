<?php
/*  
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
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
class tao_models_classes_fsAccess_DirectAccessProvider
	extends tao_models_classes_fsAccess_AccessProvider
{
    /**
     * Spawns a new filesystem access, expects the filesystem to access to
     * and the url which points to the root of the filesystem with trailling slash 
     * 
     * @param core_kernel_fileSystem_FileSystem $fileSystem
     * @param string $accessUrl
     * @return unknown
     */
    public static function spawnProvider(core_kernel_fileSystem_FileSystem $fileSystem, $accessUrl) {
        return self::spawn($fileSystem, array(
            'accessUrl' => $accessUrl
        ));
    }
    
    private $accessUrl = null;
    
    protected function getConfig() {
        return array(
            'accessUrl' => $this->accessUrl
        );
    }
    
    protected function restoreConfig($config) {
        $this->accessUrl = $config['accessUrl'];
    }
    
	public function getAccessUrl($relativePath) {
	    $path = array();
	    foreach (explode(DIRECTORY_SEPARATOR, ltrim($relativePath, DIRECTORY_SEPARATOR)) as $part) {
           $path[] = rawurlencode($part);
	    }
	    return $this->accessUrl.implode('/', $path);//(ltrim($relativePath,'/'));
	}
}