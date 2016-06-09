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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoOpenWebItem\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoOpenWebItem\model\OwiItemModel;

class OwiItemModelTest extends TaoPhpUnitTestRunner
{

    /**
     * tests initialization
     */
    public function setUp()
    {
        
        TaoPhpUnitTestRunner::initTest();
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $uri            
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getResourceMock($uri)
    {
        $resourceMock = $this->getMockBuilder('core_kernel_classes_Resource')
            ->setMockClassName('FakeResource')
            ->setConstructorArgs(array(
            $uri
        ))
            ->getMock();
        
        return $resourceMock;
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetAuthoringUrl()
    {
        $fakeUri = 'http://fakens.rdf#fakeItemUri';
        $model = new OwiItemModel();
        $resourceMock = $this->getResourceMock($fakeUri);
        $resourceMock->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue($fakeUri));
        $url = $model->getAuthoringUrl($resourceMock);
        $this->assertEquals(1, preg_match('/instance=' . urlencode($fakeUri) . '/', $url));
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetPreviewUrl()
    {
        $fakeUri = 'http://fakens.rdf#fakeUri';
        $model = new OwiItemModel();
        $resourceMock = $this->getResourceMock($fakeUri);
        $resourceMock->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue($fakeUri));
        $fakeLg = 'fake_Lg';
        $url = $model->getPreviewUrl($resourceMock, $fakeLg);
        $this->assertEquals(1, preg_match('/uri=' . urlencode($fakeUri) . '&lang=' . $fakeLg . '/', $url));
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetExportHandlers()
    {
        $model = new OwiItemModel();
        $this->assertInstanceOf('oat\taoOpenWebItem\model\export\OwiExportHandler', current($model->getExportHandlers()));
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetImportHandlers()
    {
        $model = new OwiItemModel();
        $this->assertInstanceOf('oat\taoOpenWebItem\model\import\OwiImportHandler', current($model->getImportHandlers()));
    }
}

?>