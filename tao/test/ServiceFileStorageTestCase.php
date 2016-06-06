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

//TODO simpletest testcase that need to be migrate to phpunit

include_once dirname(__FILE__) . '/../includes/raw_start.php';

class ServiceFileStorageTestCase extends UnitTestCase {

	/**
	 * tests initialization
	 */
	public function setUp(){
		TaoTestRunner::initTest();
	}
	
	public function testFileStorage() {
	    $storage = tao_models_classes_service_FileStorage::singleton();
	    
	    $publicFolder1 = $storage->spawnDirectory(true);
	    $publicFolder2 = $storage->spawnDirectory(true);
	    $privateFolder = $storage->spawnDirectory(false);
	    
	    $this->assertTrue($publicFolder1->isPublic());
	    $this->assertTrue(file_exists($publicFolder1->getPath()));
	    
	    $this->assertTrue($publicFolder1->isPublic());
	    $this->assertTrue(file_exists($publicFolder2->getPath()));
	    
	    $this->assertNotEqual($publicFolder1->getPath(), $publicFolder2->getPath());
	    
	    $this->assertFalse($privateFolder->isPublic());
	    $this->assertTrue(file_exists($privateFolder->getPath()));
	     
    }
}

