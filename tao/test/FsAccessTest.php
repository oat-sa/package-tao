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
require_once dirname(__FILE__) . '/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @package tao
 
 */
class tao_test_FsAccessTest extends TaoPhpUnitTestRunner {
    
    private $testUser;
    private $credentials = array();
    
    private static $fileSystem = null;
    
    protected function setUp()
    {
        $pass = md5(rand());
        $taoManagerRole = new core_kernel_classes_Resource(INSTANCE_ROLE_BACKOFFICE);
        $this->testUser = tao_models_classes_UserService::singleton()->addUser('testUser', $pass, $taoManagerRole );
        $this->credentials = array(
            'loginForm_sent' => 1,
            'login' => 'testUser',
            'password' => $pass,
        );
        $this->assertIsA($this->testUser, 'core_kernel_classes_Resource');
        parent::setUp();
    }
    
    protected function tearDown() {
        parent::tearDown();
        $this->testUser->delete();
    }
    
    public static function tearDownAfterClass() {
        parent::tearDownAfterClass();
        self::$fileSystem->delete();
    }
    
    /**
     * 
     * @return array
     */
    public function fileAccessProviders() {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        if (is_null(self::$fileSystem )) {
            self::$fileSystem = tao_models_classes_FileSourceService::singleton()->addLocalSource('test FS', $ext->getConstant('DIR_VIEWS'));
        }
        return array(
            array(tao_models_classes_fsAccess_DirectAccessProvider::spawnProvider(self::$fileSystem, $ext->getConstant('BASE_WWW'))),
            array(tao_models_classes_fsAccess_TokenAccessProvider::spawnProvider(self::$fileSystem)),
            array(tao_models_classes_fsAccess_ActionAccessProvider::spawnProvider(self::$fileSystem))
        );
    }
    
    /**
     * @dataProvider fileAccessProviders
     */
    public function testAccessProviders(tao_models_classes_fsAccess_AccessProvider $access) {
        
        $this->assertInstanceOf('tao_models_classes_fsAccess_AccessProvider', $access);
        $id = $access->getId();
        
        $fromManager = tao_models_classes_fsAccess_Manager::singleton()->getProvider($id);
        $this->assertInstanceOf('tao_models_classes_fsAccess_AccessProvider', $fromManager);
        
        $url = $access->getAccessUrl('img'.DIRECTORY_SEPARATOR.'tao.png');
        $this->assertTrue(file_exists($access->getFileSystem()->getPath().'img'.DIRECTORY_SEPARATOR.'tao.png'), 'reference file not found');
        $this->assertUrlHttpCode($url);
        
        $url = $access->getAccessUrl('img'.DIRECTORY_SEPARATOR.'fakeFile_thatDoesNotExist.png');
        $this->assertFalse(file_exists($access->getFileSystem()->getPath().'img'.DIRECTORY_SEPARATOR.'fakeFile_thatDoesNotExist.png'), 'reference file should not found');
        $this->assertUrlHttpCode($url, '404');
        
        $url = $access->getAccessUrl('img'.DIRECTORY_SEPARATOR);
        $this->assertUrlHttpCode($url.'tao.png');
        $this->assertUrlHttpCode($url.'errors'.DIRECTORY_SEPARATOR.'404.png');
        
        $url = $access->getAccessUrl('');
        $this->assertUrlHttpCode($url.'img'.DIRECTORY_SEPARATOR.'tao.png');
        
        $access->delete();
        try {
            tao_models_classes_fsAccess_Manager::singleton()->getProvider($id);
            $this->fail('No exception thrown');
        } catch (common_Exception $e) {
            // all good
        }
    }
    
    private function assertUrlHttpCode($url, $expectedCode = 200) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
        curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        curl_setopt($ch, CURLOPT_COOKIE, $this->getSessionCookie($this->testUser));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->assertEquals($expectedCode, $httpCode, 'Incorrect response for '.$url);
    }
    
    private function getSessionCookie(core_kernel_classes_Resource $user) {
        // login
        $ch = curl_init(_url('login', 'main', 'tao'));
        curl_setopt($ch,CURLOPT_POST, count($this->credentials)); // does not work with no body
        curl_setopt($ch,CURLOPT_POSTFIELDS, $this->credentials);
        curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        
        // get cookie
        preg_match('/^Set-Cookie:\s*([^;]*)/mi', $output, $m);
        
        $this->assertTrue(isset($m[1]), 'Failed to get Session Cookie');
        return $m[1];
        
    }
    
    
}