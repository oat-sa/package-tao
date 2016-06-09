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

use oat\generis\test\GenerisPhpUnitTestRunner;

class FileTest extends GenerisPhpUnitTestRunner {
	
	private $fsPath;
	/**
	 * @var core_kernel_versioning_Repository
	 */
	private $fileSource;
    
    public function setUp()
    {
        GenerisPhpUnitTestRunner::initTest();
        $this->fsPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'taoFileTestCase'.DIRECTORY_SEPARATOR;
        mkdir($this->fsPath);
        $this->fileSource = core_kernel_fileSystem_FileSystemFactory::createFileSystem(
        	new core_kernel_classes_Resource(INSTANCE_GENERIS_VCS_TYPE_LOCAL), '', '', '', $this->fsPath, 'testFileSource', true
        );

    }
    
    public function tearDown()
    {
        if(!is_null($this->fileSource)){
            $this->fileSource->delete();
        }
        else {
            throw new common_Exception('should not be null, something wrong happen during test');
        }
        helpers_File::remove($this->fsPath);
    }
    
    public function testIsFile()
	{
	    $clazz = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
	    $instance = $clazz->createInstance('toto.txt','toto');
	    $fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    $instance->setPropertyValue($fileNameProp,'file://toto.txt');
	    $this->assertTrue(core_kernel_file_File::isFile($instance));
	    $this->assertFalse(core_kernel_file_File::isFile($clazz));
	    $instance->delete();
	}
	
	public function testCreate()
	{
	    $file = $this->fileSource->createFile('toto.txt');
	    $this->assertTrue($file instanceof core_kernel_file_File);
	    $fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    $fileName = $file->getOnePropertyValue($fileNameProp);
	    $this->assertEquals($fileName,'toto.txt');
	    $this->assertEquals($file->getAbsolutePath(),$this->fsPath.'toto.txt');
	    $this->assertTrue($file->delete());
	    
	    
	    $file = $this->fileSource->createFile('toto.txt',DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR);
	    $this->assertEquals($file->getAbsolutePath(),$this->fsPath.'tmp'.DIRECTORY_SEPARATOR.'toto.txt');
	    $this->assertTrue($file->delete());
	    
	    // Create dir
	    $dir = $this->fileSource->createFile('', DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'myDir');
	    $this->assertEquals($dir->getAbsolutePath(), $this->fsPath. 'tmp' . DIRECTORY_SEPARATOR . 'myDir');
	    $this->assertTrue($dir->delete());
	}
	
	public function testGetAbsolutePath()
	{
	    $file = $this->fileSource->createFile('toto.txt');
	    $absolutePath = $file->getAbsolutePath();
	    $this->assertEquals($absolutePath, $this->fsPath.'toto.txt');
	    $this->assertTrue($file->delete());
	    
	    $file = $this->fileSource->createFile('toto.txt', DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR);	    
	    $absolutePath = $file->getAbsolutePath();
	    $this->assertEquals($absolutePath, $this->fsPath. 'tmp' . DIRECTORY_SEPARATOR . 'toto.txt');
	    
	    $this->assertTrue($file->delete());
	}
	
	public function testGetFileInfo()
	{
	    $file = $this->fileSource->createFile('toto.txt');
	    $file->setContent('toto is kite surfing !!! le ouf');
	    $fileInfo = $file->getFileInfo();
	    $this->assertIsA($fileInfo, 'SplFileInfo');
		$this->assertTrue($file->delete());
		
		$folderName = 'folder'.crc32(time());
		mkdir($this->fsPath.$folderName);
		$file = $this->fileSource->createFile('', $folderName);
		
	    $fileInfo = $file->getFileInfo();
	    $this->assertIsA($fileInfo,'SplFileInfo');
	    $this->assertTrue($fileInfo->isDir());
	    $this->assertTrue($file->delete());
	    $this->assertFalse($fileInfo->isDir());
	}
	
	public function testSetGetFileContent()
	{
	    $file = $this->fileSource->createFile('toto.txt', null);
	    $file->setContent('toto is kite surfing !!! le ouf');
	    $fileContent = $file->getFileContent();
	    $this->assertEquals($fileContent,'toto is kite surfing !!! le ouf');
		$this->assertTrue($file->delete(true));
		
		$file = $this->fileSource->createFile('',sys_get_temp_dir());
		$this->setExpectedException('common_Exception');
	    $fileContent = $file->getFileContent();
	   

	    $this->assertTrue($file->delete());
	}

}
	