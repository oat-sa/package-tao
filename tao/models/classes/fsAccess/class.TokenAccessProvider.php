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
 * Grants direct Access to compiled data
 * This is the fastest implementation but
 * allows anyone access that guesses the path
 * access to the compiled delivery
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItemRunner
 * @subpackage models_classes_itemAccess
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class tao_models_classes_fsAccess_TokenAccessProvider
	extends tao_models_classes_fsAccess_BaseAccessProvider
{	
    private $secret;
    
    public function __construct(core_kernel_fileSystem_FileSystem $fileSystem) {
        parent::__construct($fileSystem);
        $this->secret = md5(rand().$fileSystem->getUri());
    }
    
	public function getAccessUrl(core_kernel_file_File $directory) {
	    $path = $directory->getRelativePath();
	    $path = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $path), '/').'/';
	    $token = $this->generateToken($path);
	    $taoExtension = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
		return $taoExtension->getConstant('BASE_URL').'getFile.php/'.$token.'/'.$path.'*/';
	}
	
	private function generateToken($path) {
		$time = time();
		$config = include($this->getConfigFilePath());
		return $time.'/'.md5($time.$path.$config['secret']);
	}
	
	protected function getHtaccessContent() {
		return self::HTACCESS_DENY_CONTENT;
	}
	
	public function prepareProvider() {
		parent::prepareProvider();
		file_put_contents($this->getConfigFilePath(), "<? return ".common_Utils::toPHPVariableString(array(
			'secret' => md5(rand()),
			'folder' => $this->getFileSystem()->getPath()
		)).";");
		
	}
	
	public function cleanupProvider() {
		parent::cleanupProvider();
		unlink($this->getConfigFilePath());
	}

	private function getConfigFilePath() {
	    $taoExtension = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
	    return $taoExtension->getConstant('BASE_PATH').'includes'.DIRECTORY_SEPARATOR.'configGetFile.php';
	}
}