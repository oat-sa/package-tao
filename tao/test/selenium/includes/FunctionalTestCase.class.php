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

class FunctionalTestCase extends PHPUnit_Extensions_SeleniumTestCase {
	
	public static $taoUrls = array('backend' 	=> '/tao',
								   'frontend' 	=> '/test',
								   'main' 		=> '/tao/Main/index');
	
	public function setUp() {
		// Default browser will be firefox, essentially for testing.
		// If you need another, override the setUp function in your TestCase and
		// invoke the setBrowser method.
		$this->setBrowser('*firefox');
		$this->setBrowserUrl(TAO_SELENIUM_ROOT_URL);
		$this->setSpeed(TAO_SELENIUM_SPEED);
		$this->setHost(TAO_SELENIUM_HOST);
		$this->setPort(TAO_SELENIUM_PORT);
	}
	
	public function openLocation($name) {
		$this->open(self::$taoUrls[$name]);
	}
	
	public function guiBackendLogin() {
		$this->openLocation('backend');
        $this->assertElementPresent('id=login-form');
		$this->type("xpath=//input[@id='login']", 'admin');
		$this->type("xpath=//input[@id='password']", 'admin');
		$this->clickAndWait('id=connect');
	}
	
	public function guiBackendLogout() {
		$this->click("xpath=//a[@title='Logout']");
	}
	
	public function guiFrontendLogin($login, $password) {
		$this->openLocation('frontend');
		$this->assertElementPresent('id="login"');
		$this->assertElementPresent("xpath=//input[@name='login']");
		$this->assertElementPresent("xpath=//input[@name='password']");
		$this->type("xpath=//input[@name='login']", $login);
		$this->type("xpath=//input[@name='password']", $password);
		$this->type('id=connect');
	}
	
	public function guiFrontendLogout() {
		$this->click("xpath=//a[@title='logout']");
	}
	
	public function sysLogin() {
		$userService = tao_models_classes_UserService::singleton();
		$userService->loginUser(SYS_USER_LOGIN, SYS_USER_PASS, new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole'));
	}
	
	public function sysLogout() {
		$userService = tao_models_classes_UserService::singleton();
		$userService->logout();
	}
	
	public function importRDF($file) {
		
	}
}
?>