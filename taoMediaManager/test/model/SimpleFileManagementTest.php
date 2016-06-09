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

use oat\taoMediaManager\model\fileManagement\SimpleFileManagement;


class SimpleFileManagementTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SimpleFileManagement
     */
    private $fileManagement = null;

    public function setUp()
    {
        $this->fileManagement = new SimpleFileManagement();
    }

    public function testStoreFileValid()
    {
        $tmpDir = \tao_helpers_File::createTempDir();
        $storageDir = $tmpDir . 'media/';
        mkdir($storageDir);

        //force baseDir
        $ref = new \ReflectionProperty('oat\taoMediaManager\model\fileManagement\SimpleFileManagement', 'baseDir');
        $ref->setAccessible(true);
        $ref->setValue($this->fileManagement, $storageDir);
        $ref->setAccessible(false);

        $fileTmp = dirname(__DIR__) . '/sample/Brazil.png';

        $this->assertFileNotExists($storageDir . 'Brazil.png', 'The file is already stored');
        $link = $this->fileManagement->storeFile($fileTmp, 'brazil.png');

        // test the return link
        $this->assertInternalType('string', $link, 'The method return should be a string');
        $this->assertEquals('brazil.png', $link, 'The link is wrong');
        $this->assertFileExists($storageDir . 'brazil.png', 'The file has not been stored');

        return array($storageDir, $link);
    }

    /**
     * @depends testStoreFileValid
     */
    public function testRetrieveFile($array)
    {

        $storage = implode('', $array);

        //force baseDir
        $ref = new \ReflectionProperty('oat\taoMediaManager\model\fileManagement\SimpleFileManagement', 'baseDir');
        $ref->setAccessible(true);
        $ref->setValue($this->fileManagement, $array[0]);
        $ref->setAccessible(false);

        $file = $this->fileManagement->retrieveFile($array[1]);

        // test the return link
        $this->assertInternalType('string', $file, 'The method return should be a string');
        $this->assertEquals($storage, $file, 'The return file is wrong');
        $this->assertFileExists($file, 'The file is not stored');

        return $array;
    }

    /**
     * @depends testRetrieveFile
     */
    public function testDeleteFile($array)
    {

        //force baseDir
        $ref = new \ReflectionProperty('oat\taoMediaManager\model\fileManagement\SimpleFileManagement', 'baseDir');
        $ref->setAccessible(true);
        $ref->setValue($this->fileManagement, $array[0]);
        $ref->setAccessible(false);

        $remove = $this->fileManagement->deleteFile($array[1]);

        // test the return link
        $this->assertInternalType('boolean', $remove, 'The method return should be a string');
        $this->assertEquals(true, $remove, 'impossible to remove file');
        $this->assertFileNotExists(implode('', $array), 'The file is still here');
    }

    public function testDeleteFileFail()
    {

        $link = 'notadir/notafile.png';

        $remove = $this->fileManagement->deleteFile($link);

        // test the return link
        $this->assertInternalType('boolean', $remove, 'The method return should be a string');
        $this->assertFalse($remove, 'File was not removed');
    }


}
 