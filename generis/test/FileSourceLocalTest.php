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

class FileSourceLocalTest extends GenerisPhpUnitTestRunner {
    
    /**
     * @var core_kernel_versioning_Repository
     */
    private $repository = null;
    
    private $directory = null;
    
	public function __construct()
	{
		parent::__construct();
	}
	
    protected function setUp()
    {
	    GenerisPhpUnitTestRunner::initTest();
	    $this->directory = sys_get_temp_dir().DIRECTORY_SEPARATOR."testrepo".DIRECTORY_SEPARATOR;
	    mkdir($this->directory);
		$this->repository = core_kernel_fileSystem_FileSystemFactory::createFileSystem(
			new core_kernel_classes_Resource(INSTANCE_GENERIS_VCS_TYPE_LOCAL),
			'', '', '', $this->directory, 'UnitTestRepository', true
		);
        
    }
	
    protected function tearDown()
    {
        helpers_File::remove($this->directory);
        if($this->repository != null) {
            $directory = $this->repository->getPath();
     	    $this->repository->delete();
     	    parent::tearDown();
 	    }
 	    else {
            throw new common_Exception('Repository should never be null');
        }
	}

	protected function getTestRepository () {
		return $this->repository;
	}

    public function testRepository() {
    	$this->assertIsA($this->getTestRepository(), 'core_kernel_versioning_Repository');
    	$this->assertEquals($this->getTestRepository()->getPath(), $this->directory);
    }
	
}
