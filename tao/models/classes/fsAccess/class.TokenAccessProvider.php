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
 * @package tao
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class tao_models_classes_fsAccess_TokenAccessProvider
	extends tao_models_classes_fsAccess_AccessProvider
{	
    private $secret;
    
    public static function spawnProvider(core_kernel_fileSystem_FileSystem $fileSystem) {
        $provider = self::spawn($fileSystem, array(
        	'secret' => md5(rand().$fileSystem->getPath())
        ));
        $provider->prepareProvider();
        return $provider;
    }
    
    protected function getConfig() {
        return array(
        	'secret' => $this->secret
        );
    }
    
    protected function restoreConfig($config) {
        $this->secret = $config['secret'];
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

	/**
	 * (non-PHPdoc)
	 * @see tao_models_classes_fsAccess_AccessProvider::delete()
	 */
	public function delete() {
	    parent::delete();
	    if (file_exists($this->getConfigFilePath())) {
	        $data = include $this->getConfigFilePath();
	        unset($data[$this->getId()]);
	        file_put_contents($this->getConfigFilePath(), "<?php return ".common_Utils::toPHPVariableString($data).";");
	    }
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
		return $time.'/'.md5($time.$relPath.$this->secret);
	}
	
	/**
	 * adds the required informations to the config of getFile.php 
	 */
	private function prepareProvider() {
	    $data = file_exists($this->getConfigFilePath()) ? include $this->getConfigFilePath() : array();
	    $data[$this->getId()] = array(
			'secret' => $this->secret,
			'folder' => $this->getFileSystem()->getPath()
		); 
		file_put_contents($this->getConfigFilePath(), "<?php return ".common_Utils::toPHPVariableString($data).";");
	}
	
	/**
	 * Path of the configuration file for getFile.php 
	 * 
	 * @return string
	 */
	private function getConfigFilePath() {
	    $taoExtension = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
	    return $taoExtension->getDir().'includes'.DIRECTORY_SEPARATOR.'configGetFile.php';
	}
}