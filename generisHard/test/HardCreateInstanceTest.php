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

use oat\generisHard\models\hardapi\ResourceReferencer;
use oat\generisHard\models\switcher\Switcher;

/**
 * Test class for Class.
 * 
 * @author lionel.lecaque@tudor.lu
 * @package test
 */


class HardCreateInstanceTest extends GenerisPhpUnitTestRunner {
	protected $class;
	
	protected function setUp(){

        GenerisPhpUnitTestRunner::initTest();
        $this->installExtension('generisHard');
	    $classres = core_kernel_classes_ResourceFactory::create(new core_kernel_classes_Class(RDFS_CLASS), 'TestClass');
	    $this->class = new core_kernel_classes_Class($classres->getUri());
	    $this->assertIsA($this->class, 'core_kernel_classes_Class');
	    $this->assertTrue($this->class->hasType(new core_kernel_classes_Class(RDFS_CLASS)));
	    common_Logger::i('using class '.$this->class->getUri().' for Create instance Tests');
	}

	public function testCreateInstanceHardified() {
		
		$rr = ResourceReferencer::singleton();
		$this->assertFalse($rr->isClassReferenced($this->class));
		$softinstance = core_kernel_classes_ResourceFactory::create($this->class);
		$this->assertFalse($rr->isResourceReferenced($softinstance));
		
		$switcher = new Switcher();
		$switcher->hardify($this->class, array(
			'topclass'		=> $this->class,
		));
		unset($switcher);
		
		$this->assertTrue($rr->isClassReferenced($this->class));
		common_Logger::i('creating hardified');
		$hardinstance = core_kernel_classes_ResourceFactory::create($this->class);
		$this->assertTrue($rr->isResourceReferenced($hardinstance), 'Instance created from harmode class was added in softmode');
		
		$softinstance->delete();
		$hardinstance->delete();
		
		$switcher = new Switcher();
		$switcher->unhardify($this->class);
		unset($switcher);
	}
	
	public function after($pMethode) {
		common_Logger::i('Cleaning up class '.$this->class->getUri().' for Create instance Tests');
		$this->class->delete();
		parent::after($pMethode);
	}
}
?>