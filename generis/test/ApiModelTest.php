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

class ApiModelTest extends GenerisPhpUnitTestRunner {
	protected $object;

	function __construct() {
    	parent::__construct();
    }
	
    /**
     * Setting the Api to test
     *
     */
    protected function setUp(){
		GenerisPhpUnitTestRunner::initTest();
    	
		$this->object = core_kernel_impl_ApiModelOO::singleton();
	}
	
	public function testGetRootClass(){
		$localModel = common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri();
		$this->assertFalse(empty($localModel));
		
		$rootClasses = $this->object->getRootClasses();
		$this->assertInstanceOf('common_Collection',$rootClasses);
		$expectedResult = 	array( 	
			WIDGET_CONSTRAINT_TYPE,
			CLASS_WIDGET,
			RDFS_RESOURCE,
		    CLASS_WIDGETRENDERER
		);
		
		$pattern = "/^".preg_quote($localModel, '/')."/";
		foreach ($rootClasses->getIterator() as $rootClass) {
			
			$this->assertInstanceOf('core_kernel_classes_Class',$rootClass);
			
			$parentClasses = $rootClass->getParentClasses(true);
			$this->assertEquals(0, count($parentClasses));
			
			$types = $rootClass->getTypes(true);
			$this->assertEquals(1, count($types));
			foreach($types as $uri => $parent){
				$this->assertEquals($uri,  RDFS_CLASS);
			}
			//don't check the user root classes
			if(!preg_match($pattern, $rootClass->getUri())){
				$this->assertTrue(in_array($rootClass->getUri(), $expectedResult));
			}
		}
	}
	
	public function testGetMetaClasses(){
		$metaClasses = $this->object->getMetaClasses();
		$this->assertInstanceOf('core_kernel_classes_ContainerCollection',$metaClasses);
		foreach ($metaClasses as $metaClass){
		    if ($metaClass->getUri() == RDFS_DATATYPE) {
        		$this->assertInstanceOf('core_kernel_classes_Class',$metaClass);
        		$this->assertEquals($metaClass->getLabel(),'Datatype');
        		$this->assertEquals($metaClass->getComment(),'The class of RDF datatypes.');
		    }
		}
	}
	
	public function testSetStatement(){
		$true = new core_kernel_classes_Resource(GENERIS_TRUE, __METHOD__);
		$predicate = RDFS_SEEALSO;
		$property = new core_kernel_classes_Property($predicate,__METHOD__); 
		$this->assertTrue($this->object->setStatement($true->getUri(), $predicate, 'test', DEFAULT_LANG), 
						  "setStatement should be able to set a value.");
		
		$values = $true->getPropertyValues($property);
		$this->assertTrue(count($values) > 0);
		
		$tripleFound = false;
		foreach ($values as $value) {
			if (!common_Utils::isUri($value) && $value == 'test') {
				$tripleFound = true;
				break;
			}
		}
		
		$this->assertTrue($tripleFound, "A property value for property " . $property->getUri() . 
										" should be found for resource " . $true->getUri());
		
		$this->object->removeStatement($true->getUri(), $predicate, 'test', DEFAULT_LANG);
	}
	
	public function testRemoveStatement(){
		$true = new core_kernel_classes_Resource(GENERIS_TRUE, __METHOD__);
		$predicate = RDFS_SEEALSO;
		$property = new core_kernel_classes_Property($predicate,__METHOD__); 
		$this->assertTrue($this->object->setStatement(GENERIS_TRUE,$predicate,'test', 'EN'));
		$remove = $this->object->removeStatement(GENERIS_TRUE,$predicate,'test','EN');
		$this->assertTrue($remove);
		$value = $true->getPropertyValuesCollection($property);
		$this->assertTrue($value->isEmpty());
	}
	
	public function testGetSubject(){
		$set = $this->object->getSubject(RDFS_LABEL , 'True');
		if($set instanceof core_kernel_classes_ContainerCollection) {
			$this->assertFalse($set->isEmpty());
			$found = false;
			foreach($set->getIterator() as $resource){
				if($resource->getUri() == GENERIS_TRUE){
					$found = true;
					break;
				}
			}
			$this->assertTrue($found);
		}
		else {
			$this->fail('GetSubject do not retrieve proper resource');
		}
	}

	public function testGetAllClasses(){
		$collection = $this->object->getAllClasses();
		$this->assertInstanceOf('core_kernel_classes_ContainerCollection',$collection);
		foreach ($collection->getIterator() as $aClass) {
			$this->assertInstanceOf('core_kernel_classes_Class',$aClass);
			if($aClass->getUri() === RDFS_CLASS){
				$this->assertEquals($aClass->getLabel(),'Class');
				$this->assertEquals($aClass->getComment(),'The class of classes.');
			}
			if($aClass->getUri() === RDF_STATEMENT){
				$this->assertEquals($aClass->getLabel(),'Statement');
				$this->assertEquals($aClass->getComment(), 'The class of RDF statements.');
			}
			if($aClass->getUri() === RDFS_RESOURCE){
				$this->assertEquals($aClass->getLabel(),'Resource');
				$this->assertEquals($aClass->getComment(), 'The class resource, everything.');
			}
			if($aClass->getUri() ===  RDF_PROPERTY){
				$this->assertEquals($aClass->getLabel(),'Property');
				$this->assertEquals($aClass->getComment(), 'The class of RDF properties.');
			}
			if($aClass->getUri() ===  CLASS_GENERIS_RESOURCE){
				$this->assertEquals($aClass->getLabel(),'generis_Ressource');
				$this->assertEquals($aClass->getComment(), 'generis_Ressource');
			}
			if($aClass->getUri() ===  RDFS_DATATYPE){
				$this->assertEquals($aClass->getLabel(),'Datatype');
				$this->assertEquals($aClass->getComment(), 'The class of RDF datatypes.');
			}
		}
	}
}