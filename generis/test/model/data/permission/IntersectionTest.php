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
use oat\generis\model\data\permission\implementation\Intersection;

class IntersectionTest extends GenerisPhpUnitTestRunner
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

   
    private function createIntersection()
    {
        $permissionModel1 = $this->prophesize('oat\generis\model\data\permission\PermissionInterface');
        $permissionModel1->getSupportedRights()->willReturn(array('rightA', 'rightB', 'rightC', 'right'));
        
        $permissionModel2 = $this->prophesize('oat\generis\model\data\permission\PermissionInterface');
        $permissionModel2->getSupportedRights()->willReturn(array('rightB', 'rightC', 'rightD', 'rightAB'));
        
        $permissionModel3 = $this->prophesize('oat\generis\model\data\permission\PermissionInterface');
        $permissionModel3->getSupportedRights()->willReturn(array('rightC', 'rightD', 'rightE', 'rightABC'));
        
        // res1
        $permissionModel1->getPermissions($this->user, array('res1'))->willReturn(array('res1' => array('rightA')));
        $permissionModel2->getPermissions($this->user, array('res1'))->willReturn(array('res1' => array('rightB')));
        $permissionModel3->getPermissions($this->user, array('res1'))->willReturn(array('res1' => array('rightC')));
        
        // res2
        $permissionModel1->getPermissions($this->user, array('res1', 'res2'))->willReturn(array('res1' => array('rightA', 'rightC'), 'res2' => array()));
        $permissionModel2->getPermissions($this->user, array('res1', 'res2'))->willReturn(array('res1' => array('rightC'), 'res2' => array()));
        $permissionModel3->getPermissions($this->user, array('res1', 'res2'))->willReturn(array('res1' => array('rightC', 'rightD'), 'res2' => array()));
        
        return Intersection::spawn(array($permissionModel1->reveal(), $permissionModel2->reveal(), $permissionModel3->reveal()));
    }
    
    public function testConstruct()
    {
        $this->assertInstanceOf('oat\generis\model\data\permission\PermissionInterface', $this->createIntersection());
    }
    
    public function testGetPermissions()
    {
        $model = $this->createIntersection();
        $this->assertEquals(array('res1' => array()), $model->getPermissions($this->user, array('res1')));
        $this->assertEquals(array('res1' => array('rightC'), 'res2' => array()), $model->getPermissions($this->user, array('res1', 'res2')));
    }

    public function testGetSupportedRights()
    {
        $model = $this->createIntersection();
        $this->assertEquals(array('rightC'), $model->getSupportedRights());
        
    }
    
    public function testOnResourceCreated(){
        $permissionModel = $this->prophesize('oat\generis\model\data\permission\PermissionInterface');
        $permissionModel2 = $this->prophesize('oat\generis\model\data\permission\PermissionInterface');
        
        $model = Intersection::spawn(array($permissionModel->reveal(),$permissionModel2->reveal()));
        $resourceprophecy = $this->prophesize('core_kernel_classes_Resource');
        $resource = $resourceprophecy->reveal();
        $model->onResourceCreated($resource);
        $permissionModel->onResourceCreated($resource)->shouldHaveBeenCalled();
        $permissionModel2->onResourceCreated($resource)->shouldHaveBeenCalled();
        
    }
    
    public function testPhpSerialize()
    {
        // no idea how to test
    }
    
}