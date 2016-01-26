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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT); *
 * 
 *  
 */

namespace oat\taoDacSimple\test\model;

use oat\taoDacSimple\model\DataBaseAccess;

/**
 * Test database access
 * 
 * @author Antoine Robin, <antoine.robin@vesperiagroup.com>
 * @package taodacSimple
 *
 */
class DataBaseAccessTest extends \PHPUnit_Framework_TestCase {


    /**
     * @var DataBaseAccess
     */
    protected $instance;

    public function setUp() {
        $this->instance = new DataBaseAccess();
    }

    public function tearDown() {
        $this->instance = null;
    }

    /**
     * Return a persistence Mock object
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getPersistenceMock($queryParams, $queryFixture, $resultFixture) {


        $statementMock = $this->getMock('PDOStatementFake', array('fetchAll'),array(),'', false, false, true, false);
        $statementMock->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue($resultFixture));

        
        $driverMock =$this->getMockForAbstractClass('common_persistence_Driver', array(), 'common_persistence_Driver_Mock', false, false, true, array('query'), false);

        
        $persistenceMock = $this->getMock(
            'common_persistence_Persistence',
            array('getDriver','query'),
            array(array(),$driverMock),
            '',
            false,
            true,
            false
        );
        $persistenceMock
            ->method('getDriver')
            ->with(array(),$driverMock)
            ->will($this->returnValue($driverMock));
        
        $persistenceMock
            ->method('query')
            ->with($queryFixture, $queryParams)
            ->will($this->returnValue($statementMock)); 
        
        return $persistenceMock;
    }

    
    
    /**
     * @return array
     */
    public function resourceIdsProvider() {
        return array(
            array(array(1)),
            array(array(1, 2, 3, 4)),
            array(array(1, 2)),
        );
    }

    /**
     * @dataProvider resourceIdsProvider
     * @preserveGlobalState disable
     * @param $resourceIds
     */
    public function testGetUsersWithPermissions($resourceIds)
    {



        $inQuery = implode(',', array_fill(0, count($resourceIds), '?'));
        $queryFixture = "SELECT resource_id, user_id, privilege FROM " . \oat\taoDacSimple\model\DataBaseAccess::TABLE_PRIVILEGES_NAME . "
        WHERE resource_id IN ($inQuery)";

        $resultFixture = array(
            array('fixture')
        );

        $persistenceMock = $this->getPersistenceMock($resourceIds, $queryFixture, $resultFixture);

        $this->instance->setPersistence($persistenceMock);

        $this->assertSame($resultFixture, $this->instance->getUsersWithPermissions($resourceIds));
    }


    /**
     * @return array
     */
    public function getPermissionProvider() {
        return array(
            array(array(1,2,3), array(1, 2, 3)),
            array(array(1), array(2)),
        );
    }
    /**
     * Get the permissions a user has on a list of ressources
     * @dataProvider getPermissionProvider
     * @access public
     * @param  array $userIds
     * @param  array $resourceIds
     * @return array()
     */
    public function testGetPermissions($userIds, array $resourceIds)
    {
        // get privileges for a user/roles and a resource
        $returnValue = array();

        $inQueryResource = implode(',', array_fill(0, count($resourceIds), '?'));
        $inQueryUser = implode(',', array_fill(0, count($userIds), '?'));
        $query = "SELECT resource_id, privilege FROM " . DataBaseAccess::TABLE_PRIVILEGES_NAME
            . " WHERE resource_id IN ($inQueryResource) AND user_id IN ($inQueryUser)";


        $fetchResultFixture = array(
            array('resource_id' => 1, 'privilege' => 'open'),
            array('resource_id' => 2, 'privilege' => 'close'),
            array('resource_id' => 3, 'privilege' => 'create'),
            array('resource_id' => 3, 'privilege' => 'delete'),
        );

        $resultFixture = array(
            1 =>  array('open'),
            2 => array('close'),
            3 => array('create', 'delete')
        );

        $params = $resourceIds;
        foreach ($userIds as $userId) {
            $params[] = $userId;
        }
        $persistenceMock = $this->getPersistenceMock($params, $query, $fetchResultFixture);

        $this->instance->setPersistence($persistenceMock);

        $this->assertSame($resultFixture, $this->instance->getPermissions($userIds, $resourceIds));
    }

    /**
     * add permissions of a user to a resource
     *
     * @access public
     * @param  string $user
     * @param  string $resourceId
     * @param  array $rights
     * @return boolean
     */
    public function addPermissions($user, $resourceId, $rights)
    {

        foreach ($rights as $privilege) {
            // add a line with user URI, resource Id and privilege
            $this->persistence->insert(
                self::TABLE_PRIVILEGES_NAME,
                array('user_id' => $user, 'resource_id' => $resourceId, 'privilege' => $privilege)
            );
        }
        return true;
    }

    /**
     * remove permissions to a resource for a user
     *
     * @access public
     * @param  string $user
     * @param  string $resourceId
     * @param  array $rights
     * @return boolean
     */
    public function removePermissions($user, $resourceId, $rights)
    {
        //get all entries that match (user,resourceId) and remove them
        $inQueryPrivilege = implode(',', array_fill(0, count($rights), '?'));
        $query = "DELETE FROM " . self::TABLE_PRIVILEGES_NAME . " WHERE resource_id = ? AND privilege IN ($inQueryPrivilege) AND user_id = ?";
        $params = array($resourceId);
        foreach ($rights as $rightId) {
            $params[] = $rightId;
        }
        $params[] = $user;

        $this->persistence->exec($query, $params);

        return true;
    }

    /**
     * Remove all permissions from a resource
     *
     * @access public
     * @param  array $resourceIds
     * @return boolean
     */
    public function removeAllPermissions($resourceIds)
    {
        //get all entries that match (resourceId) and remove them
        $inQuery = implode(',', array_fill(0, count($resourceIds), '?'));
        $query = "DELETE FROM " . self::TABLE_PRIVILEGES_NAME . " WHERE resource_id IN ($inQuery)";
        $this->persistence->exec($query, $resourceIds);

        return true;
    }


}