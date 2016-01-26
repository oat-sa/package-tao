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

/**
 * Test class for Class.
 * 
 * @author lionel.lecaque@tudor.lu
 * @package test
 */


class CreateInstanceTest extends GenerisPhpUnitTestRunner {
	protected $class;
	
	protected function setUp(){

        GenerisPhpUnitTestRunner::initTest();
	    $classres = core_kernel_classes_ResourceFactory::create(new core_kernel_classes_Class(RDFS_CLASS), 'TestClass');
	    $this->class = new core_kernel_classes_Class($classres->getUri());
	    $this->assertIsA($this->class, 'core_kernel_classes_Class');
	    $this->assertTrue($this->class->hasType(new core_kernel_classes_Class(RDFS_CLASS)));
	    common_Logger::i('using class '.$this->class->getUri().' for Create instance Tests');
	}
	
	public function testCreateInstance(){
		$class = $this->class;
		$instance = $class->createInstance('toto' , 'tata');
		$this->assertEquals($instance->getLabel(), 'toto', 'label should be equal to toto');
		$this->assertEquals($instance->getComment(), 'tata', 'comment should be equal to tata');
		$instance2 = $class->createInstance('toto' , 'tata');
		$this->assertNotSame($instance,$instance2, 'resources are identical');
		$instance->delete();
		$instance2->delete();
	}
	
	public function testCreateInstanceViaFactory(){
		$class = $this->class;
		$instance = core_kernel_classes_ResourceFactory::create($class, 'testlabel', 'testcomment');
		$this->assertTrue($instance->hasType($class));
		$this->assertEquals($instance->getLabel(), 'testlabel');
		$this->assertEquals($instance->getComment(), 'testcomment');
		$instance2 = core_kernel_classes_ResourceFactory::create($class);
		$this->assertTrue($instance2->hasType($class));
		$this->assertNotSame($instance,$instance2);
		$instance->delete();
		$instance2->delete();
	}
	
	public function testCreateInstanceWithProperties(){
		
		// simple case, without params

		$class			= $this->class;
		$litproperty	= new core_kernel_classes_Property(core_kernel_classes_ResourceFactory::create(new core_kernel_classes_Class(RDF_PROPERTY))->getUri());
		$property		= new core_kernel_classes_Property(core_kernel_classes_ResourceFactory::create(new core_kernel_classes_Class(RDF_PROPERTY))->getUri());
		
		$instance = $class->createInstanceWithProperties(array());
		$this->assertTrue($instance->hasType($class));
		
		// simple literal properties
		$instance1 = $class->createInstanceWithProperties(array(
			RDFS_LABEL		=> 'testlabel',
			RDFS_COMMENT	=> 'testcomment'
		));
		
		$this->assertTrue($instance1->hasType($class));
		$this->assertEquals($instance1->getLabel(), 'testlabel');
		$this->assertEquals($instance1->getComment(), 'testcomment');
		
		// multiple literal properties
		$instance2 = $class->createInstanceWithProperties(array(
			RDFS_LABEL				=> 'testlabel',
			$litproperty->getUri()	=> array('testlit1', 'testlit2')
		));
		$this->assertTrue($instance2->hasType($class));
		$this->assertEquals($instance2->getLabel(), 'testlabel');
		$comments = $instance2->getPropertyValues($litproperty);
		sort($comments);
		$this->assertEquals($comments, array('testlit1', 'testlit2'));
		
		// single ressource properties
		$propInst = core_kernel_classes_ResourceFactory::create($class);				
		$instance3 = $class->createInstanceWithProperties(array(
			RDFS_LABEL			=> 'testlabel',
			RDFS_COMMENT		=> 'testcomment',
			$property->getUri()	=> $propInst
		));
		$this->assertTrue($instance3->hasType($class));
		$this->assertEquals($instance3->getLabel(), 'testlabel');
		$propActual = $instance3->getUniquePropertyValue($property); // returns a ressource
		$this->assertEquals($propInst->getUri(), $propActual->getUri());
		
		// multiple ressource properties
		$propInst2 = core_kernel_classes_ResourceFactory::create($class);
		$instance4 = $class->createInstanceWithProperties(array(
			RDFS_LABEL			=> 'testlabel',
			RDFS_COMMENT		=> 'testcomment',
			$property->getUri()	=> array($propInst, $propInst2)
		));
		$this->assertTrue($instance4->hasType($class));
		$this->assertEquals($instance4->getLabel(), 'testlabel');
		
		$propActual = array_values($instance4->getPropertyValues($property));// returns uris
		$propNormative = array($propInst->getUri(), $propInst2->getUri());
		sort($propActual);
		sort($propNormative);
		$this->assertEquals($propActual, $propNormative);
		
		// multiple classes
		$classres = core_kernel_classes_ResourceFactory::create(new core_kernel_classes_Class(RDFS_CLASS), 'TestClass2');
	    $class2 = new core_kernel_classes_Class($classres);
		$classres = core_kernel_classes_ResourceFactory::create(new core_kernel_classes_Class(RDFS_CLASS), 'TestClass3');
	    $class3 = new core_kernel_classes_Class($classres);
	    
		// 2 classes (by ressource)
	    $instance5 = $class->createInstanceWithProperties(array(
			RDFS_LABEL			=> 'testlabel5',
			RDF_TYPE			=> $classres
		));

	    
		$this->assertTrue($instance5->hasType($class));
		$this->assertTrue($instance5->hasType($class3));
		$this->assertEquals($instance5->getLabel(), 'testlabel5');
		
		// 3 classes (by uri + class)
		$instance6 = $class->createInstanceWithProperties(array(
			RDFS_LABEL			=> 'testlabel6',
			RDF_TYPE			=> array($class2, $class3->getUri())
		));
		$this->assertTrue($instance6->hasType($class));
		$this->assertTrue($instance6->hasType($class2));
		$this->assertTrue($instance6->hasType($class3));
		$this->assertEquals($instance6->getLabel(), 'testlabel6');
		
		$instance->delete();
		$instance1->delete();
		$instance2->delete();
		
		$propInst->delete();
		$propInst2->delete();
		$property->delete();
		$litproperty->delete();
		
		$instance5->delete();
		$instance6->delete();
		$class2->delete();
		$class3->delete();
	}
	
	public function after($pMethode) {
		common_Logger::i('Cleaning up class '.$this->class->getUri().' for Create instance Tests');
		$this->class->delete();
		parent::after($pMethode);
	}
}