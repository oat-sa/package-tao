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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * This is the base class of the Access Providers
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItemRunner
 * @subpackage models_classes_itemAccess
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
abstract class tao_models_classes_fsAccess_BaseAccessProvider
	implements tao_models_classes_fsAccess_FilesystemAccessProvider
{
	const HTACCESS_DENY_CONTENT = 'Deny from All';
	
	/**
	 * @var core_kernel_fileSystem_FileSystem
	 */
	private $fileSystem = null;
	
	public function __construct(core_kernel_fileSystem_FileSystem $fileSystem) {
	    $this->fileSystem = $fileSystem;
	}

	public function getFileSystem() {
	    return $this->fileSystem;
	}
	
	public function prepareProvider() {
		$this->writeHtaccessFile($this->getHtaccessContent());
	}
	
	public function cleanupProvider() {
		$this->writeHtaccessFile(self::HTACCESS_DENY_CONTENT);
	}

	private function writeHtaccessFile($content) {
		$filePath = $this->getBasePath() . '.htaccess';
    	if (false !== ($fp = @fopen($filePath, 'c')) && true === flock($fp, LOCK_EX)){
    		
    		// We first need to truncate.
    		ftruncate($fp, 0);
    		
    		fwrite($fp, $content);
    		@flock($fp, LOCK_UN);
    		@fclose($fp);
    	} else {
    		throw new common_exception_Error('Could not prepare htaccess \''.$filePath.'\' for provider '.get_class($this));
    	}
        return true;
	}
	
	protected function getBasePath() {
	    return $this->getFileSystem()->getPath();
	}
	
	/**
	 * Returns the content of the .htaccess to write
	 * @return string
	 */
	protected abstract function getHtaccessContent();
}