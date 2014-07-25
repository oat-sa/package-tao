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
error_reporting(E_ALL);

require_once dirname(__FILE__) . '/GenerisTestRunner.php';


class ApiModelTestCase extends UnitTestCase {
	protected $object;

	function __construct() {
    	parent::__construct();
    }
	
    /**
     * Setting the Api to test
     *
     */
    public function setUp(){
		GenerisTestRunner::initTest();
    	
		$this->object = core_kernel_impl_ApiModelOO::singleton();
		core_kernel_classes_DbWrapper::singleton()->dbConnector->debug=false;
	}
	
	public function testGetRootClass(){
		$localModel = common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri();
		$this->assertFalse(empty($localModel));
		
		$rootClasses = $this->object->getRootClasses();
		$this->assertIsA($rootClasses,'common_Collection');
		$expectedResult = 	array( 	
			WIDGET_CONSTRAINT_TYPE,
			CLASS_WIDGET,
			RDFS_RESOURCE
		);
		
		$pattern = "/^".preg_quote($localModel, '/')."/";
		foreach ($rootClasses->getIterator() as $rootClass) {
			
			$this->assertIsA($rootClass,'core_kernel_classes_Class');
			
			$parentClasses = $rootClass->getParentClasses(true);
			$this->assertEqual(count($parentClasses), 1);
			foreach($parentClasses as $uri => $parent){
				$this->assertEqual($uri,  RDFS_CLASS);
			}
			//don't check the user root classes
			if(!preg_match($pattern, $rootClass->getUri())){
				$this->assertTrue(in_array($rootClass->getUri(), $expectedResult));
			}
		}
	}
	
	public function testGetMetaClasses(){
		$metaClasses = $this->object->getMetaClasses();
		$this->assertIsA($metaClasses,'core_kernel_classes_ContainerCollection');
		foreach ($metaClasses as $metaClass){
		    if ($metaClass->getUri() == RDFS_DATATYPE) {
        		$this->assertIsA($metaClass,'core_kernel_classes_Class');
        		$this->assertEqual($metaClass->getLabel(),'Datatype');
        		$this->assertEqual($metaClass->getComment(),'The class of RDF datatypes.');
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
		$this->assertIsA($collection,'core_kernel_classes_ContainerCollection');
		foreach ($collection->getIterator() as $aClass) {
			$this->assertIsA($aClass,'core_kernel_classes_Class');
			if($aClass->getUri() === RDFS_CLASS){
				$this->assertEqual($aClass->getLabel(),'Class');
				$this->assertEqual($aClass->getComment(),'The class of classes.');
			}
			if($aClass->getUri() === RDF_STATEMENT){
				$this->assertEqual($aClass->getLabel(),'Statement');
				$this->assertEqual($aClass->getComment(), 'The class of RDF statements.');
			}
			if($aClass->getUri() === RDFS_RESOURCE){
				$this->assertEqual($aClass->getLabel(),'Resource');
				$this->assertEqual($aClass->getComment(), 'The class resource, everything.');
			}
			if($aClass->getUri() ===  RDF_PROPERTY){
				$this->assertEqual($aClass->getLabel(),'Property');
				$this->assertEqual($aClass->getComment(), 'The class of RDF properties.');
			}
			if($aClass->getUri() ===  CLASS_GENERIS_RESOURCE){
				$this->assertEqual($aClass->getLabel(),'generis_Ressource');
				$this->assertEqual($aClass->getComment(), 'generis_Ressource');
			}
			if($aClass->getUri() ===  RDFS_DATATYPE){
				$this->assertEqual($aClass->getLabel(),'Datatype');
				$this->assertEqual($aClass->getComment(), 'The class of RDF datatypes.');
			}
		}
	}
}
?>