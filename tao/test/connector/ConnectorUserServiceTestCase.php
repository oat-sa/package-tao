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
?>
<?php
require_once dirname(__FILE__) . '/../TaoTestRunner.php';
include_once dirname(__FILE__).'/taoConnector/taoUserService.php';
include_once dirname(__FILE__).'/taoConnector/taoSessionRequiredException.php';

/**
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @package tao
 
 */
class ConnectorUserServiceTestCase extends UnitTestCase {
	
	private $userUri;
	private $UserData = array();
	private $langLookup;
	
	private $userService;
	
    public function __construct()
    {
    }
    
    public function setUp()
    {
        parent::setUp();
		TaoTestRunner::initTest();
		$return = $this->rawCurl(ROOT_URL.'tao/test/connector/setUp.php', array());
		$data = json_decode($return, true);
		$this->assertNotNull($data);

		if (!defined('TAO_ROOT_URL')) {
			define('TAO_ROOT_URL',	$data['rootUrl']);
		}
		$this->UserData			= $data['userData'];
		$this->userUri			= $data['userUri'];
		
		$this->langLookup		= $data['lang'];
		
		$this->userService = new taoUserService();
    	$this->assertIsA($this->userService, 'taoUserService');
		
	}
    
    public function tearDown() {
        parent::tearDown();
		$return = $this->rawCurl(ROOT_URL.'tao/test/connector/tearDown.php', array('uri' => $this->userUri));
		$this->assertTrue(json_decode($return, true));
		/*
        $this->user->delete();
        */
    }
    
    public function testLogin() {

    	$this->assertFalse($this->userService->hasSession());
    	$data = $this->userService->startSession($this->UserData[PROPERTY_USER_LOGIN], 'wrong'.$this->UserData[PROPERTY_USER_PASSWORD]);
    	$this->assertFalse($data);
    	$this->assertFalse($this->userService->hasSession());
    	try {
    	    $roles = $this->userService->getRoles();
    	    $this->fail('No taoConnector session present, but no exception on call to getRoles()');
    	} catch(taoSessionRequiredException $e) {
    		$this->pass('expected exception received');
    	}
    	
    	// with verify
    	$success = $this->userService->initRestSession(ROOT_URL, $this->userUri, 'invalidToken');
    	$this->assertFalse($success);

        try {
    	    $roles = $this->userService->getRoles();
    	    $this->fail('No taoConnector session present, but no exception on call to getRoles()');
    	} catch(taoSessionRequiredException $e) {
    		$this->pass('expected exception received');
    	}
    	
    	// without verify
    	$success = $this->userService->initRestSession(ROOT_URL, $this->userUri, 'invalidToken', true);
    	$this->assertTrue($success);
    	
        try {
    	    $roles = $this->userService->getRoles();
    	    $this->fail('No valid taoConnector session present, but no exception on call to getRoles()');
    	} catch(taoSessionRequiredException $e) {
    		$this->pass('expected exception received');
    	}
    	
    	$data = $this->userService->startSession($this->UserData[PROPERTY_USER_LOGIN], $this->UserData[PROPERTY_USER_PASSWORD]);
    	$this->assertNotEqual($data, false, 'Login failed during '.__FUNCTION__);
    	$this->assertTrue($this->userService->hasSession());
    	
    	$this->assertTrue(isset($data['id']));
    	$this->assertTrue(isset($data['roles']));
    	$this->assertTrue(isset($data['lang']));
    	
    	$this->userService->closeSession();
    	$this->assertFalse($this->userService->hasSession());
    	
    }
    
    public function testGetInfo() {
    	
    	$data = $this->userService->startSession($this->UserData[PROPERTY_USER_LOGIN], $this->UserData[PROPERTY_USER_PASSWORD]);
    	$this->assertNotEqual($data, false);
    	$this->assertTrue($this->userService->hasSession());
    	
    	$roles = $this->userService->getRoles();
    	$diff = array_diff($this->UserData[PROPERTY_USER_ROLES], array_keys($roles));
    	$this->assertTrue(empty($diff), 'created user is missing a role');
    	
    	$data = $this->userService->getInfo();
    	$this->assertTrue(isset($data['id']));
    	$this->assertEqual($this->userUri, $data['id']);

    	$this->assertTrue(isset($data['roles']));
    	$this->assertEqual($roles, $data['roles']);
    	
    	$this->assertTrue(isset($data['lang']));
    	$code = $this->getLangValue($this->UserData[PROPERTY_USER_UILG]);
    	$this->assertEqual($data['lang'], $code);
    	// cannot check, since it returns the code, not the uri
    	
    	// other fields are not mandatory    	
    	
    	$this->userService->closeSession();
    	$this->assertFalse($this->userService->hasSession());
    	
    }
    
    public function testChangePass() {
    	
    	$data = $this->userService->startSession($this->UserData[PROPERTY_USER_LOGIN], $this->UserData[PROPERTY_USER_PASSWORD]);
    	$this->assertNotEqual($data, false, 'Login failed during '.__FUNCTION__);
    	$this->assertTrue($this->userService->hasSession());
    	
    	// change password
    	$newpass = $this->UserData[PROPERTY_USER_PASSWORD].'_new';
    	$this->assertTrue($this->userService->changePassword($this->UserData[PROPERTY_USER_PASSWORD], $newpass));
    	$this->assertTrue($this->userService->hasSession());
    	
    	// close session
    	$this->userService->closeSession();
    	$this->assertFalse($this->userService->hasSession());
    	
    	// login with old password
    	$data = $this->userService->startSession($this->UserData[PROPERTY_USER_LOGIN], $this->UserData[PROPERTY_USER_PASSWORD]);
    	$this->assertFalse($data);
    	$this->assertFalse($this->userService->hasSession());
    	
    	// login with new password
    	$data = $this->userService->startSession($this->UserData[PROPERTY_USER_LOGIN], $newpass);
    	$this->assertNotEqual($data, false, 'Login with new password failed during '.__FUNCTION__);
    	$this->assertTrue($this->userService->hasSession());
    	
    	// test if we're still able to make requests, should throw exception 
    	$this->assertTrue(is_array($this->userService->getInfo()), 'Request failed after password change');
    	    	
    	// restore old pass
    	$this->assertTrue($this->userService->changePassword($newpass, $this->UserData[PROPERTY_USER_PASSWORD]), 'password not restored');
    	$this->assertTrue($this->userService->hasSession());
    	
    	// close session
    	$this->userService->closeSession();
    	$this->assertFalse($this->userService->hasSession());
    }
    
    public function testChangeLang() {
    	$data = $this->userService->startSession($this->UserData[PROPERTY_USER_LOGIN], $this->UserData[PROPERTY_USER_PASSWORD]);
    	$this->assertNotEqual($data, false, 'Login failed during '.__FUNCTION__);
    	$this->assertTrue($this->userService->hasSession());
    	
    	// determin new language candidate
    	$oldLang = $data['lang'];
    	$candidates = $this->langLookup;
    	do {
    		$newLang = array_pop($candidates);
    	} while ($newLang == $oldLang && !empty($candidates));
    	$this->assertNotEqual($newLang, $oldLang);
    	
    	// set new language
    	$this->assertTrue($this->userService->setLanguage($newLang));
    	$data = $this->userService->getInfo();
    	$this->assertNotEqual($data, false);
    	
    	// test new language
    	$currentLang = $data['lang'];
    	$this->assertEqual($newLang, $currentLang);
    	
    	// close session
    	$this->userService->closeSession();
    	$this->assertFalse($this->userService->hasSession());
    	
    	// test new language after relog
    	$data = $this->userService->startSession($this->UserData[PROPERTY_USER_LOGIN], $this->UserData[PROPERTY_USER_PASSWORD]);
    	$this->assertNotEqual($data, false, 'Login failed during '.__FUNCTION__);
    	$this->assertTrue($this->userService->hasSession());
    	$currentLang = $data['lang'];
    	$this->assertEqual($newLang, $currentLang);
    	
    	// restore old language
    	$this->assertTrue($this->userService->setLanguage($oldLang));
    	
    	// close session
    	$this->userService->closeSession();
    	$this->assertFalse($this->userService->hasSession());
    	
    	
    	
    	// close session
    	$this->userService->closeSession();
    	$this->assertFalse($this->userService->hasSession());
    }
    
    public function testException() {
    	try {
    		$this->userService->getInfo();
    		$this->fail('Missign exception on getInfo() without user');
    	} catch (taoSessionRequiredException $e) {
    		$this->pass('Got exception on getInfo : '.$e->getMessage());
    	}
    }
    
    // Helpers
    
    private function getLangValue($langUri) {
    	if (isset($this->langLookup[$langUri])) {
    		return $this->langLookup[$langUri];
    	} else {
    		$this->fail('Langugae \''.$langUri.'\' not found.');
    	}
    }
    
    private function rawCurl($url, $parms) {
    	$curlHandler = curl_init();
		curl_setopt($curlHandler, CURLOPT_URL, $url);
		curl_setopt($curlHandler, CURLOPT_POST, 1);
		curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $parms);
		curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($curlHandler);
		
		// evaluate
		if(curl_errno($curlHandler) != 0){
			common_Logger::w("Curl request failed with Error No. : ".curl_errno($curlHandler));
		}
		curl_close($curlHandler);
		return $data;
    }
}
?>