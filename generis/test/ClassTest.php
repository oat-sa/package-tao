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


class ClassTest extends GenerisPhpUnitTestRunner {
	protected $object;
	
	protected function setUp(){

        GenerisPhpUnitTestRunner::initTest();

		$this->object = new core_kernel_classes_Class(RDFS_RESOURCE);
		$this->object->debug = __METHOD__;
	}
    
	public function testGetSubClasses(){

		$generisResource = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
	
		$subClass0 = $generisResource->createSubClass('test0','test0 Comment');
		$subClass1 = $subClass0->createSubClass('test1','test1 Comment');

	
		$subClass2 = $subClass0->createSubClass('test2','test2 Comment');
		$subClass3 = $subClass2->createSubClass('test3','test3 Comment');
		$subClass4 = $subClass3->createSubClass('test4','test4 Comment');
		
		$subClassesArray = $subClass0->getSubClasses();
		foreach ( $subClassesArray as $subClass) {
			$this->assertTrue($subClass->isSubClassOf($subClass0));
		}
		
		$subClassesArray2 = $subClass0->getSubClasses(true);
		foreach ( $subClassesArray2 as $subClass) {
			if($subClass->getLabel() == 'test1'){
				$this->assertTrue($subClass->isSubClassOf($subClass0));
			}
			if($subClass->getLabel() == 'test2'){
				$this->assertTrue($subClass->isSubClassOf($subClass0));
			}
			if($subClass->getLabel() == 'test3'){
				$this->assertTrue($subClass->isSubClassOf($subClass2));
			}
			if($subClass->getLabel() == 'test4'){
				$this->assertTrue($subClass->isSubClassOf($subClass3));
				$this->assertTrue($subClass->isSubClassOf($subClass2));
				$this->assertFalse($subClass->isSubClassOf($subClass1));
			}
			
		}

		$subClass0->delete();
		$subClass1->delete();
		$subClass2->delete();
		$subClass3->delete();
		$subClass4->delete();
	}

	public function testGetParentClasses(){
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN);
		$indirectParentClasses = $class->getParentClasses(true);

		$this->assertEquals(2,count($indirectParentClasses));
		$expectedResult = array (CLASS_GENERIS_RESOURCE , RDFS_RESOURCE);
		foreach ($indirectParentClasses  as $parentClass) {
			$this->assertInstanceOf('core_kernel_classes_Class',$parentClass);	
			$this->assertTrue(in_array($parentClass->getUri(),$expectedResult));
		}
		
		$directParentClass = $class->getParentClasses(); 
		$this->assertEquals(1,count($directParentClass));
		foreach ($directParentClass  as $parentClass) {
			$this->assertInstanceOf('core_kernel_classes_Class', $parentClass);	
			$this->assertEquals(CLASS_GENERIS_RESOURCE, $parentClass->getUri()); 
		}
	}
	

	

	public function testGetProperties(){
		$list = new core_kernel_classes_Class(RDF_LIST);
		$properties = $list->getProperties();
		$this->assertTrue(count($properties) == 2);
		$expectedResult = array (	RDF_FIRST, RDF_REST);
	
		foreach ($properties as $property) {
			
			$this->assertTrue($property instanceof core_kernel_classes_Property);
			$this->assertTrue(in_array($property->getUri(),$expectedResult));
			if ($property->getUri() === RDF_FIRST) {
				$this->assertEquals($property->getRange()->getUri(), RDFS_RESOURCE);
				$this->assertEquals($property->getLabel(),'first');
				$this->assertEquals($property->getComment(),'The first item in the subject RDF list.');		
			}
			if ($property->getUri() === RDF_REST) {
				$this->assertEquals($property->getRange()->getUri(), RDF_LIST);
				$this->assertEquals($property->getLabel(),'rest');
				$this->assertEquals($property->getComment(),'The rest of the subject RDF list after the first item.');		
			}
		}
		
		
		$class = $list->createSubClass('toto','toto');
		$properties2 = $class->getProperties(true);
		$this->assertFalse(empty($properties2));
		
		$class->delete();
	}

	

	
 	public function testGetInstances(){
 		$class = new core_kernel_classes_Class(CLASS_WIDGET);
 		$plop = $class->createInstance('test','comment');
 		$instances = $class->getInstances();
		$subclass = $class->createSubClass('subTest Class', 'subTest Class');
		$subclassInstance = $subclass->createInstance('test3','comment3');
		

 		$this->assertTrue(count($instances)  > 0);

 		foreach ($instances as $k=>$instance) {
 			$this->assertTrue($instance instanceof core_kernel_classes_Resource );
 						
 			if ($instance->getUri() === WIDGET_COMBO) {
 				$this->assertEquals($instance->getLabel(),'Drop down menu' );
 				$this->assertEquals($instance->getComment(),'In drop down menu, one may select 1 to N options' );
 			}
 		 	if ($instance->getUri() === WIDGET_RADIO) {
 				$this->assertEquals($instance->getLabel(),'Radio button' );
 				$this->assertEquals($instance->getComment(),'In radio boxes, one may select exactly one option' );
 			}
 		 	if ($instance->getUri() === WIDGET_CHECK) {
 				$this->assertEquals($instance->getLabel(),'Check box' );
 				$this->assertEquals($instance->getComment(),'In check boxes, one may select 0 to N options' );
 			}
 		  	if ($instance->getUri() === WIDGET_FTE) {
 				$this->assertEquals($instance->getLabel(),'A Text Box' );
 				$this->assertEquals($instance->getComment(),'A particular text box' );
 			}
 			if ($instance->getUri() === $subclassInstance->getUri()){
 				$this->assertEquals($instance->getLabel(),'test3' );
 				$this->assertEquals($instance->getComment(),'comment3' );
 			}			
 		}
 		
 		$instances2 = $class->getInstances(true);
 		$this->assertTrue(count($instances2)  > 0);
 		foreach ($instances2 as $k=>$instance) {
 			$this->assertTrue($instance instanceof core_kernel_classes_Resource );		
 			if ($instance->getUri() === WIDGET_COMBO) {
 				$this->assertEquals($instance->getLabel(),'Drop down menu' );
 				$this->assertEquals($instance->getComment(),'In drop down menu, one may select 1 to N options' );
 			}
 		 	if ($instance->getUri() === WIDGET_RADIO) {
 				$this->assertEquals($instance->getLabel(),'Radio button' );
 				$this->assertEquals($instance->getComment(),'In radio boxes, one may select exactly one option' );
 			}
 		 	if ($instance->getUri() === WIDGET_CHECK) {
 				$this->assertEquals($instance->getLabel(),'Check box' );
 				$this->assertEquals($instance->getComment(),'In check boxes, one may select 0 to N options' );
 			}
 		  	if ($instance->getUri() === WIDGET_FTE) {
 				$this->assertEquals($instance->getLabel(),'A Text Box' );
 				$this->assertEquals($instance->getComment(),'A particular text box' );
 			}
 			if ($instance->getUri() === $plop->getUri()){
 				$this->assertEquals($instance->getLabel(),'test' );
 				$this->assertEquals($instance->getComment(),'comment' );
 			}	
			if ($instance->getUri() === $plop->getUri()){
 				$this->assertEquals($instance->getLabel(),'test' );
 				$this->assertEquals($instance->getComment(),'comment' );
 			}	
 			
 		}
 		
 		$plop->delete();
 		$subclass->delete();
 		$subclassInstance->delete();
 	}
 	
 	
 	
	public function testIsSubClassOf(){
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN);
		$subClass = $class->createSubClass('test', 'test'); 
		$this->assertTrue($class->isSubClassOf(new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE)));
		$this->assertTrue($subClass->isSubClassOf($class) );
		$this->assertFalse($subClass->isSubClassOf($subClass) );
		$this->assertTrue($subClass->isSubClassOf(new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE)));
		$subClass->delete();

	}
	

	
	public function testSetSubClasseOf(){
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');
		$subClass = $class->createSubClass('test', 'test'); 
		$subClass1 = $subClass->createSubClass('subclass of test', 'subclass of test'); 
		$subClass2 = $subClass->createSubClass('subclass of test2', 'subclass of test2'); 
		
		$this->assertTrue($subClass->isSubClassOf($class) );
		$this->assertTrue($subClass1->isSubClassOf($class) );
		$this->assertTrue($subClass2->isSubClassOf($class) );
		
		$this->assertFalse($subClass2->isSubClassOf($subClass1) );
		$subClass2->setSubClassOf($subClass1);
		$this->assertTrue($subClass2->isSubClassOf($subClass1) );

		
		$subClass->delete();
		$subClass1->delete();
		$subClass2->delete();

	}

	public function testCreateInstance(){
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');
		$instance = $class->createInstance('toto' , 'tata');
		$this->assertEquals($instance->getLabel(), 'toto');
		$this->assertEquals($instance->getComment(), 'tata');
		$instance2 = $class->createInstance('toto' , 'tata');
		$this->assertNotSame($instance,$instance2);
		$instance->delete();
		$instance2->delete();
	}
	
	public function testCreateSubClass(){
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');
		$subClass = $class->createSubClass('toto' , 'tata');
		$this->assertNotEquals($class,$subClass);
		$this->assertEquals($subClass->getLabel(),'toto');
		$this->assertEquals($subClass->getComment(), 'tata');
		$subClassOfProperty = new core_kernel_classes_Property('http://www.w3.org/2000/01/rdf-schema#subClassOf');
		$subClassOfPropertyValue = $subClass->getPropertyValues($subClassOfProperty);
		$this->assertTrue(in_array($class->getUri(), array_values($subClassOfPropertyValue))); 
		$subClass->delete();
	}

	public function testCreateProperty(){
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');

		$property = $class->createProperty('tata','toto');
		$property2 = $class->createProperty('tata2','toto2',true);
		$this->assertTrue($property->getLabel() == 'tata');

		$this->assertTrue($property->getComment() == 'toto');
		$this->assertTrue($property2->isLgDependent());
		$this->assertTrue($property->getDomain()->get(0)->getUri() ==$class->getUri() );
		$property->delete();
		$property2->delete();
	}
	
    public function testSearchInstances() {

        $propertyClass = new core_kernel_classes_Class(RDF_PROPERTY);

        $propertyFilter = array(
            PROPERTY_IS_LG_DEPENDENT => GENERIS_TRUE
        );
        $options = array('like' => false, 'recursive' => false);
        $languagesDependantProp = $propertyClass->searchInstances($propertyFilter, $options);

        $found = count($languagesDependantProp);
        $this->assertTrue($found > 0);

        $propertyFilter = array(
            PROPERTY_IS_LG_DEPENDENT => GENERIS_TRUE,
            RDF_TYPE => RDF_PROPERTY
        );
        $languagesDependantProp = $propertyClass->searchInstances($propertyFilter, $options);
        $nfound = count($languagesDependantProp);
        $this->assertTrue($nfound > 0);
        $this->assertEquals($found, $nfound);

        $userClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User');
        $user1 = $userClass->createInstance('user1');
        $user1->setPropertyValue(new core_kernel_classes_Property('prop1'), 'toto');
        $user1->setPropertyValue(new core_kernel_classes_Property('prop2'), 'titi');
        $user1->setPropertyValue(new core_kernel_classes_Property('prop3'), 'Weeman');
        $user2 = $userClass->createInstance('user2');
        $user2->setPropertyValue(new core_kernel_classes_Property('prop1'), 'toto');
        $user2->setPropertyValue(new core_kernel_classes_Property('prop2'), 'titi');
        $user2->setPropertyValue(new core_kernel_classes_Property('prop3'), 'G-girl');
        $user3 = $userClass->createInstance('user3');
        $user3->setPropertyValue(new core_kernel_classes_Property('prop1'), 'toto');
        $user3->setPropertyValue(new core_kernel_classes_Property('prop2'), 'titi');
        $user3->setPropertyValue(new core_kernel_classes_Property('prop3'), 'Alpha');

        $propertyFilter = array(
            'prop1' => 'toto'
        );
        $options = array('like' => false, 'recursive' => false, 'offset' => 0, 'limit' => 2); //User 2 & 3
        $languagesDependantProp = $userClass->searchInstances(array(), $options);
        $nfound = count($languagesDependantProp);
        $this->assertEquals($nfound, 2);
        
        $options = array('order' => 'prop3', 'orderdir' => 'ASC');
        $result = $userClass->searchInstances(array(), $options);
        
        $this->assertEquals($user3->getUri(), key($result)); next($result);
        $this->assertEquals($user2->getUri(), key($result)); next($result);
        $this->assertEquals($user1->getUri(), key($result));
        
        $options = array_merge($options, array('orderdir' => 'DESC'));
        $result = $userClass->searchInstances($propertyFilter, $options);
        $this->assertEquals($user1->getUri(), key($result)); next($result);
        $this->assertEquals($user2->getUri(), key($result)); next($result);
        $this->assertEquals($user3->getUri(), key($result));
        
        $user1->delete();
        $user2->delete();
        $user3->delete();
    }
    
    //Test search instances with a model shared between smooth and hard implentation
    public function testSearchInstancesMultipleImpl()
    {
        $clazz = new core_kernel_classes_Class(RDFS_CLASS);
        $sub1Clazz = $clazz->createSubClass();
        $sub1ClazzInstance = $sub1Clazz->createInstance('test case instance');
        $sub2Clazz = $sub1Clazz->createSubClass();
        $sub2ClazzInstance = $sub2Clazz->createInstance('test case instance');
        $sub3Clazz = $sub2Clazz->createSubClass();
        $sub3ClazzInstance = $sub3Clazz->createInstance('test case instance');
        
        $options = array(
            'recursive'				=> true,
            'append'				=> true,
            'createForeigns'		=> true,
            'referencesAllTypes'	=> true,
            'rmSources'				=> true
        );
        
        //Test the search instances on the smooth impl
        $propertyFilter = array(
            RDFS_LABEL => 'test case instance'
        );
        $instances = $clazz->searchInstances($propertyFilter, array('recursive'=>true));
        $this->assertTrue(array_key_exists($sub1ClazzInstance->getUri(), $instances));
        $this->assertTrue(array_key_exists($sub2ClazzInstance->getUri(), $instances));
        $this->assertTrue(array_key_exists($sub3ClazzInstance->getUri(), $instances));

        //clean data test
        foreach($sub1Clazz->getInstances(true) as $instance){ $instance->delete(); }
        $sub1Clazz->delete(true);
        $sub2Clazz->delete(true);
        $sub3Clazz->delete(true);
    }
	
	//Test the function getInstancesPropertyValues of the class Class with literal properties
	public function testGetInstancesPropertyValuesWithLiteralProperties () {
		// create a class
		$class = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
		$subClass = $class->createSubClass('GetInstancesPropertyValuesClass', 'GetInstancesPropertyValues_Class');
		// create a first property for this class
		$p1 = core_kernel_classes_ClassFactory::createProperty($subClass, 'GetInstancesPropertyValues_Property1', 'GetInstancesPropertyValues_Property1', false, LOCAL_NAMESPACE. "#P1");
		$p1->setRange(new core_kernel_classes_Class(RDFS_LITERAL));
		// create a second property for this class
		$p2 = core_kernel_classes_ClassFactory::createProperty($subClass, 'GetInstancesPropertyValues_Property2', 'GetInstancesPropertyValues_Property2', false, LOCAL_NAMESPACE."#P2");
		$p2->setRange(new core_kernel_classes_Class(RDFS_LITERAL));
		// create a second property for this class
		$p3 = core_kernel_classes_ClassFactory::createProperty($subClass, 'GetInstancesPropertyValues_Property3', 'GetInstancesPropertyValues_Property3', false, LOCAL_NAMESPACE."#P3");
		$p2->setRange(new core_kernel_classes_Class(RDFS_LITERAL));
		// $i1
		$i1 = $subClass->createInstance("i1", "i1");
		$i1->setPropertyValue($p1, "p11 litteral");
		$i1->setPropertyValue($p2, "p21 litteral");
		$i1->setPropertyValue($p3, "p31 litteral");
		$i1->getLabel();
		// $i2
		$i2 = $subClass->createInstance("i2", "i2");
		$i2->setPropertyValue($p1, "p11 litteral");
		$i2->setPropertyValue($p2, "p22 litteral");
		$i2->setPropertyValue($p3, "p31 litteral");
		$i2->getLabel();
		
		// Search * P1 for P1=P11 litteral
		// Expected 2 results, but 1 possibility
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => "p11 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters);
		$this->assertEquals(count($result), 2);
		$this->assertTrue (in_array("p11 litteral", $result));
		
		// Search * P1 for P1=P11 litteral WITH DISTINCT options
		// Expected 1 results, and 1 possibility
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => "p11 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, array ("distinct" => true));
		$this->assertEquals(count($result), 1);
		$this->assertTrue (in_array("p11 litteral", $result));
		
		// Search * P2 for P1=P11 litteral WITH DISTINCT options
		// Expected 2 results, and 2 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => "p11 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, array ("distinct" => true));
		$this->assertEquals(count($result), 2);
		$this->assertTrue (in_array("p21 litteral", $result));
		$this->assertTrue (in_array("p22 litteral", $result));
		
		// Search * P2 for P1=P12 litteral WITH DISTINCT options
		// Expected 0 results, and 0 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => "p12 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, array ("distinct" => true));
		$this->assertEquals(count($result), 0);
		
		// Search * P1 for P2=P21 litteral WITH DISTINCT options
		// Expected 1 results, and 1 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P2" => "p21 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, array ("distinct" => true));
		$this->assertEquals(count($result), 1);
		$this->assertTrue (in_array("p11 litteral", $result));
		
		// Search * P1 for P2=P22 litteral WITH DISTINCT options
		// Expected 1 results, and 1 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P2" => "p22 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, array ("distinct" => true));
		$this->assertEquals(count($result), 1);
		$this->assertTrue (in_array("p11 litteral", $result));
		
		// Search * P3 for P1=P11 & P2=P21 litteral WITH DISTINCT options
		// Expected 1 results, and 1 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => "p11 litteral"
			, LOCAL_NAMESPACE. "#P2" => "p21 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p3, $propertyFilters, array ("distinct" => true));
		$this->assertEquals(count($result), 1);
		$this->assertTrue (in_array("p31 litteral", $result));
		
		// Search * P2 for P1=P11 & P3=P31 litteral WITH DISTINCT options
		// Expected 2 results, and 2 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => "p11 litteral"
			, LOCAL_NAMESPACE. "#P3" => "p31 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, array ("distinct" => true));
		$this->assertEquals(count($result), 2);
		$this->assertTrue (in_array("p21 litteral", $result));
		$this->assertTrue (in_array("p22 litteral", $result));
		
		// Clean the model		
		$i1->delete();
		$i2->delete();
		
		$p1->delete();
		$p2->delete();
		$p3->delete();
		
		$subClass->delete();
	}
	
	//Test the function getInstancesPropertyValues of the class Class  with resource properties
	public function testGetInstancesPropertyValuesWithResourceProperties () {
		// create a class
		$class = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
		$subClass = $class->createSubClass('GetInstancesPropertyValuesClass', 'GetInstancesPropertyValues_Class');
		// create a first property for this class
		$p1 = core_kernel_classes_ClassFactory::createProperty($subClass, 'GetInstancesPropertyValues_Property1', 'GetInstancesPropertyValues_Property1', false, LOCAL_NAMESPACE. "#P1");
		$p1->setRange(new core_kernel_classes_Class(GENERIS_BOOLEAN));
		// create a second property for this class
		$p2 = core_kernel_classes_ClassFactory::createProperty($subClass, 'GetInstancesPropertyValues_Property2', 'GetInstancesPropertyValues_Property2', false, LOCAL_NAMESPACE. "#P2");
		$p1->setRange(new core_kernel_classes_Class(GENERIS_BOOLEAN));
		// create a second property for this class
		$p3 = core_kernel_classes_ClassFactory::createProperty($subClass, 'GetInstancesPropertyValues_Property3', 'GetInstancesPropertyValues_Property3', false, LOCAL_NAMESPACE. "#P3");
		$p1->setRange(new core_kernel_classes_Class(RDFS_LITERAL));
		// $i1
		$i1 = $subClass->createInstance("i1", "i1");
		$i1->setPropertyValue($p1, GENERIS_TRUE);
		$i1->setPropertyValue($p2, new core_kernel_classes_Class(GENERIS_TRUE));
		$i1->setPropertyValue($p3, "p31 litteral");
		$i1->getLabel();
		// $i2
		$i2 = $subClass->createInstance("i2", "i2");
		$i2->setPropertyValue($p1, GENERIS_TRUE);
		$i2->setPropertyValue($p2, new core_kernel_classes_Class(GENERIS_FALSE));
		$i2->setPropertyValue($p3, "p31 litteral");
		$i2->getLabel();
		
		// Search * P1 for P1=GENERIS_TRUE
		// Expected 2 results, but 1 possibility
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => GENERIS_TRUE
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters);
		$this->assertEquals(count($result), 2);
		foreach ($result as $property) {
			$this->assertTrue($property->getUri() == GENERIS_TRUE);
		}
		// Search * P1 for P1=GENERIS_TRUE WITH DISTINCT options
		// Expected 1 results, and 1 possibility
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => GENERIS_TRUE
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, array ("distinct" => true));
		$this->assertEquals(count($result), 1);
		$this->assertTrue($result[0]->getUri() == GENERIS_TRUE);
		
		// Search * P2 for P1=GENERIS_TRUE WITH DISTINCT options
		// Expected 2 results, and 2 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => GENERIS_TRUE
		);
		$result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, array ("distinct" => true));
		$this->assertEquals(count($result), 2);
		foreach ($result as $property){
			$this->assertTrue ($property->getUri() == GENERIS_TRUE || $property->getUri() == GENERIS_FALSE);
		}
		
		// Search * P2 for P1=NotExistingProperty litteral WITH DISTINCT options
		// Expected 1 results, and 1 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => "NotExistingProperty"
		);
		$result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, array ("distinct" => true));
		$this->assertEquals(count($result), 0);
		
		// Search * P1 for P2=GENERIS_TRUE litteral WITH DISTINCT options
		// Expected 1 results, and 1 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P2" => GENERIS_TRUE
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, array ("distinct" => true));
		$this->assertEquals(count($result), 1);
		$this->assertTrue($result[0]->getUri() == GENERIS_TRUE);
		
		// Search * P1 for P2=GENERIS_FALSE WITH DISTINCT options
		// Expected 1 results, and 1 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P2" => GENERIS_FALSE
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, array ("distinct" => true));
		$this->assertEquals(count($result), 1);
		$this->assertTrue($result[0]->getUri() == GENERIS_TRUE);
		
		// Search * P3 for P1=GENERIS_TRUE & P2=GENERIS_TRUE litteral WITH DISTINCT options
		// Expected 1 results, and 1 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => GENERIS_TRUE
			, LOCAL_NAMESPACE. "#P2" => GENERIS_TRUE
		);
		$result = $subClass->getInstancesPropertyValues($p3, $propertyFilters, array ("distinct" => true));
		$this->assertEquals(count($result), 1);
		$this->assertTrue (in_array("p31 litteral", $result));

		// Search * P2 for P1=P11 & P3=P31 litteral WITH DISTINCT options
		// Expected 2 results, and 2 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => GENERIS_TRUE
			, LOCAL_NAMESPACE. "#P3" => "p31 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, array ("distinct" => true));
		$this->assertEquals(count($result), 2);
		foreach ($result as $property){
			$this->assertTrue ($property->getUri() == GENERIS_TRUE || $property->getUri() == GENERIS_FALSE);
		}
		
		// Clean the model		
		$i1->delete();
		$i2->delete();
		
		$p1->delete();
		$p2->delete();
		$p3->delete();
		
		$subClass->delete();
	}
	
	public function testDeleteInstances(){
		$taoClass = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
		$creativeWorkClass = $taoClass->createSubClass('Creative Work');
		$authorProperty = $taoClass->createProperty('Author');
		$relatedWorksProperty = $creativeWorkClass->createProperty('Related Works');
		
		$bookLotr = $creativeWorkClass->createInstance('The Lord of The Rings (book)');
		$bookLotr->setPropertyValue($authorProperty, 'J.R.R. Tolkien');
		
		$movieLotr = $creativeWorkClass->createInstance('The Lord of The Rings (movie)');
		$movieLotr->setPropertyValue($authorProperty, 'Peter Jackson');
		
		$movieLotr->setPropertyValue($relatedWorksProperty, $bookLotr);
		$bookLotr->setPropertyValue($relatedWorksProperty, $movieLotr);
		
		$movieMinorityReport = $creativeWorkClass->createInstance('Minority Report');
		$movieMinorityReport->setPropertyValue($authorProperty, 'Steven Spielberg');

		$this->assertEquals(count($creativeWorkClass->getInstances()), 3);
		
		// delete the LOTR movie with its references.
		$relatedWorks = $bookLotr->getPropertyValuesCollection($relatedWorksProperty);
		$this->assertEquals($relatedWorks->sequence[0]->getLabel(), 'The Lord of The Rings (movie)');
		$creativeWorkClass->deleteInstances(array($movieLotr), true);
		$relatedWorks = $bookLotr->getPropertyValues($relatedWorksProperty);
		$this->assertEquals(count($relatedWorks), 0);
		
		// Only 1 resource deleted ?
		$this->assertFalse($movieLotr->exists());
		$this->assertTrue($bookLotr->exists());
		$this->assertTrue($movieMinorityReport->exists());
		
		// Remove the rest.
		$creativeWorkClass->deleteInstances(array($bookLotr, $movieMinorityReport));
		$this->assertEquals(count($creativeWorkClass->getInstances()), 0);
		$this->assertFalse($bookLotr->exists());
		$this->assertFalse($movieMinorityReport->exists());
		
		$this->assertTrue($authorProperty->delete());
		$this->assertTrue($relatedWorksProperty->delete());
		
		$creativeWorkClass->delete(true);
	}
}
?>