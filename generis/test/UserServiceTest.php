<?php
/**  
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
 *               2012-2014 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */
use oat\generis\test\GenerisPhpUnitTestRunner;

class UserServiceTestCase extends GenerisPhpUnitTestRunner
{

    const TESTCASE_USER_LOGIN = 'testcase_user';

    /**
     *
     * @var core_kernel_users_Service
     */
    protected $service;

    private $sampleUser;

    public function __construct($label = false)
    {
        parent::__construct($label);
        $this->initRoles();
    }

    public function setUp()
    {
        GenerisPhpUnitTestRunner::initTest();
        $this->service = core_kernel_users_Service::singleton();
        $this->sampleUser = $this->service->addUser(self::TESTCASE_USER_LOGIN, 'pwd' . rand());
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->sampleUser->delete();
        ;
    }

    public function initRoles()
    {
        // Main parent role.
        $roleClass = new core_kernel_classes_Class(CLASS_ROLE);
        $isSystemProperty = new core_kernel_classes_Property(PROPERTY_ROLE_ISSYSTEM);
        $includesRoleProperty = new core_kernel_classes_Property(PROPERTY_ROLE_INCLUDESROLE);
        $trueInstance = new core_kernel_classes_Resource(GENERIS_TRUE);
        $falseInstance = new core_kernel_classes_Resource(GENERIS_FALSE);
        $prefix = LOCAL_NAMESPACE . '#';
        
        // Do not forget that more you go deep in the Roles hierarchy, more rights you have.
        $baseRole = $roleClass->createInstance('BASE Role', 'The base role of the hierarchy (minimal rights).', $prefix . 'baseRole');
        $baseRole->setPropertyValue($isSystemProperty, $falseInstance);
        
        $subRole1 = $roleClass->createInstance('SUB Role 1', 'Includes BASE role.', $prefix . 'subRole1');
        $subRole1->setPropertyValue($includesRoleProperty, $baseRole);
        $subRole1->setPropertyValue($isSystemProperty, $falseInstance);
        
        $subRole2 = $roleClass->createInstance('SUB Role 2', 'Includes BASE role.', $prefix . 'subRole2');
        $subRole2->setPropertyValue($includesRoleProperty, $baseRole);
        $subRole2->setPropertyValue($isSystemProperty, $falseInstance);
        
        $subRole3 = $roleClass->createInstance('SUB Role 3', 'Includes BASE role.', $prefix . 'subRole3');
        $subRole3->setPropertyValue($includesRoleProperty, $baseRole);
        $subRole3->setPropertyValue($isSystemProperty, $falseInstance);
        
        $subRole11 = $roleClass->createInstance('SUB Role 11', 'Includes SUB Role 1.', $prefix . 'subRole11');
        $subRole11->setPropertyValue($includesRoleProperty, $subRole1);
        $subRole11->setPropertyValue($isSystemProperty, $falseInstance);
        
        $subRole12 = $roleClass->createInstance('SUB Role 12', 'Includes SUB Role 1.', $prefix . 'subRole12');
        $subRole12->setPropertyValue($includesRoleProperty, $subRole1);
        $subRole12->setPropertyValue($isSystemProperty, $falseInstance);
        
        $subRole13 = $roleClass->createInstance('SUB Role 13', 'Includes SUB Role 1, SUB Role 11, SUB Role 12.', $prefix . 'subRole13');
        $subRole13->setPropertyValue($includesRoleProperty, $subRole1);
        $subRole13->setPropertyValue($includesRoleProperty, $subRole11);
        $subRole13->setPropertyValue($includesRoleProperty, $subRole12);
        $subRole13->setPropertyValue($isSystemProperty, $falseInstance);
    }

    public function testModel()
    {
        // We must have a property dedicated to Roles in the Generis User class.
        $userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
        $this->assertTrue($userClass->exists());
        
        // The userClass must have a dedicated userRoles property.
        $this->assertTrue(array_key_exists(PROPERTY_USER_ROLES, $userClass->getProperties()));
        
        // The class that gather all Roles instances together must exist.
        $roleClass = new core_kernel_classes_Class(CLASS_ROLE);
        $this->assertTrue($roleClass->exists());
        
        // The Role class must have the right properties (isSystem and includesRole)
        $roleClassProperties = $roleClass->getProperties();
        $this->assertTrue(array_key_exists(PROPERTY_ROLE_ISSYSTEM, $roleClassProperties));
        $this->assertTrue(array_key_exists(PROPERTY_ROLE_INCLUDESROLE, $roleClassProperties));
        
        // The Generis Role must exist after installation.
        $generisRole = new core_kernel_classes_Resource(INSTANCE_ROLE_GENERIS);
        $this->assertTrue($generisRole->exists());
    }

    public function testGetOneUser()
    {
        $sysUser = $this->service->getOneUser(self::TESTCASE_USER_LOGIN);
        $this->assertFalse(empty($sysUser));
        $this->assertIsA($sysUser, 'core_kernel_classes_Resource');
        $loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
        $login = $sysUser->getUniquePropertyValue($loginProperty);
        $this->assertIsA($login, 'core_kernel_classes_Literal');
        $this->assertEquals($login->literal, self::TESTCASE_USER_LOGIN);
        
        $unknownUser = $this->service->getOneUser('unknown');
        $this->assertTrue(empty($unknownUser));
    }

    public function testGetIncludedRoles()
    {
        $prefix = LOCAL_NAMESPACE . '#';
        
        $subRole13 = new core_kernel_classes_Resource($prefix . 'subRole13');
        $includedRoles = $this->service->getIncludedRoles($subRole13);
        $this->assertEquals(count($includedRoles), 4);
        $this->assertFalse(array_key_exists($prefix . 'subRole13', $includedRoles));
        $this->assertTrue(array_key_exists($prefix . 'baseRole', $includedRoles));
        $this->assertTrue(array_key_exists($prefix . 'subRole1', $includedRoles));
        $this->assertTrue(array_key_exists($prefix . 'subRole11', $includedRoles));
        $this->assertTrue(array_key_exists($prefix . 'subRole12', $includedRoles));
    }

    public function testIsPasswordValid()
    {
        $role = new core_kernel_classes_Resource(INSTANCE_ROLE_GENERIS);
        $user = $this->service->addUser('passwordValid', 'passwordValid', $role);
        $this->assertIsA($user, 'core_kernel_classes_Resource');
        $this->assertTrue($this->service->isPasswordValid('passwordValid', $user));
        $this->assertTrue($user->delete());
    }

    public function testLogin()
    {
        $role = $this->service->addRole('LOGINROLE');
        
        if ($this->service->loginExists('login')) {
            $this->service->getOneUser('login')->delete();
        }
        $user = $this->service->addUser('login', 'password', $role);
        $taoManagerRole = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole');
        
        $this->assertTrue($this->service->login('login', 'password', $role));
        $this->assertTrue($this->service->isASessionOpened());
        $this->assertTrue($this->service->logout());
        $this->assertTrue($this->restoreTestSession()); // relog sys user.
        $this->assertFalse($this->service->login('toto', '', $taoManagerRole));
        
        $role->delete();
        $user->delete();
    }

    public function testLoginExists()
    {
        $this->assertTrue($this->service->loginExists(self::TESTCASE_USER_LOGIN));
        $this->assertFalse($this->service->loginExists('toto'));
    }

    public function testAddUser()
    {
        $userRolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
        
        // single role.
        $role1 = $this->service->addRole('ADDUSERROLE 1');
        if ($this->service->loginExists('user1')) {
            $this->service->getOneUser('user1')->delete();
        }
        $user = $this->service->addUser('user1', 'password1', $role1);
        
        $this->assertTrue($this->service->loginExists('user1'));
        $userRoles = $user->getUniquePropertyValue($userRolesProperty);
        $this->assertEquals($userRoles->getUri(), $role1->getUri());
        $this->assertTrue($this->service->logout());
        $this->assertTrue($this->service->login('user1', 'password1', $role1));
        $this->assertTrue($this->service->logout());
        $this->assertTrue($this->restoreTestSession());
        
        $user->delete();
        $this->assertFalse($user->exists());
        $role1->delete();
        $this->assertFalse($role1->exists());
        
        // No role provided. Will be given the genuine GENERIS ROLE.
        $user = $this->service->addUser('user2', 'password2');
        $this->assertTrue($this->service->loginExists('user2'));
        $userRoles = $user->getUniquePropertyValue($userRolesProperty);
        $this->assertEquals($userRoles->getUri(), INSTANCE_ROLE_GENERIS);
        $user->delete();
    }

    public function testAddRole()
    {
        $roleClass = new core_kernel_classes_Class(CLASS_ROLE);
        $includesRoleProperty = new core_kernel_classes_Property(PROPERTY_ROLE_INCLUDESROLE);
        
        // Prepare roles to be included to others.
        $iRole1 = $this->service->addRole('INCLUDED ROLE 1');
        
        // Role without included roles.
        $role1 = $this->service->addRole('NEWROLE 1');
        $this->assertIsA($role1, 'core_kernel_classes_Resource');
        $includesRoles = $role1->getPropertyValues($includesRoleProperty);
        $this->assertTrue(empty($includesRoles));
        
        $role1->delete();
        $this->assertFalse($role1->exists());
        
        // Role with included roles.
        $role2 = $this->service->addRole('NEWROLE 2', $iRole1);
        $includedRoles = $role2->getUniquePropertyValue($includesRoleProperty);
        $this->assertTrue($includedRoles->isInstanceOf($roleClass));
        $this->assertEquals($includedRoles->getUri(), $iRole1->getUri());
        
        $role2->delete();
        $iRole1->delete();
        $this->assertFalse($iRole1->exists());
        $this->assertFalse($role2->exists());
    }

    public function testGetUserRoles()
    {
        $prefix = LOCAL_NAMESPACE . '#';
        
        $subRole2 = new core_kernel_classes_Resource($prefix . 'subRole2');
        if ($this->service->loginExists('user')) {
            $this->service->getOneUser('user')->delete();
        }
        $user = $this->service->addUser('user', 'password', $subRole2);
        $userRoles = $this->service->getUserRoles($user);
        
        $this->assertEquals(count($userRoles), 2);
        $this->assertTrue(array_key_exists($prefix . 'subRole2', $userRoles));
        $this->assertTrue(array_key_exists($prefix . 'baseRole', $userRoles));
        $user->delete();
        
        $subRole11 = new core_kernel_classes_Resource($prefix . 'subRole11');
        $user = $this->service->addUser('user', 'password', $subRole11);
        $userRoles = $this->service->getUserRoles($user);
        
        $this->assertEquals(count($userRoles), 3);
        $this->assertTrue(array_key_exists($prefix . 'subRole11', $userRoles));
        $this->assertTrue(array_key_exists($prefix . 'subRole1', $userRoles));
        $this->assertTrue(array_key_exists($prefix . 'baseRole', $userRoles));
        $user->delete();
    }

    public function testUserHasRoles()
    {
        $prefix = LOCAL_NAMESPACE . '#';
        $baseRole = new core_kernel_classes_Resource($prefix . 'baseRole');
        $subRole1 = new core_kernel_classes_Resource($prefix . 'subRole1');
        $subRole2 = new core_kernel_classes_Resource($prefix . 'subRole2');
        $subRole3 = new core_kernel_classes_Resource($prefix . 'subRole3');
        $subRole11 = new core_kernel_classes_Resource($prefix . 'subRole11');
        $subRole12 = new core_kernel_classes_Resource($prefix . 'subRole12');
        $subRole13 = new core_kernel_classes_Resource($prefix . 'subRole13');
        $allRolesOf13 = array(
            $baseRole,
            $subRole1,
            $subRole11,
            $subRole12,
            $subRole13
        );
        
        $this->assertTrue($baseRole->exists());
        $this->assertTrue($subRole1->exists());
        
        $user = $this->service->addUser('user', 'password', $baseRole);
        $this->assertTrue($this->service->userHasRoles($user, $baseRole));
        $this->assertFalse($this->service->userHasRoles($user, array(
            $baseRole,
            $subRole1
        )));
        $user->delete();
        
        $user = $this->service->addUser('user', 'password', $subRole1);
        $this->assertTrue($this->service->userHasRoles($user, $baseRole));
        $this->assertTrue($this->service->userHasRoles($user, $subRole1));
        $this->assertFalse($this->service->userHasRoles($user, $subRole2));
        $this->assertTrue($this->service->userHasRoles($user, array(
            $baseRole,
            $subRole1
        )));
        $this->assertFalse($this->service->userHasRoles($user, array(
            $baseRole,
            $subRole1,
            $subRole2
        )));
        $user->delete();
        
        $user = $this->service->addUser('user', 'password', $subRole13);
        $this->assertTrue($this->service->userHasRoles($user, $subRole13));
        $this->assertTrue($this->service->userHasRoles($user, $baseRole));
        $this->assertTrue($this->service->userHasRoles($user, $allRolesOf13));
        $user->delete();
    }

    public function testIncludeRole()
    {
        $prefix = LOCAL_NAMESPACE . '#';
        $role = new core_kernel_classes_Resource($prefix . 'subRole3');
        $user = $this->service->addUser('user', 'password', $role);
        
        $userRoles = $this->service->getUserRoles($user);
        $this->assertEquals(count($userRoles), 2);
        $this->assertTrue(array_key_exists($prefix . 'baseRole', $userRoles));
        $this->assertTrue(array_key_exists($role->getUri(), $userRoles));
        
        // subRole3 includes subRole2 for this test.
        $roleToInclude = new core_kernel_classes_Resource($prefix . 'subRole2');
        $this->service->includeRole($role, $roleToInclude);
        
        $userRoles = $this->service->getUserRoles($user);
        $this->assertEquals(count($userRoles), 3);
        
        $user->delete();
        $this->assertFalse($user->exists());
    }

    /**
     * @depends testLoginExists
     */
    public function testUnincludeRole()
    {
        $prefix = LOCAL_NAMESPACE . '#';
        $role = new core_kernel_classes_Resource($prefix . 'subRole11');
        $user = $this->service->addUser('user', 'password', $role);
        
        $userRoles = $this->service->getUserRoles($user);
        $baseRole = new core_kernel_classes_Resource($prefix . 'baseRole');
        $subRole1 = new core_kernel_classes_Resource($prefix . 'subRole1');
        
        $this->assertEquals(3, count($userRoles));
        $this->assertTrue(array_key_exists($baseRole->getUri(), $userRoles));
        $this->assertTrue(array_key_exists($subRole1->getUri(), $userRoles));
        $this->assertTrue(array_key_exists($role->getUri(), $userRoles));
        
        $this->service->unincludeRole($subRole1, $baseRole);
        $userRoles = $this->service->getUserRoles($user);
        $this->assertEquals(2, count($userRoles));
        $this->assertTrue(array_key_exists($role->getUri(), $userRoles));
        $this->assertTrue(array_key_exists($subRole1->getUri(), $userRoles));
        
        $this->service->includeRole($role, $baseRole);
        $userRoles = $this->service->getUserRoles($user);
        $this->assertEquals(3, count($userRoles));
        $this->assertTrue(array_key_exists($baseRole->getUri(), $userRoles));
        
        $user->delete();
        $this->assertFalse($user->exists());
    }

    public function testRemoveRole()
    {
        $prefix = LOCAL_NAMESPACE . '#';
        
        $subRole13 = new core_kernel_classes_Resource($prefix . 'subRole13');
        $user = $this->service->addUser('user', 'password', $subRole13);
        $this->assertTrue($this->service->userHasRoles($user, $subRole13));
        
        $this->assertTrue($this->service->removeRole($subRole13));
        $this->assertFalse($this->service->userHasRoles($user, $subRole13));
        $userRoles = $this->service->getUserRoles($user);
        $this->assertTrue(empty($userRoles));
        
        $user->delete();
    }

    public function testRemoveUser()
    {
        $role = new core_kernel_classes_Resource(INSTANCE_ROLE_GENERIS);
        $user = $this->service->addUser('removeUser', 'removeUser', $role);
        $this->assertTrue($user->exists());
        $this->service->removeUser($user);
        $this->assertFalse($user->exists());
    }

    public function testSetPassword()
    {
        $role = new core_kernel_classes_Resource(INSTANCE_ROLE_GENERIS);
        $user = $this->service->addUser('passwordUser', 'passwordUser', $role);
        $this->assertTrue($user->exists());
        $this->assertTrue($this->service->isPasswordValid('passwordUser', $user));
        $this->assertFalse($this->service->isPasswordValid('password', $user));
        $this->service->setPassword($user, 'password');
        $this->assertTrue($this->service->isPasswordValid('password', $user));
        $this->service->removeUser($user);
        $this->assertFalse($user->exists());
    }

    public function testAttachUnnatachRole()
    {
        $prefix = LOCAL_NAMESPACE . '#';
        $user = $this->service->addUser('attachUser', 'attachUser');
        
        $role = new core_kernel_classes_Resource($prefix . 'baseRole');
        $this->service->attachRole($user, $role);
        $userRoles = $this->service->getUserRoles($user);
        $this->assertFalse(empty($userRoles));
        $this->assertEquals(count($userRoles), 2); // also contains Generis Role.
        $this->assertTrue(array_key_exists($prefix . 'baseRole', $userRoles));
        
        $this->service->unnatachRole($user, $role);
        $userRoles = $this->service->getUserRoles($user);
        $this->assertEquals(count($userRoles), 1);
        $this->assertFalse(array_key_exists($prefix . 'baseRole', $userRoles));
        
        $this->assertTrue($user->delete());
    }

    public function testGetDefaultRole()
    {
        $defaultRole = $this->service->getDefaultRole();
        $expectedRole = new core_kernel_classes_Resource(INSTANCE_ROLE_GENERIS);
        $this->assertEquals($defaultRole->getUri(), $expectedRole->getUri());
    }

    public function testGetAllowedRoles()
    {
        $expectedRole = new core_kernel_classes_Resource(INSTANCE_ROLE_GENERIS);
        $allowedRoles = $this->service->getAllowedRoles();
        $this->assertEquals(count($allowedRoles), 1);
        $this->assertTrue(array_key_exists($expectedRole->getUri(), $allowedRoles));
        $role = current($allowedRoles);
        $this->assertEquals($expectedRole->getUri(), $role->getUri());
    }

    public function testRolesCache()
    {
        $includedRole = $this->service->addRole('INCLUDED ROLE');
        $role = $this->service->addRole('CACHE ROLE', $includedRole);
        
        // Nothing is in the cache, we should get an exception.
        try {
            core_kernel_users_Cache::retrieveIncludedRoles($role);
            $this->assertTrue(false, 'An exception should be raised when trying to retrieve included roles that are not yet in the cache memory.');
        } catch (core_kernel_users_CacheException $e) {
            $this->assertTrue(true);
        }
        
        $includedRoles = $this->service->getIncludedRoles($role);
        $this->assertEquals(count($includedRoles), 1);
        $this->assertTrue(array_key_exists($includedRole->getUri(), $includedRoles));
        
        // try to put included roles in the cache.
        try {
            core_kernel_users_Cache::cacheIncludedRoles($role, $includedRoles);
            
            // now try to retrieve it.
            $includedRolesFromCache = core_kernel_users_Cache::retrieveIncludedRoles($role);
            $this->assertTrue(is_array($includedRolesFromCache));
            $this->assertEquals(count($includedRolesFromCache), 1);
            $this->assertTrue(array_key_exists($includedRole->getUri(), $includedRolesFromCache));
            
            // and remove it !
            $this->assertTrue(core_kernel_users_Cache::removeIncludedRoles($role));
            $this->assertFalse(core_kernel_users_Cache::areIncludedRolesInCache($role));
        } catch (core_kernel_users_CacheException $e) {
            $this->assertTrue(false, 'An exception occured while writing included roles in the cache memory.');
        }
        
        // try to flush users cache.
        try {
            core_kernel_users_Cache::flush();
        } catch (core_kernel_users_CacheException $e) {
            $this->assertTrue(false, 'An error occured while flushing the users cache.');
        }
        
        $includedRole->delete();
        $role->delete();
    }

    public function testClearRoles()
    {
        $prefix = LOCAL_NAMESPACE . '#';
        $roleUris = array(
            $prefix . 'baseRole',
            $prefix . 'subRole1',
            $prefix . 'subRole2',
            $prefix . 'subRole3',
            $prefix . 'subRole11',
            $prefix . 'subRole12',
            $prefix . 'subRole13'
        );
        
        foreach ($roleUris as $ru) {
            $r = new core_kernel_classes_Class($ru);
            $r->delete(true);
            $this->assertFalse($r->exists());
        }
    }

    public function testLogout()
    {
        $this->assertTrue($this->service->isASessionOpened());
        $this->service->logout();
        $this->assertFalse($this->service->isASessionOpened());
    }
}