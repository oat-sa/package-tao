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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */
?>
<?php

require_once dirname(__FILE__) . '/../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Test Case aiming at testing the filemanager_helpers_FileUtils class.
 * 
 * @author Jerome Bogaerts, <taosupport@tudor.lu>
 * @package filemanager
 
 */
class FileUtilsTest extends TaoPhpUnitTestRunner {
    
    public function setUp()
    {		
        parent::setUp();
		TaoPhpUnitTestRunner::initTest();
	}
    
    public function tearDown() {
        parent::tearDown();
    }
    
    public function testGetFolderPath()
    {
    	// We first get the base path
    	$base = filemanager_helpers_FileUtils::getBasePath();
    	
    	$filePath = $base . 'Animals/puss-in-boots.png';
    	$folderPath = filemanager_helpers_FileUtils::getFolderPath($filePath);
    	$this->assertEquals($folderPath, '/Animals/');
    	
    	// It does not exist!
    	$filePath = $base . 'Animals/hippo-in-boots.png';
    	$folderPath = filemanager_helpers_FileUtils::getFolderPath($filePath);
    	$this->assertNull($folderPath);
    	
    	$base = 'C:\\wamp3\\www\\taotrunk\\filemanager\\views\\data\\';
    	$filePath = $base . 'Animals/chicken.png';
    	$folderPath = filemanager_helpers_FileUtils::getFolderPath($filePath, $base, false);
    	$this->assertEquals($folderPath, '/Animals/');
    	
    	// Test a linux like test.
    	$base = '/var/www/taoinstall/filemanager/views/data/';
    	$filePath = $base . 'Animals/puss-in-boots.png';
    	$folderPath = filemanager_helpers_FileUtils::getFolderPath($filePath, $base, false);
    	$this->assertEquals($folderPath, '/Animals/');
    	
    	$filePath = $base . 'puss-in-boots.png';
    	$folderPath = filemanager_helpers_FileUtils::getFolderPath($filePath, $base, false);
    	$this->assertEquals($folderPath, '/');
    }
}
?>