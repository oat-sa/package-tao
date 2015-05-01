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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
namespace oat\generis\test;

use \common_session_SessionManager;
use \common_test_TestUserSession;
use \common_ext_ExtensionsManager;
use \common_ext_ExtensionInstaller;
use \common_ext_ExtensionUninstaller;
use \common_persistence_Manager;

/**
 * @author CRP Henri Tudor - TAO Team
 * @license GPLv2
 *
 */

abstract class GenerisPhpUnitTestRunner extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var boolean
     */
    private static $connected = false;
    
    private $config; 
    
    private $files = array();
    
    /**
     * Create a new temporary file
     * @param string $pContent
     * @return string
     */
    public function createFile($pContent = '', $name = null) {
    	if (is_null($name)) {
    		$tmpfname = tempnam(sys_get_temp_dir(), "tst");
    	} else {
    		$tmpfname = sys_get_temp_dir().DIRECTORY_SEPARATOR.$name;
    	}
    	$this->files[] = $tmpfname;
    
    	if (!empty($pContent)) {
    		$handle = fopen($tmpfname, "w");
    		fwrite($handle, $pContent);
    		fclose($handle);
    	}
    
    	return $tmpfname;
    }
    
    protected function getSampleDir(){
        return __DIR__.'/samples';
    }
    
    /**
     * Cleanup of files
     * @see SimpleTestCase::after()
     */
    public function after($method) {
    	parent::after($method);
    	foreach ($this->files as $file) {
    		@unlink($file);
    	}
    	$this->files = array();
    }
    /**
     * shared methods for test initialization
     */
    public static function initTest(){
        //connect the API
        if(!self::$connected){          
            common_session_SessionManager::startSession(new common_test_TestUserSession());
            self::$connected = true;
        }
    }
    
    public function restoreTestSession() {
        return common_session_SessionManager::startSession(new common_test_TestUserSession());
    }
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $var
     * @param string $className
     * @deprecated use assertInstanceOf instead
     */
    public function assertIsA($var,$className){
    	$this->assertInstanceOf($className,$var);
    }
    
    public function installExtension($extid){
        if (!common_ext_ExtensionsManager::singleton()->isInstalled($extid)) {
            $extension = common_ext_ExtensionsManager::singleton()->getExtensionById($extid);
            $installer = new common_ext_ExtensionInstaller($extension);
            $installer->install();
        }
    }
    
    public function uninstallExtension($extid){
        if (common_ext_ExtensionsManager::singleton()->isInstalled($extid)) {
            $extension = common_ext_ExtensionsManager::singleton()->getExtensionById($extid);
            $installer = new common_ext_ExtensionUninstaller($extension);
            $installer->uninstall();
        }
    }
    
    protected function disableCache()
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('generis') ;
        $this->config = $ext->getConfig(common_persistence_Manager::CONFIG_KEY);
        $conf = $this->config;
        if(isset($conf['cache']) && isset($conf['cache']['driver'])){
            $conf['cache']['driver'] = 'no_storage';
            \common_Logger::i('Set cache on NO STORAGE');
        
            $ext->setConfig(common_persistence_Manager::CONFIG_KEY,$conf);
        }
    }
    
    
    protected function restoreCache()
    {
        \common_Logger::i('Restore cache persistence'); 
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('generis') ;
        $ext->setConfig(common_persistence_Manager::CONFIG_KEY,$this->config);
        
    }
    /**
     * Returns the test session if available
     * @throws common_exception_Error
     * @return common_test_TestUserSession
     */
    public static function getTestSession() {
        if(!self::$connected){
            throw new common_exception_Error('Trying to retrieve TestSession without initialising it first via initTest()');
        }
        $session = common_session_SessionManager::getSession();
        if(!$session instanceof common_test_TestUserSession){
            throw new common_exception_Error('current session is no longer a common_test_TestUserSession');
        }
        return $session;
    }
}
