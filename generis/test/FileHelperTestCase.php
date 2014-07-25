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
?>
<?php

require_once dirname(__FILE__) . '/GenerisTestRunner.php';


class FileHelperTestCase extends UnitTestCase {
    
    public function setUp()
    {
        GenerisTestRunner::initTest();
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
	
	private function mkdir($basePath) {
		$file = tempnam($basePath, 'dir');
		$this->assertTrue(unlink($file));
		$this->assertTrue(mkdir($file));
		return $file;
	}
	
}
	