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
?>
<?php
require_once dirname(__FILE__) . '/GenerisTestRunner.php';

class HardImplTestCase extends UnitTestCase {
	
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
	
	public function setUp(){

		GenerisTestRunner::initTest();

	}
	
	public function testCreateContextOfThetest(){
		// ----- Top Class : TaoSubject
		$subjectClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		// Create a new subject class for the unit test
		$this->targetSubjectClass = $subjectClass->createSubClass ("Sub Subject Class (Unit Test)");
		// Add a custom property to the newly created class

		// Add an instance to this subject class
		$this->subject1 = $this->targetSubjectClass->createInstance ("Sub Subject (Unit Test)");
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);
		
		// Create a new subject sub class to the previous sub class
		$this->targetSubjectSubClass = $this->targetSubjectClass->createSubClass ("Sub Sub Subject Class (Unit Test)");
		// Add an instance to this sub subject class
		$this->subject2 = $this->targetSubjectSubClass->createInstance ("Sub Sub Subject (Unit Test)");
		$this->assertEqual (count($this->targetSubjectSubClass->getInstances ()), 1);
		
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);
		// If get instances in the sub classes of the targetSubjectClass, we should get 2 instances
		$this->assertEqual (count($this->targetSubjectClass->getInstances (true)), 2);
		
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
		$this->assertTrue($this->targetMovieClass->isSubClassOf($this->targetWorkClass));
		$this->assertEqual(count($this->targetWorkClass->getSubClasses()), 1);
		
		// Add properties to the Movie class.
		$this->targetProducerProperty = $this->targetMovieClass->createProperty('Producer', 'The producer of the movie.');
		$this->targetProducerProperty->setRange($literalClass);
		$this->targetProducerProperty->setMultiple(true);
		$this->assertTrue($this->targetProducerProperty->isMultiple());
		$this->targetActorsProperty = $this->targetMovieClass->createProperty('Actors', 'The actors playing in the movie.');
		$this->targetActorsProperty->setRange($literalClass);
		$this->targetActorsProperty->setMultiple(true);
		$this->targetRelatedMoviesProperty = $this->targetMovieClass->createProperty('Related Movies', 'Movies related to the movie.');
		$this->targetRelatedMoviesProperty->setRange($this->targetMovieClass);
		$this->targetRelatedMoviesProperty->setMultiple(true);
	}
	
	public function testHardifier (){
		$switcher = new core_kernel_persistence_Switcher();
		$switcher->hardify($this->targetSubjectClass, array(
			'recursive'	=> true,
		));
		
		$switcher->hardify($this->targetWorkClass, array(
			'recursive' => true,
		));
	}
	
	public function testHardSwitchOK(){
		// Test that resource are now available from the hard sql implementation
		$classProxy = core_kernel_persistence_ClassProxy::singleton();
		$propertyProxy = core_kernel_persistence_PropertyProxy::singleton();
		$this->assertIsA($classProxy->getImpToDelegateTo($this->targetSubjectClass), 'core_kernel_persistence_hardsql_Class');
		$this->assertIsA($classProxy->getImpToDelegateTo($this->targetSubjectSubClass), 'core_kernel_persistence_hardsql_Class');
		$this->assertIsA($classProxy->getImpToDelegateTo($this->targetWorkClass), 'core_kernel_persistence_hardsql_Class');
		$this->assertIsA($classProxy->getImpToDelegateTo($this->targetMovieClass), 'core_kernel_persistence_hardsql_Class');
		$this->assertIsA($propertyProxy->getImpToDelegateTo($this->targetActorsProperty), 'core_kernel_persistence_hardsql_Property');
		$this->assertIsA($propertyProxy->getImpToDelegateTo($this->targetAuthorProperty), 'core_kernel_persistence_hardsql_Property');
		$this->assertIsA($propertyProxy->getImpToDelegateTo($this->targetProducerProperty), 'core_kernel_persistence_hardsql_Property');
		$this->assertIsA($propertyProxy->getImpToDelegateTo($this->targetRelatedMoviesProperty), 'core_kernel_persistence_hardsql_Property');
	}
	
	public function testHardModel(){
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$propertyProxy = core_kernel_persistence_PropertyProxy::singleton();
		$proxy = core_kernel_persistence_ResourceProxy::singleton();
		
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
		$this->assertEqual(2, count($parentClasses));
		$this->assertTrue(array_key_exists(RDFS_CLASS, $parentClasses));
		$this->assertTrue(array_key_exists($this->targetWorkClass->getUri(), $parentClasses));
		
		$prop = new core_kernel_classes_Property($this->targetRelatedMoviesProperty);
		$this->assertTrue($prop->isMultiple());
	}
	
	public function testHardGetInstances (){
		// Get the hardified instance from the hard sql imlpementation
		$this->assertEqual(count($this->targetSubjectClass->getInstances()), 1);
		$this->assertEqual(count($this->targetSubjectSubClass->getInstances()), 1);
		$this->assertEqual(count($this->targetSubjectClass->getInstances(true)), 2);
		$this->assertEqual(count($this->targetWorkClass->getInstances(true)), 0);
		$this->assertEqual(count($this->targetMovieClass->getInstances()), 0);
	}
	
	public function testHardCreateInstance() {
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$proxy = core_kernel_persistence_ResourceProxy::singleton();
		
		$labelProperty = new core_kernel_classes_Property(RDFS_LABEL);
		$valueProperty = new core_kernel_classes_Property(RDF_VALUE);
		
		// Create instance with the hard sql implementation
		$subject = $this->targetSubjectClass->createInstance("Hard Sub Subject (Unit Test)");
		$this->assertTrue($referencer->isResourceReferenced($subject));
		$this->assertIsA($proxy->getImpToDelegateTo($subject), 'core_kernel_persistence_hardsql_Resource');
		$this->assertEqual(count($this->targetSubjectClass->getInstances()), 2);

		$subSubject = $this->targetSubjectSubClass->createInstance("Hard Sub Sub Subject (Unit Test)");
		$this->assertTrue($referencer->isResourceReferenced($subSubject));
		$this->assertIsA($proxy->getImpToDelegateTo($subSubject), 'core_kernel_persistence_hardsql_Resource');
		$this->assertEqual(count($this->targetSubjectSubClass->getInstances()), 2);
		$this->assertEqual(count($this->targetSubjectClass->getInstances(true)), 4);
		
		$work1Label = 'Mona Lisa';
		$work1Author = 'Leonardo da Vinci';
		$work1 = $this->targetWorkClass->createInstance($work1Label, 'Mona Lisa, a half-length portait of a woman');
		$this->assertTrue($work1->exists());
		$this->assertTrue($referencer->isResourceReferenced($work1));
		$this->assertIsA($proxy->getImpToDelegateTo($work1), 'core_kernel_persistence_hardsql_Resource');
		$this->assertEqual($work1->getLabel(), $work1Label);
		$work1->setPropertyValue($this->targetAuthorProperty, $work1Author);
		
		// Test property (that exists) values for $work1.
		$this->assertEqual($work1->getUniquePropertyValue($labelProperty)->literal, $work1Label);
		$work1Labels = $work1->getPropertyValues($labelProperty);
		$this->assertEqual(count($work1Labels), 1);
		$this->assertEqual($work1Labels[0], $work1Label);
		$work1Labels = $work1->getPropertyValues($labelProperty, array('one' => true));
		$this->assertEqual(count($work1Labels), 1);
		$this->assertEqual($work1Labels[0], $work1Label);
		$work1Labels = $work1->getPropertyValues($labelProperty, array('last', true));
		$this->assertEqual(count($work1Labels), 1);
		$this->assertEqual($work1Labels[0], $work1Label);
		$literal = $work1->getOnePropertyValue($this->targetAuthorProperty);
		$this->assertIsA($literal, 'core_kernel_classes_Literal');
		$this->assertEqual($literal->literal, $work1Author);
		$work1PropertiesValues = $work1->getPropertiesValues(array($labelProperty, $this->targetAuthorProperty));
		$this->assertEqual(count($work1PropertiesValues), 2);
		$this->assertTrue(array_key_exists(RDFS_LABEL, $work1PropertiesValues));
		$this->assertTrue(array_key_exists($this->targetAuthorProperty->getUri(), $work1PropertiesValues));
		$this->assertEqual($work1->getUsedLanguages($labelProperty), array(DEFAULT_LANG));
		
		// Test property (that doesn't exist) values for $work1.
		$unknownProperty = new core_kernel_classes_Property('unknown property');
		$unknownProperty2 = new core_kernel_classes_Property('unknown property 2');
		try{
			$work1->getUniquePropertyValue($unknownProperty);
			$this->fail('common_exception_EmptyProperty expected');
		}
		catch (common_exception_EmptyProperty $e){
			$this->pass();
		}
		
		$work1Unknown = $work1->getPropertyValues($unknownProperty);
		$this->assertEqual($work1Unknown, array());
		$work1Unknown = $work1->getPropertyValues($unknownProperty, array('one' => true));
		$this->assertEqual($work1Unknown, array());
		$work1Unknown = $work1->getPropertyValues($unknownProperty, array('last' => true));
		$this->assertEqual($work1Unknown, array());
		$literal = $work1->getOnePropertyValue($unknownProperty);
		$this->assertNull($literal);
		$work1PropertiesValues = $work1->getPropertiesValues(array($unknownProperty, $unknownProperty2));
		$this->assertTrue(count($work1PropertiesValues) == 0);
		$work1PropertiesValues = $work1->getPropertiesValues(array($labelProperty, $unknownProperty));
		$this->assertTrue(array_key_exists(RDFS_LABEL, $work1PropertiesValues));
		$this->assertTrue(count($work1PropertiesValues) == 1);
	}
	
	public function testHardSearchInstances(){
		$movieClass = $this->targetMovieClass;
		$workClass = $this->targetWorkClass;
		$authorProperty = $this->targetAuthorProperty;
		$producerProperty = $this->targetProducerProperty;
		$actorsProperty = $this->targetActorsProperty;
		$relatedMoviesProperty = $this->targetRelatedMoviesProperty;
		$labelProperty = new core_kernel_classes_Property(RDFS_LABEL);
		
		// ---- CREATIVE WORKS
		$bookOfTheRings = $workClass->createInstance('The Lord of the Rings');
		$bookOfTheRings->setPropertyValue($authorProperty, 'John Ronald Reuel Tolkien');
		
		// Works with a rdfs:label which is 'The Lord of the ...'.
		$propertyFilters = array($labelProperty->getUri() => 'The Lord of the');
		$instances = $workClass->searchInstances($propertyFilters, array('like' => true));
		$this->assertEqual(count($instances), 1);
		$this->assertEqual($instances[key($instances)]->getLabel(), 'The Lord of the Rings');
		
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
		
		// Works with a rdfs:label which is 'The Lord of the ...' (recursive).
		$propertyFilters = array($labelProperty->getUri() => 'The Lord of the');
		$instances = $workClass->searchInstances($propertyFilters, array('like' => true, 'recursive' => 1));
		$this->assertEqual(count($instances), 2);
		$this->assertEqual($instances[key($instances)]->getLabel(), 'The Lord of the Rings'); next($instances);
		$this->assertEqual($instances[key($instances)]->getLabel(), 'The Lord of the Rings'); next($instances);
		
		$instances = $workClass->searchInstances($propertyFilters, array('like' => true, 'recursive' => 0));
		$this->assertEqual(count($instances), 1);
		$this->assertEqual($instances[key($instances)]->getLabel(), 'The Lord of the Rings');
		
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
		
		// Movie with rdfs:label equals to 'The Hobbit: An Unexpected Journey'.
		$propertyFilters = array($labelProperty->getUri() => 'The Hobbit: An Unexpected Journey');
		$instances = $movieClass->searchInstances($propertyFilters);
		$this->assertTrue(count($instances) == 1);
		$instance = new core_kernel_classes_Resource($instances[key($instances)]);
		$this->assertIsA($instance, 'core_kernel_classes_Resource');
		$this->assertTrue($instance->exists());
		$this->assertEqual($instance->getLabel(), 'The Hobbit: An Unexpected Journey');
		
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
		$this->assertEqual($instance->getLabel(), 'The Lord of the Rings');
		
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
		$this->assertEqual($foundCount1 + $foundCount2, 2);
		
		// Movie with rdfs:label equals to 'Le Seigneur des Anneaux' in the Belgian French locale.
		$propertyFilters = array($labelProperty->getUri() => 'Le Seigneur des Anneaux');
		$instances = $movieClass->searchInstances($propertyFilters, array('lang' => 'FR-be'));
		$this->assertEqual(count($instances), 1);
		
		// All Works limited to 2 results. We should have 'The Lord of The Rings' (movie + book),
		// 'The Hobbit: An Unexpected Journey' and 'Mona Lisa' in the Knowledge Base at
		// the moment.
		$propertyFilters = array();
		$instances = $workClass->searchInstances($propertyFilters, array('limit' => 3, 'recursive' => 1));
		$this->assertEqual(count($instances), 3);
		
		// Same as previous, but without limit and orderedy by author.
		$propertyFilters = array();
		$instances = $workClass->searchInstances($propertyFilters, array('recursive' => 1, 'order' => $authorProperty->getUri()));
		$this->assertEqual(count($instances), 4);
		
		// Same as previous, but with a descendent orderdir.
		$propertyFilters = array();
		$instances = $workClass->searchInstances($propertyFilters, array('order' => $authorProperty->getUri(), 'orderdir' => 'ASC'));
		$this->assertEqual(count($instances), 2);
		$this->assertEqual($instances[key($instances)]->getUniquePropertyValue($authorProperty)->literal, 'John Ronald Reuel Tolkien'); next($instances);
		$this->assertEqual($instances[key($instances)]->getUniquePropertyValue($authorProperty)->literal, 'Leonardo da Vinci'); next($instances);
		
		// Get all movies that are produced by 'Peter Jackson' ordered by rdfs:label.
		$propertyFilters = array($producerProperty->getUri() => 'Peter Jackson');
		$instances = $movieClass->searchInstances($propertyFilters, array('order' => $labelProperty->getUri()));
		$this->assertEqual(count($instances), 2);
		$this->assertEqual($instances[key($instances)]->getUniquePropertyValue($labelProperty)->literal, 'The Hobbit: An Unexpected Journey'); next($instances);
		$this->assertEqual($instances[key($instances)]->getUniquePropertyValue($labelProperty)->literal, 'The Lord of the Rings'); next($instances);
		
		// Get Lord of the Rings by Peter Jackson (produced).
		$propertyFilters = array($producerProperty->getUri() => 'Peter Jackson');
		$instances = $movieClass->searchInstances($propertyFilters, array('order' => $labelProperty->getUri()));
		$this->assertEqual(count($instances), 2);
		$this->assertEqual($instances[key($instances)]->getUniquePropertyValue($labelProperty)->literal, 'The Hobbit: An Unexpected Journey'); next($instances);
		$this->assertEqual($instances[key($instances)]->getUniquePropertyValue($labelProperty)->literal, 'The Lord of the Rings'); next($instances);
		
		// try to search a property that does not exist.
		$propertyFilters = array('http://www.unknown.com/i-do-not-exist' => 'do-not-exist');
		$instances = $movieClass->searchInstances($propertyFilters);
		$this->assertEqual(count($instances), 0);
	}
	
	public function testGetRdfTriples(){
		$workClass = $this->targetWorkClass;
		$authorProperty = $this->targetAuthorProperty;
		
		// We now test rdfTriples on a hardified resource.
		$filters = array($authorProperty->getUri() => 'John Ronald Reuel Tolkien');
		$options = array('like' => false);
		$instances = $workClass->searchInstances($filters, $options);
		$this->assertEqual(count($instances), 1);
		$book = current($instances);
		$this->assertEqual($book->getLabel(), 'The Lord of the Rings');
		$triples = $book->getRdfTriples()->toArray();
		$this->assertEqual($triples[1]->predicate, 'http://www.w3.org/2000/01/rdf-schema#label');
		$this->assertEqual($triples[0]->predicate, $authorProperty->getUri());
		$this->assertEqual($triples[0]->object, 'John Ronald Reuel Tolkien');
		$this->assertEqual($triples[2]->predicate, RDF_TYPE);
		$this->assertEqual($triples[2]->object, $workClass->getUri());
		
		// We now test rdfTriples on a hardified class.
		$triples = $workClass->getRdfTriples()->toArray();
		$this->assertEqual($triples[0]->predicate, RDF_TYPE);
		$this->assertEqual($triples[0]->object, RDFS_CLASS);
	}
	
	public function testHardPropertyModifications(){
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		
		$movieClass = $this->targetMovieClass;
		$workClass = $this->targetWorkClass;
		$authorProperty = $this->targetAuthorProperty;
		$producerProperty = $this->targetProducerProperty;
		$actorsProperty = $this->targetActorsProperty;
		$labelProperty = new core_kernel_classes_Property(RDFS_LABEL);
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$propertyProxy = core_kernel_persistence_PropertyProxy::singleton();
		
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
			$testPropertyShortName = core_kernel_persistence_hardapi_Utils::getShortName($testProperty);
			$testPropertyLocations = $referencer->propertyLocation($testProperty);
			$movieClassLocations = $referencer->classLocations($movieClass);
			$movieClassTable = $movieClassLocations[0]['table'];
			$movieClassTableColumns = $dbWrapper->getColumnNames($movieClassTable);
			$workClassLocations = $referencer->classLocations($workClass);
			$workClassTable = $movieClassLocations[0]['table'];
			$workClassTableColumns = $dbWrapper->getColumnNames($workClassTable);
			$testPropertyShortName = core_kernel_persistence_hardapi_Utils::getShortName($testProperty);
			$authorPropertyShortName = core_kernel_persistence_hardapi_Utils::getShortName($authorProperty);
			
			// test delegation and presence of the column.
			$this->assertFalse($testProperty->isMultiple());
			$this->assertTrue(in_array($testPropertyShortName, $movieClassTableColumns));
			$this->assertIsA($propertyProxy->getImpToDelegateTo($testProperty), 'core_kernel_persistence_hardsql_Property');
	
			// set language dependency of the test property to true.
			$testProperty->setLgDependent(true);
			$this->assertTrue($testProperty->isLgDependent());
			$movieClassTableColumns = $dbWrapper->getColumnNames($movieClassTable);
			$this->assertFalse(in_array($testPropertyShortName, $movieClassTableColumns));
			
			// create some property values for the test property.
			$testMovie = $movieClass->createInstance('A Test Movie');
			$testMovie->setPropertyValue($testProperty, 'EN-TestPropertyValue-1');
			$testMovie->setPropertyValueByLg($testProperty, 'EN-TestPropertyValue-2', DEFAULT_LANG);
			$testMovie->setPropertyValueByLg($testProperty, 'FR-TestPropertyValue-1', 'FR');
			$testPropertyValues = $testMovie->getPropertyValues($testProperty);
			$this->assertEqual(count($testPropertyValues), 2); // Only EN values will come back.
			$testPropertyValues = $testMovie->getPropertyValuesByLg($testProperty, DEFAULT_LANG);
			$this->assertEqual(count($testPropertyValues->sequence), 2);
			$testPropertyValues = $testMovie->getPropertyValuesByLg($testProperty, 'FR');
			$this->assertEqual(count($testPropertyValues->sequence), 1);
			
			// set back the language dependency of the test property to false.
			$testProperty->setLgDependent(false);
			$this->assertFalse($testProperty->isLgDependent());
			$movieClassTableColumns = $dbWrapper->getColumnNames($movieClassTable);
			$this->assertTrue(in_array($testPropertyShortName, $movieClassTableColumns));
			$testPropertyValues = $testMovie->getPropertyValues($testProperty);
			$this->assertEqual(count($testPropertyValues), 1);
			
			// set the author property to multiple.
			$this->assertTrue(in_array($authorPropertyShortName, $movieClassTableColumns));
			$this->assertFalse($authorProperty->isMultiple());
			$authorProperty->setMultiple(true);
			$this->assertTrue($authorProperty->isMultiple());
			$movieClassTableColumns = $dbWrapper->getColumnNames($movieClassTable);
			$this->assertFalse(in_array($authorPropertyShortName, $movieClassTableColumns));
			// Add a fake value to make it multi valued
			$theHobbit->setPropertyValue($authorProperty, 'The Clone of Peter Jackson');
			
			$authors = $theHobbit->getPropertyValues($authorProperty);
			$this->assertEqual(count($authors), 2);
			$this->assertEqual(current($authors), 'Peter Jackson'); next($authors);
			$this->assertEqual(current($authors), 'The Clone of Peter Jackson');
			$authors = $monaLisa->getPropertyValues($authorProperty);
			$this->assertEqual(count($authors), 1);
			$this->assertEqual(current($authors), 'Leonardo da Vinci');
	
			// reset the author property to scalar.
			$authorProperty->setMultiple(false);
			$this->assertFalse($authorProperty->isMultiple());
			$movieClassTableColumns = $dbWrapper->getColumnNames($movieClassTable);
			$this->assertTrue(in_array($authorPropertyShortName, $movieClassTableColumns));
			$authors = $theHobbit->getPropertyValues($authorProperty);
			$this->assertEqual(count($authors), 1);
			$this->assertEqual(current($authors), 'Peter Jackson');
		}
	}
	
	public function testCreateSubClassOf(){
		$classProxy = core_kernel_persistence_ClassProxy::singleton();
		
		// Any new subclass of an hardified class must be hardified as well.
		$this->targetSongClass = $this->targetWorkClass->createSubClass('Song', 'The Song Class');
		$this->assertTrue($this->targetSongClass->exists());
		$this->assertIsA($classProxy->getImpToDelegateTo($this->targetSongClass), 'core_kernel_persistence_hardsql_Class');
	}
	
	public function testDeleteInstances(){
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
		$this->assertEqual($relatedMovie->getLabel(), 'The Matrix Reloaded');
	}
	
	public function testForceMode (){
		// Check if the returner implementation are correct
		core_kernel_persistence_PersistenceProxy::forceMode (PERSISTENCE_SMOOTH);
		$classProxy = core_kernel_persistence_ClassProxy::singleton();
		$impl = $classProxy->getImpToDelegateTo($this->targetSubjectClass);
		$this->assertTrue ($impl instanceof core_kernel_persistence_smoothsql_Class);
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);
		$this->assertEqual (count($this->targetSubjectSubClass->getInstances ()), 1);
		core_kernel_persistence_PersistenceProxy::restoreImplementation();
		$this->assertTrue (core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass) instanceof core_kernel_persistence_hardsql_Class);
		$this->assertTrue (core_kernel_persistence_ResourceProxy::singleton()->getImpToDelegateTo($this->subject1) instanceof core_kernel_persistence_hardsql_Resource);
	}
	
	public function testSetProperties (){
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
		$this->assertEqual($instfound, 1);
		$this->assertEqual($sanityfound, 1);
		
		foreach ($this->targetSongClass->getInstances() as $songInst) {
			$this->assertFalse($songInst->equals($instance), 'instance in getInstances of targetSongclass');
			$this->assertFalse($songInst->equals($sanityCheckInstance), 'sanity check instance in getInstances of targetSongclass');
		}
		
		$types = $instance->getTypes();
		$this->assertEqual(count($types), 1);
		$this->assertTrue($this->targetWorkClass->equals(current($types)));
		
		// remove the old type (targetWorkClass)
		
		$this->assertTrue($instance->removeType($this->targetWorkClass));
		$types = $instance->getTypes();
		$this->assertEqual(count($types), 0);
		$sanityfound = 0;
		foreach ($this->targetWorkClass->getInstances() as $workInst) {
			if ($workInst->equals($sanityCheckInstance)) {
				$sanityfound++;
			}
			$this->assertFalse($workInst->equals($instance), 'instance still in getInstances() of class after removeType()');
		}
		$this->assertEqual($sanityfound, 1);
		foreach ($this->targetSongClass->getInstances() as $songInst) {
			$this->assertFalse($songInst->equals($instance), 'instance in getInstances of targetSongclass');
			$this->assertFalse($songInst->equals($sanityCheckInstance), 'sanity check instance in getInstances of targetSongclass');
		}
		
		// set the new type (targetSongClass)
		
		$this->assertTrue($instance->setType($this->targetSongClass));
		$types = $instance->getTypes();
		$this->assertEqual(count($types), 1);
		$this->assertTrue($this->targetSongClass->equals(current($types)));
		$sanityfound = 0;
		foreach ($this->targetWorkClass->getInstances() as $workInst) {
			if ($workInst->equals($sanityCheckInstance)) {
				$sanityfound++;
			}
			$this->assertFalse($workInst->equals($instance), 'instance still in getInstances() of class after removeType()');
		}
		$this->assertEqual($sanityfound, 1);
		$instfound = 0;
		foreach ($this->targetSongClass->getInstances() as $songInst) {
			if ($songInst->equals($instance)) {
				$instfound++;
			}
			$this->assertFalse($songInst->equals($sanityCheckInstance), 'sanity check instance in getInstances of targetSongclass');
		}
		$this->assertEqual($instfound, 1);
		
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
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			
			// Get property values on single (literal) property 
			$props = $instance->getPropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
			$this->assertEqual(count($props), 1);
			foreach ($props as $prop){
				$this->assertTrue(is_string($prop));
			}
			// Get property values on single (resource) property 
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'));
			$this->assertEqual(count($props), 1);
			foreach ($props as $prop){
				$this->assertTrue(common_Utils::isUri($prop));
			}
			// Get property values on mutltiple property
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertEqual(count($props), 2);
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
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			
			$props = $instance->getPropertyValuesCollection(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertTrue($props instanceof core_kernel_classes_ContainerCollection);
			$this->assertEqual($props->count(), 2);
			foreach ($props->getIterator() as $prop){
				
				$this->assertTrue($prop instanceof core_kernel_classes_Resource);
			}		
		}
	}
	
	public function testGetPropertyValuesByLg (){
		foreach ($this->targetSubjectClass->getInstances() as $instance){	
			
			$props = $instance->getPropertyValuesByLg (new core_kernel_classes_Property(RDFS_LABEL), 'FR');
			$this->assertEqual($props->count(), 1);
			$this->assertTrue($props->get(0) instanceof core_kernel_classes_Literal);
			$this->assertEqual((string)$props->get(0), 'LABEL FR');
		}
	}
	
	public function testRemovePropertyValues (){
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
		
		$this->testSetProperties();
		
		foreach ($this->targetSubjectClass->getInstances() as $instance){	
			
			// Remove foreign single property
			$instance->removePropertyValueByLg(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'), 'FR');
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'));
			$this->assertFalse(empty($props));
			
			// Remove literal multiple property
			$instance->removePropertyValueByLg(new core_kernel_classes_Property(RDFS_LABEL), 'FR');
			$props = $instance->getPropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
			$this->assertTrue(empty($props));
			
			// Remove foreign multiple property
			$instance->removePropertyValueByLg(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'), 'FR');
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertFalse(empty($props));
		}
	}
	
	public function testClean (){
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$classProxy = core_kernel_persistence_ClassProxy::singleton();
		$switcher = new core_kernel_persistence_Switcher();
		
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
		$this->assertTrue($this->targetSongClass->delete(true));
		
		$this->assertFalse($referencer->isClassReferenced($this->targetWorkClass));
		$this->assertFalse($referencer->isClassReferenced($this->targetMovieClass));
		$this->assertFalse($referencer->isClassReferenced($this->targetSongClass));
		$this->assertFalse($this->targetWorkClass->exists());
		$this->assertFalse($this->targetWorkClass->exists());
		$this->assertFalse($this->targetSongClass->exists());
	}
	
	public function testFilterByLanguage() {
		return;
		$session = GenerisTestRunner::getTestSession();
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$true = new core_kernel_classes_Resource(GENERIS_TRUE);
		
		$this->object->setStatement($true->getUri(), RDFS_SEEALSO,'test1', '');
		$this->object->setStatement($true->getUri(), RDFS_SEEALSO,'test2', '');
		$this->object->setStatement($true->getUri(), RDFS_SEEALSO,'testing', 'EN');
		$this->object->setStatement($true->getUri(), RDFS_SEEALSO,'essai', 'FR');
		$this->object->setStatement($true->getUri(), RDFS_SEEALSO,'testung1', 'SE');
		$this->object->setStatement($true->getUri(), RDFS_SEEALSO,'testung2', 'SE');
		
		// Get some propertyValues as if it was obtained by an SQL Statement.
		// First test is made with the default language selected.
		$modelIds	= implode(',', array_keys($session->getLoadedModels()));
        $query =  "SELECT object, l_language FROM statements 
		    		WHERE subject = ? AND predicate = ?
		    		AND (l_language = '' OR l_language = ? OR l_language = ?)
		    		AND modelID IN ({$modelIds})";
		    		
        $result	= $dbWrapper->query($query, array(
        	GENERIS_TRUE,
        	RDFS_SEEALSO,
        	DEFAULT_LANG,
        	$session->getDataLanguage()
        ));
        
        $result = $result->fetchAll();
        
        $sorted = core_kernel_persistence_smoothsql_Utils::sortByLanguage($result, 'l_language');
        $filtered = core_kernel_persistence_smoothsql_Utils::getFirstLanguage($sorted);
        $this->assertTrue(count($sorted) == 3 && $sorted[0]['value'] == 'testing');
        $this->assertTrue(count($filtered) == 1 && $filtered[0] == 'testing');
       
        // Second test is based on a particular language.
        $session->setDataLanguage('FR');
        $result	= $dbWrapper->query($query, array(
        	GENERIS_TRUE,
        	RDFS_SEEALSO,
        	DEFAULT_LANG,
        	$session->getDataLanguage()
        ));
        
        $result = $result->fetchAll();
        
        $sorted = core_kernel_persistence_smoothsql_Utils::sortByLanguage($result, 'l_language');
        $filtered = core_kernel_persistence_smoothsql_Utils::getFirstLanguage($sorted);
        $this->assertTrue(count($sorted) == 4 && $sorted[0]['value'] == 'essai');
        $this->assertTrue(count($filtered) == 1 && $filtered[0] == 'essai');
		
		// Third test looks if the default language is respected.
		// No japanese values here, but default language set to EN.
		// Here we use the function filterByLanguage which aggregates sortByLanguage
		// and getFirstLanguage.
		$session->setDataLanguage('JA');
        $result	= $dbWrapper->query($query, array(
        	GENERIS_TRUE,
        	RDFS_SEEALSO,
        	DEFAULT_LANG,
        	$session->getDataLanguage()
        ));
        
        $result = $result->fetchAll();
        
        $filtered = core_kernel_persistence_smoothsql_Utils::filterByLanguage($result, 'l_language');
        $this->assertTrue(count($filtered) == 1 && $filtered[0] == 'testing');
		
		$session->setDataLanguage(DEFAULT_LANG);
		
		// Set back ontology to normal.
		$this->object->removeStatement($true->getUri(),RDFS_SEEALSO,'test1', '');
		$this->object->removeStatement($true->getUri(),RDFS_SEEALSO,'test2', '');
		$this->object->removeStatement($true->getUri(),RDFS_SEEALSO,'testing', 'EN');
		$this->object->removeStatement($true->getUri(),RDFS_SEEALSO,'essai', 'FR');
		$this->object->removeStatement($true->getUri(),RDFS_SEEALSO,'testung1', 'SE');
		$this->object->removeStatement($true->getUri(),RDFS_SEEALSO,'testung2', 'SE');
	}
	
	public function testIdentifyFirstLanguage() {
		return;
		$values = array(
			array('language' => 'EN', 'value' => 'testFallback'),
			array('language' => '', 'value' => 'testEN')
		);
		
		$this->assertTrue(core_kernel_persistence_smoothsql_Utils::identifyFirstLanguage($values) == 'EN');
		
		$values = array(
			array('language' => 'JA', 'value' => 'testJA1'),
			array('language' => 'JA', 'value' => 'testJA2'),
			array('language' => 'EN', 'value' => 'testEN1'),
			array('language' => 'EN', 'value' => 'testEN1'),
			array('language' => '', 'value' => 'testFallback1'),
			array('language' => '', 'value' => 'testFallback2')	
		);
		
		$this->assertTrue(core_kernel_persistence_smoothsql_Utils::identifyFirstLanguage($values) == 'JA');
	}

}
?>
