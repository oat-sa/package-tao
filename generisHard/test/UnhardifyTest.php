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
require_once dirname(__FILE__) . '/../../generis/test/GenerisPhpUnitTestRunner.php';

use oat\generisHard\models\switcher\Switcher;
use oat\generisHard\models\proxy\ClassProxy;

class HardDbSubjectTestCase extends GenerisPhpUnitTestRunner {

	protected $targetSubjectClass = null;
	protected $targetSubjectSubClass = null;
	protected $dataIntegrity = array ();

	public function setUp(){
        GenerisPhpUnitTestRunner::initTest();
        $this->installExtension('generisHard');
        $this->createContextOfThetest();
	}
	
	protected function tearDown(){
		$this->clean();
	}

	private function countStatements (){
		return core_kernel_classes_DbWrapper::singleton()->getRowCount('statements');
	}
	
	public function createContextOfThetest(){
		// Top Class : TaoSubject
		$subjectClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		// Create a new subject class for the unit test
		$this->targetSubjectClass = $subjectClass->createSubClass ("Sub Subject Class (Unit Test)");

		// Add an instance to this subject class
		$subject1 = $this->targetSubjectClass->createInstance ("Sub Subject (Unit Test)");
		$this->assertEquals (count($this->targetSubjectClass->getInstances ()), 1);

		// Create a new subject sub class to the previous sub class
		$this->targetSubjectSubClass = $this->targetSubjectClass->createSubClass ("Sub Sub Subject Class (Unit Test)");
		// Add an instance to this sub subject class
		$subject2 = $this->targetSubjectSubClass->createInstance ("Sub Sub Subject (Unit Test)");
		$this->assertEquals (count($this->targetSubjectSubClass->getInstances ()), 1);

		$this->assertEquals (count($this->targetSubjectClass->getInstances ()), 1);
		// If get instances in the sub classes of the targetSubjectClass, we should get 2 instances
		$this->assertEquals (count($this->targetSubjectClass->getInstances (true)), 2);
		
		$this->dataIntegrity['statements0'] = $this->countStatements();
		$this->dataIntegrity['subSubjectClassCount0'] = $this->targetSubjectClass->countInstances();
		$this->dataIntegrity['subSubSubjectClassCount0'] = $this->targetSubjectSubClass->countInstances();
	}

	
	
	public function testHardifier () {
		
		$switcher = new Switcher();
		$switcher->hardify($this->targetSubjectClass, array(
			'topclass'		=> new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User'),
			'recursive'		=> true,
			'createForeigns'=> false,
			'rmSources'		=> true
		));
		unset ($switcher);
		
		$this->assertIsA(ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass), 'oat\generisHard\models\hardsql\Clazz');
		$this->assertIsA(ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectSubClass), 'oat\generisHard\models\hardsql\Clazz');
	}

	public function testUnhardifier () {
		
		$switcher = new Switcher();
		$switcher->unhardify($this->targetSubjectClass, array(
			'recursive'			=> true,
			'removeForeigns'	=> false
		));
		unset ($switcher);
	}
	
	public function testDataIntegrity (){
		$this->dataIntegrity['statements1'] = $this->countStatements();
		$this->dataIntegrity['subSubjectClassCount1'] = $this->targetSubjectClass->countInstances();
		$this->dataIntegrity['subSubSubjectClassCount1'] = $this->targetSubjectSubClass->countInstances();
		
		$this->assertEquals($this->dataIntegrity['statements0'], $this->dataIntegrity['statements1']);
		$this->assertEquals($this->dataIntegrity['subSubjectClassCount0'], $this->dataIntegrity['subSubjectClassCount1']);
		$this->assertEquals($this->dataIntegrity['subSubSubjectClassCount0'], $this->dataIntegrity['subSubSubjectClassCount1']);
		
		$this->assertFalse(ClassProxy::singleton()->isValidContext('hardsql', $this->targetSubjectClass));
		$this->assertTrue(ClassProxy::singleton()->isValidContext('smoothsql', $this->targetSubjectClass));
		$this->assertIsA(ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass), 'core_kernel_persistence_smoothsql_Class');
		$this->assertFalse(ClassProxy::singleton()->isValidContext('hardsql', $this->targetSubjectSubClass));
		$this->assertTrue(ClassProxy::singleton()->isValidContext('smoothsql', $this->targetSubjectSubClass));
		$this->assertIsA(ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectSubClass), 'core_kernel_persistence_smoothsql_Class');
	}
	
	public function clean (){
		// Remove the resources
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			$instance->delete ();
		}
		foreach ($this->targetSubjectSubClass->getInstances() as $instance){
			$instance->delete ();
		}
		
		$this->targetSubjectClass->delete(true);
		$this->targetSubjectSubClass->delete(true);
	}
	
}
