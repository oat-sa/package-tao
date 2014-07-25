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

use oat\generisHard\models\hardapi\TableManager;
use oat\generisHard\models\hardapi\Utils;
use oat\generisHard\models\hardapi\ResourceReferencer;
use oat\generisHard\models\hardapi\Exception;

/**
 * HardApiTestCase enables you to test the classes that manage data storage 
 * in hard database
 */
class HardApiTest extends GenerisPhpUnitTestRunner {
	
	/**
     * Make test case initializations
	 * @see SimpleTestCase::setUp()
	 */
	protected function setUp(){
        GenerisPhpUnitTestRunner::initTest();
        $this->installExtension('generisHard');
	}
	
	/**
	 * Test the HarApi utils class
	 * @see Utils
	 */
	public function testUtils(){
		
		$class = new core_kernel_classes_Class(CLASS_ROLE);
		$shortName = Utils::getShortName($class);
		$this->assertEquals($shortName, "06ClassRole");
		
		$longName = Utils::getLongName($shortName);
		$this->assertEquals($longName, $class->getUri());
	}
	
	/**
	 * test the creation of a simple table with the TableManager
	 * @see TableManager
	 */
	public function testCreateTable(){
		$myTblMgr = new TableManager('_15ClassRole');
		$this->assertFalse($myTblMgr->exists());
		
		$this->assertTrue($myTblMgr->create());
		$this->assertTrue($myTblMgr->exists());
		
		$this->assertTrue($myTblMgr->remove());
		$this->assertFalse($myTblMgr->exists());
	}
	
	
	/**
	 * test the creation of a complex table with the TableManager
	 * @see TableManager
	 */
	public function testCreateComplexTable(){
		
		$myLevelTblMgr = new TableManager('_15ClassLevel');
		$this->assertFalse($myLevelTblMgr->exists());
		$this->assertTrue($myLevelTblMgr->create());
		$this->assertTrue($myLevelTblMgr->exists());
		
		$myRoleTblMgr = new TableManager('_15ClassRole');
		$this->assertFalse($myRoleTblMgr->exists());
		$this->assertTrue($myRoleTblMgr->create(array(
			array('name' => '15Description'),
			array(
				'name' 		=> '15Level',
				'foreign'	=> '15ClassLevel'
			)
		)));
		$this->assertTrue($myRoleTblMgr->exists());
		
		$this->assertTrue($myLevelTblMgr->remove());
		$this->assertFalse($myLevelTblMgr->exists());
		
		$this->assertTrue($myRoleTblMgr->remove());
		$this->assertFalse($myRoleTblMgr->exists());
		
		
	}
	
	public function testFailures(){
		try{
			$tblmgr = new TableManager('');
			$this->assertTrue(false, "An exception should be thrown because the table name is empty.");
		}
		catch (Exception $e){
			$this->assertTrue(true);
		}
		
		try{
			$tblmgr = new TableManager('statements');
			$this->assertTrue(false, "An exception should be thrown because the table name is dangerous.");
		}
		catch (Exception $e){
			$this->assertTrue(true);
		}
	}
	
	/**
	 * Test the referencer on resources
	 * @see oat\generisHard\models\hardapi\ResourceReferencer
	 */
	public function testResourceReferencer(){
		$referencer = ResourceReferencer::singleton();
		$this->assertIsA($referencer, 'oat\generisHard\models\hardapi\ResourceReferencer');
		
		$testTakerClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		$testTaker = $testTakerClass->createInstance('test taker 1');
		
		$table = '_'.Utils::getShortName($testTakerClass);
		$referencer->referenceResource($testTaker, $table);
		$this->assertTrue($referencer->isResourceReferenced($testTaker));
		$this->assertEquals($referencer->resourceLocation($testTaker), $table);
		
		$referencer->unReferenceResource($testTaker);
		$this->assertFalse($referencer->isResourceReferenced($testTaker));
		$testTaker->delete();
	}
	
	/**
	 * Test the referencer on classes
	 * @see oat\generisHard\models\hardapi\ResourceReferencer
	 */
	public function testClassReferencer(){
		
		$referencer = ResourceReferencer::singleton();
		$this->assertIsA($referencer, 'oat\generisHard\models\hardapi\ResourceReferencer');
		
		$class = new core_kernel_classes_Class(CLASS_ROLE) ;
		
		$table = '_'.Utils::getShortName($class);
		
		$myTblMgr = new TableManager($table);
		$this->assertFalse($myTblMgr->exists());
		
		$this->assertTrue($myTblMgr->create());
		$this->assertTrue($myTblMgr->exists());
		
		
		$referencer->referenceClass($class, array ('table'=>$table));
		
		$this->assertTrue($referencer->isClassReferenced($class));
		$this->assertTrue($referencer->isClassReferenced($class, $table));
		$foundTables = $referencer->classLocations($class);
		foreach($foundTables as $foundTable){
			$this->assertEquals($foundTable['table'], $table);
			$this->assertEquals($foundTable['uri'], $class->getUri());
		}
		
		$this->assertTrue($myTblMgr->exists());
		$referencer->unReferenceClass($class);
		$this->assertFalse($referencer->isClassReferenced($class));
		$this->assertFalse($myTblMgr->exists());
	}
	
	/**
	 * Test the referencer on properties, using the file caching mode
	 * (it's the default caching mode for the properties)
	 * @see oat\generisHard\models\hardapi\ResourceReferencer
	 */
	public function testPropertyReferencer(){
		$referencer = ResourceReferencer::singleton();
		$this->assertIsA($referencer, 'oat\generisHard\models\hardapi\ResourceReferencer');
		
		$referencer->setPropertyCache(ResourceReferencer::CACHE_FILE);
		$referencer->clearCaches();
		
		$class = new core_kernel_classes_Class(CLASS_GENERIS_USER);
		$table = '_'.Utils::getShortName($class);
		
		// this part simulates a hardifying of the Userclass
		$myUserTblMgr = new TableManager($table);
		$this->assertFalse($myUserTblMgr->exists());
		$this->assertTrue($myUserTblMgr->create(array(
			array('name' => '05label'),
			array('name' => '05comment'),
			array('name' => '07login'),
			array('name' => '07password'),
			array('name' => '07userMail'),
			array('name' => '07userFirstName'),
			array('name' => '07userLastName')
		)));
		$this->assertTrue($myUserTblMgr->exists());
		$referencer->referenceClass($class);
		$this->assertTrue($referencer->isClassReferenced($class));
		
		// test start on the cache containing the simulated data
		// in case of a  fallback to the real sata (class_to_table) the tests fail
		
		$labelProperty = new core_kernel_classes_Property(RDFS_LABEL);
		$this->assertTrue($referencer->isPropertyReferenced($labelProperty));
		
		$commentProperty = new core_kernel_classes_Property(RDFS_COMMENT);
		$this->assertTrue($referencer->isPropertyReferenced($commentProperty));
		
		$loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
		$this->assertTrue($referencer->isPropertyReferenced($loginProperty));
		
		$passwordProperty = new core_kernel_classes_Property(PROPERTY_USER_PASSWORD);
		$this->assertTrue($referencer->isPropertyReferenced($passwordProperty));
		
		$firstNameProperty = new core_kernel_classes_Property(PROPERTY_USER_FIRSTNAME);
		foreach($referencer->propertyLocation($firstNameProperty) as $foundTable){
			$this->assertEquals($foundTable, $table);
		}
		
		$this->assertTrue($myUserTblMgr->exists());
		$referencer->unReferenceClass($class);
		$this->assertFalse($referencer->isClassReferenced($class));
		$this->assertFalse($myUserTblMgr->exists());
		
		// Testing the cache...
		$cache = common_cache_FileCache::singleton();
		$serial = 'hard-api-property';
		$this->assertTrue($cache->has($serial));
		
		try{
			$cacheContent = $cache->get($serial);
			$this->assertTrue(is_array($cacheContent));
			$this->assertTrue(count($cacheContent) > 0);
			$this->assertTrue(array_key_exists(RDFS_LABEL, $cacheContent));
			$this->assertTrue(array_key_exists(PROPERTY_USER_LOGIN, $cacheContent));
		}
		catch (common_cache_Exception $e){
			$this->fail('Cannot access hard-api-property cache.');
		}

		//clear the cache
		$cache->remove($serial);
		$this->assertFalse($cache->has($serial));
	}
	

}
?>