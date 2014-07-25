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

/*
 * Versioning Test Case has been wrote to test versioning features.
 * When versioning is enabled or not.
 */

require_once dirname(__FILE__) . '/../GenerisTestRunner.php';

class VersioningDisabledTestCase extends UnitTestCase {
    
	private $repositoryUrl = GENERIS_VERSIONED_REPOSITORY_URL;
	private $repositoryPath = GENERIS_VERSIONED_REPOSITORY_PATH;
	private $repositoryType = GENERIS_VERSIONED_REPOSITORY_TYPE;
	private $repositoryLogin = GENERIS_VERSIONED_REPOSITORY_LOGIN;
	private $repositoryPassword = GENERIS_VERSIONED_REPOSITORY_PASSWORD;
	private $repositoryLabel = GENERIS_VERSIONED_REPOSITORY_LABEL;
	
	public function __construct()
	{
		parent::__construct();
        if(GENERIS_VERSIONED_REPOSITORY_TYPE == ''){
            $this->repositoryType = 'http://tao.local#VersioningTypeBidon';
        }
	}
	
    public function setUp()
    {
        GenerisTestRunner::initTest();
	}
	
	/* --------------
	 * UNIT TEST CASE TOOLS
	 -------------- */
	
	// Create repository by using generis API
	public function createRepository()
	{
		return core_kernel_fileSystem_FileSystemFactory::createFileSystem(
			new core_kernel_classes_Resource($this->repositoryType),
			$this->repositoryUrl,
			$this->repositoryLogin,
			$this->repositoryPassword,
			$this->repositoryPath,
			$this->repositoryLabel
		);
	}
	
	/* --------------
	 * REPOSITORY
	 -------------- */
	
	// Create repository by using generis API
	public function testCreateRepository()
	{
		$repository = core_kernel_fileSystem_FileSystemFactory::createFileSystem(
			new core_kernel_classes_Resource($this->repositoryType),
			$this->repositoryUrl,
			$this->repositoryLogin,
			$this->repositoryPassword,
			$this->repositoryPath,
			$this->repositoryLabel
		);
		
		$VersioningRepositoryUrlProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL);
		$VersioningRepositoryPathProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH);
		$VersioningRepositoryTypeProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE);
		$VersioningRepositoryLoginProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN);
		$VersioningRepositoryPasswordProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PASSWORD);
		
		$this->assertEqual((string)$repository->getOnePropertyValue($VersioningRepositoryUrlProp), $this->repositoryUrl);
		$this->assertEqual((string)$repository->getOnePropertyValue($VersioningRepositoryPathProp), $this->repositoryPath);
		$this->assertEqual($repository->getOnePropertyValue($VersioningRepositoryTypeProp)->getUri(), $this->repositoryType);
		$this->assertEqual((string)$repository->getOnePropertyValue($VersioningRepositoryLoginProp), $this->repositoryLogin);
		$this->assertEqual((string)$repository->getOnePropertyValue($VersioningRepositoryPasswordProp), $this->repositoryPassword);
		
		$repository->delete();
	}
	
	public function testRespositoryCheckout()
	{
		$repository = core_kernel_fileSystem_FileSystemFactory::createFileSystem(
			new core_kernel_classes_Resource(PROPERTY_GENERIS_VCS_TYPE_SUBVERSION),
			$this->repositoryUrl,
			$this->repositoryLogin,
			$this->repositoryPassword,
			$this->repositoryPath,
			$this->repositoryLabel
		);
		
		try{
			$repository->checkout();
			$this->assertTrue(false);
		}
        catch(core_kernel_versioning_exception_VersioningDisabledException $e){
			$this->assertTrue(true);
		}
		
	    $this->assertTrue($repository->delete(true));
	}

	/* --------------
	 * FILE
	 -------------- */
	
	// Test versioned file factory
	public function testVersionedFileCreate()
	{
		$repository = $this->createRepository();
	    $instance = $repository->createFile('file_test_case.txt', '/');
        $this->assertTrue($instance->delete(true));
	    $this->assertTrue($repository->delete(true));
	}
	
	// Test versioned file function add
	public function testVersionedFileAdd()
	{
        $repository = $this->createRepository();
	    $instance = $repository->createFile('file_test_case.txt', '/');
	    $instance->setContent(__CLASS__.':'.__METHOD__.'()');
        
        //try to add the versioned file to the repository
        try{
            $this->assertFalse($instance->add());
            //the following code should not be executed
            $this->assertFalse(true);
            $this->assertFalse($instance->commit());
        }
        catch(core_kernel_versioning_exception_VersioningDisabledException $e){
            //expected behavior
            $this->assertTrue(true);
        }
        
        //the file should not be versioned
        try{
            $this->assertFalse($instance->isVersioned());
            //the following code should not be executed
            $this->assertFalse(true);
        }
        catch(core_kernel_versioning_exception_VersioningDisabledException $e){
            //expected behavior
            $this->assertTrue(true);
        }
        
        //delete the file and the tao resource
        $filePath = $instance->getAbsolutePath();
        //delete the versioned resource with GENERIS_VERSIONING_ENABLED constant set to true
        // => the resource will be deleted but the file will exist anymore
        $this->assertTrue($instance->delete(true));
        $this->assertFalse(helpers_File::resourceExists($filePath));
        $this->assertTrue(file_exists($filePath));
        //remove the file manually
        $this->assertTrue(unlink($filePath));
        
        //delete the repository
	    $repository->delete(true);
	}
	
	// Test versioned file function commit
	public function testVersionedFileCommit()
	{
		$repository = $this->createRepository();
	    $instance = $repository->createFile('file_test_case.txt', '/');
	    $instance->setContent(__CLASS__.':'.__METHOD__.'()');
        
        //try to add the versioned file to the repository
        try{
            //commit without add, the system should throw an exception anymore
            $this->assertFalse($instance->commit());
            //the following code should not be executed
            $this->assertFalse(true);
        }
        catch(core_kernel_versioning_exception_VersioningDisabledException $e){
            //expected behavior
            $this->assertTrue(true);
        }
        
        //the file should not be versioned
        try{
            $this->assertFalse($instance->isVersioned());
            //the following code should not be executed
            $this->assertFalse(true);
        }
        catch(core_kernel_versioning_exception_VersioningDisabledException $e){
            //expected behavior
            $this->assertTrue(true);
        }
        
        //delete the file and the tao resource
        $filePath = $instance->getAbsolutePath();
        //delete the versioned resource with GENERIS_VERSIONING_ENABLED constant set to true
        // => the resource will be deleted but the file will exist anymore
        $this->assertTrue($instance->delete(true));
        $this->assertFalse(helpers_File::resourceExists($filePath));
        $this->assertTrue(file_exists($filePath));
        //remove the file manually
        $this->assertTrue(unlink($filePath));
        
        //delete the repository
	    $repository->delete(true);
	}
	
	// Test versioned file test history
	public function testHistory()
	{
		$repository = $this->createRepository();
	    $instance = $repository->createFile('file_test_case.txt', '/');
	    $instance->setContent(__CLASS__.':'.__METHOD__.'()');
		
        //try to get the history of a versioned file
        try{
            $this->assertFalse($instance->getHistory());
            //the following code should not be executed
            $this->assertFalse(true);
        }
        catch(core_kernel_versioning_exception_VersioningDisabledException $e){
            //expected behavior
            $this->assertTrue(true);
        }
        
		//delete the file and the tao resource
        $filePath = $instance->getAbsolutePath();
        //delete the versioned resource with GENERIS_VERSIONING_ENABLED constant set to true
        // => the resource will be deleted but the file will exist anymore
        $this->assertTrue($instance->delete(true));
        $this->assertFalse(helpers_File::resourceExists($filePath));
        $this->assertTrue(file_exists($filePath));
        //remove the file manually
        $this->assertTrue(unlink($filePath));
        
        //delete the repository
	    $repository->delete(true);
	}
	
	// Test versioned file revert
	public function testRevertTo()
	{
		$repository = $this->createRepository();
	    $instance = $repository->createFile('file_test_case.txt', '/');
	    $instance->setContent(__CLASS__.':'.__METHOD__.'()');
		
        //try to get the history of a versioned file
        try{
            $this->assertFalse($instance->revert(0));
            //the following code should not be executed
            $this->assertFalse(true);
        }
        catch(core_kernel_versioning_exception_VersioningDisabledException $e){
            //expected behavior
            $this->assertTrue(true);
        }
        
		//delete the file and the tao resource
        $filePath = $instance->getAbsolutePath();
        //delete the versioned resource with GENERIS_VERSIONING_ENABLED constant set to true
        // => the resource will be deleted but the file will exist anymore
        $this->assertTrue($instance->delete(true));
        $this->assertFalse(helpers_File::resourceExists($filePath));
        $this->assertTrue(file_exists($filePath));
        //remove the file manually
        $this->assertTrue(unlink($filePath));
        
        //delete the repository
	    $repository->delete(true);
	}
}
