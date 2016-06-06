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
error_reporting(E_ALL);

use oat\generis\test\GenerisPhpUnitTestRunner;

/**
 * Test class for Collection.
 * 
 * @author lionel.lecaque@tudor.lu
 * @package test
 */


class CollectionTest extends GenerisPhpUnitTestRunner {

	protected $object;
	private $toto;
	private $tata;

	function __construct() {
    	parent::__construct();
    }
	
    /**
     * Setting the collection to test
     *
     */
    protected function setUp(){
        GenerisPhpUnitTestRunner::initTest();
		$this->object = new common_Collection(new common_Object(__METHOD__));
		$this->toto =  new core_kernel_classes_Literal('toto',__METHOD__);
		$this->tata =  new core_kernel_classes_Literal('tata',__METHOD__);
		$this->object->sequence[0] = $this->toto;
		$this->object->sequence[1] = $this->tata;
	}
	
	/**
	 * Test common_Collection->add
	 *
	 */
	public function testAdd(){
		$titi = new common_Object(__METHOD__);
		$this->object->add($titi);
		$this->assertEquals($this->object->sequence[2] , $titi);
	}
	
	/**
	 * Test common_Collection->count
	 *
	 */
	public function testCount(){
		$this->assertTrue($this->object->count() == 2);
	}
	
	/**
	 * Test common_Collection->indexOf
	 *
	 */
	public function testIndexOf(){
		$this->assertTrue($this->object->indexOf($this->toto) == 0);
		$this->assertTrue($this->object->indexOf($this->tata) == 1);
		$this->assertFalse($this->object->indexOf(new common_Object(__METHOD__)) == 2);
	}
	
	/**
	 * Test common_Collection->get
	 *
	 */
	public function testGet(){
		$this->assertEquals($this->object->get(0),$this->object->sequence[0]);
		$this->assertEquals($this->object->get(0)->literal , 'toto');
	}
	
	/**
	 * Test common_Collection->isEmtpy
	 *
	 */
	public function testisEmpty(){
		$emtpy = new common_Collection(new common_Object(__METHOD__));
		$this->assertTrue($emtpy->isEmpty());
		$emtpy->add(new common_Object(__METHOD__));
		$this->assertFalse($emtpy->isEmpty());

	}
	
	/**
	 * Test common_Collection->remove
	 *
	 */
	public function testRemove(){
		$this->object->remove($this->toto);
		$this->assertFalse($this->object->indexOf($this->toto) == 0);

	}
	
	 /**
	  * Test common_Collection->union
	  */
	public function testUnion(){
		$collection = new common_Collection(new common_Object('__METHOD__'));
		$collection->add(new core_kernel_classes_Literal('plop'));
		$results = $this->object->union($collection);
		$this->assertIsA($results,'common_Collection');
		$this->assertFalse($results->isEmpty());
		$this->assertTrue($results->count() == 3);
		$this->assertTrue($results->get($results->indexOf($this->toto))->literal == 'toto');
		$this->assertTrue($results->get($results->indexOf($this->tata))->literal == 'tata');
		$this->assertTrue($results->get(2)->literal == 'plop');
	}
	
	 /**
	  * Test common_Collection->intersect
	  */
	public function testIntersect(){
		$collection = new common_Collection(new common_Object('__METHOD__'));
		$collection->add(new core_kernel_classes_Literal('plop'));
		$collection->add(new core_kernel_classes_Literal('plop2'));
		$collection->add($this->toto);
		$collection->add($this->tata);
		$results = $collection->intersect($this->object);
		$this->assertIsA($results,'common_Collection');
		$this->assertTrue($results->count() == 2);
		$this->assertTrue($results->get($results->indexOf($this->toto))->literal == 'toto');
		$this->assertTrue($results->get($results->indexOf($this->tata))->literal == 'tata');
	}
}
