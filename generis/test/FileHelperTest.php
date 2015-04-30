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


class FileHelperTest extends GenerisPhpUnitTestRunner 
{
    
    protected function setUp()
    {
        GenerisPhpUnitTestRunner::initTest();
    }
    
	public function testRemoveFile()
	{
		$basedir	= $this->mkdir(sys_get_temp_dir());
		$this->assertTrue(is_dir($basedir));
		$file01		= tempnam($basedir, 'testdir');
		$file02		= tempnam($basedir, 'testdir');
		
		$subDir1	= $this->mkdir($basedir);
		
		$subDir2	= $this->mkdir($basedir);
		$file21		= tempnam($subDir2, 'testdir');
		$subDir21	= $this->mkdir($subDir2);
		$file211	= tempnam($subDir21, 'testdir');
		$subDir22	= $this->mkdir($subDir2);
		$this->assertTrue(helpers_File::remove($basedir));
		$this->assertFalse(is_dir($basedir));
	}
	
	private function mkdir($basePath) 
	{
		$file = tempnam($basePath, 'dir');
		$this->assertTrue(unlink($file));
		$this->assertTrue(mkdir($file));
		return $file;
	}
	
	
	/**
	 * @dataProvider scandirDataProvider
	 * 
	 * @param string $toScan Directory path to be scanned
	 * @param array $expectedResult The expected return value of helpers_File::scanDir().
	 * @param boolean $recursive Value of the 'recursive' option.
	 * @param boolean $absolute Value of the 'absolute' option.
	 */
	public function testScandir($toScan, $expectedResult, $recursive = false, $absolute = false) {
        $result = helpers_File::scanDir($toScan, array('recursive' => $recursive, 'absolute' => $absolute , 'only' => helpers_File::$FILE));
        $this->assertEquals(count($expectedResult), count($result));
        // The order might vary depending on the file system implementation...
        foreach ($expectedResult as $expected) {
            $this->assertTrue(in_array($expected, $result));
        }
    }
    
    public function scandirDataProvider() 
    {

        $ds = DIRECTORY_SEPARATOR;

        return array(
            array(dirname(__FILE__) . $ds . 'rules', array('ExpressionFactoryTest.php', 'ExpressionTest.php', 'OperationFactoryTest.php', 'OperationTest.php', 'TermFactoryTest.php', 'TermTest.php')),
        );
    }
    
    /**
     * @dataProvider containsFileTypeProvider
     * 
     * @param string $toScan The directory to be scanned.
     * @param string|array $types The types to check for e.g. 'php', 'js', ...
     * @param boolean $recursive Whether or not to check recursively in the directory.
     * @param boolean $expectedResult The expected result of the containsFileType helper method.
     */
    public function testContainsFileType($toScan, $types, $recursive, $expectedResult)
    {
        $this->assertSame($expectedResult, helpers_File::containsFileType($toScan, $types, $recursive));
    }
    
    public function containsFileTypeProvider()
    {
        return array(
            array(dirname(__FILE__), 'php', true, true),
            array(dirname(__FILE__), 'php', false, true),
            array(dirname(__FILE__), 'js', true, false),
            array(dirname(__FILE__), 'js', false, false),
            array(dirname(__FILE__) . '/..', 'rdf', false, false),
            array(dirname(__FILE__) . '/..', 'rdf', true, true),
                        
            // edge cases.
            // - unexisting directory.
            array(dirname(__FILE__) . '/foo', 'php', false, false),
            array(dirname(__FILE__) . '/foo', 'php', true, false),
            // - scan a file.
            array(dirname(__FILE__) . '/../index.php', 'php', false, false),
            array(dirname(__FILE__) . '/../index.php', 'php', true, false),
        );
    }
}
	