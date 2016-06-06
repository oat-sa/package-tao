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
namespace oat\tao\model\websource;

use oat\oatbox\Configurable;
use core_kernel_fileSystem_FileSystem;
/**
 * This is the base class of the Access Providers
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
abstract class BaseWebsource extends Configurable
implements Websource
{
    const OPTION_ID            = 'id';
    const OPTION_FILESYSTEM_ID = 'fsUri';
    
	/**
	 * Filesystem that is being made available
	 * 
	 * @var core_kernel_fileSystem_FileSystem
	 */
	private $fileSystem = null;
	
	/**
	 * Identifier of the Access Provider 
	 * 
	 * @var string
	 */
	private $id;
	
	/**
	 * Used to instantiate new AccessProviders
	 * 
	 * @param core_kernel_fileSystem_FileSystem $fileSystem
	 * @param unknown $customConfig
	 * @return tao_models_classes_fsAccess_AccessProvider
	 */
	protected static function spawn(core_kernel_fileSystem_FileSystem $fileSystem, $customConfig = array()) {
	    $customConfig[self::OPTION_FILESYSTEM_ID] = $fileSystem->getUri();
	    $customConfig[self::OPTION_ID] = uniqid();
	    $websource = new static($customConfig);
	    WebsourceManager::singleton()->addWebsource($websource);
	    return $websource;
	}

	/**
	 * Filesystem made available by this Access Provider
	 * 
	 * @return core_kernel_fileSystem_FileSystem
	 */
	public function getFileSystem() {
	    if (is_null($this->fileSystem)) {
	        $this->fileSystem = new core_kernel_fileSystem_FileSystem($this->getOption(self::OPTION_FILESYSTEM_ID));
	    }
	    return $this->fileSystem;
	}

	/**
	 * Return the identifer of the AccessProvider
	 * 
	 * @return string
	 */
	public function getId() {
	    return $this->getOption(self::OPTION_ID);
	}
	
}