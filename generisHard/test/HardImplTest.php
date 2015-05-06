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
use oat\generisHard\models\proxy\PropertyProxy;
use oat\generisHard\models\proxy\PersistenceProxy;
use oat\generisHard\models\hardapi\ResourceReferencer;
use oat\generisHard\models\proxy\ResourceProxy;
use oat\generisHard\models\hardsql\Clazz;
use oat\generisHard\models\hardsql\Resource;
use oat\generisHard\models\hardapi\Utils;

class HardImplTest extends GenerisPhpUnitTestRunner {
	
	/**
	 * 
	 * @var core_kernel_classes_Class
	 */
	protected $targetSubjectClass = null;
	
	/**
	 *
	 * @var core_kernel_classes_Class
	 */
	protected $targetSubjectSubClass = null;
	
	/**
	 *
	 * @var core_kernel_classes_Class
	 */
	protected $targetWorkClass = null;
	
	/**
	 *
	 * @var core_kernel_classes_Class
	 */
	protected $targetMovieClass = null;
	
	/**
	 *
	 * @var core_kernel_classes_Class
	 */
	protected $targetSongClass = null;
	
	/**
	 *
	 * @var core_kernel_classes_Class
	 */
	protected $taoClass = null;
	
	/**
	 *
	 * @var core_kernel_classes_Property
	 */
	protected $targetAuthorProperty = null;
	
	/**
	 *
	 * @var core_kernel_classes_Property
	 */
	protected $targetProducerProperty = null;
	
	/**
	 *
	 * @var core_kernel_classes_Property
	 */
	protected $targetActorsProperty = null;
	
	/**
	 *
	 * @var core_kernel_classes_Property
	 */
	protected $targetRelatedMoviesProperty = null;
	
	
	
	protected function tearDown(){
	    
	    $this->clean();
	}
	
	
	protected function setUp(){
	    
	    GenerisPhpUnitTestRunner::initTest();
	    $this->installExtension('generisHard');
		$this->createContextOfThetest();

	}
	
	public function createContextOfThetest(){
	    
		// ----- Top Class : TaoSubject
		$subjectClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		// Create a new subject class for the unit test
		$this->targetSubjectClass = $subjectClass->createSubClass ("Sub Subject Class (Unit Test)");
		// Add a custom property to the newly created class
		
		// Add an instance to this subject class
		$this->subject1 = $this->targetSubjectClass->createInstance ("Sub Subject (Unit Test)");
		
		// Create a new subject sub class to the previous sub class
		$this->targetSubjectSubClass = $this->targetSubjectClass->createSubClass ("Sub Sub Subject Class (Unit Test)");
		// Add an instance to this sub subject class
		$this->subject2 = $this->targetSubjectSubClass->createInstance ("Sub Sub Subject (Unit Test)");
		
		// ----- Top Class : Work
		// Create a class and test its instances & properties.
		$this->taoClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#TAOObject');
		$this->targetWorkClass = $this->taoClass->createSubClass('Work', 'The Work class');
		// Add properties to the Work class.
		$this->targetAuthorProperty = $this->targetWorkClass->createProperty('Author', 'The author of the work.');
		$literalClass = new core_kernel_classes_Class(RDFS_LITERAL);
		$this->targetAuthorProperty->setRange($literalClass);
		
		// Create the Movie class that extends the Work class. 
		$this->targetMovieClass = $this->targetWorkClass->createSubClass('Movie', 'The Movie class');
		$this->targetMovieClass = new core_kernel_classes_Class($this->targetMovieClass->getUri());
		
		// Add properties to the Movie class.
		$this->targetProducerProperty = $this->targetMovieClass->createProperty('Producer', 'The producer of the movie.');
		$this->targetProducerProperty->setRange($literalClass);
		$this->targetProducerProperty->setMultiple(true);
		$this->targetActorsProperty = $this->targetMovieClass->createProperty('Actors', 'The actors playing in the movie.');
		$this->targetActorsProperty->setRange($literalClass);
		$this->targetActorsProperty->setMultiple(true);
		$this->targetRelatedMoviesProperty = $this->targetMovieClass->createProperty('Related Movies', 'Movies related to the movie.');
		$this->targetRelatedMoviesProperty->setRange($this->targetMovieClass);
		$this->targetRelatedMoviesProperty->setMultiple(true);
		
		//$this->generisUser = core_kernel_users_Service::singleton()->addUser('testCaseUser','testCasepass');
		
	}

	public function clean (){
	
	    $referencer = ResourceReferencer::singleton();
	    $classProxy = ClassProxy::singleton();
	    $switcher = new Switcher();
	
	    // Remove the resources
	    foreach ($this->targetSubjectClass->getInstances() as $instance){
	        $instance->delete();
	        $this->assertFalse($referencer->isResourceReferenced($instance));
	    }
	    foreach ($this->targetSubjectSubClass->getInstances() as $instance){
	        $instance->delete();
	        $this->assertFalse($referencer->isResourceReferenced($instance));
	    }
	
	    // delete the sub subject class (will be internally unhardified)
	    $this->targetSubjectClass->delete(true);
	    $this->assertFalse($referencer->isClassReferenced($this->targetSubjectClass));
	    $this->assertIsA($classProxy->getImpToDelegateTo($this->targetSubjectClass), 'core_kernel_persistence_smoothsql_Class');
	
	    // delete the sub sub subject class (will be internally unhardified)
	    $this->targetSubjectSubClass->delete(true);
	    $this->assertFalse($referencer->isClassReferenced($this->targetSubjectSubClass));
	    $this->assertIsA($classProxy->getImpToDelegateTo($this->targetSubjectSubClass), 'core_kernel_persistence_smoothsql_Class');
	
	    $this->assertTrue($this->targetWorkClass->delete(true));
	    $this->assertTrue($this->targetMovieClass->delete(true));
	    if($this->targetSongClass!=null){
	        $this->targetSongClass->delete(true);
	    }
	
	
	    //$this->assertFalse($referencer->isClassReferenced($this->targetWorkClass));
	    //$this->assertFalse($referencer->isClassReferenced($this->targetMovieClass));
	    //$this->assertFalse($referencer->isClassReferenced($this->targetSongClass));
	    //$this->assertFalse($this->targetWorkClass->exists());
	    //$this->assertFalse($this->targetWorkClass->exists());
	    //$this->assertFalse($this->targetSongClass->exists());
	}	
	private function hardify (){
		$switcher = new Switcher();
		$switcher->hardify($this->targetSubjectClass, array(
			'recursive'	=> true,
		));
		
		$switcher->hardify($this->targetWorkClass, array(
			'recursive' => true,
		));
	}
	
	public function testContext(){
	    $this->assertEquals (count($this->targetSubjectClass->getInstances ()), 1);
	    $this->assertEquals (count($this->targetSubjectSubClass->getInstances ()), 1);
	    
	    $this->assertEquals (count($this->targetSubjectClass->getInstances ()), 1);
	    // If get instances in the sub classes of the targetSubjectClass, we should get 2 instances
	    $this->assertEquals (count($this->targetSubjectClass->getInstances (true)), 2);
	    $this->assertTrue($this->targetMovieClass->isSubClassOf($this->targetWorkClass));
	    $this->assertEquals(count($this->targetWorkClass->getSubClasses()), 1);
	    $this->assertTrue($this->targetProducerProperty->isMultiple());
	}
	
	public function testCreateIndex(){
	    $this->hardify();
	    Switcher::createIndex(array($this->targetAuthorProperty->getUri()));
	    $sm = core_kernel_classes_DbWrapper::singleton()->getSchemaManager();
	    $shortName = Utils::getShortName($this->targetWorkClass);
	    $indexes = $sm->getTableIndexes('_'.$shortName);
	    
	    $this->assertFalse(empty($indexes));
        $indexToFind = 'idx__'.$shortName.'_'.Utils::getShortName($this->targetAuthorProperty); 
        $this->assertTrue(array_key_exists($indexToFind,$indexes));

	}
	
	public function testHardSwitchOK(){
		$this->hardify();
		// Test that resource are now available from the hard sql implementation
		$classProxy = ClassProxy::singleton();
		$propertyProxy = PropertyProxy::singleton();
		$this->assertIsA($classProxy->getImpToDelegateTo($this->targetSubjectClass), 'oat\generisHard\models\hardsql\Clazz');
		$this->assertIsA($classProxy->getImpToDelegateTo($this->targetSubjectSubClass), 'oat\generisHard\models\hardsql\Clazz');
		$this->assertIsA($classProxy->getImpToDelegateTo($this->targetWorkClass), 'oat\generisHard\models\hardsql\Clazz');
		$this->assertIsA($classProxy->getImpToDelegateTo($this->targetMovieClass), 'oat\generisHard\models\hardsql\Clazz');
		$this->assertIsA($propertyProxy->getImpToDelegateTo($this->targetActorsProperty), 'oat\generisHard\models\hardsql\Property');
		$this->assertIsA($propertyProxy->getImpToDelegateTo($this->targetAuthorProperty), 'oat\generisHard\models\hardsql\Property');
		$this->assertIsA($propertyProxy->getImpToDelegateTo($this->targetProducerProperty), 'oat\generisHard\models\hardsql\Property');
		$this->assertIsA($propertyProxy->getImpToDelegateTo($this->targetRelatedMoviesProperty), 'oat\generisHard\models\hardsql\Property');
	}
	
	public function testHardModel(){
	    $this->hardify();
		$referencer = ResourceReferencer::singleton();
		$propertyProxy = PropertyProxy::singleton();
		$proxy = ResourceProxy::singleton();
		
		$domainProperty = new core_kernel_classes_Property(RDFS_DOMAIN);
		$rangeProperty = new core_kernel_classes_Property(RDFS_RANGE);
		$literalClass = new core_kernel_classes_Class(RDFS_LITERAL);
		$subClassOfProperty = new core_kernel_classes_Property(RDFS_SUBCLASSOF);
		
		$this->assertTrue($this->targetActorsProperty->exists());
		$this->assertTrue($this->targetMovieClass->exists());
		
		$this->assertTrue($this->targetWorkClass->isSubclassOf($this->taoClass));
		$this->assertTrue($this->targetWorkClass->getOnePropertyValue($subClassOfProperty)->getUri() == $this->taoClass->getUri());
		
		$this->assertTrue($referencer->isClassReferenced($this->targetWorkClass));
		$this->assertFalse(is_a($proxy->getImpToDelegateTo($this->targetWorkClass), 'core_kernel_persistence_smoothsql_Class'));
		$this->assertFalse(is_a($proxy->getImpToDelegateTo($this->targetMovieClass), 'core_kernel_persistence_smoothsql_Class'));
		
		$this->assertTrue($this->targetAuthorProperty->getOnePropertyValue($domainProperty)->getUri() == $this->targetWorkClass->getUri());
		$this->assertTrue($this->targetAuthorProperty->getOnePropertyValue($rangeProperty)->getUri() == RDFS_LITERAL);
		
		$this->assertTrue($this->targetMovieClass->isSubclassOf($this->targetWorkClass));
		$this->assertTrue($this->targetMovieClass->getOnePropertyValue($subClassOfProperty)->getUri() == $this->targetWorkClass->getUri());
		$this->assertTrue($referencer->isClassReferenced($this->targetMovieClass));
		
		$this->assertTrue($this->targetProducerProperty->getOnePropertyValue($domainProperty)->getUri() == $this->targetMovieClass->getUri());
		$this->assertTrue($this->targetProducerProperty->getOnePropertyValue($rangeProperty)->getUri() == RDFS_LITERAL);
		
		$this->assertTrue($this->targetActorsProperty->getOnePropertyValue($domainProperty)->getUri() == $this->targetMovieClass->getUri());
		$this->assertTrue($this->targetActorsProperty->getOnePropertyValue($rangeProperty)->getUri() == RDFS_LITERAL);
		
		$this->assertTrue($this->targetRelatedMoviesProperty->getOnePropertyValue($domainProperty)->getUri() == $this->targetMovieClass->getUri());
		$this->assertTrue($this->targetRelatedMoviesProperty->getOnePropertyValue($rangeProperty)->getUri() == $this->targetMovieClass->getUri());
		
		$parentClasses = $this->targetMovieClass->getParentClasses();
		$this->assertEquals(1, count($parentClasses));
		$this->assertTrue(array_key_exists($this->targetWorkClass->getUri(), $parentClasses));
		
		$prop = new core_kernel_classes_Property($this->targetRelatedMoviesProperty);
		$this->assertTrue($prop->isMultiple());
	}
	
	
	
	public function testHardGetInstances (){
		$this->hardify();
		// Get the hardified instance from the hard sql imlpementation
		$this->assertEquals(count($this->targetSubjectClass->getInstances()), 1);
		$this->assertEquals(count($this->targetSubjectSubClass->getInstances()), 1);
		$this->assertEquals(count($this->targetSubjectClass->getInstances(true)), 2);
		$this->assertEquals(count($this->targetWorkClass->getInstances(true)), 0);
		$this->assertEquals(count($this->targetMovieClass->getInstances()), 0);
		
		$userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
		$users = $userClass->getInstances();
		$this->assertEquals(count($users), 1);
		$user = $userClass->createInstance('toto','toto');
		
		$switcher = new Switcher();
		$switcher->hardify($userClass, array(
		    'recursive'	=> false,
		));
		$users = $userClass->getInstances();
		$this->assertEquals(count($users), 2);
		
		
		$switcher->unhardify($userClass, array(
		    'recursive'	=> false,
		));
		$this->assertTrue($user->delete());
	}
	
	
	public function testHardCreateInstance() {
	    $this->hardify();
		$referencer = ResourceReferencer::singleton();
		$proxy = ResourceProxy::singleton();
		
		$labelProperty = new core_kernel_classes_Property(RDFS_LABEL);
		$valueProperty = new core_kernel_classes_Property(RDF_VALUE);
		
		// Create instance with the hard sql implementation
		$subject = $this->targetSubjectClass->createInstance("Hard Sub Subject (Unit Test)");
		$this->assertTrue($referencer->isResourceReferenced($subject));
		$this->assertIsA($proxy->getImpToDelegateTo($subject), 'oat\generisHard\models\hardsql\Resource');
		$this->assertEquals(count($this->targetSubjectClass->getInstances()), 2);

		$subSubject = $this->targetSubjectSubClass->createInstance("Hard Sub Sub Subject (Unit Test)");
		$this->assertTrue($referencer->isResourceReferenced($subSubject));
		$this->assertIsA($proxy->getImpToDelegateTo($subSubject), 'oat\generisHard\models\hardsql\Resource');
		$this->assertEquals(count($this->targetSubjectSubClass->getInstances()), 2);
		$this->assertEquals(count($this->targetSubjectClass->getInstances(true)), 4);
		
		$work1Label = 'Mona Lisa';
		$work1Author = 'Leonardo da Vinci';
		$work1 = $this->targetWorkClass->createInstance($work1Label, 'Mona Lisa, a half-length portait of a woman');
		$work1->setPropertyValue($this->targetAuthorProperty, $work1Author);
		$this->assertTrue($work1->exists());
		$this->assertTrue($referencer->isResourceReferenced($work1));
		$this->assertIsA($proxy->getImpToDelegateTo($work1), 'oat\generisHard\models\hardsql\Resource');
		$this->assertEquals($work1->getLabel(), $work1Label);
		
		
		// Test property (that exists) values for $work1.
		$this->assertEquals($work1->getUniquePropertyValue($labelProperty)->literal, $work1Label);
		$work1Labels = $work1->getPropertyValues($labelProperty);
		$this->assertEquals(count($work1Labels), 1);
		$this->assertEquals($work1Labels[0], $work1Label);
		$work1Labels = $work1->getPropertyValues($labelProperty, array('one' => true));
		$this->assertEquals(count($work1Labels), 1);
		$this->assertEquals($work1Labels[0], $work1Label);
		$work1Labels = $work1->getPropertyValues($labelProperty, array('last', true));
		$this->assertEquals(count($work1Labels), 1);
		$this->assertEquals($work1Labels[0], $work1Label);
		$literal = $work1->getOnePropertyValue($this->targetAuthorProperty);
		$this->assertIsA($literal, 'core_kernel_classes_Literal');
		$this->assertEquals($literal->literal, $work1Author);
		$work1PropertiesValues = $work1->getPropertiesValues(array($labelProperty, $this->targetAuthorProperty));
		$this->assertEquals(count($work1PropertiesValues), 2);
		$this->assertTrue(array_key_exists(RDFS_LABEL, $work1PropertiesValues));
		$this->assertTrue(array_key_exists($this->targetAuthorProperty->getUri(), $work1PropertiesValues));
		$this->assertEquals($work1->getUsedLanguages($labelProperty), array(DEFAULT_LANG));
		
		// Test property (that doesn't exist) values for $work1.
		$unknownProperty = new core_kernel_classes_Property('unknown property');
		$unknownProperty2 = new core_kernel_classes_Property('unknown property 2');

		$this->setExpectedException('core_kernel_classes_EmptyProperty');
		$work1->getUniquePropertyValue($unknownProperty);
		


		
		$work1Unknown = $work1->getPropertyValues($unknownProperty);
		$this->assertEquals($work1Unknown, array());
		$work1Unknown = $work1->getPropertyValues($unknownProperty, array('one' => true));
		$this->assertEquals($work1Unknown, array());
		$work1Unknown = $work1->getPropertyValues($unknownProperty, array('last' => true));
		$this->assertEquals($work1Unknown, array());
		$literal = $work1->getOnePropertyValue($unknownProperty);
		$this->assertNull($literal);
		$work1PropertiesValues = $work1->getPropertiesValues(array($unknownProperty, $unknownProperty2));
		$this->assertTrue(count($work1PropertiesValues) == 0);
		$work1PropertiesValues = $work1->getPropertiesValues(array($labelProperty, $unknownProperty));
		$this->assertTrue(array_key_exists(RDFS_LABEL, $work1PropertiesValues));
		$this->assertTrue(count($work1PropertiesValues) == 1);
	}
	
	
	private function createData(){
	    
	    $movieClass = $this->targetMovieClass;
	    $workClass = $this->targetWorkClass;
	    $authorProperty = $this->targetAuthorProperty;
	    $producerProperty = $this->targetProducerProperty;
	    $actorsProperty = $this->targetActorsProperty;
	    $relatedMoviesProperty = $this->targetRelatedMoviesProperty;
	    $labelProperty = new core_kernel_classes_Property(RDFS_LABEL);
	    
	    
	    $work1Label = 'Mona Lisa';
	    $work1Author = 'Leonardo da Vinci';
	    $work1 = $this->targetWorkClass->createInstance($work1Label, 'Mona Lisa, a half-length portait of a woman');
	    $work1->setPropertyValue($this->targetAuthorProperty, $work1Author);
	    
	    $bookOfTheRings = $workClass->createInstance('The Lord of the Rings');
	    $bookOfTheRings->setPropertyValue($authorProperty, 'John Ronald Reuel Tolkien');
	    
	    // ---- MOVIES: The Lord of The Rings
	    $lordOfTheRings = $movieClass->createInstance('The Lord of the Rings');
	    $lordOfTheRings->setPropertyValueByLg($labelProperty, 'Le Seigneur des Anneaux', 'FR-be');
	    $lordOfTheRings->setPropertyValue($authorProperty, 'Peter Jackson');
	    $lordOfTheRings->setPropertyValue($producerProperty, 'Peter Jackson');
	    $lordOfTheRings->setPropertyValue($producerProperty, 'Barrie M. Osborne');
	    $lordOfTheRings->setPropertyValue($producerProperty, 'Fran Walsh');
	    $lordOfTheRings->setPropertyValue($producerProperty, 'Mark Ordersky');
	    $lordOfTheRings->setPropertyValue($producerProperty, 'Tim Sanders');
	    $lordOfTheRings->setPropertyValue($actorsProperty, 'Viggo Mortensen');
	    $lordOfTheRings->setPropertyValue($actorsProperty, 'Elijah Wood');
	    $lordOfTheRings->setPropertyValue($actorsProperty, 'Sean Bean');
	    $lordOfTheRings->setPropertyValue($actorsProperty, 'Dominic Monaghan');
	    $lordOfTheRings->setPropertyValue($actorsProperty, 'Sean Astin');
	    $lordOfTheRings->setPropertyValue($actorsProperty, 'Ian McKellen');
	    $lordOfTheRings->setPropertyValue($actorsProperty, 'John Rhys-Davies');
	    $lordOfTheRings->setPropertyValue($actorsProperty, 'Orlando Bloom');
	    $lordOfTheRings->setPropertyValue($actorsProperty, 'Billy Boyd');
	    
	    
	    // ---- MOVIES: The Hobbit
	    $theHobbit = $movieClass->createInstance('The Hobbit: An Unexpected Journey');
	    $theHobbit->setPropertyValue($authorProperty, 'Peter Jackson');
	    $theHobbit->setPropertyValue($producerProperty, 'Peter Jackson');
	    $theHobbit->setPropertyValue($producerProperty, 'Fran Walsh');
	    $theHobbit->setPropertyValue($producerProperty, 'Carolynne Cunningham');
	    $theHobbit->setPropertyValue($producerProperty, 'Zane Weiner');
	    $theHobbit->setPropertyValue($actorsProperty, 'Martin Freeman');
	    $theHobbit->setPropertyValue($actorsProperty, 'Ian McKellen');
	    $theHobbit->setPropertyValue($actorsProperty, 'Richard Armitage');
	    $theHobbit->setPropertyValue($actorsProperty, 'Ian Holm');
	    $theHobbit->setPropertyValue($actorsProperty, 'Andy Serkis');
	    $theHobbit->setPropertyValue($actorsProperty, 'Benedict Cumberbatch');
	    $theHobbit->setPropertyValue($actorsProperty, 'Graham McTavish');
	    $theHobbit->setPropertyValue($actorsProperty, 'Ken Stott');
	    $theHobbit->setPropertyValue($relatedMoviesProperty, $lordOfTheRings);
	    
	     
	}
	
	
	
	public function testHardSearchInstances(){
	    $this->hardify();
		$movieClass = $this->targetMovieClass;
		$workClass = $this->targetWorkClass;
		$authorProperty = $this->targetAuthorProperty;
		$producerProperty = $this->targetProducerProperty;
		$actorsProperty = $this->targetActorsProperty;
		$relatedMoviesProperty = $this->targetRelatedMoviesProperty;
		$labelProperty = new core_kernel_classes_Property(RDFS_LABEL);
		
        $this->createData();		
		// Works with a rdfs:label which is 'The Lord of the ...'.
		$propertyFilters = array($labelProperty->getUri() => 'The Lord of the');
		$instances = $workClass->searchInstances($propertyFilters, array('like' => true));
		$this->assertEquals(count($instances), 1);
		$this->assertEquals($instances[key($instances)]->getLabel(), 'The Lord of the Rings');
		

		
		// Works with a rdfs:label which is 'The Lord of the ...' (recursive).
		$propertyFilters = array($labelProperty->getUri() => 'The Lord of the');
		$instances = $workClass->searchInstances($propertyFilters, array('like' => true, 'recursive' => 1));
		$this->assertEquals(count($instances), 2);
		$this->assertEquals($instances[key($instances)]->getLabel(), 'The Lord of the Rings'); next($instances);
		$this->assertEquals($instances[key($instances)]->getLabel(), 'The Lord of the Rings'); next($instances);
		
		$instances = $workClass->searchInstances($propertyFilters, array('like' => true, 'recursive' => 0));
		$this->assertEquals(count($instances), 1);
		$this->assertEquals($instances[key($instances)]->getLabel(), 'The Lord of the Rings');
		

		
		// Movie with rdfs:label equals to 'The Hobbit: An Unexpected Journey'.
		$propertyFilters = array($labelProperty->getUri() => 'The Hobbit: An Unexpected Journey');
		$instances = $movieClass->searchInstances($propertyFilters);
		$this->assertTrue(count($instances) == 1);
		$instance = new core_kernel_classes_Resource($instances[key($instances)]);
		$this->assertIsA($instance, 'core_kernel_classes_Resource');
		$this->assertTrue($instance->exists());
		$this->assertEquals($instance->getLabel(), 'The Hobbit: An Unexpected Journey');
		
		// Movie with rdfs:label equals to 'The Hobbit: An Unexpected Journey'
		// and mov:producer equals to 'Peter Jackson'.
		$propertyFilters = array($labelProperty->getUri() => 'The Hobbit: An Unexpected Journey',
								 $authorProperty->getUri() => 'Peter Jackson');
								 
		$instances = $movieClass->searchInstances($propertyFilters);
		$this->assertTrue(count($instances) == 1);
		$instance = new core_kernel_classes_Resource($instances[key($instances)]);
		$this->assertIsA($instance, 'core_kernel_classes_Resource');
		$this->assertTrue($instance->exists());
		
		// Same as previous one but with 'like' option set to false.
		$propertyFilters = array($labelProperty->getUri() => 'The Hobbit: An Unexpected Journey',
								 $authorProperty->getUri() => 'Peter Jackson');
								 
		$instances = $movieClass->searchInstances($propertyFilters, array('like' => false));
		$this->assertTrue(count($instances) == 1);
		$instance = new core_kernel_classes_Resource($instances[key($instances)]);
		$this->assertIsA($instance, 'core_kernel_classes_Resource');
		$this->assertTrue($instance->exists());
		
		// Movie with 'Sean Bean' produced by 'Peter Jackson'
		$propertyFilters = array($actorsProperty->getUri() => 'Sean Bean',
								 $authorProperty->getUri() => 'Peter Jackson');
								 
		$instances = $movieClass->searchInstances($propertyFilters);
		$this->assertTrue(count($instances) == 1);
		$instance = new core_kernel_classes_Resource($instances[key($instances)]);
		$this->assertIsA($instance, 'core_kernel_classes_Resource');
		$this->assertTrue($instance->exists());
		$this->assertEquals($instance->getLabel(), 'The Lord of the Rings');
		
		// Movie with 'Sean Bean' OR 'Richard Armitage' produced by 'Peter Jackson'
		$propertyFilters = array($actorsProperty->getUri() => array('Richard Armitage', 'Sean Bean'),
								 $producerProperty->getUri() => 'Peter Jackson');

		$instances = $movieClass->searchInstances($propertyFilters, array('chaining' => 'or'));
		$this->assertTrue(count($instances) == 2);
		$foundCount1 = 0;
		$foundCount2 = 0;
		foreach ($instances as $i){
			if ($i->getLabel() == 'The Hobbit: An Unexpected Journey'){
				$foundCount1++;
			}
			
			if ($i->getLabel() == 'The Lord of the Rings'){
				$foundCount2++;
			}
		}
		$this->assertEquals($foundCount1 + $foundCount2, 2);
		
		// Movie with rdfs:label equals to 'Le Seigneur des Anneaux' in the Belgian French locale.
		$propertyFilters = array($labelProperty->getUri() => 'Le Seigneur des Anneaux');
		$instances = $movieClass->searchInstances($propertyFilters, array('lang' => 'FR-be'));
		$this->assertEquals(count($instances), 1);
		
		// All Works limited to 2 results. We should have 'The Lord of The Rings' (movie + book),
		// 'The Hobbit: An Unexpected Journey' and 'Mona Lisa' in the Knowledge Base at
		// the moment.
		$propertyFilters = array();
		$instances = $workClass->searchInstances($propertyFilters, array('limit' => 3, 'recursive' => 1));
		$this->assertEquals(count($instances), 3);
		
		// Same as previous, but without limit and orderedy by author.
		$propertyFilters = array();
		$instances = $workClass->searchInstances($propertyFilters, array('recursive' => 1, 'order' => $authorProperty->getUri()));
		$this->assertEquals(count($instances), 4);
		
		// Same as previous, but with a descendent orderdir.
		$propertyFilters = array();
		$instances = $workClass->searchInstances($propertyFilters, array('order' => $authorProperty->getUri(), 'orderdir' => 'ASC'));
		$this->assertEquals(count($instances), 2);
		$this->assertEquals($instances[key($instances)]->getUniquePropertyValue($authorProperty)->literal, 'John Ronald Reuel Tolkien'); next($instances);
		$this->assertEquals($instances[key($instances)]->getUniquePropertyValue($authorProperty)->literal, 'Leonardo da Vinci'); next($instances);
		
		// Get all movies that are produced by 'Peter Jackson' ordered by rdfs:label.
		$propertyFilters = array($producerProperty->getUri() => 'Peter Jackson');
		$instances = $movieClass->searchInstances($propertyFilters, array('order' => $labelProperty->getUri()));
		$this->assertEquals(count($instances), 2);
		$this->assertEquals($instances[key($instances)]->getUniquePropertyValue($labelProperty)->literal, 'The Hobbit: An Unexpected Journey'); next($instances);
		$this->assertEquals($instances[key($instances)]->getUniquePropertyValue($labelProperty)->literal, 'The Lord of the Rings'); next($instances);
		
		// Get Lord of the Rings by Peter Jackson (produced).
		$propertyFilters = array($producerProperty->getUri() => 'Peter Jackson');
		$instances = $movieClass->searchInstances($propertyFilters, array('order' => $labelProperty->getUri()));
		$this->assertEquals(count($instances), 2);
		$this->assertEquals($instances[key($instances)]->getUniquePropertyValue($labelProperty)->literal, 'The Hobbit: An Unexpected Journey'); next($instances);
		$this->assertEquals($instances[key($instances)]->getUniquePropertyValue($labelProperty)->literal, 'The Lord of the Rings'); next($instances);
		
		// try to search a property that does not exist.
		$propertyFilters = array('http://www.unknown.com/i-do-not-exist' => 'do-not-exist');
		$instances = $movieClass->searchInstances($propertyFilters);
		$this->assertEquals(count($instances), 0);
	}
	
	public function testGetRdfTriples(){
	    $this->hardify();
		$workClass = $this->targetWorkClass;
		$authorProperty = $this->targetAuthorProperty;
		
		$this->createData();
		
		// We now test rdfTriples on a hardified resource.
		$filters = array($authorProperty->getUri() => 'John Ronald Reuel Tolkien');
		$options = array('like' => false);
		$instances = $workClass->searchInstances($filters, $options);
		$this->assertEquals(count($instances), 1);
		$book = current($instances);
		$this->assertEquals($book->getLabel(), 'The Lord of the Rings');
		$triples = $book->getRdfTriples()->toArray();
		$this->assertEquals($triples[1]->predicate, 'http://www.w3.org/2000/01/rdf-schema#label');
		$this->assertEquals($triples[0]->predicate, $authorProperty->getUri());
		$this->assertEquals($triples[0]->object, 'John Ronald Reuel Tolkien');
		$this->assertEquals($triples[2]->predicate, RDF_TYPE);
		$this->assertEquals($triples[2]->object, $workClass->getUri());
		
		// We now test rdfTriples on a hardified class.
		$triples = $workClass->getRdfTriples()->toArray();
		foreach ($triples as $t){
		    $this->assertIsA($t,'core_kernel_classes_Triple');
		    $this->assertTrue(in_array($t->predicate, array(RDF_SUBCLASSOF,RDFS_COMMENT,RDFS_LABEL)));
		    $this->assertTrue(in_array($t->object, array('Work','The Work class','http://www.tao.lu/Ontologies/TAO.rdf#TAOObject')));
		}

	}
	
	public function testHardPropertyModifications(){
	    $this->hardify();
	    $this->createData();
	    
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		
		$movieClass = $this->targetMovieClass;
		$workClass = $this->targetWorkClass;
		$authorProperty = $this->targetAuthorProperty;
		$producerProperty = $this->targetProducerProperty;
		$actorsProperty = $this->targetActorsProperty;
		$labelProperty = new core_kernel_classes_Property(RDFS_LABEL);
		$referencer = ResourceReferencer::singleton();
		$propertyProxy = PropertyProxy::singleton();
		
		// Retrieve interesting resources.
		$instances = $workClass->searchInstances(array($authorProperty->getUri() => 'Leonardo da Vinci'),
												 array('like' => false, 'recursive' => false));
		$monaLisa = current($instances);
		$instances = $movieClass->searchInstances(array($labelProperty->getUri() => 'The Lord of the Rings'),
												  array('like' => false, 'recursive' => false));
		$lordOfTheRings = current($instances);
		
		$instances = $movieClass->searchInstances(array($labelProperty->getUri() => 'The Hobbit'),
												  array('like' => true, 'recursive' => false));
		$theHobbit = current($instances);
		
		if (empty($monaLisa) || empty($lordOfTheRings) || empty($theHobbit)){
			$this->fail("Unable to retrieve instances that will be used in the following tests.");
		}
		else{
			// Try to create a new scalar property after hardification.
			$testProperty = $movieClass->createProperty('after hardify property');
			$testPropertyShortName = Utils::getShortName($testProperty);
			$testPropertyLocations = $referencer->propertyLocation($testProperty);
			$movieClassLocations = $referencer->classLocations($movieClass);
			$movieClassTable = $movieClassLocations[0]['table'];
			$movieClassTableColumns = array();
			foreach ($dbWrapper->getColumnNames($movieClassTable) as $col){
			    $movieClassTableColumns[]=$col->getName();
			}
			
			$workClassLocations = $referencer->classLocations($workClass);
			$workClassTable = $movieClassLocations[0]['table'];
			$workClassTableColumns = $dbWrapper->getColumnNames($workClassTable);
			$testPropertyShortName = Utils::getShortName($testProperty);
			$authorPropertyShortName = Utils::getShortName($authorProperty);
			
			// test delegation and presence of the column.
			$this->assertFalse($testProperty->isMultiple());
			$this->assertTrue(in_array($testPropertyShortName, $movieClassTableColumns));
			$this->assertIsA($propertyProxy->getImpToDelegateTo($testProperty), 'oat\generisHard\models\hardsql\Property');
	
			// set language dependency of the test property to true.
			$testProperty->setLgDependent(true);
			$this->assertTrue($testProperty->isLgDependent());
		    $movieClassTableColumns = array();
			foreach ($dbWrapper->getColumnNames($movieClassTable) as $col){
			    $movieClassTableColumns[]=$col->getName();
			}
			$this->assertFalse(in_array($testPropertyShortName, $movieClassTableColumns));
			
			// create some property values for the test property.
			$testMovie = $movieClass->createInstance('A Test Movie');
			$testMovie->setPropertyValue($testProperty, 'EN-TestPropertyValue-1');
			$testMovie->setPropertyValueByLg($testProperty, 'EN-TestPropertyValue-2', DEFAULT_LANG);
			$testMovie->setPropertyValueByLg($testProperty, 'FR-TestPropertyValue-1', 'FR');
			$testPropertyValues = $testMovie->getPropertyValues($testProperty);
			$this->assertEquals(count($testPropertyValues), 2); // Only EN values will come back.
			$testPropertyValues = $testMovie->getPropertyValuesByLg($testProperty, DEFAULT_LANG);
			$this->assertEquals(count($testPropertyValues->sequence), 2);
			$testPropertyValues = $testMovie->getPropertyValuesByLg($testProperty, 'FR');
			$this->assertEquals(count($testPropertyValues->sequence), 1);
			
			// set back the language dependency of the test property to false.
			$testProperty->setLgDependent(false);
			$this->assertFalse($testProperty->isLgDependent());
		    $movieClassTableColumns = array();
			foreach ($dbWrapper->getColumnNames($movieClassTable) as $col){
			    $movieClassTableColumns[]=$col->getName();
			}
			$this->assertTrue(in_array($testPropertyShortName, $movieClassTableColumns));
			$testPropertyValues = $testMovie->getPropertyValues($testProperty);
			$this->assertEquals(count($testPropertyValues), 1);
			
			// set the author property to multiple.
			$this->assertTrue(in_array($authorPropertyShortName, $movieClassTableColumns));
			$this->assertFalse($authorProperty->isMultiple());
			$authorProperty->setMultiple(true);
			$this->assertTrue($authorProperty->isMultiple());
		    $movieClassTableColumns = array();
			foreach ($dbWrapper->getColumnNames($movieClassTable) as $col){
			    $movieClassTableColumns[]=$col->getName();
			}
			$this->assertFalse(in_array($authorPropertyShortName, $movieClassTableColumns));
			// Add a fake value to make it multi valued
			$theHobbit->setPropertyValue($authorProperty, 'The Clone of Peter Jackson');
			
			$authors = $theHobbit->getPropertyValues($authorProperty);
			$this->assertEquals(count($authors), 2);
			$this->assertEquals(current($authors), 'Peter Jackson'); next($authors);
			$this->assertEquals(current($authors), 'The Clone of Peter Jackson');
			$authors = $monaLisa->getPropertyValues($authorProperty);
			$this->assertEquals(count($authors), 1);
			$this->assertEquals(current($authors), 'Leonardo da Vinci');
	
			// reset the author property to scalar.
			$authorProperty->setMultiple(false);
			$this->assertFalse($authorProperty->isMultiple());
		    $movieClassTableColumns = array();
			foreach ($dbWrapper->getColumnNames($movieClassTable) as $col){
			    $movieClassTableColumns[]=$col->getName();
			}
			$this->assertTrue(in_array($authorPropertyShortName, $movieClassTableColumns));
			$authors = $theHobbit->getPropertyValues($authorProperty);
			$this->assertEquals(count($authors), 1);
			$this->assertEquals(current($authors), 'Peter Jackson');
		}
	}
	
	public function testCreateSubClassOf(){
	    $this->hardify();
	    $this->createData();
		$classProxy = ClassProxy::singleton();
		
		// Any new subclass of an hardified class must be hardified as well.
		$this->targetSongClass = $this->targetWorkClass->createSubClass('Song', 'The Song Class');
		$this->assertTrue($this->targetSongClass->exists());
		$this->assertIsA($classProxy->getImpToDelegateTo($this->targetSongClass), 'oat\generisHard\models\hardsql\Clazz');
	}
	
	public function testDeleteInstances(){
	    $this->hardify();
	    $this->createData();
		$movieClass = $this->targetMovieClass;
		$authorProperty = $this->targetAuthorProperty;
		$actorsProperty = $this->targetActorsProperty;
		$producerProperty = $this->targetProducerProperty;
		$relatedMoviesProperty = $this->targetRelatedMoviesProperty;
		
		$matrixMovie = $movieClass->createInstance('The Matrix');
		$matrixMovie->setPropertyValue($authorProperty, 'Andy & Larry Wachowski');
		$matrixMovie->setPropertyValue($producerProperty, 'Silver Pictures');
		$matrixMovie->setPropertyValue($actorsProperty, 'Keanu Reaves');
		$matrixMovie->setPropertyValue($actorsProperty, 'Laurence Fishburne');
		$matrixMovie->setPropertyValue($actorsProperty, 'Carrie-Anne Moss');
		$matrixMovie->setPropertyValue($actorsProperty, 'Hugo Weaving');
		
		$matrixReloadedMovie = $movieClass->createInstance('The Matrix Reloaded');
		$matrixReloadedMovie->setPropertyValue($authorProperty, 'Andy & Larry Wachowski');
		$matrixReloadedMovie->setPropertyValue($producerProperty, 'Silver Pictures');
		$matrixReloadedMovie->setPropertyValue($actorsProperty, 'Keanu Reaves');
		$matrixReloadedMovie->setPropertyValue($actorsProperty, 'Laurence Fishburne');
		$matrixReloadedMovie->setPropertyValue($actorsProperty, 'Carrie-Anne Moss');
		$matrixReloadedMovie->setPropertyValue($actorsProperty, 'Hugo Weaving');
		$matrixReloadedMovie->setPropertyValue($relatedMoviesProperty, $matrixMovie);
		
		$matrixRevolutionsMovie = $movieClass->createInstance('The Matrix Reloaded');
		$matrixRevolutionsMovie->setPropertyValue($authorProperty, 'Andy & Larry Wachowski');
		$matrixRevolutionsMovie->setPropertyValue($producerProperty, 'Silver Pictures');
		$matrixRevolutionsMovie->setPropertyValue($actorsProperty, 'Keanu Reaves');
		$matrixRevolutionsMovie->setPropertyValue($actorsProperty, 'Laurence Fishburne');
		$matrixRevolutionsMovie->setPropertyValue($actorsProperty, 'Carrie-Anne Moss');
		$matrixRevolutionsMovie->setPropertyValue($actorsProperty, 'Hugo Weaving');
		$matrixRevolutionsMovie->setPropertyValue($relatedMoviesProperty, $matrixMovie);
		$matrixRevolutionsMovie->setPropertyValue($relatedMoviesProperty, $matrixReloadedMovie);
		
		$vForVendettaMovie = $movieClass->createInstance('V for Vendetta');
		$vForVendettaMovie->setPropertyValue($authorProperty, 'Andy & Larry Wachowski');
		$vForVendettaMovie->setPropertyValue($producerProperty, 'Silver Pictures');
		$vForVendettaMovie->setPropertyValue($actorsProperty, 'Hugo Weaving');
		$vForVendettaMovie->setPropertyValue($actorsProperty, 'Natalie Portman');
		
		$cloudAtlasMovie = $movieClass->createInstance('Cloud Atlas');
		$cloudAtlasMovie->setPropertyValue($authorProperty, 'Andy & Larry Wachowski');
		$cloudAtlasMovie->setPropertyValue($producerProperty, 'Cloud Atlas Productions');
		$cloudAtlasMovie->setPropertyValue($producerProperty, 'Anarchos Pictures');
		$cloudAtlasMovie->setPropertyValue($actorsProperty, 'Tom Hanks');
		$cloudAtlasMovie->setPropertyValue($actorsProperty, 'Halle Berry');
		$cloudAtlasMovie->setPropertyValue($actorsProperty, 'Jim Sturgess');
		$cloudAtlasMovie->setPropertyValue($actorsProperty, 'Hugo Weaving');
		
		$speedRacerMovie = $movieClass->createInstance('Speed Racer');
		$speedRacerMovie->setPropertyValue($authorProperty, 'Andy & Larry Wachowski');
		$speedRacerMovie->setPropertyValue($producerProperty, 'Silver Pictures');
		$speedRacerMovie->setPropertyValue($actorsProperty, 'Emile Hirsch');
		$speedRacerMovie->setPropertyValue($actorsProperty, 'Nicholas Elia');
		$speedRacerMovie->setPropertyValue($actorsProperty, 'Susan Sarando');
		$speedRacerMovie->setPropertyValue($actorsProperty, 'Ariel Winter');
		
		$movieClass->deleteInstances(array($speedRacerMovie));
		$this->assertFalse($speedRacerMovie->exists());
		
		$movieClass->deleteInstances(array($vForVendettaMovie, $cloudAtlasMovie->getUri()));
		$this->assertFalse($vForVendettaMovie->exists());
		$this->assertFalse($cloudAtlasMovie->exists());
		
		$movieClass->deleteInstances(array($matrixMovie), true);
		$this->assertFalse($matrixMovie->exists());
		
		// We get the related movies of matrix revolutions. We should only
		// get $matrixReladedMovie but no more $matrixMovie because it was deleted
		// with its references.
		$relatedMovie = $matrixRevolutionsMovie->getUniquePropertyValue($relatedMoviesProperty);
		$this->assertEquals($relatedMovie->getLabel(), 'The Matrix Reloaded');
	}
	
	public function testForceMode (){
	    $this->hardify();

	    
		// Check if the returner implementation are correct
		PersistenceProxy::forceMode (PERSISTENCE_SMOOTH);
		$classProxy = ClassProxy::singleton();
		$impl = $classProxy->getImpToDelegateTo($this->targetSubjectClass);
		$this->assertTrue ($impl instanceof core_kernel_persistence_smoothsql_Class);
		$this->assertEquals (count($this->targetSubjectClass->getInstances ()), 1);
		$this->assertEquals (count($this->targetSubjectSubClass->getInstances ()), 1);
		PersistenceProxy::restoreImplementation();
		$this->assertTrue (ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass) instanceof Clazz);
		$this->assertTrue (ResourceProxy::singleton()->getImpToDelegateTo($this->subject1) instanceof Resource);
	}
	
	private function setProperties (){


		// Set properties
		foreach ($this->targetSubjectClass->getInstances(true) as $instance){
			// Set mutltiple property
			$instance->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'), new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN'));
			$instance->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'), new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangFR'));
			// Set property value by lg
			$instance->setPropertyValueByLg(new core_kernel_classes_Property(RDFS_LABEL), 'LABEL FR', 'FR');
			// Set property type (SPECIAL CASE)
			$instance->setPropertyValue(new core_kernel_classes_Property(RDF_TYPE), 'http://www.tao.lu/Ontologies/TAO.rdf#TaoSubjectRole');
			// Set foreign property
			$instance->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'), 'http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
			$instance->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userUiLg'), 'http://www.tao.lu/Ontologies/TAO.rdf#LangFR');
		}
	}
	
	public function testSetType (){
	    $this->hardify();
	    $this->setProperties();
	    $this->targetSongClass = $this->targetWorkClass->createSubClass('Song', 'The Song Class');
	    
		$instance = $this->targetWorkClass->createInstance('setType test instance');
		$sanityCheckInstance = $this->targetWorkClass->createInstance('sanityCheck setType test');
		
		// verify everything is sane to begin with
		
		$this->assertIsA($instance, 'core_kernel_classes_resource');
		$this->assertTrue($instance->exists());
		$this->assertIsA($sanityCheckInstance, 'core_kernel_classes_resource');
		$this->assertTrue($sanityCheckInstance->exists());
		$instfound = 0;
		$sanityfound = 0;
		foreach ($this->targetWorkClass->getInstances() as $workInst) {
			if ($workInst->equals($instance)) {
				$instfound++;
			}
			if ($workInst->equals($sanityCheckInstance)) {
				$sanityfound++;
			}
		}
		$this->assertEquals($instfound, 1);
		$this->assertEquals($sanityfound, 1);

		foreach ($this->targetSongClass->getInstances() as $songInst) {
			$this->assertFalse($songInst->equals($instance), 'instance in getInstances of targetSongclass');
			$this->assertFalse($songInst->equals($sanityCheckInstance), 'sanity check instance in getInstances of targetSongclass');
		}
		
		$types = $instance->getTypes();
		$this->assertEquals(count($types), 1);
		$this->assertTrue($this->targetWorkClass->equals(current($types)));
		
		// remove the old type (targetWorkClass)
		
		$this->assertTrue($instance->removeType($this->targetWorkClass));
		$types = $instance->getTypes();
		$this->assertEquals(count($types), 0);
		$sanityfound = 0;
		foreach ($this->targetWorkClass->getInstances() as $workInst) {
			if ($workInst->equals($sanityCheckInstance)) {
				$sanityfound++;
			}
			$this->assertFalse($workInst->equals($instance), 'instance still in getInstances() of class after removeType()');
		}
		$this->assertEquals($sanityfound, 1);
		foreach ($this->targetSongClass->getInstances() as $songInst) {
			$this->assertFalse($songInst->equals($instance), 'instance in getInstances of targetSongclass');
			$this->assertFalse($songInst->equals($sanityCheckInstance), 'sanity check instance in getInstances of targetSongclass');
		}
		
		// set the new type (targetSongClass)
		
		$this->assertTrue($instance->setType($this->targetSongClass));
		$types = $instance->getTypes();
		$this->assertEquals(count($types), 1);
		$this->assertTrue($this->targetSongClass->equals(current($types)));
		$sanityfound = 0;
		foreach ($this->targetWorkClass->getInstances() as $workInst) {
			if ($workInst->equals($sanityCheckInstance)) {
				$sanityfound++;
			}
			$this->assertFalse($workInst->equals($instance), 'instance still in getInstances() of class after removeType()');
		}
		$this->assertEquals($sanityfound, 1);
		$instfound = 0;
		foreach ($this->targetSongClass->getInstances() as $songInst) {
			if ($songInst->equals($instance)) {
				$instfound++;
			}
			$this->assertFalse($songInst->equals($sanityCheckInstance), 'sanity check instance in getInstances of targetSongclass');
		}
		$this->assertEquals($instfound, 1);
		
		// cleanup and check cleanup
		
		$this->assertTrue($instance->delete());
		$this->assertFalse($instance->exists());
		
		$this->assertTrue($sanityCheckInstance->delete());
		$this->assertFalse($sanityCheckInstance->exists());
		foreach ($this->targetWorkClass->getInstances() as $workInst) {
			$this->assertFalse($workInst->equals($instance), 'instance still in getInstances() of class targetWorkClass after delete()');
			$this->assertFalse($workInst->equals($sanityCheckInstance), 'instance still in getInstances() of class targetWorkClass after delete()');
		}
		foreach ($this->targetSongClass->getInstances() as $songInst) {
			$this->assertFalse($songInst->equals($instance), 'instance still in getInstances() of class targetSongClass after delete()');
			$this->assertFalse($songInst->equals($sanityCheckInstance), 'instance still in getInstances() of class targetSongClass after delete()');
		}
		
	}
	
	public function testGetOnePropertyValue (){
        $this->hardify();
	    $subject = $this->targetSubjectClass->createInstance("Hard Sub Subject (Unit Test)");
	    $subSubject = $this->targetSubjectSubClass->createInstance("Hard Sub Sub Subject (Unit Test)");
	    $this->setProperties();
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			
			// Specific case show later
			$prop = $instance->getOnePropertyValue(new core_kernel_classes_Property(RDF_TYPE));
			$this->assertTrue($prop instanceof core_kernel_classes_Resource);
			
			// Get single property label
			$prop = $instance->getOnePropertyValue(new core_kernel_classes_Property(RDFS_LABEL));

			$this->assertTrue($prop instanceof core_kernel_classes_Literal);
			
			// Get single property value
			$prop = $instance->getOnePropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertTrue($prop instanceof core_kernel_classes_Resource);
		}
	}
	
	public function testGetPropertyValues () {
	    $this->hardify();
	    $subject = $this->targetSubjectClass->createInstance("Hard Sub Subject (Unit Test)");
	    $subSubject = $this->targetSubjectSubClass->createInstance("Hard Sub Sub Subject (Unit Test)");
	    $this->setProperties();
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			
			// Get property values on single (literal) property 
			$props = $instance->getPropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
			$this->assertEquals(count($props), 1);
			foreach ($props as $prop){
				$this->assertTrue(is_string($prop));
			}
			// Get property values on single (resource) property 
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'));
			$this->assertEquals(count($props), 1);
			foreach ($props as $prop){
				$this->assertTrue(common_Utils::isUri($prop));
			}
			// Get property values on mutltiple property
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertEquals(count($props), 2);
			foreach ($props as $prop){
				$this->assertTrue(common_Utils::isUri($prop));
			}
			// Get property values on mutltiple (by lg) property
			// Common behavior is to return reccords function of a defined language or function of the default system language if the record is language dependent
//			$props = $instance->getPropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent'));
//			$this->assertEqual (count($props), 1);
//			foreach ($props as $prop){
//				$this->assertTrue (is_string($prop));
//			}		
		}
	}
			
	public function testGetPropertyValuesCollection (){
	    $this->hardify();
	    $subject = $this->targetSubjectClass->createInstance("Hard Sub Subject (Unit Test)");
	    $subSubject = $this->targetSubjectSubClass->createInstance("Hard Sub Sub Subject (Unit Test)");
	    $this->setProperties();
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			
			$props = $instance->getPropertyValuesCollection(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertTrue($props instanceof core_kernel_classes_ContainerCollection);
			$this->assertEquals($props->count(), 2);
			foreach ($props->getIterator() as $prop){
				
				$this->assertTrue($prop instanceof core_kernel_classes_Resource);
			}		
		}
	}
	
	public function testGetPropertyValuesByLg (){
	    $this->hardify();
	    $subject = $this->targetSubjectClass->createInstance("Hard Sub Subject (Unit Test)");
	    $subSubject = $this->targetSubjectSubClass->createInstance("Hard Sub Sub Subject (Unit Test)");
	    $this->setProperties();
		foreach ($this->targetSubjectClass->getInstances() as $instance){	
			
			$props = $instance->getPropertyValuesByLg (new core_kernel_classes_Property(RDFS_LABEL), 'FR');
			$this->assertEquals($props->count(), 1);
			$this->assertTrue($props->get(0) instanceof core_kernel_classes_Literal);
			$this->assertEquals((string)$props->get(0), 'LABEL FR');
		}
	}
	
	public function testRemovePropertyValues (){
	    $this->hardify();
	    $subject = $this->targetSubjectClass->createInstance("Hard Sub Subject (Unit Test)");
	    $subSubject = $this->targetSubjectSubClass->createInstance("Hard Sub Sub Subject (Unit Test)");
	    $this->setProperties();
		foreach ($this->targetSubjectClass->getInstances() as $instance){	
			
			// Remove foreign single property
			$instance->removePropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'));
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'));
			$this->assertTrue(empty($props));
			
			// Remove literal multiple property
			$instance->removePropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
			$props = $instance->getPropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
			$this->assertTrue(empty($props));
			
			// Remove foreign multiple property
			$instance->removePropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertTrue(empty($props));
			
		}
	}
	
	public function testRemovePropertyValuesByLg (){
	    $this->hardify();
	    $subject = $this->targetSubjectClass->createInstance("Hard Sub Subject (Unit Test)");
	    $subSubject = $this->targetSubjectSubClass->createInstance("Hard Sub Sub Subject (Unit Test)");
	    $this->setProperties();
        
		
		foreach ($this->targetSubjectClass->getInstances() as $instance){	
			
			// Remove foreign single property
			$instance->removePropertyValueByLg(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'), 'FR');
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'));
			$this->assertFalse(empty($props));
			
			// Remove foreign multiple property
			$instance->removePropertyValueByLg(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'), 'FR');
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertFalse(empty($props));
		}
	}
	

	public function testSetPropertiesValues(){
	    $this->hardify();
	    $instance = $this->targetMovieClass->createInstance("Hard Sub Movie (Unit Test)");
	    $instance2 = $this->targetMovieClass->createInstance("Hard Sub Movie2 (Unit Test)");
	    
		$instance->setPropertiesValues(array(
		    $this->targetRelatedMoviesProperty->getUri() => $instance2,
		    $this->targetActorsProperty->getUri() => array(
	            'I\'m special"! !',
		        'So special"! !'
		    ),
		    RDFS_LABEL => 'I\'m special"! !'
	    ));
		
		$actors = $instance->getPropertyValues($this->targetActorsProperty);
		$this->assertEquals(2, count($actors));
		$this->assertTrue(in_array('I\'m special"! !', $actors));
		$this->assertTrue(in_array('So special"! !', $actors));
		
		$relateds = $instance->getPropertyValues($this->targetRelatedMoviesProperty);
		$this->assertEquals(1, count($relateds));
		$related = current($relateds);
		$this->assertEquals($instance2->getUri(),$related);
		
		$labels = $instance->getPropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
		$this->assertEquals(2, count($labels));
		$this->assertTrue(in_array('I\'m special"! !', $labels));
		$this->assertTrue(in_array("Hard Sub Movie (Unit Test)", $labels));
		
		$instance->delete();
		$instance2->delete();
	}
}
