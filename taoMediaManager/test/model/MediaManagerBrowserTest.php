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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoMediaManager\test\model;

use oat\taoMediaManager\model\MediaSource;


class MediaManagerBrowserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var MediaSource
     */
    private $mediaManagerManagement = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $fileManagerMock = null;


    private $rootClass = '';

    public function setUp()
    {
        $this->rootClass = 'http://myFancyDomaine.com/myGreatCLassUriForBrowserTest';
        $this->mediaManagerManagement = new MediaSource(array('lang' => 'EN_en', 'rootClass' => $this->rootClass));

        //fileManagerMock
        $this->fileManagerMock = $this->getMockBuilder('oat\taoMediaManager\model\fileManagement\SimpleFileManagement')
            ->getMock();

        $ref = new \ReflectionProperty('oat\taoMediaManager\model\fileManagement\FileManager', 'fileManager');
        $ref->setAccessible(true);
        $ref->setValue(null, $this->fileManagerMock);
    }

    public function tearDown()
    {
        $this->fileManagerMock = null;

        $ref = new \ReflectionProperty('oat\taoMediaManager\model\fileManagement\FileManager', 'fileManager');
        $ref->setAccessible(true);
        $ref->setValue(null, null);
        $ref->setAccessible(false);

    }

    public function testGetDirectory()
    {

        $root = new \core_kernel_classes_Class($this->rootClass);

        //Remove what has been done
        $subclasses = $root->getSubClasses();
        foreach ($subclasses as $subclass) {
            $subclass->delete(true);
        }

        $root->delete();
        $root->setLabel('myRootClass');

        $acceptableMime = array();
        $depth = 1;

        $directory = $this->mediaManagerManagement->getDirectory($this->rootClass, $acceptableMime, $depth);

        $this->assertInternalType('array', $directory, 'The result should be an array');
        $this->assertArrayHasKey('label', $directory, 'The result should contain "label"');
        $this->assertArrayHasKey('path', $directory, 'The result should contain "path"');
        $this->assertArrayHasKey('children', $directory, 'The result should contain "children"');

        $this->assertInternalType('array', $directory['children'], 'Children should be an array');
        $this->assertEmpty($directory['children'], 'Children should be empty');
        $this->assertEquals('myRootClass', $directory['label'], 'The label is not correct');
        $this->assertEquals('taomedia://mediamanager/' . \tao_helpers_Uri::encode($this->rootClass), $directory['path'], 'The path is not correct');

        $root->createSubClass('mySubClass1');
        $root->createSubClass('mySubClass0');

        $newDirectory = $this->mediaManagerManagement->getDirectory($this->rootClass, $acceptableMime, $depth);
        $this->assertInternalType('array', $newDirectory['children'], 'Children should be an array');
        $this->assertNotEmpty($newDirectory['children'], 'Children should not be empty');

        $labels = array();
        foreach ($newDirectory['children'] as $i => $child) {
            $this->assertInternalType('array', $child, 'The result should be an array');
            $this->assertArrayHasKey('label', $child, 'The result should contain "label"');
            $this->assertArrayHasKey('path', $child, 'The result should contain "path"');

            $labels[] = $child['label'];
        }
        $this->assertEquals(2, count($labels));
        $this->assertContains('mySubClass0', $labels);
        $this->assertContains('mySubClass1', $labels);

        //Remove what has been done
        $subclasses = $root->getSubClasses();
        foreach ($subclasses as $subclass) {
            $subclass->delete();
        }
        $root->delete();

    }

    public function testGetFileInfo()
    {

        $fileTmp = dirname(__DIR__) . '/sample/Brazil.png';

        $root = new \core_kernel_classes_Class($this->rootClass);
        $instance = $root->createInstance('Brazil.png');
        $instance->setPropertyValue(new \core_kernel_classes_Property(MEDIA_LINK), 'myGreatLink');
        $instance->setPropertyValue(new \core_kernel_classes_Property(MEDIA_MIME_TYPE), 'image/png');

        $uri = $instance->getUri();
        $this->fileManagerMock->expects($this->once())
            ->method('retrieveFile')
            ->with('myGreatLink')
            ->willReturn($fileTmp);

        $fileInfo = $this->mediaManagerManagement->getFileInfo($uri);
        $instance->delete(true);
        $this->assertInternalType('array', $fileInfo, 'The result should be an array');
        $this->assertArrayHasKey('name', $fileInfo, 'The result should contain "name"');
        $this->assertArrayHasKey('mime', $fileInfo, 'The result should contain "mime"');
        $this->assertArrayHasKey('size', $fileInfo, 'The result should contain "size"');
        $this->assertArrayHasKey('uri', $fileInfo, 'The result should contain "size"');

        $this->assertEquals($instance->getLabel(), $fileInfo['name'], 'The file name is not correct');
        $this->assertEquals('image/png', $fileInfo['mime'], 'The mime type is not correct');
        $this->assertEquals('taomedia://mediamanager/' . \tao_helpers_Uri::encode($uri), $fileInfo['uri'], 'The uri is not correct');
    }

    /**
     * @expectedException        \tao_models_classes_FileNotFoundException
     * @expectedExceptionMessage File A Fake link not found
     */
    public function testGetFileInfoFail()
    {

        $link = 'A Fake link';

        $this->mediaManagerManagement->getFileInfo($link);
    }

}
 