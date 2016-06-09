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
use \oat\tao\test\TaoPhpUnitTestRunner;

class StorageDirectoryTest extends TaoPhpUnitTestRunner
{
    protected $id;
    protected $path;
    protected $fsPath;
    protected $adapterUrl ;
    protected $instance;
    protected $sampleDir;

    /**
     * tests initialization
     */
    public function setUp()
    {
        $this->id = 123;
        $this->path = 'samples/';
        $this->fsPath = 'path/directory/';
        $this->adapterUrl = 'fixtureUrl';

        $this->sampleDir = tao_helpers_File::createTempDir();
    }

    public function tearDown()
    {
        if ($this->instance) {
            unset($this->instance);
        }

        $this->rrmdir($this->sampleDir);
    }

    protected function rrmdir($dir)
    {
        foreach(glob($dir . '/*') as $file) {
            if(is_dir($file)) {
                $this->rrmdir($file);
            }
            else {
                unlink($file);
            }
        }
        rmdir($dir);
    }

    /**
     * Create StorageDirectory
     * @return tao_models_classes_service_StorageDirectory
     */
    protected function getDirectoryStorage()
    {
        $fsFixture = $this->getFs();
        $providerFixture = $this->getAccessProvider($this->path);

        return new tao_models_classes_service_StorageDirectory(
            $this->id,
            $fsFixture,
            $this->path,
            $providerFixture
        );
    }

    /**
     * Create FileSytemMock
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFs()
    {
        $fsFixture = $this->getMockBuilder('core_kernel_fileSystem_FileSystem')
            ->disableOriginalConstructor()
            ->getMock();
        $fsFixture->method('getPath')->willReturn($this->fsPath);
        $fsFixture->method('getUri')->willReturn($this->adapterUrl);

        return $fsFixture;
    }

    /**
     * Create accessProvider with expected value for accessUrl
     * @param $pathFixture
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAccessProvider($pathFixture)
    {
        $providerFixture = $this->getMockBuilder('oat\tao\model\websource\TokenWebSource')->getMock();
        $providerFixture->method('getAccessUrl')->with($pathFixture)->willReturn($this->adapterUrl);

        return $providerFixture;
    }

    /**
     * Test if accessProvider is set
     */
    public function testInitAccessProviderUrl()
    {
        $this->instance = $this->getDirectoryStorage();
        $this->assertEquals($this->adapterUrl, $this->instance->getPublicAccessUrl());
    }

    /**
     * Test if id is set
     */
    public function testInitId()
    {
        $this->instance = $this->getDirectoryStorage();
        $this->assertEquals($this->id, $this->instance->getId());
    }

    /**
     * Test if path is correctly retrieved
     */
    public function testInitPathWithFsPath()
    {
        $this->instance = $this->getDirectoryStorage();
        $this->assertEquals($this->fsPath.$this->path, $this->instance->getPath());
    }

    /**
     * Test if path is set
     */
    public function testInitPath()
    {
        $this->instance = $this->getDirectoryStorage();
        $this->assertEquals($this->path, $this->instance->getRelativePath());
    }

    /**
     * Test if storage directory use ServiceLocatorAwareTrait
     */
    public function testServiceLocatorAwareTrait()
    {
        $this->instance = $this->getDirectoryStorage();
        $this->assertTrue(method_exists($this->instance, 'getServiceLocator'));
        $this->assertTrue(method_exists($this->instance, 'setServiceLocator'));
    }

    /**
     * Test if directory is public when provider is supplied
     */
    public function testIsPublic()
    {
        $this->instance = new tao_models_classes_service_StorageDirectory(1, 2, 3 ,4);
        $this->assertTrue($this->instance->isPublic());
    }

    /**
     * Test if directory is not public when provider is null
     */
    public function testIsNotPublic()
    {
        $this->instance = new tao_models_classes_service_StorageDirectory(1, 2, 3 ,null);
        $this->assertFalse($this->instance->isPublic());

        $this->setExpectedException(common_Exception::class);
        $this->instance->getPublicAccessUrl('test');
    }

    /**
     * Create serviceLocator with custom filesystem using adapter for sample
     * @return object
     */
    public function getServiceLocatorWithFileSystem()
    {
        $adaptersFixture = array (
            'filesPath' => $this->sampleDir,
            'adapters' => array (
                $this->adapterUrl => array(
                    'class' => 'Local',
                    'options' => array(
                        'root' => $this->sampleDir
                    )
                )
            )
        );

        $fileSystemService = new \oat\oatbox\filesystem\FileSystemService($adaptersFixture);

        $smProphecy = $this->prophesize(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $smProphecy->get(\oat\oatbox\filesystem\FileSystemService::SERVICE_ID)->willReturn($fileSystemService);
        return $smProphecy->reveal();
    }

    public function testGetIterator()
    {
        $this->instance = $this->getDirectoryStorage();
        $serviceLocatorFixture = $this->getServiceLocatorWithFileSystem();
        $this->instance->setServiceLocator($serviceLocatorFixture);

        $iterator = $this->instance->getIterator();
        $this->assertInstanceOf(Traversable::class, $iterator);
        
        $files = array('43bytes.php', 'test/myFile.css', 'test/sample');
        foreach($this->instance as $key => $file){
            $this->assertEquals($files[$key], $file);
        }
    }


    /**
     * Test read and write from resource
     */
    public function testReadWriteUsingAdapter()
    {
        $tmpFile = uniqid() . '.php';
        $this->instance = $this->getDirectoryStorage();
        $serviceLocatorFixture = $this->getServiceLocatorWithFileSystem();
        $this->instance->setServiceLocator($serviceLocatorFixture);

        $resource = fopen(__DIR__ . '/samples/43bytes.php', 'r');
        $this->instance->write($tmpFile, $resource);
        $this->assertTrue(file_exists($this->sampleDir . $this->path . $tmpFile));
        fclose($resource);
        $this->assertEquals(
            file_get_contents(__DIR__ . '/samples/43bytes.php'),
            file_get_contents($this->sampleDir . $this->path . $tmpFile)
        );

        $readFixture = $this->instance->read($tmpFile);
        $this->assertTrue(is_resource($readFixture));
        fclose($readFixture);
    }

    /**
     * Test read and write from stream
     */
    public function testWriteReadStream()
    {
        $tmpFile = uniqid() . '.php';
        $this->instance = $this->getDirectoryStorage();
        $serviceLocatorFixture = $this->getServiceLocatorWithFileSystem();
        $this->instance->setServiceLocator($serviceLocatorFixture);

        $resource = fopen(__DIR__ . '/samples/43bytes.php', 'r');
        $streamFixture = GuzzleHttp\Psr7\stream_for($resource);

        $this->instance->writeStream($tmpFile, $streamFixture);
        $this->assertTrue(file_exists($this->sampleDir . $this->path . $tmpFile));
        fclose($resource);
        $streamFixture->close();
        $this->assertEquals(
            file_get_contents(__DIR__ . '/samples/43bytes.php'),
            file_get_contents($this->sampleDir . $this->path . $tmpFile)
        );

        $readFixture = $this->instance->readStream($tmpFile);
        $this->assertInstanceOf(GuzzleHttp\Psr7\Stream::class, $readFixture);
        $readFixture->close();

    }

    /**
     * Test write stream in case of remote resource
     */
    public function testWriteRemoteStream()
    {
        $tmpFile = uniqid() . '.php';
        $this->instance = $this->getDirectoryStorage();
        $serviceLocatorFixture = $this->getServiceLocatorWithFileSystem();
        $this->instance->setServiceLocator($serviceLocatorFixture);

        $client = new \GuzzleHttp\Client();
        $response = $client->get('http://www.google.org');
        $this->assertTrue($this->instance->writeStream($tmpFile, $response->getBody()));
        $this->assertNotEquals(0, $response->getBody()->getSize());
    }

    /**
     * Test write stream in case of remote resource
     */
    public function testSeekToEndOfFileForWriteStream()
    {
        $tmpFile = uniqid() . '.php';
        $this->instance = $this->getDirectoryStorage();
        $serviceLocatorFixture = $this->getServiceLocatorWithFileSystem();
        $this->instance->setServiceLocator($serviceLocatorFixture);

        $resource = fopen(__DIR__ . '/samples/43bytes.php', 'r');
        fseek($resource, 43);
       // echo filesize(__DIR__ . '/samples/43bytes.php');
        $streamFixture = GuzzleHttp\Psr7\stream_for($resource);

        $this->instance->writeStream($tmpFile, $streamFixture);
        $this->assertTrue(file_exists($this->sampleDir . $this->path . $tmpFile));
        fclose($resource);
        $streamFixture->close();
        $this->assertEquals(
            file_get_contents(__DIR__ . '/samples/43bytes.php'),
            file_get_contents($this->sampleDir . $this->path . $tmpFile)
        );

        $readFixture = $this->instance->readStream($tmpFile);
        $this->assertInstanceOf(GuzzleHttp\Psr7\Stream::class, $readFixture);
        $readFixture->close();
    }

    /**
     * Test exception for unseekable resource
     */
     public function testUnseekableResource()
     {
         $tmpFile = uniqid() . '.php';
         $this->instance = $this->getDirectoryStorage();
         $serviceLocatorFixture = $this->getServiceLocatorWithFileSystem();
         $this->instance->setServiceLocator($serviceLocatorFixture);

         $resource = fopen('http://www.google.org', 'r');
         $streamFixture = GuzzleHttp\Psr7\stream_for($resource);

         $this->setExpectedException(common_Exception::class);
         $this->instance->writeStream($tmpFile, $streamFixture);

         fclose($resource);
     }

    /**
     * Test has and delete file function with no file
     */
    public function testHasAndDeleteWithNoFile()
    {
        $tmpFile = uniqid() . '.php';
        $this->instance = $this->getDirectoryStorage();
        $serviceLocatorFixture = $this->getServiceLocatorWithFileSystem();
        $this->instance->setServiceLocator($serviceLocatorFixture);

        $this->assertFalse($this->instance->has($tmpFile));

        $this->setExpectedException(tao_models_classes_FileNotFoundException::class);
        $this->instance->delete($tmpFile);
    }

    /**
     * Test has and delete file function with valid file
     */
    public function testHasAndDeleteWithValidFile()
    {
        $tmpFile = uniqid() . '.php';
        $this->instance = $this->getDirectoryStorage();
        $serviceLocatorFixture = $this->getServiceLocatorWithFileSystem();
        $this->instance->setServiceLocator($serviceLocatorFixture);

        $resource = fopen(__DIR__ . '/samples/43bytes.php', 'r');
        $streamFixture = GuzzleHttp\Psr7\stream_for($resource);
        $this->instance->writeStream($tmpFile, $streamFixture);

        $this->assertTrue($this->instance->has($tmpFile));
        $this->assertTrue($this->instance->delete($tmpFile));
    }
}

