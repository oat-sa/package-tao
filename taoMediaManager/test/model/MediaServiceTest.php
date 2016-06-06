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

use oat\taoMediaManager\model\MediaService;
use oat\tao\test\TaoPhpUnitTestRunner;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

class MediaServiceTest extends TaoPhpUnitTestRunner
{

    /**
     * @var MediaService
     */
    private $mediaService = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $fileManagerMock = null;

    /**
     * @var \core_kernel_classes_Class
     */
    private $testClass = null;

    public function setUp()
    {
        // ensure a user is logged in, in case the repository commit gets triggered
        $this->initTest();
        $this->mediaService = MediaService::singleton();

        //fileManagerMock
        $this->fileManagerMock = $this->getMockBuilder('oat\taoMediaManager\model\fileManagement\SimpleFileManagement')
            ->setMethods(array('storeFile', 'deleteFile'))
            ->getMock();

        $ref = new \ReflectionProperty('oat\taoMediaManager\model\fileManagement\FileManager', 'fileManager');
        $ref->setAccessible(true);
        $ref->setValue(null, $this->fileManagerMock);
        
        $this->testClass = $this->mediaService->getRootClass()->createSubClass('test class');
    }

    public function tearDown()
    {
        $this->fileManagerMock = null;
        $this->mediaService->deleteClass($this->testClass);

        $ref = new \ReflectionProperty('oat\taoMediaManager\model\fileManagement\FileManager', 'fileManager');
        $ref->setAccessible(true);
        $ref->setValue(null, null);
        $ref->setAccessible(false);

    }

    public function testGetRootClass()
    {
        $rootClass = new \core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOMedia.rdf#Media');
        $this->assertEquals(
            $rootClass,
            $this->mediaService->getRootClass(),
            'The root class of the service is not correct'
        );
    }

    private function initializeMock($fileTmp)
    {
        $this->fileManagerMock->expects($this->once())
            ->method('storeFile')
            ->with($fileTmp)
            ->willReturn('MyGreatLink');
    }

    public function testCreateMediaInstance()
    {

        $fileTmp = dirname(__DIR__) . '/sample/Brazil.png';

        $this->initializeMock($fileTmp);
        
        $lang = 'EN-en';
        $classUri = $this->testClass->getUri();

        //clear previous tests
        $root = new \core_kernel_classes_Class($classUri);

        $link = $this->mediaService->createMediaInstance($fileTmp, $classUri, $lang);

        $root = new \core_kernel_classes_Class($classUri);
        $instances = $root->getInstances();
        /** @var \core_kernel_classes_Resource $instance */
        $instance = array_pop($instances);
        $thing = $instance->getUniquePropertyValue(new \core_kernel_classes_Property(MEDIA_LINK));
        $linkResult = $thing instanceof \core_kernel_classes_Resource ? $thing->getUri() : (string)$thing;
        $this->assertInstanceOf(
            '\core_kernel_classes_Resource',
            $instance,
            'It should create an instance under the class in parameter'
        );
        $this->assertEquals('Brazil.png', $instance->getLabel(), 'The instance label is wrong');
        $this->assertInternalType('string', $link, 'The method return should be a string');
        $this->assertEquals($instance->getUri(), $link, 'The instance link is wrong');
        $this->assertEquals($linkResult, 'MyGreatLink', 'The returned link is wrong');
        $this->assertEquals(
            $lang,
            $instance->getUniquePropertyValue(new \core_kernel_classes_Property(MEDIA_LANGUAGE)),
            'The instance language is wrong'
        );

        $root->delete(true);
        $root->setSubClassOf($this->mediaService->getRootClass());

    }

    public function testEditMediaInstance()
    {

        $fileTmp = dirname(__DIR__) . '/sample/Italy.png';
        $this->initializeMock($fileTmp);

        $this->fileManagerMock->expects($this->once())
            ->method('deleteFile')
            ->with('MyLink')
            ->willReturn(true);

        $lang = 'EN-en';
        $instanceUri = 'http://myFancyDomain.com/myGreatInstanceUri';
        $instance = new \core_kernel_classes_Class($instanceUri);
        $instance->setPropertyValue(new \core_kernel_classes_Property(MEDIA_LINK), 'MyLink');

        $this->mediaService->editMediaInstance($fileTmp, $instanceUri, $lang);

        $this->assertEquals(
            $lang,
            $instance->getUniquePropertyValue(new \core_kernel_classes_Property(MEDIA_LANGUAGE)),
            'The instance language is wrong'
        );

        // remove what has been done
        $inst = new \core_kernel_classes_Resource($instanceUri);
        $inst->delete();

    }
}
 