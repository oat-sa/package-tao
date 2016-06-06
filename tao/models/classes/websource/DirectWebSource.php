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
 */
namespace oat\tao\model\websource;

use core_kernel_fileSystem_FileSystem;
use common_ext_ExtensionsManager;

/**
 * Grants Access to compiled data via a direct URL link
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class DirectWebSource extends BaseWebsource
{	
    const OPTION_URL = 'accessUrl';
    
    public static function spawnWebsource(core_kernel_fileSystem_FileSystem $fileSystem, $accessUrl) {
        $provider = self::spawn($fileSystem, array(
        	self::OPTION_URL => $accessUrl
        ));
        return $provider;
    }
    
    public function getAccessUrl($relativePath) {
        $path = array();
        foreach (explode(DIRECTORY_SEPARATOR, ltrim($relativePath, DIRECTORY_SEPARATOR)) as $part) {
            $path[] = rawurlencode($part);
        }
        return $this->getOption(self::OPTION_URL).implode('/', $path);
    }
    
}