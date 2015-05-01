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
 * Copyright (c) (original work) 2015 Open Assessment Technologies SA
 * 
 */
namespace oat\generis\test\model\data\permission;

use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\generis\model\data\permission\implementation\NoAccess;

class NoAccessTest extends GenerisPhpUnitTestRunner
{
    /**
     * @var oat\oatbox\user\User
     */
    private $user;

    public function setUp()
    {
        GenerisPhpUnitTestRunner::initTest();
        $user = $this->prophesize('oat\oatbox\user\User');
        $user->getIdentifier()->willReturn('tastIdentifier\\_of_//User');
        
        $this->user = $user->reveal();
    }

    public function testConstruct()
    {
        $model = new NoAccess();
        $this->assertInstanceOf('oat\generis\model\data\permission\PermissionInterface', $model);
    }
    
    public function testGetPermissions()
    {
        $model = new NoAccess();
        $this->assertEquals(array('res1' => array()), $model->getPermissions($this->user, array('res1')));
        $this->assertEquals(array('res1' => array(), 'res2' => array()), $model->getPermissions($this->user, array('res1', 'res2')));
    }

    public function testGetSupportedRights()
    {
        $model = new NoAccess();
        $this->assertEquals(array(), $model->getSupportedRights());
        
    }
}