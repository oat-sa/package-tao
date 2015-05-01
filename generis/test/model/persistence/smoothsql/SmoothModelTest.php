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
namespace oat\generis\test\model\persistence\smoothsql;

use \core_kernel_persistence_smoothsql_SmoothModel;
use oat\generis\test\GenerisPhpUnitTestRunner;

class SmoothModelTest extends GenerisPhpUnitTestRunner
{

    /**
     * 
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        GenerisPhpUnitTestRunner::initTest();
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return \core_kernel_persistence_smoothsql_SmoothModel
     */
    public function testConstuct()
    {
        // $this->markTestSkipped('test it');
        try {
            $model = new core_kernel_persistence_smoothsql_SmoothModel(array());
        } catch (\common_Exception $e) {
            $this->assertInstanceOf('common_exception_MissingParameter', $e);
        }
        $conf = array(
            'persistence' => 'default'
        );
        $model = new core_kernel_persistence_smoothsql_SmoothModel($conf);
        return $model;
    }

    /**
     * @depends testConstuct
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetConfig($model)
    {
        $this->assertEquals(array(
            'persistence' => 'default'
        ), $model->getOptions());
    }

    /**
     * @depends testConstuct
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param array $model            
     */
    public function testGetRdfInterface($model)
    {
        $this->assertInstanceOf('core_kernel_persistence_smoothsql_SmoothRdf', $model->getRdfInterface());
    }

    /**
     * @depends testConstuct
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param array $model            
     */
    public function testGetRdfsInterface($model)
    {
        $this->assertInstanceOf('core_kernel_persistence_smoothsql_SmoothRdfs', $model->getRdfsInterface());
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetUpdatableModelIds()
    {
        $models = core_kernel_persistence_smoothsql_SmoothModel::getUpdatableModelIds();
        $this->assertArraySubset(array(
            1
        ), $models);
        
        core_kernel_persistence_smoothsql_SmoothModel::forceReloadModelIds();
        $models2 = core_kernel_persistence_smoothsql_SmoothModel::getUpdatableModelIds();
        
        $this->assertArraySubset(array(
            1
        ), $models2);
        $this->assertEquals($models, $models2);
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetReadableModelIds()
    {
        $models = core_kernel_persistence_smoothsql_SmoothModel::getReadableModelIds();
        $this->assertArraySubset(array(
            1
        ), $models);
    }

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     * @expectedException common_exception_Error
     */
    public function testForceUpdatableModelIds()
    {
        core_kernel_persistence_smoothsql_SmoothModel::forceUpdatableModelIds(array(
            38,
            39
        ));
   }
}