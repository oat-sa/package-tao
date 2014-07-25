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
require_once dirname(__FILE__) . '/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class FuncACLTestCase extends UnitTestCase {
	
	private $user;
	private $testrole;
	
	public function setUp() {
        parent::setUp();
        
        $userService = tao_models_classes_UserService::singleton();
        $roleService = tao_models_classes_RoleService::singleton();
		$baseRole = new core_kernel_classes_Resource(INSTANCE_ROLE_BACKOFFICE);
		$this->testRole = $roleService->addRole('testrole', $baseRole);
		$this->user = $userService->addUser('testcase', md5('testcase'));
		$userService->attachRole($this->user, $this->testRole);
    }
    
	public function tearDown() {
        parent::tearDown();
        $userService = tao_models_classes_UserService::singleton();
        $roleService = tao_models_classes_RoleService::singleton();
        $userService->removeUser($this->user);
		$roleService->removeRole($this->testRole);
    }
	
	public function testFuncACL() {
		$roleService = tao_models_classes_funcACL_RoleService::singleton();
		$baseRole = $this->testrole;
		
		$srv = tao_models_classes_UserService::singleton();
		$this->assertTrue($srv->loginUser('testcase', 'testcase'));

		// -- Test uri creation
		$emauri = FUNCACL_NS . '#a_tao_Users_add';
		$emaurimod = FUNCACL_NS . '#m_tao_Users';
		$makeemauri = tao_models_classes_funcACL_AccessService::singleton()->makeEMAUri('tao', 'Users', 'add');
		$makeemaurimod = tao_models_classes_funcACL_AccessService::singleton()->makeEMAUri('tao', 'Users');
		$this->assertEqual($emauri, $makeemauri);
		$this->assertEqual($emaurimod, $makeemaurimod);
		
		// -- Try to access a restricted action
		$this->assertFalse(tao_helpers_funcACL_funcACL::hasAccess('tao', 'Users', 'add'));
		
		// -- Try to access a unrestricted action
		// (BACKOFFICE has access to the backend login action because it includes the TAO Role)
		$this->assertTrue(tao_helpers_funcACL_funcACL::hasAccess('tao', 'Main', 'login'));
		
		// -- Try to access an action that does not exist.
		$this->assertFalse(tao_helpers_funcACL_funcACL::hasAccess('tao', 'Unknown', 'action'));
		
		// -- Try to access a unrestricted action
		// Add access for this action to the Manager role.
		tao_models_classes_funcACL_ActionAccessService::singleton()->add($this->testRole->getUri(), $makeemauri);
		
		// Add the Manager role the the currently tested user
		$roleService->attachUser($this->user->getUri(), $this->testRole->getUri());
		
		// Logoff/login, to refresh roles cache
		$this->assertTrue($srv->loginUser('testcase', 'testcase'));
		
		// Ask for access
		$this->assertTrue(tao_helpers_funcACL_funcACL::hasAccess('tao', 'Users', 'add'));

		// Remove the access to this action from the Manager role
		tao_models_classes_funcACL_ActionAccessService::singleton()->remove($this->testRole->getUri(), $makeemauri);
		
		// We should not have access anymore to this action with the Manager role
		$this->assertFalse(tao_helpers_funcACL_funcACL::hasAccess('tao', 'Users', 'add'));
		
		// -- Give access to the entire module and try to access the previously tested action
		tao_models_classes_funcACL_ModuleAccessService::singleton()->add($this->testRole->getUri(), $makeemaurimod);
		$this->assertTrue(tao_helpers_funcACL_funcACL::hasAccess('tao', 'Users', 'add'));
		
		// -- Remove the entire module access and try again
		tao_models_classes_funcACL_ModuleAccessService::singleton()->remove($this->testRole->getUri(), $makeemaurimod);
		$this->assertFalse(tao_helpers_funcACL_funcACL::hasAccess('tao', 'Users', 'add'));
		
		// reset
		tao_models_classes_funcACL_ModuleAccessService::singleton()->add($this->testRole->getUri(), $makeemaurimod);
		
		// Unattach role from user
		tao_models_classes_funcACL_RoleService::singleton()->unattachUser($this->user->getUri(), $this->testRole->getUri());
		
	}
	
	public function testACLCache(){
		$moduleUri = tao_models_classes_funcACL_AccessService::singleton()->makeEMAUri('tao', 'Users');
		$module = new core_kernel_classes_Resource($moduleUri);
		$this->assertTrue($module->exists());
		
		tao_helpers_funcACL_Cache::cacheModule($module);
		$moduleCache = tao_helpers_funcACL_Cache::retrieveModule($module);
	}
}
?>
