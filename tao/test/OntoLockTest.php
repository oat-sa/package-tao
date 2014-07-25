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
 * Copyright (c) 2013 Open Assessment Technologies S.A.
 */

require_once dirname(__FILE__) . '/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Test the lock implemented as a property in the ontology
 * 
 * @author plichart
 * @package tao
 
 */
class OntoLockTest extends TaoPhpUnitTestRunner {
	
    protected $tempResource = null;
    protected $lockService = null;
    
    public function setUp() {
        $resourceClass = new core_kernel_classes_Class(RDFS_RESOURCE);
        $this->tempResource = $resourceClass->createInstance('MyTest');
        $this->lockService = tao_models_classes_lock_OntoLock::singleton();
        $this->lockService->setEnabled(true);
        //$this->owner = tao_models_classes_UserService::singleton()->getCurrentUser();
        $this->owner = new core_kernel_classes_Resource('#virtualOwner');
    }
    public function tearDown() {

        $this->tempResource->delete();
        $this->lockService->restoreEnabled();
    }
	public function testSetLock(){
        $this->assertFalse($this->lockService->isLocked($this->tempResource));
        $this->lockService->setLock($this->tempResource, $this->owner);
        $this->assertTrue($this->lockService->isLocked($this->tempResource));

        //setting a lock while it is locked should return an exception
        try {
            $this->lockService->setLock($this->tempResource, $this->owner);
            $this->assertTrue(false);// how to test exceptions aboev correctly ?

        } catch (Exception $e) {
            if (get_class($e)=='common_Exception') {
                $this->assertTrue(True);
            }
        }


	}

    public function testReleaseLock(){
        $this->assertFalse($this->lockService->isLocked($this->tempResource));
        $this->lockService->setLock($this->tempResource, $this->owner);
        $this->assertTrue($this->lockService->isLocked($this->tempResource));
        $this->lockService->releaseLock($this->tempResource, $this->owner);
        $this->assertFalse($this->lockService->isLocked($this->tempResource));

        //$this->lockService->setLock(new core_kernel_classes_Resource('http://tao-dev/taodev.rdf#i1382009411219412'), $this->owner);

    }


	
}
?>