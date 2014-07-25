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

class VersioningEnabledTestCase extends UnitTestCase {
    
	// used to recover correct repo
	private $repositoryPath = null;
	
	private $repositoryUrl = null;
	private $repositoryType = null;
	private $repositoryLogin = null;
	private $repositoryPassword = null;
	private $envName = 'VERSIONING_TEST_CASE_ENV';
    private $envDeep = 2;
    private $envNbFiles = 12;
    
	public function __construct()
	{
		parent::__construct();
		// test repo path
		$this->repositoryPath = GENERIS_BASE_PATH
			.DIRECTORY_SEPARATOR.'data'
			.DIRECTORY_SEPARATOR.'versioning'
			.DIRECTORY_SEPARATOR.'DEFAULT'.DIRECTORY_SEPARATOR;
	}
	
    public function setUp()
    {
        GenerisTestRunner::initTest();
	    $repo = $this->getDefaultRepository();
	    $props = $repo->getPropertiesValues(array(
	    	PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL,
	    	PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN,
	    	PROPERTY_GENERIS_VERSIONEDREPOSITORY_PASSWORD,
	    	PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE,
	    	RDFS_LABEL,
	    	RDFS_COMMENT
	    ));
	    
	    $type = current($props[PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE]);
	    $this->repositoryUrl 		= current($props[PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL]);
		$this->repositoryLogin		= current($props[PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN]);
		$this->repositoryPassword	= current($props[PROPERTY_GENERIS_VERSIONEDREPOSITORY_PASSWORD]);
		$this->repositoryType		= $type->getUri();
	}
	
	/* --------------
	 * UNIT TEST CASE TOOLS
	 -------------- */

	// Get the default repository of the TAO instance
	protected function getDefaultRepository ()
	{
		$versioningRepositoryClass = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDREPOSITORY);
		$repositories = $versioningRepositoryClass->getInstances();
		$repository = null;
        
		common_Logger::i('Search for '.$this->repositoryPath);
        foreach($repositories as $r){
            if((string) $r->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH)) == $this->repositoryPath){
                $repository = new core_kernel_versioning_Repository($r->getUri());
                break;
            }
        }
        if(is_null($repository)){
            throw new common_exception_Error('Repository not found for '.__CLASS__);
        }
		
		return $repository;
	}
	
	// Create repository by using generis API
	protected function createRepository()
	{
		return core_kernel_fileSystem_FileSystemFactory::createFileSystem(
			new core_kernel_classes_Resource($this->repositoryType),
			$this->repositoryUrl,
			$this->repositoryLogin,
			$this->repositoryPassword,
			$this->repositoryPath,
			'test_repo'
		);
	}
	
	
	// Create version file sample by creating triples
	protected function createVersionedFile_byTriple()
	{
		$clazz = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
	    $instance = $clazz->createInstance('myVersionedFile');
	    
	    // Add version number
	    $versionedFileVersionProp = new core_kernel_classes_Property(PROPERTY_VERSIONEDFILE_VERSION);
	    $instance->setPropertyValue($versionedFileVersionProp, '1');
	    
	    // Add filename
	    $versionedFilenameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    $instance->setPropertyValue($versionedFilenameProp, 'myFile.txt');
	    
	    // Add repository
	    $versionedFileRepositoryProp = new core_kernel_classes_Property(PROPERTY_FILE_FILESYSTEM);
	    $instance->setPropertyValue($versionedFileRepositoryProp, $this->getDefaultRepository());
	    
	    $instance = new core_kernel_versioning_File($instance->getUri());
	    
	    return $instance;
	}
	
    // Create env folder with some folders and files
    protected function createEnvTest($rootPath=null, $dirName=null, $deep=null)
    {
        $rootPath = !is_null($rootPath) ? $rootPath : $this->getDefaultRepository()->getPath();
        $deep =     !is_null($deep)     ? $deep     : $this->envDeep;
        $dirName =  !is_null($dirName)  ? $dirName  : $this->envName;
        $dirPath = $rootPath.'/'.$dirName;

        //create the folder
        $relativePath = substr($dirPath, strlen($this->getDefaultRepository()->getPath()));
        $instance = $this->getDefaultRepository()->createFile('', $relativePath);
        //if is already versioned, delete the path
        if($instance->isVersioned()){
            $instance->delete();
            $instance = $this->getDefaultRepository()->createFile('', $relativePath);
        }
        
        //create the dir
        if(file_exists($dirPath)){
            helpers_File::remove($dirPath);
        }
        mkdir($dirPath);
        
        $this->assertTrue(is_dir($dirPath));
        for($i=0;$i<$this->envNbFiles;$i++){
            $tempnam = tempnam($dirPath, '');
            $this->assertTrue(is_file($tempnam));
        }
        
        //add & commit the directory
        $this->assertTrue($instance->add(true));
        $this->assertTrue($instance->commit("", true));
        
        if($deep > 0){
            $this->createEnvTest($dirPath, 'DIR_'.$deep, $deep-1);
        }
        
        return $instance;
    }
    
	/* --------------
	 * UNIT TEST CASE - REPOSITORY
	 -------------- */

	public function testModel()
	{	
		$this->assertTrue(defined('CLASS_GENERIS_FILE'));
	}
	
	public function testRepositoryModel()
	{
		$repository = $this->getDefaultRepository();
		$this->assertIsA($repository, 'core_kernel_versioning_Repository');
		
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
        
	}
	
	public function testRepositoryCreate()
	{
		$repository = $this->createRepository();
		$this->assertIsA($repository, 'core_kernel_versioning_Repository');
		
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
		
		$this->assertTrue($repository->delete(true)); 
	}
	
	// Test the repository's type
	public function testRepositoryType()
	{
	    $repository = $this->getDefaultRepository();
	    $type = $repository->getVCSType();
	    $this->assertTrue($type->getUri(), $this->repositoryType);
	}

	public function testRepositoryAuthenticate()
	{
		$repository = $this->getDefaultRepository();
		
		 // @NOTE If a valid conexion has been established with a remote server during the session
		 //  => The access (login/pass) of this session can not be dropped
		$repository->editPropertyValues(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN), 'bad_login');
		//$this->assertFalse($repository->authenticate());
		$repository->editPropertyValues(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN), $this->repositoryLogin);
		//$this->assertTrue($repository->authenticate());
	}
	
	public function testRespositoryCheckout()
	{
        $repository = $this->getDefaultRepository();
		$path = $repository->getPath();
		$this->assertTrue($repository->checkout());
        $this->assertTrue(file_exists($path));
	}

	// --------------
	// UNIT TEST CASE - FILE
	// -------------- 

	public function testVersionedFileModel()
	{
		$versionedFile = $this->createVersionedFile_byTriple();
		$this->assertIsA($versionedFile, 'core_kernel_versioning_File');
		
	    $versionedFileVersionProp = new core_kernel_classes_Property(PROPERTY_VERSIONEDFILE_VERSION);
		$this->assertEqual((string)$versionedFile->getOnePropertyValue($versionedFileVersionProp), '1');
		
	    $versionedFileRepositoryProp = new core_kernel_classes_Property(PROPERTY_FILE_FILESYSTEM);
		$this->assertEqual($versionedFile->getOnePropertyValue($versionedFileRepositoryProp)->getUri(), $this->getDefaultRepository()->getUri());
		
		$this->assertTrue($versionedFile->delete());
	}

	// Test if a resource is a versioned file
	public function testIsVersionedFile()
	{
	    $instance = $this->createVersionedFile_byTriple();
	    $this->assertTrue(core_kernel_versioning_File::isVersionedFile($instance));
	    $instance->delete();
	}
	
	// Test versioned file factory
	public function testVersionedFileCreate()
	{
		$instance = $this->getDefaultRepository()->createFile(__CLASS__.'_'.__METHOD__.'.txt', '/');
	    $this->assertFalse($instance->isVersioned()); //the file is not yet versioned
	    $this->assertFalse($instance->fileExists()); //the file does not exist
	    $this->assertFalse(file_exists($instance->getAbsolutePath()));//the file does not exist in file system
	    $this->assertTrue($instance->delete(true));
	}
	
	public function _testCleanCreatedResources(){
		
		$repository1 = $this->getDefaultRepository();
		$instance = $repository1->createFile(__CLASS__.'_'.__METHOD__.'.txt', '/');
		$filePath = $instance->getAbsolutePath();
		$files = helpers_File::searchResourcesFromPath($filePath);
		var_dump($files);
		foreach($files as $file){
			$this->assertTrue($file->delete(true));
			if($file instanceof core_kernel_versioning_File){
				echo 'delete commited * ';
				$this->assertTrue($file->commit());
			}
		}
		
		var_dump(helpers_File::searchResourcesFromPath($filePath));
		$this->assertFalse(helpers_File::resourceExists($filePath));
		
//		exit;
	}
	
	/*
	// Test the versioned file proxy
	public function testVersioningProxy()
	{
        // @todo This test is dedicated to the subversion implementation, be carrefull!!!
         
	     		$instance = $this->getDefaultRepository()->createFile(__CLASS__.'_'.__METHOD__.'.txt', '/');
	    $implementationToDelegateTo = core_kernel_versioning_FileProxy::singleton()->getImplementationToDelegateTo($instance);
	    $this->assertTrue($implementationToDelegateTo instanceof core_kernel_versioning_subversion_File);
	    $instance->delete(true);
	}
	*/
    
	// Test versioned file function add
	public function testVersionedFileAdd()
	{
	    $instance = $this->getDefaultRepository()->createFile(__CLASS__.'_'.__METHOD__.'.txt', '/');
		
		$filePath = $instance->getAbsolutePath();
        //the file is not versioned
	    $this->assertFalse($instance->isVersioned());
        //the resource exists in the onthology
        $this->assertTrue(helpers_File::resourceExists($filePath));
        //set the content & create the file in the same move
	    $instance->setContent(__CLASS__.':'.__METHOD__.'()');
        //the file exists
	    $this->assertTrue($instance->fileExists());
        
        //add the file to the system
	    $this->assertTrue($instance->add());
        //the file is not considered as versioned by tao
	    $this->assertFalse($instance->isVersioned());
        
        //delete the file
	    $this->assertTrue($instance->delete(true));
        //the resource does not exist anymore in the onthology
        $this->assertFalse(helpers_File::resourceExists($filePath));
        //the file does not exist in file system
	    $this->assertFalse(file_exists($filePath));
	}
	
	// Test versioned file function commit
	public function testVersionedFileCommit()
	{
	    $instance = $this->getDefaultRepository()->createFile(__CLASS__.'_'.__METHOD__.'.txt', '/');
        $filePath = $instance->getAbsolutePath();
        
        //the file is not versioned
	    $this->assertFalse($instance->isVersioned());
        //the resource exists in the onthology
        $this->assertTrue(helpers_File::resourceExists($filePath));
        //set the content & create the file in the same move
	    $instance->setContent(__CLASS__.':'.__METHOD__.'()');
        //the file exists
	    $this->assertTrue($instance->fileExists());
        //add the file to the system
	    $this->assertTrue($instance->add());
        //the file is not considered as versioned by tao
	    $this->assertFalse($instance->isVersioned());
        
        //commit the file to the system
	    $this->assertTrue($instance->commit());
        //the file is considered as versioned by tao
	    $this->assertTrue($instance->isVersioned());
        
        //delete the file
	    $this->assertTrue($instance->delete(true));
        //the resource does not exist anymore in the onthology
        $this->assertFalse(helpers_File::resourceExists($filePath));
        //the file does not exist in file system
	    $this->assertFalse(file_exists($filePath));
	}
    
	// Test if the resource has local changes
	public function testHasLocalChanges()
	{
		
        $instance = $this->getDefaultRepository()->createFile(__CLASS__.'_'.__METHOD__.'.txt', '/');
        $filePath = $instance->getAbsolutePath();
        
        //the file is not versioned
	    $this->assertFalse($instance->isVersioned());
        //the resource exists in the onthology
        $this->assertTrue(helpers_File::resourceExists($filePath));
        //set the content & create the file in the same move
	    $instance->setContent(__CLASS__.':'.__METHOD__.'()');
        //the file exists
	    $this->assertTrue($instance->fileExists());
        //add the file to the system
	    $this->assertTrue($instance->add());
        //the file is not considered as versioned by tao
	    $this->assertFalse($instance->isVersioned());
        //commit the file to the system
	    $this->assertTrue($instance->commit());
        //the file is considered as versioned by tao
	    $this->assertTrue($instance->isVersioned());
        
        //Test the file has no local changes
	    $this->assertFalse($instance->hasLocalChanges());
        //set the content & create the file in the same move
        $this->assertEqual(VERSIONING_FILE_STATUS_NORMAL, $instance->getStatus());          // <--------- GET STATUS : NORMAL
	    $instance->setContent(__CLASS__.':'.__METHOD__.'() updated');
        //check the new content of the file
        $this->assertEqual(__CLASS__.':'.__METHOD__.'() updated', $instance->getFileContent());
        //check the file has local changes
        $this->assertEqual(VERSIONING_FILE_STATUS_MODIFIED, $instance->getStatus());        // <--------- GET STATUS : MODIFIED
	    $this->assertTrue($instance->hasLocalChanges());
        
        //delete the file
	    $this->assertTrue($instance->delete(true));
        //the resource does not exist anymore in the onthology
        $this->assertFalse(helpers_File::resourceExists($filePath));
        //the file does not exist in file system
	    $this->assertFalse(file_exists($filePath));
		
	}
	
	public function testIsVersioned()
	{
		
		$instance = $this->getDefaultRepository()->createFile(__CLASS__.'_'.__METHOD__.'.txt', '/');
        $filePath = $instance->getAbsolutePath();
        
        //the file is not versioned
	    $this->assertEqual($instance->getStatus(), VERSIONING_FILE_STATUS_UNVERSIONED); // <--------- GET STATUS : UNVERSIONED
	    $this->assertFalse($instance->isVersioned());                                   // <--------- IS VERSIONED
        //the resource exists in the onthology
        $this->assertTrue(helpers_File::resourceExists($filePath));
        //set the content & create the file in the same move
	    $instance->setContent(__CLASS__.':'.__METHOD__.'()');
        //the file exists
	    $this->assertTrue($instance->fileExists());
        //add the file to the system
	    $this->assertTrue($instance->add());
	    $this->assertEqual($instance->getStatus(), VERSIONING_FILE_STATUS_ADDED);       // <--------- GET STATUS : ADDED
        //the file is not considered as versioned by tao
	    $this->assertFalse($instance->isVersioned());                                   // <--------- IS VERSIONED
        //commit the file to the system
	    $this->assertTrue($instance->commit());
	    $this->assertEqual($instance->getStatus(), VERSIONING_FILE_STATUS_NORMAL);      // <--------- GET STATUS : NORMAL
        //the file is considered as versioned by tao
	    $this->assertTrue($instance->isVersioned());                                    // <--------- IS VERSIONED
        //delete the file
	    $this->assertTrue($instance->delete(true));
        //the resource does not exist anymore in the onthology
        $this->assertFalse(helpers_File::resourceExists($filePath));
        //the file does not exist in file system
	    $this->assertFalse(file_exists($filePath));
		
	}
	
	// Test versioned file function delete
	public function testVersionedFileDelete()
	{
		// The function is tested in other tests :
		// * Delete unversioned file
		// * Delete a file which has been added to the repo
		// * Delete a file which has been commited
	}
	
	// Test versioned file function update
	public function testVersionedFileUpdate()
	{
		$repository1 = $this->getDefaultRepository();
		$instance = $repository1->getDefaultRepository()->createFile(__CLASS__.'_'.__METHOD__.'.txt', '/');
		
	    $originalFileContent = __CLASS__.':'.__METHOD__.'()';
        $instance->setContent($originalFileContent);
	    $this->assertTrue($instance->add());
	    $this->assertTrue($instance->commit());
        $this->assertTrue($instance->isVersioned());
	    
	    // Update the file from another repository
	    $repository2 = core_kernel_fileSystem_FileSystemFactory::createFileSystem(
			new core_kernel_classes_Resource($this->repositoryType),
			$this->repositoryUrl,
			$this->repositoryLogin,
			$this->repositoryPassword,
			GENERIS_FILES_PATH.'/versioning/TMP_TEST_CASE_REPOSITORY',
			'TMP Repository'
		);
		$this->assertTrue($repository2->checkout());
		
		$file = $repository2->createFile(__CLASS__.'_'.__METHOD__.'.txt', '/');
		$this->assertTrue($file->setContent($originalFileContent.' updated'));
		$this->assertTrue($file->commit());
		$this->assertTrue($repository2->delete(true));
		$this->assertTrue(helpers_file::remove(GENERIS_FILES_PATH.'/versioning/TMP_TEST_CASE_REPOSITORY'));
	    
		// Test the file has been updated in the first repository
		$this->assertTrue($instance->update());
		$this->assertEqual($instance->getFileContent(), $originalFileContent.' updated');
		
        $filePath = $instance->getAbsolutePath();
        
        $this->assertTrue(helpers_File::resourceExists($filePath));
	    $this->assertTrue($instance->delete(true));
        $this->assertFalse(helpers_File::resourceExists($filePath));
	}
    
	//test the versioning function revert without parameter (revert local change)
	public function testRevert()
	{
		$instance = $this->getDefaultRepository()->createFile(__CLASS__.'_'.__METHOD__.'.txt', '/');
        $filePath = $instance->getAbsolutePath();
        
        //the file is not versioned
	    $this->assertEqual($instance->getStatus(), VERSIONING_FILE_STATUS_UNVERSIONED); // <--------- GET STATUS : UNVERSIONED
	    $this->assertFalse($instance->isVersioned());                                   // <--------- IS VERSIONED
        //the resource exists in the onthology
        $this->assertTrue(helpers_File::resourceExists($filePath));
        //set the content & create the file in the same move
		$originalFileContent = __CLASS__.':'.__METHOD__.'()';
	    $instance->setContent($originalFileContent);
        //test file content
        $this->assertEqual($instance->getFileContent(), $originalFileContent);
        //the file exists
	    $this->assertTrue($instance->fileExists());
        //add the file to the system
	    $this->assertTrue($instance->add());
        //the file is not considered as versioned by tao
	    $this->assertFalse($instance->isVersioned());                                   // <--------- IS VERSIONED
        //commit the file to the system
	    $this->assertTrue($instance->commit());
        //the file is considered as versioned by tao
	    $this->assertTrue($instance->isVersioned());                                    // <--------- IS VERSIONED
        
        $instance->setContent($instance->getFileContent().' updated');
		$this->assertEqual($instance->getFileContent(), $originalFileContent.' updated');
	    $this->assertTrue($instance->revert());
		$this->assertEqual($instance->getFileContent(), $originalFileContent);
        
        //delete the file
	    $this->assertTrue($instance->delete(true));
        //the resource does not exist anymore in the onthology
        $this->assertFalse(helpers_File::resourceExists($filePath));
        //the file does not exist in file system
	    $this->assertFalse(file_exists($filePath));
	}

	public function testHistory()
	{
		$instance = $this->getDefaultRepository()->createFile(__CLASS__.'_'.__METHOD__.'.txt', '/');
        $commonContent = __CLASS__.':'.__METHOD__.'()';
        
        //set the content & create the file in the same move
	    $instance->setContent($commonContent);
	    $this->assertTrue($instance->add());
	    $this->assertTrue($instance->commit('test case : testHistory : commit 1'));
        //add a version
	    $instance->setContent($commonContent.' update 1');
	    $this->assertTrue($instance->commit('test case : testHistory : commit 2'));
	    //add a version
	    $instance->setContent($commonContent.' update 2');
	    $this->assertTrue($instance->commit('test case : testHistory : commit 3'));
	    //get the history
	    $history = $instance->getHistory();
	    //check the history
	    $this->assertEqual($history[0]['msg'], 'test case : testHistory : commit 3');
	    $this->assertEqual($history[1]['msg'], 'test case : testHistory : commit 2');
	    $this->assertEqual($history[2]['msg'], 'test case : testHistory : commit 1');
	    
	    $this->assertTrue($instance->delete(true));
	}

	public function testVersion()
	{
		$instance = $this->getDefaultRepository()->createFile(__CLASS__.'_'.__METHOD__.'.txt', '/');
        $commonContent = __CLASS__.':'.__METHOD__.'()';
        
        //set the content & create the file in the same move
	    $instance->setContent($commonContent);
	    $this->assertTrue($instance->add());
	    $this->assertTrue($instance->commit('test case : testVersion : commit 1'));
        //get the version number of the file
	    $this->assertEqual($instance->getVersion(), 1);
	    
	    $instance->setContent($commonContent.' update 1');
	    $this->assertTrue($instance->commit('test case : testVersion : commit 2'));
	    $this->assertEqual($instance->getVersion(), 2);
	    
	    $this->assertTrue($instance->delete(true));
	}
    
	public function testRevertTo()
	{
		$instance = $this->getDefaultRepository()->createFile(__CLASS__.'_'.__METHOD__.'.txt', '/');
		$commonContent = __CLASS__.':'.__METHOD__.'()';
        
	    $this->assertTrue($instance->setContent($commonContent));
	    $this->assertTrue($instance->add());
	    $this->assertTrue($instance->commit('test case : testRevertTo : commit 1'));
	    $this->assertTrue($instance->isVersioned());
		$this->assertEqual($instance->getFileContent(), $commonContent);
	    $this->assertEqual($instance->getVersion(), 1);
		
	    $this->assertTrue($instance->setContent($commonContent.' update 1'));
		$this->assertTrue($this->assertEqual($instance->getFileContent(), $commonContent.' update 1'));
	    $this->assertTrue($instance->commit('test case : testRevertTo : commit 2'));
	    $this->assertEqual($instance->getVersion(), 2);
	    
	    $this->assertTrue($instance->setContent($commonContent.' update 2'));
		$this->assertTrue($this->assertEqual($instance->getFileContent(), $commonContent.' update 2'));
	    $this->assertTrue($instance->commit('test case : testRevertTo : commit 3'));
	    $this->assertEqual($instance->getVersion(), 3);
	    
        //get the versioned file history
	    $history = $instance->getHistory();
	    $this->assertNotNull($history);
        
	    // Revert to first revision
	    $this->assertTrue($instance->revert(2));
	    $this->assertTrue($instance->fileExists());
	    $this->assertTrue($instance->isVersioned());
		$this->assertEqual($instance->getFileContent(), $commonContent.' update 1');
	    $this->assertEqual($instance->getVersion(), 4);
	    
		// Revert to second revision
	    $this->assertTrue($instance->revert(3));
	    $this->assertTrue($instance->fileExists());
	    $this->assertTrue($instance->isVersioned());
		$this->assertEqual($instance->getFileContent(), $commonContent.' update 2');
	    $this->assertEqual($instance->getVersion(), 5);
		
		$this->assertTrue($instance->delete(true));
	}

//	// Delete the test repository
//	public function testDeleteVersionedRepository()
//	{
//		//$this->getDefaultRepository()->delete();
//	}

    //test file conflict
    public function testVersionedFileConflict()
    {
        $repository1 = $this->getDefaultRepository();
		$instance = $repository1->createFile(__CLASS__.'_'.__METHOD__.'.txt', '/');
	    $originalFileContent = __CLASS__.':'.__METHOD__.'()';
        $instance->setContent($originalFileContent.' mine 1');
	    $this->assertTrue($instance->add());
	    $this->assertTrue($instance->commit());
        $this->assertTrue($instance->isVersioned());
	    
	    // Update the file from another repository
	    $repository2 = core_kernel_fileSystem_FileSystemFactory::createFileSystem(
			new core_kernel_classes_Resource(PROPERTY_GENERIS_VCS_TYPE_SUBVERSION),
			$this->repositoryUrl,
			$this->repositoryLogin,
			$this->repositoryPassword,
			GENERIS_FILES_PATH.'/versioning/TMP_TEST_CASE_REPOSITORY',
			'TMP Repository'
		);
		$this->assertTrue($repository2->checkout());
		
		$repository2Instance = $repository2->createFile(__CLASS__.'_'.__METHOD__.'.txt', '/');
		$otherUpdatedFileContent = $originalFileContent.' other 1';
		$this->assertTrue($repository2Instance->setContent($otherUpdatedFileContent));
        $this->assertTrue($repository2Instance->getFileContent(), $otherUpdatedFileContent);
		$this->assertTrue($repository2Instance->commit());
	    
		//Test the file has been updated in the first repository
        $this->assertEqual($instance->getStatus(), VERSIONING_FILE_STATUS_REMOTELY_MODIFIED);           // <--------- GET STATUS : REMOTELY MODIFIED
        
        //USE MINE VERSION
        //Write a change before update to make a conflict
        $instance->setContent($originalFileContent.' mine 2');
		$this->assertTrue($instance->update());
        $this->assertEqual($instance->getStatus(), VERSIONING_FILE_STATUS_CONFLICTED);                  // <--------- GET STATUS : CONFLICTED
        try{
            $instance->commit();
            //The following code should not be executed
            $this->fail('commit should fail because the versionned file should be in conflict state');
        }
        catch(core_kernel_versioning_exception_FileRemainsInConflictException $e){
            $this->assertTrue(true);
            //resolve the conflict by using mine version of the file
            $this->assertTrue($instance->resolve(VERSIONING_FILE_VERSION_MINE));
			$this->assertTrue($instance->commit());
            $this->assertEqual($instance->getStatus(), VERSIONING_FILE_STATUS_NORMAL);                  // <--------- GET STATUS : NORMAL
            $this->assertEqual($instance->getFileContent(), $originalFileContent.' mine 2');
        }
		
        //USE THEIRS VERSION
		$this->assertTrue($repository2Instance->update());
		$this->assertTrue($repository2Instance->setContent($originalFileContent.' remote update 2'));
		try{
            $this->assertTrue($repository2Instance->commit());
        }catch(Exception $e){
            var_dump($e);
        }
        $instance->setContent($originalFileContent.' update 3');
		$this->assertTrue($instance->update());
        $this->assertEqual($instance->getStatus(), VERSIONING_FILE_STATUS_CONFLICTED);                  // <--------- GET STATUS : CONFLICTED
        try{
            $instance->commit();
            //The following code should not be executed
            $this->assertFalse(true);
        }
        catch(core_kernel_versioning_exception_FileRemainsInConflictException $e){
            $this->assertTrue(true);
            //resolve the conflict by using the incoming version of the file
            $this->assertTrue($instance->resolve(VERSIONING_FILE_VERSION_THEIRS));
			try{
				$this->assertTrue($instance->commit());
			}catch(core_kernel_versioning_exception_FileRemainsInConflictException $e){
				$this->fail('conflict not correctly solved');
			}
            $this->assertEqual($instance->getStatus(), VERSIONING_FILE_STATUS_NORMAL);                  // <--------- GET STATUS : NORMAL
            $this->assertEqual($instance->getFileContent(), $originalFileContent.' remote update 2');
        }
        
        //Delete the repository 2 resources
		$this->assertTrue($repository2Instance->update());
		$this->assertTrue($repository2Instance->delete());
		$this->assertTrue($repository2->delete(true));
		helpers_File::remove(GENERIS_FILES_PATH.'/versioning/TMP_TEST_CASE_REPOSITORY');
        
        //Clean
        $filePath = $instance->getAbsolutePath();
        $this->assertTrue(helpers_File::resourceExists($filePath));
	    $this->assertTrue($instance->update());
	    $this->assertTrue($instance->delete(true));
		
    }
    
    ///////////////////////////////////////////////////////////////////////////
    //  MANAGE FOLDER WITH THE VERSIONING API
    ///////////////////////////////////////////////////////////////////////////
  
    //Test list content
    public function testListContentRepository()
    {
        //create the env test
        $rootFile = $this->createEnvTest();
        $filePathName = '';
        //list folder content
        $repository = $this->getDefaultRepository();
//        //file path
//	    //file name
//	    $versionedFilenameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
//	    $fileName= (string)$rootFile->getOnePropertyValue($versionedFilenameProp);
//        //build file path
//        $filePathName = $filePath.$fileName;
        //list folder content
        $list = $repository->listContent($rootFile->getAbsolutePath());
        //Check the number of listed files
        //$this->assertEqual(count($list), $this->envDeep*$this->envNbFiles+$this->envDeep);
        $this->assertEqual(count($list), (1*$this->envNbFiles +1));
        //remove the env test
        $rootfilePath = $rootFile->getAbsolutePath();
        $this->assertTrue($rootFile->delete());
    }

    ///////////////////////////////////////////////////////////////////////////
    //  REPOSITORY
    ///////////////////////////////////////////////////////////////////////////
    
    //Test the import function on the repository
	public function testRespositoryImport()
	{
		$repository = $this->getDefaultRepository();
        $tmpFolder = sys_get_temp_dir().'/TAO_TEST_CASE_TEST_RESPOSITORY_IMPORT';
        $importedFolder = null;
        //create tmp folder with some folders & files
        if(file_exists($tmpFolder)){
            $this->assertTrue(helpers_File::remove($tmpFolder));
            $this->assertFalse(file_exists($tmpFolder));
        }
        else {
            $this->assertTrue(mkdir($tmpFolder));
        }
        $this->assertTrue(touch($tmpFolder.'/file_test_empty'));

        //import the tmp folder in the TAO repository & save the resource
        $importedFolder = $repository->import($tmpFolder, $repository->getUrl().'/TAO_TEST_CASE_TEST_RESPOSITORY_IMPORT', 'Import test case message', array('saveResource'=>true));
        $this->assertNotNull($importedFolder);
        
        $path = $importedFolder->getAbsolutePath().'/';
        $this->assertNotNull($importedFolder);
        $this->assertTrue($importedFolder instanceof core_kernel_versioning_File);
        //check the resource exists
        $searchedFile = helpers_File::getResource($path);
        $this->assertNotNull($searchedFile);
        $this->assertEqual($importedFolder->getUri(), $searchedFile->getUri());
        //the file exists
        $this->assertTrue(file_exists($path));
        //delete the imported folder
        $this->assertTrue($importedFolder->delete(true));
        //check the resource does not exist
        $this->assertFalse(helpers_File::resourceExists($path));
        $this->assertFalse(file_exists($path));

        //import the tmp folder in the TAO repository & do not save the resource
        $importedFolder = $repository->import($tmpFolder, $repository->getUrl().'/TAO_TEST_CASE_TEST_RESPOSITORY_IMPORT', 'Import test case message');
        $this->assertNull($importedFolder);
        //check the resource has not been saved
        $this->assertFalse(helpers_File::resourceExists($path));
        //the imported folder exists on the filesystem
        $this->assertTrue(file_exists($path));
        
        //create it
        $importedFolder = $repository->getDefaultRepository()->createFile('', '/TAO_TEST_CASE_TEST_RESPOSITORY_IMPORT');
        
        $this->assertTrue($importedFolder instanceof core_kernel_versioning_File);
        //check the resource exists
        $searchedFile = helpers_File::getResource($path);
        $this->assertNotNull($searchedFile);
        $this->assertEqual($importedFolder->getUri(), $searchedFile->getUri());
        //delete the imported folder
        $this->assertTrue($importedFolder->delete(true));
        //check the resource does not exist
        $this->assertFalse(helpers_File::resourceExists($path));
        $this->assertFalse(file_exists($path));
        
        //delete the imported folder
        $this->assertFalse(helpers_File::resourceExists($path));

		//delete the tmp folder
        $this->assertTrue(helpers_File::remove($tmpFolder));
	}

    //test the export function on the repository
    public function testRepositoryExport()
    {
		return;
        $rootFile = $this->createEnvTest();
        $repository = $this->getDefaultRepository();
        $list = $repository->listContent($rootFile->getAbsolutePath());
        $this->assertEqual(count($list), (1*$this->envNbFiles)+($this->envDeep>0?1:0));
        $exportPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'testRepositoryExport/';
        $this->assertTrue($repository->export($rootFile->getAbsolutePath(), $exportPath));
        
        $listPath = $exportPath;
        for($i=0;$i<$this->envDeep;$i++){
            $listExport = tao_helpers_File::scandir($listPath);
            $this->assertEqual(count($listExport), (1*$this->envNbFiles)+($this->envDeep>0?1:0));
            $listExportDir = tao_helpers_File::scandir($listPath, array('only'=>tao_helpers_File::$DIR));
            $this->assertEqual(count($listExportDir), 1);
            $listExportFile = tao_helpers_File::scandir($listPath, array('only'=>tao_helpers_File::$FILE));
            $this->assertEqual(count($listExportFile), $this->envNbFiles);
            foreach($listExportDir as $file){
                $listPath = $file;
            }
        }
        
        $this->assertTrue(helpers_File::remove($exportPath));
        $this->assertTrue($rootFile->delete(true));
    }
	
}
