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
require_once dirname(__FILE__) . '/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * This class enable you to test the models managment of the tao extension
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package tao
 * @subpackage test
 */
class ServiceTestCase extends UnitTestCase {
	
	/**
	 * @var tao_models_classes_TaoService we share the service instance between the tests
	 */
	protected $taoService = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoTestRunner::initTest();
	}
	
	
	
	/**
	 * Test the service factory: dynamical instantiation and single instance serving  
	 * @see tao_models_classes_ServiceFactory::get
	 */
	public function testServiceFactory(){
		
		$this->assertNull($this->taoService);
		
		//test factory instantiation
		$this->taoService = tao_models_classes_TaoService::singleton();
		$this->assertIsA($this->taoService, 'tao_models_classes_TaoService');
		
		$userService = tao_models_classes_UserService::singleton();
		$this->assertIsA($userService, 'tao_models_classes_UserService');
		
		$taoService2 = tao_models_classes_TaoService::singleton();
		$this->assertIsA($taoService2, 'tao_models_classes_TaoService');
		
		//test factory singleton
		$this->assertReference($this->taoService, $taoService2);
		
	}
	
	
	/**
	 * Test the taoService methods, the extensions loading
	 * @see tao_models_classes_TaoService::getLoadedExtensions
	 */
	public function testTaoServiceExtention(){
		
		foreach ($this->taoService->getAllStructures() as $structure) {
			$this->assertTrue(isset($structure['extension']));
			$this->assertTrue(isset($structure['id']));
			$this->assertIsA($structure['data'], 'SimpleXMLElement');

			$this->assertTrue(isset($structure['id']));
			foreach ($structure['sections'] as $sectionData) {
				$this->assertTrue(isset($sectionData['name']));
				$this->assertTrue(isset($sectionData['url']));
			}
		}
	}
	
	/**
	 * Test the Service methods from the abtract Service class, 
	 * but using the tao_models_classes_TaoService as a common child to access the methods of the abtract class
	 * @see tao_models_classes_Service
	 */
	public function testAbstractService(){
		
		//we create a temp object for the needs of the test
		$generisResourceClass = new core_kernel_classes_Class(GENERIS_RESOURCE);
		$testModelClass = $generisResourceClass->createSubClass('aModel', 'test model');
		$this->assertIsA($testModelClass, 'core_kernel_classes_Class');
		
		$testProperty = $testModelClass->createProperty('aKey', 'test property');
		$this->assertIsA($testProperty, 'core_kernel_classes_Property');
		
		//get the diff between the class and the subclass
		$diffs = $this->taoService->getPropertyDiff($testModelClass, $generisResourceClass);
		$this->assertIsA($diffs, 'array');
		$diffProperty = $diffs[0];
		$this->assertNotNull($diffProperty);
		$this->assertIsA($diffProperty, 'core_kernel_classes_Property');
		$this->assertEqual($testProperty->getUri(), $diffProperty->getUri());
		
		//test the createInstance method 
		$testInstance = $this->taoService->createInstance($testModelClass, 'anInstance');
		$this->assertIsA( $testInstance, 'core_kernel_classes_Resource');
		
		//get the class from the instance
		$clazz = $this->taoService->getClass($testInstance);
		$this->assertIsA($clazz, 'core_kernel_classes_Class');
		$this->assertEqual($clazz->getUri(), $testModelClass->getUri());
		
		//test the bindProperties method
		$testInstance = $this->taoService->bindProperties(
			$testInstance, 
			array(
				$testProperty->getUri() => array('value' => 'aValue')
			)
		);
		$this->assertIsA( $testInstance, 'core_kernel_classes_Resource');
		$this->assertEqual($testInstance->getUniquePropertyValue($testProperty)->literal, 'aValue');
		
		
		//clone instance
		$clonedInstance = $this->taoService->cloneInstance($testInstance, $testModelClass);
		$this->assertIsA( $clonedInstance, 'core_kernel_classes_Resource');
		$this->assertNotEqual($clonedInstance->getUri(), $testInstance->getUri());
		$this->assertEqual($testInstance->getUniquePropertyValue($testProperty), $clonedInstance->getUniquePropertyValue($testProperty));
		
		//get the properties between 2 classes
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		$itemSubClasses = $itemClass->getSubClasses(false);
		if(count($itemSubClasses) > 0){
			foreach($itemSubClasses as $testClass){ break; }
		}
		else{
			$testClass =$itemClass;
		}
		$foundProp = $this->taoService->getClazzProperties($testClass);
		$this->assertIsA($foundProp, 'array');
        $this->assertTrue(count($foundProp) >= 3, 'the class item or one of is subclasses has less then three properties');
        
		//delete the item class in case it has been created if it was not in the model
		$localNamspace = common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri();
		if(preg_match("/^".preg_quote($localNamspace, "/")."/", $itemClass->getUri())){
			$itemClass->delete();
		}
		
		//clean them
		$testInstance->delete();
		$clonedInstance->delete();
		$testProperty->delete();
		$testModelClass->delete();
	}
	
	public function testFileCacheService(){
		$fc = common_cache_FileCache::singleton();
		
		$fc->put("string1", 'testcase1');
		$fromCache = $fc->get('testcase1');
		$this->assertTrue(is_string($fromCache), 'string is not returned as string from FileCache');
		$this->assertEqual($fromCache,  "string1");
		$this->assertTrue($fc->has('testcase1'), ' has() did not find serial "testcase1"');
		$this->assertFalse($fc->has('testcase2'), ' has() did find non existal serial "testcase2"');
		$fc->remove('testcase1');
		$this->assertFalse($fc->has('testcase1'), ' has() finds removed serial "testcase1"');
		
		$fc->put(42, 'testcase2');
		$fromCache = $fc->get('testcase2');
		$this->assertTrue(is_numeric($fromCache), 'numeric is not returned as numeric from FileCache');
		$this->assertEqual($fromCache,  42);
		$fc->remove('testcase2');
		
		$testarr = array(
			'a' => 'astring',
			'b' => 3.1415
		);
		$fc->put($testarr, 'testcase3');
		$fromCache = $fc->get('testcase3');
		$this->assertTrue(is_array($fromCache), 'array is not returned as array from FileCache');
		$this->assertEqual($fromCache,  $testarr);
		$fc->remove('testcase3');
		
		
		$e = new Exception('message');
		$fc->put($e, 'testcase4');
		$fromCache = $fc->get('testcase4');
		$this->assertTrue(is_object($fromCache), 'object is not returned as object from FileCache');
		$this->assertIsA($fromCache, 'Exception');
		$this->assertEqual($e->getMessage(),  $fromCache->getMessage());
		$fc->remove('testcase4');
		
		$badstring = 'abc\'abc\'\'abc"abc""abc\\abc\\\\abc'."abc\n\nabc\l\nabc\l\nabc".'_NULL_é_NUL_'.chr(0).'_';
		$fc->put($badstring, 'testcase5');
		$fromCache = $fc->get('testcase5');
		$this->assertEqual($fromCache, $badstring);
		$fc->remove('testcase5');
	}
	
}
?>