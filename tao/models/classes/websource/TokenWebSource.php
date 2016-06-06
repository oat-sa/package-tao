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
 * Grants Access to compiled data via the getFile entry point
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class TokenWebSource extends BaseWebsource
{	
    const OPTION_SECRET = 'secret';
    const OPTION_PATH = 'path';
    const OPTION_TTL = 'ttl';
    
    public static function spawnWebsource(core_kernel_fileSystem_FileSystem $fileSystem) {
        $provider = self::spawn($fileSystem, array(
        	self::OPTION_SECRET => md5(rand().$fileSystem->getPath()),
            self::OPTION_PATH => $fileSystem->getPath(),
            self::OPTION_TTL => (int) ini_get('session.gc_maxlifetime')
        ));
        return $provider;
    }
    
	public function getAccessUrl($relativePath) {
	    $path = array();
	    foreach (explode(DIRECTORY_SEPARATOR, ltrim($relativePath, DIRECTORY_SEPARATOR)) as $ele) {
	        $path[] = rawurlencode($ele);
	    }
	    $relUrl = implode('/', $path);
	    $relPath = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $relativePath));
	    $token = $this->generateToken($relUrl);
	    $taoExtension = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
	    return $taoExtension->getConstant('BASE_URL').'getFile.php/'.$this->getId().'/'.$token.'/'.$relUrl.'*/';
	}
	// helpers
	
	/**
	 * Generate a token for the resource
	 * Same algorithm is implemented again in getFile.php
	 * 
	 * @param string $relPath
	 * @return string
	 */
	private function generateToken($relPath) {
		$time = time();
		return $time.'/'.md5($time.$relPath.$this->getOption(self::OPTION_SECRET));
	}
}