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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2015 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */
namespace oat\generis\test;

use oat\generis\test\GenerisPhpUnitTestRunner;
use \core_kernel_classes_Class;
use \core_kernel_classes_Resource;
use \core_kernel_classes_Property;
use \core_kernel_impl_ApiModelOO;
use \common_Utils;
use \core_kernel_classes_Literal;
use \common_Collection;
use \core_kernel_classes_Triple;
use \Exception;
use Prophecy\Prophet;

class ResourceTest extends GenerisPhpUnitTestRunner{

	protected $object;
	
	public function setUp()
	{
        GenerisPhpUnitTestRunner::initTest();

		$this->object = new core_kernel_classes_Resource(GENERIS_BOOLEAN);
		
		//create test class
		$clazz = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
		$this->clazz = $clazz->createSubClass($clazz);
	}

	function tearDown()
	{
        $this->clazz->delete();
    }
    
	/*
	 * 
	 * TOOLS FUNCTIONS
	 * 
	 */
	
	private function createTestResource()
	{
		return $this->clazz->createInstance();
	}
	
	private function createTestProperty()
	{
		return $this->clazz->createProperty('ResourceTestCaseProperty '.common_Utils::getNewUri());
	}
	
	
	/*
	 * 
	 * TEST CASE FUNCTIONS
	 * 
	 */
	/**
	 * 
	 * @author Lionel Lecaque, lionel@taotesting.com
	 */
	public function testGetPropertyValuesCollection()
	{
		$session = GenerisPhpUnitTestRunner::getTestSession();
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = new core_kernel_classes_Property(RDFS_SEEALSO,__METHOD__);
		$api = core_kernel_impl_ApiModelOO::singleton();
		$api->setStatement($instance->getUri(), RDFS_SEEALSO, GENERIS_TRUE, '');
		$api->setStatement($instance->getUri(), RDFS_SEEALSO, GENERIS_FALSE, '');
		$api->setStatement($instance->getUri(), RDFS_SEEALSO, 'plop', '');
		$api->setStatement($instance->getUri(), RDFS_SEEALSO, 'plup', 'FR');
		$api->setStatement($instance->getUri(), RDFS_SEEALSO, 'plip', 'FR');
		$api->setStatement($instance->getUri(), RDFS_SEEALSO, GENERIS_TRUE, 'FR');

		// Default language is EN (English) so that we should get a collection
		// containing 3 triples because we will receive the ones with no language
		// tags (2 instances of GENERIS_BOOLEAN and 'plop'.
		// Session::lg should contain a empty string.
		$collection = $instance->getPropertyValuesCollection($seeAlso);

		$this->assertTrue($collection->count() == 3);
		foreach ($collection->getIterator() as $value) {
			$this->assertIsA($value, 'core_kernel_classes_Container' );
			if($value instanceof core_kernel_classes_Resource ){
				$this->assertTrue($value->getUri() == GENERIS_TRUE || $value->getUri() == GENERIS_FALSE);
			}
			if ( $value instanceof core_kernel_classes_Literal){
				$this->assertEquals($value->literal, 'plop');
			}
		}

		// We now explicitly change the current language to EN (English), we should
		// get exactly the same behaviour.
		$session->setDataLanguage('EN');
		$collection = $instance->getPropertyValuesCollection($seeAlso);
		$this->assertTrue($collection->count() == 3);
		foreach ($collection->getIterator() as $value) {
			$this->assertIsA($value, 'core_kernel_classes_Container' );
			if($value instanceof core_kernel_classes_Resource ){
				$this->assertTrue($value->getUri() == GENERIS_TRUE || $value->getUri() == GENERIS_FALSE);
			}
			if ( $value instanceof core_kernel_classes_Literal){
				$this->assertEquals($value->literal, 'plop');
			}
		}

		// We now go to FR (French). we should receive a collection of 3 values:
		// a Generis True, 'plup'@fr, 'plip'@fr.
		$session->setDataLanguage('FR');
		$collection = $instance->getPropertyValuesCollection($seeAlso);
		$this->assertTrue($collection->count() == 3);
		foreach ($collection->getIterator() as $value) {
			$this->assertIsA($value, 'core_kernel_classes_Container' );
			if($value instanceof core_kernel_classes_Resource ){
				$this->assertTrue($value->getUri() == GENERIS_TRUE, $value->getUri() . ' must be equal to ' . GENERIS_TRUE);
			}
			if ( $value instanceof core_kernel_classes_Literal){
				$this->assertTrue($value->literal == 'plup' || $value->literal == 'plip', $value->literal . ' must be equal to plip or plop');
			}
		}
		
		// Back to normal.
		$session->setDataLanguage(DEFAULT_LANG);

		$instance->delete();
	}

	/**
	 * Test the function Resource:getPropertiesValues();
	 */
	public function testGetPropertiesValues()
	{
		//create test resource
		$resource = $this->createTestResource();
		$property1 = $this->createTestProperty();
		$property2 = $this->createTestProperty();
		$property3 = $this->createTestProperty();
		$resource->setPropertyValue($property1, 'prop1');
		$resource->setPropertyValue($property2, 'prop2');
		$resource->setPropertyValue($property3, 'prop3');
		
		//test that the get properties values is getting an array as parameter, if the parameter is not an array, the function will return an exception
		try{
			$resource->getPropertiesValues($property1);
			$this->assertTrue(false);
		}catch(\Exception $e){
			$this->assertTrue(true);
		}
		
		//test with one property
		$result = $resource->getPropertiesValues(array($property1));
		$this->assertTrue(in_array(new core_kernel_classes_Literal('prop1'), $result[$property1->getUri()]));
		//test with an other one
		$result = $resource->getPropertiesValues(array($property2));
		$this->assertTrue(in_array('prop2', $result[$property2->getUri()]));
		
		//test with several properties
		$result = $resource->getPropertiesValues(array($property1, $property2, $property3));
		$this->assertTrue(in_array('prop1', $result[$property1->getUri()]));
		$this->assertTrue(in_array('prop2', $result[$property2->getUri()]));
		$this->assertTrue(in_array('prop3', $result[$property3->getUri()]));
		
		//clean all
		$property1->delete();
		$property2->delete();
		$property3->delete();
		$resource->delete();
	}
	/**
	 * 
	 * @author Lionel Lecaque, lionel@taotesting.com
	 */
	public function testGetUniquePropertyValue(){
	    $resource = $this->createTestResource();
	    $property1 = $this->createTestProperty();
	    try {
            $resource->getUniquePropertyValue($property1);
            $resource = $this->createTestResource();
            $property1 = $this->createTestProperty();
            $this->fail('core_kernel_classes_EmptyProperty should have been thrown');
	    }
	    catch (\Exception $e ) {
	        $this->assertInstanceOf('core_kernel_classes_EmptyProperty', $e);
	        $property1->delete();        
	        $resource->delete();
	    }
	    
	    $resource = $this->createTestResource();
	    $property1 = $this->createTestProperty();
	    $resource->setPropertyValue($property1, 'prop1');
	    $resource->setPropertyValue($property1, 'prop2');
	    try {
	        $resource->getUniquePropertyValue($property1);
	        $this->fail('core_kernel_classes_MultiplePropertyValuesException should have been thrown');
	    }
	    catch (\Exception $e ) {
	        $this->assertInstanceOf('core_kernel_classes_MultiplePropertyValuesException', $e);
	        $property1->delete();
	        $resource->delete(true);
	    }

	}
	
	
	/**
	 * 
	 * @author Lionel Lecaque, lionel@taotesting.com
	 */
	public function testGetRdfTriples()
	{
		$collectionTriple = $this->object->getRdfTriples();

		$this->assertTrue($collectionTriple instanceof common_Collection);
		foreach ($collectionTriple->getIterator() as $triple){
			$this->assertTrue( $triple instanceof core_kernel_classes_Triple );
			$this->assertEquals($triple->subject, GENERIS_BOOLEAN );
			if ($triple->predicate === RDFS_LABEL) {
				$this->assertEquals($triple->object,'Boolean' );
				$this->assertEquals($triple->lg, DEFAULT_LANG );
			}
			if ($triple->predicate === RDFS_COMMENT) {
				$this->assertEquals($triple->object,'Boolean' );
				$this->assertEquals($triple->lg, DEFAULT_LANG );
			}
		}

	}

	public function testDelete()
	{

		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);

		$instance = $class->createInstance('test' , 'test');
		$label = new core_kernel_classes_Property(RDFS_LABEL,__METHOD__);
		$this->assertTrue($instance->delete());
		$this->assertTrue($instance->getPropertyValuesCollection($label)->isEmpty());

		$class2 = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance2 = $class->createInstance('test2' , 'test2');
		$property2 = $class->createProperty('multi','multi',true);

		$instance3 = $class->createInstance('test3' , 'test3');
		$instance3->setPropertyValue($property2, $instance2->getUri());

		$api = core_kernel_impl_ApiModelOO::singleton();
		$api->setStatement($instance2->getUri(),$property2->getUri(),'vrai','FR');
		$api->setStatement($instance2->getUri(),$property2->getUri(),'true','EN');

		$this->assertTrue($instance2->delete(true));
		$this->assertNull($instance3->getOnePropertyValue($property2));
		$this->assertTrue($instance3->delete());
		$this->assertTrue($property2->delete());
	}


	public function testSetPropertyValue()
	{
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test', 'test');
		$seeAlso = new core_kernel_classes_Property(RDFS_SEEALSO,__METHOD__);
		$instance->setPropertyValue($seeAlso,GENERIS_TRUE);
		$instance->setPropertyValue($seeAlso,GENERIS_FALSE);
		$instance->setPropertyValue($seeAlso,"&plop n'\"; plop'\' plop");
		$collection = $instance->getPropertyValuesCollection($seeAlso);
		foreach ($collection->getIterator() as $value) {
			$this->assertIsA($value, 'core_kernel_classes_Container' );
			if($value instanceof core_kernel_classes_Resource ){
				$this->assertTrue($value->getUri() == GENERIS_TRUE || $value->getUri() ==GENERIS_FALSE);
			}
			if ( $value instanceof core_kernel_classes_Literal){
				$this->assertEquals($value->literal, "&plop n'\"; plop'\' plop");
			}
		}
		$instance->delete(true);
	}

	public function testSetPropertiesValues()
	{

		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN );
		$instance = $class->createInstance('a label', 'a comment');
		$this->assertIsA($instance, 'core_kernel_classes_Resource' );

			$instance->setPropertiesValues(array(
			RDFS_SEEALSO	=> "&plop n'\"; plop'\' plop",
			RDFS_LABEL		=> array('new label', 'another label', 'yet a last one'),
			RDFS_COMMENT 	=> 'new comment'
		));
			
		$seeAlso = $instance->getOnePropertyValue(new core_kernel_classes_Property(RDFS_SEEALSO));
		$this->assertNotNull($seeAlso);
		$this->assertIsA($seeAlso, 'core_kernel_classes_Literal');
		$this->assertEquals($seeAlso->literal, "&plop n'\"; plop'\' plop");

		$instance->delete(true);
	}

	public function testGetUsedLanguages()
	{
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = $class->createProperty('seeAlso','multilingue',true);

		$api = core_kernel_impl_ApiModelOO::singleton();
		$api->setStatement($instance->getUri(),$seeAlso->getUri(),GENERIS_TRUE,'FR');
		$api->setStatement($instance->getUri(),$seeAlso->getUri(),GENERIS_TRUE,'EN');
		$lg = $instance->getUsedLanguages($seeAlso);
		$this->assertTrue(in_array('FR',$lg));
		$this->assertTrue(in_array('EN',$lg));
		$seeAlso->delete();
		$instance->delete();
	}

	public function testGetPropertyValuesByLg()
	{
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = $class->createProperty('seeAlso','multilingue',true);
		$api = core_kernel_impl_ApiModelOO::singleton();
		$api->setStatement($instance->getUri(),$seeAlso->getUri(),'vrai','FR');
		$api->setStatement($instance->getUri(),$seeAlso->getUri(),'vrai peut etre','FR');
		$api->setStatement($instance->getUri(),$seeAlso->getUri(),'true','EN');

		$collectionFr = $instance->getPropertyValuesByLg($seeAlso,'FR');
		$this->assertTrue($collectionFr->count() == 2);
		$collectionEn = $instance->getPropertyValuesByLg($seeAlso,'EN');
		$this->assertTrue($collectionEn->count() == 1);
		$this->assertTrue($collectionFr->get(0)->literal == 'vrai peut etre' || $collectionFr->get(0)->literal == 'vrai');
		$this->assertTrue($collectionFr->get(1)->literal == 'vrai peut etre' || $collectionFr->get(1)->literal == 'vrai');
		$this->assertTrue($collectionEn->get(0)->literal == 'true');
		$instance->delete();
		$seeAlso->delete();
	}

	public function testSetPropertyValueByLg()
	{
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = $class->createProperty('seeAlso','multilingue',true);

		$instance->setPropertyValueByLg($seeAlso,'vrai','FR');
		$instance->setPropertyValueByLg($seeAlso,'vrai peut etre','FR');
		$instance->setPropertyValueByLg($seeAlso,'true','EN');

		$collectionFr = $instance->getPropertyValuesByLg($seeAlso,'FR');
		$this->assertTrue($collectionFr->count() == 2);
		$collectionEn = $instance->getPropertyValuesByLg($seeAlso,'EN');
		$this->assertTrue($collectionEn->count() == 1);
		$this->assertTrue($collectionFr->get(0)->literal == 'vrai peut etre' || $collectionFr->get(0)->literal == 'vrai');
		$this->assertTrue($collectionFr->get(1)->literal == 'vrai peut etre' || $collectionFr->get(1)->literal == 'vrai');
		$this->assertTrue($collectionEn->get(0)->literal == 'true');
		$instance->delete();
		$seeAlso->delete();
	}

	public function testRemovePropertyValue(){
	    $class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
	    $instance = $class->createInstance('test' , 'test');
	    $seeAlso = $class->createProperty('seeAlso','multilingue');
	    $instance->setPropertyValue($seeAlso,'foo');
	    $instance->setPropertyValue($seeAlso,'bar');
	    $this->assertTrue(in_array('foo', $instance->getPropertyValues($seeAlso)));
	    $this->assertTrue(in_array('bar', $instance->getPropertyValues($seeAlso)));

	    $instance->removePropertyValue($seeAlso,'foo');
	    $this->assertEquals(array('bar'), $instance->getPropertyValues($seeAlso));
	     
	    
	    $instance->delete();
	    $seeAlso->delete();
	}
	
	public function testRemovePropertyValueByLg()
	{
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = $class->createProperty('seeAlso','multilingue',true);

		$instance->setPropertyValueByLg($seeAlso,'vrai','FR');
		$instance->setPropertyValueByLg($seeAlso,'vrai peut etre','FR');
		$instance->setPropertyValueByLg($seeAlso,'true','EN');

		$this->assertTrue($instance->removePropertyValueByLg($seeAlso,'FR'));
		$collectionFr = $instance->getPropertyValuesByLg($seeAlso,'FR');
		$this->assertTrue($collectionFr->count() == 0);
		$collectionEn = $instance->getPropertyValuesByLg($seeAlso,'EN');
		$this->assertTrue($collectionEn->count() == 1);
		$this->assertTrue($collectionEn->get(0)->literal == 'true');

		$instance->delete();
		$seeAlso->delete();
	}
	
	public function testRemovePropertyValues()
	{
		$session = GenerisPhpUnitTestRunner::getTestSession();
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test', 'test');
		$instance2 = $class->createInstance('test2', 'test2');
		
		$prop1 = $class->createProperty('property1','monologingual');
		$prop2 = $class->createProperty('property2','multilingual',true);
		
		// We first go with monolingual property.
		$instance->setPropertyValue($prop1, 'mono');
		$propValue = $instance->getOnePropertyValue($prop1);
		$this->assertTrue($propValue->literal == 'mono');
		$this->assertTrue($instance->removePropertyValues($prop1));
		$this->assertTrue(count($instance->getPropertyValues($prop1)) == 0);
		
		// And new we go multilingual.
		$instance->setPropertyValue($prop2,'multi');
		$instance->setPropertyValueByLg($prop2,'multiFR1','FR');
		$instance->setPropertyValueByLg($prop2,'multiFR2','FR');
		$instance->setPropertyValueByLg($prop2,'multiSE1','SE');
		$instance->setPropertyValueByLg($prop2,'multiSE1','SE');
		$instance->setPropertyValueByLg($prop2,'multiJA','JA');
		$this->assertEquals(count($instance->getPropertyValues($prop2)),1);
		
		// We are by default in EN language (English). If we call removePropertyValues
		// for property values on a language dependant property, we should only remove
		// one triple with value 'multi'@EN.
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 1);
		$this->assertTrue($instance->removePropertyValues($prop2));
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 0);
		
		// We now switch to Swedish language and remove the values in the language.
		$session->setDataLanguage('SE');
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 2);
		$this->assertTrue($instance->removePropertyValues($prop2));
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 0);
		
		// Same as above with Japanese language.
		$session->setDataLanguage('JA');
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 1);
		$this->assertTrue($instance->removePropertyValues($prop2));
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 0);
		
		// And now final check in French language.
		$session->setDataLanguage('FR');
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 2);
		$this->assertTrue($instance->removePropertyValues($prop2));
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 0);
		
		$prop1->delete();
		$prop2->delete();
		$instance->delete();
		$instance2->delete();
	}

	public function testEditPropertyValues(){
	    $class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
	    $instance = $class->createInstance('test' , 'test');
	    $seeAlso = $class->createProperty('seeAlso','multilingue',true);
	    
	    $this->assertEquals(array(), $instance->getPropertyValues($seeAlso));
	    $instance->editPropertyValues($seeAlso,'foo');
	    $this->assertEquals(array('foo'), $instance->getPropertyValues($seeAlso));
	    $instance->editPropertyValues($seeAlso,'bar');
	    $this->assertEquals(array('bar'), $instance->getPropertyValues($seeAlso));
	    
	    $instance->editPropertyValues($seeAlso,array('foo2','bar'));
	    $val = $instance->getPropertyValues($seeAlso);
    
	    $this->assertTrue(in_array('bar',$val));
	    $this->assertTrue(in_array('foo2',$val));
	     
	    $instance->delete();
	    $seeAlso->delete();
	}
	
	public function testEditPropertyValueByLg()
	{
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = $class->createProperty('seeAlso','multilingue',true);

		$instance->setPropertyValueByLg($seeAlso,'vrai','FR');
		$instance->setPropertyValueByLg($seeAlso,'vrai peut etre','FR');
		$instance->setPropertyValueByLg($seeAlso,'true','EN');

		$this->assertTrue($instance->editPropertyValueByLg($seeAlso, 'aboslutly true','EN'));
		$collectionEn = $instance->getPropertyValuesByLg($seeAlso,'EN');
		$this->assertTrue($collectionEn->count() == 1);
		$this->assertTrue($collectionEn->get(0)->literal == 'aboslutly true');

		$instance->delete();
		$seeAlso->delete();
	}

	public function testGetOnePropertyValue()
	{
		$session = GenerisPhpUnitTestRunner::getTestSession();
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = $class->createProperty('seeAlsoDo','multilingue',true);

		// If there is no value for the targeted property,
		// it should return null.
		$one = $instance->getOnePropertyValue($seeAlso);
		$this->assertEquals($one,null);

		
		$instance->setPropertyValue($seeAlso,'plop');
		$one = $instance->getOnePropertyValue($seeAlso);
		$this->assertEquals($one->literal,'plop');

        try {		
		  $one = $instance->getOnePropertyValue($seeAlso, true);
		  $this->fail('core_kernel_persistence_Exception should have been thrown');
        }
        catch(\Exception $e){
            $this->assertInstanceOf('core_kernel_persistence_Exception', $e);
            $this->assertEquals("parameter 'last' for getOnePropertyValue no longer supported",$e->getMessage());
        }

		// We now go multilingual.
		$session->setDataLanguage('FR');
		$instance->setPropertyValue($seeAlso, 'plopFR');
		$one = $instance->getPropertyValuesByLg($seeAlso, 'FR');
		
		// Back to default language.
		$session->setDataLanguage(DEFAULT_LANG);
		$seeAlso->delete();
		$instance->delete(true);
	}

	public function testGetTypes(){
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$typeUri = array_keys($instance->getTypes());
		$this->assertEquals($typeUri[0],GENERIS_BOOLEAN);
		$this->assertTrue(count($typeUri) == 1);
		$instance->delete();
	}
	
	public function testHasType(){
	    $class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
	    $file = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
	     
	    $instance = $class->createInstance('test' , 'test');
	    $this->assertTrue($instance->hasType($class));
	    $this->assertFalse($instance->hasType($file));
	     
	    $instance->delete();
	}
	
	
	public function testSetType(){
	    $class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
	    $file = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
	
	    $instance = $class->createInstance('test' , 'test');
	    $this->assertTrue($instance->hasType($class));
	    $this->assertFalse($instance->hasType($file));
	    $instance->setType($file);
	    $this->assertTrue($instance->hasType($class));
	    $this->assertTrue($instance->hasType($file));

	    $instance->delete();
	}
	
	public function testRemoveType(){
	    $class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
	    $instance = $class->createInstance('test' , 'test');
	    
	    $file = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
	    $instance->setType($file);

	    $instance->removeType($class);
	    $this->assertTrue($instance->hasType($file));
	    $this->assertFalse($instance->hasType($class));
	    
	    $instance->delete();
	}

	public function testGetComment()
	{
		$inst = new core_kernel_classes_Resource(CLASS_GENERIS_RESOURCE);
		$this->assertTrue($inst->getLabel()== 'generis_Ressource');
		$this->assertTrue($inst->getComment() == 'generis_Ressource');
	}
	
	
	public function testIsInstanceOf()
	{
		$baseClass = new core_kernel_classes_Class(RDFS_CLASS);
		$level1a = $baseClass->createSubClass('level1a');
		$level1b = $baseClass->createSubClass('level1b');
		$level2a = $level1a->createSubClass('level2a');
		$level2b = $level1b->createSubClass('level2b');
		
		// single type
		$instance = $level2a->createInstance('test Instance');
		$this->assertTrue($instance->isInstanceOf($level2a));
		$this->assertTrue($instance->isInstanceOf($level1a));
		$this->assertTrue($instance->isInstanceOf($baseClass));
		$this->assertFalse($instance->isInstanceOf($level1b));
		$this->assertFalse($instance->isInstanceOf($level2b));
		
		// multiple types
		$instance->setType($level2b);
		$this->assertTrue($instance->isInstanceOf($level2a));
		$this->assertTrue($instance->isInstanceOf($level1a));
		$this->assertTrue($instance->isInstanceOf($baseClass));
		$this->assertTrue($instance->isInstanceOf($level1b));
		$this->assertTrue($instance->isInstanceOf($level2b));

		// ensure no reverse inheritence
		$instance2 = $level1b->createInstance('test Instance2');
		$this->assertFalse($instance2->isInstanceOf($level2a));
		$this->assertFalse($instance2->isInstanceOf($level1a));
		$this->assertTrue($instance2->isInstanceOf($baseClass));
		$this->assertTrue($instance2->isInstanceOf($level1b));
		$this->assertFalse($instance2->isInstanceOf($level2b));
		
		
		$instance2->delete();
		$instance->delete();
		$level2b->delete();
		$level2a->delete();
		$level1b->delete();
		$level1a->delete();
	}
	
	
	public function testIsProperty()
	{
	    $prop = $this->createTestProperty();
	    $this->assertTrue($prop->isProperty());
	    $prop->delete();
	    
	    $instance = $this->createTestResource();
	    $this->assertFalse($instance->isProperty());
	    
	    
	    $class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
	    $this->assertFalse($class->isProperty());
	    $prop->delete();
	    $instance->delete();
	    
	}
	
	/**
	 * @expectedException        common_exception_Error
	 * @expectedExceptionMessage could not create resource from NULL debug:
	 * @author Lionel Lecaque, lionel@taotesting.com
	 */
	public function testConstructNull(){
	    $new = new core_kernel_classes_Resource(null);
	}
	
	/**
	 * @expectedException        common_exception_Error
	 * @expectedExceptionMessage cannot construct the resource because the uri cannot be empty, debug:
	 * @author Lionel Lecaque, lionel@taotesting.com
	 */
	public function testConstructEmtpy(){
	    $new = new core_kernel_classes_Resource('');
	}
	
	public function testIsClass()
	{
	    $prop = $this->createTestProperty();
	    $this->assertFalse($prop->isClass());
	    $prop->delete();
	     
	    $class = new core_kernel_classes_Class(RDFS_CLASS,__METHOD__);
	    $sublClass = $class->createInstance('subclass','subclass');
	    $this->assertTrue($class->isClass());
	    $this->assertTrue($sublClass->isClass());

	    $instance = $this->createTestResource();
	    $this->assertFalse($instance->isClass());
	    
	    $prop->delete();
	    $instance->delete();
	    $sublClass->delete();
	}
	/**
	 * @expectedException        common_exception_DeprecatedApiMethod
	 * @expectedExceptionMessage Use duplicated instead, because clone resource could not share same uri that original
	 * @author Lionel Lecaque, lionel@taotesting.com
	 */
	public function testClone()
	{
	    $class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
	    $instance = $class->createInstance('test' , 'test');
	    $clone = clone $instance;
	       
	    $instance->delete();
	    $clone->delete();
	    
	}
	
	public function testDuplicate()
	{
        $class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
        $instance = $class->createInstance('test' , 'test');
        $prop = $this->createTestProperty();
        $instance->setPropertyValue($prop,'plop');
        
        
        $duplicate = $instance->duplicate();
        $this->assertNotEquals($duplicate->getUri(),$instance->getUri());
        $this->assertEquals($duplicate->getLabel(),$instance->getLabel());
        $result = $instance->getPropertiesValues(array($prop));
        $this->assertTrue(in_array('plop', $result[$prop->getUri()]));
        

        
        $duplicate->delete();
        $instance->delete();
        $prop->delete();
    }

	
	
}