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


class VirtuosoImplTestCase extends UnitTestCase {
        
        public function setUp(){
            GenerisTestRunner::initTest();
            core_kernel_persistence_PersistenceProxy::forceMode(PERSISTENCE_VIRTUOSO);
	}
        
        public function __testInstallTAO(){
                
                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                $rootPath = (substr(ROOT_PATH, -1)=='/')? substr(ROOT_PATH,0,-1) : ROOT_PATH;
                
                //load procedure:
//                $procedureScript = $rootPath.'/tao/install/db/VirtBulkRDFLoaderScript.sql';
//                $loaded = $virtuoso->execProcedure(file_get_contents($procedureScript));
//                var_dump($loaded);
//                
//                if(!$loaded){
//                        throw new core_kernel_persistence_virtuoso_Exception('cannot load TAO ontology loader procedure');
//                }
                
                $extensionManager = common_ext_ExtensionsManager::singleton();
		        $extensions = $extensionManager->getInstalledExtensions();
                $tmpLocalFiles = array();
                foreach($extensions as $extensionId => $extension){
			if($extensionId == 'generis') continue; 	//generis is the root and has been installed above 
                        $file = ROOT_PATH . $extensionId . '/models/ontology/local.rdf';
			if(file_exists($file)){
                                if (!file_exists($file) || !is_readable($file)) {
                                        throw new Exception("Unable to load ontology : $file");
                                }
                                
                                $directory = dirname($file);
                                if(!is_writable($directory)){
                                        throw new Exception("Unable to load ontology {$file} because the directory {$directory} is not writable");
                                }
                                
                                $model = file_get_contents($file);
                                $model = str_replace('LOCAL_NAMESPACE', LOCAL_NAMESPACE, $model);
                                $model = str_replace('{ROOT_PATH}', ROOT_PATH, $model);
                                
                                $tmpLocalFile = $directory.'/local.tmp.rdf';
                                $tmpLocalFiles[] = $tmpLocalFile;
                                file_put_contents($tmpLocalFile, $model);
			}
		}
                
                $loaded = $virtuoso->execProcedure('CALL loadTAOontology(?)', array($rootPath));
                if(!$loaded){
                        throw new core_kernel_persistence_virtuoso_Exception('cannot load TAO ontology');
                }
                
                foreach($tmpLocalFiles as $tmpLocalFile){
                        tao_helpers_File::remove($tmpLocalFile);
                }
                
        }
        
        public function testGetType(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $types = $resource->getType();
                
                $this->assertFalse(empty($types));
                $theType = array_pop($types);
                $this->assertEqual($theType->getUri(), 'http://www.tao.lu/Ontologies/TAO.rdf#Languages');
        }
        
        public function testGetLabel(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $this->assertEqual($resource->getLabel(), 'EN');
        }
        
        public function testGetPropertyValues(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $property1 = new core_kernel_classes_Property('http://www.w3.org/1999/02/22-rdf-syntax-ns#type');
                $types = $resource->getPropertyValues($property1);
                
                $this->assertFalse(empty($types));
                $this->assertEqual($types[0], 'http://www.tao.lu/Ontologies/TAO.rdf#Languages');
                
                $property2 = new core_kernel_classes_Property('http://www.w3.org/1999/02/22-rdf-syntax-ns#value');
                $values = $resource->getPropertyValues($property2);
                $this->assertEqual($values[0], 'EN');
                
                $property3 = new core_kernel_classes_Property('http://www.w3.org/2000/01/rdf-schema#label');
                $values = $resource->getPropertyValues($property3);
                $this->assertEqual($values[0], 'EN');
                
                $label = $resource->getOnePropertyValue($property3);
                $this->assertIsA($label, 'core_kernel_classes_Literal');
                
                
        }
        
        public function testPropertyValuesCollection(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $property1 = new core_kernel_classes_Property('http://www.w3.org/1999/02/22-rdf-syntax-ns#type');
                $typesCollection = $resource->getPropertyValuesCollection($property1);
                $this->assertFalse($typesCollection->isEmpty());
                $this->assertEqual($typesCollection->count(), 1);
                
                foreach($typesCollection->getIterator() as $type){
                        $this->assertIsA($type, 'core_kernel_classes_Resource');
                        $this->assertEqual($type->getUri(), 'http://www.tao.lu/Ontologies/TAO.rdf#Languages');
                }
                
        }
        
        public function testSetGetPropertyValue(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $property1 = new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOtestCase.rdf#Property1');
                $value1 = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOtestCase.rdf#Resource1_'.time());
                
                $this->assertTrue($resource->setPropertyValue($property1, $value1->getUri()));
                $this->assertTrue($resource->removePropertyValues($property1));
                $this->assertEqual(count($resource->getPropertyValues($property1)), 0);
                
                //language dependent:
                $value2 = 'personal value EN '.date('d-m-Y H:i:s');
                $this->assertTrue($resource->setPropertyValueByLg($property1, $value2, 'EN'));
                $values = $resource->getPropertyValuesByLg($property1, 'EN');
                $this->assertEqual(count($values), 1);
                $this->assertEqual($values[0], $value2);
                
                $value3 = 'personal value DE '.date('d-m-Y H:i:s');
                $this->assertTrue($resource->setPropertyValueByLg($property1, $value3, 'DE'));
                $values = $resource->getPropertyValuesByLg($property1, 'DE');
                $this->assertEqual(count($values), 1);
                $this->assertEqual($values[0], $value3);
                
                $this->assertEqual(count($resource->getPropertyValues($property1)), 2);
                $this->assertTrue($resource->removePropertyValueBylg($property1, 'EN'));
                $this->assertEqual(count($resource->getPropertyValues($property1)), 1);
                $this->assertTrue($resource->removePropertyValues($property1));
                $this->assertEqual(count($resource->getPropertyValues($property1)), 0);
        }
        
        public function testSetType(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#myLanguages');
                
                //add type 'myLanguages':
                $this->assertTrue($resource->setType($class));
                $this->assertEqual(count($resource->getType()), 2);
                
                //remove type 'myLanguages'
                $this->assertTrue($resource->removeType($class));
                $this->assertEqual(count($resource->getType()), 1);
        }
        
        public function testSetPropertiesValues(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                
                $propertiesValues = array(
                    'http://www.tao.lu/Ontologies/TAOTestCase1.rdf#Prop1' => 'value@'.time(),
                    'http://www.tao.lu/Ontologies/TAOTestCase2.rdf#Prop2' => 'value2@'.time(),
                    'http://www.tao.lu/Ontologies/TAOTestCase1.rdf#Prop3' => 'http://www.tao.lu/Ontologies/TAOtestCase3.rdf#Value_'.time()
                );
                
                $this->assertTrue($resource->setPropertiesValues($propertiesValues));
                
                foreach($propertiesValues as $propUri => $val){
                        $this->assertTrue($resource->removePropertyValues(new core_kernel_classes_Property($propUri)));
                }
        }
        
        public function testGetRDFtriples(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $triplesCollection = $resource->getRdfTriples();
                $this->assertFalse($triplesCollection->isEmpty());
        }
        
        public function testDuplicate(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $clone = $resource->duplicate();
                
                $this->assertIsA($clone, 'core_kernel_classes_Resource');
                $this->assertEqual($clone->getLabel(), $resource->getLabel());
                $this->assertTrue($clone->delete());
        }
        
        public function testCreateCountInstances(){
                $class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#Languages');
                $count = $class->countInstances();
                $instances = $class->getInstances();
                $this->assertEqual($count, count($instances));
        }
        
        public function testCreateInstances(){
                $class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#Languages');
                $count = $class->countInstances();
                $instances = $class->getInstances();
                
                $newLabel = 'newInstance';
                $newInstance =  $class->createInstance($newLabel, 'created for unit virtuoso test @ '.date('Y:i:s'));
                $this->assertIsA($newInstance, 'core_kernel_classes_Resource');
                $this->assertEqual($class->countInstances(), $count+1);
                $this->assertEqual($newLabel, $newInstance->getLabel());
                
                
                //delete it and count instances again:
                $this->assertTrue($newInstance->delete());
                $this->assertEqual($class->countInstances(), $count);
        }
        
        public function testCreateSubclass(){
                $class = new core_kernel_classes_Class(RDFS_CLASS);
                $label = 'new subclass';
                $comment = 'created for unit virtuoso test @ '.date('Y:i:s');
                $subclass = $class->createSubClass($label, $comment);
                $this->assertIsA($subclass, 'core_kernel_classes_Class');
                
                $label2 = 'sub_'.$label;
                $subSubClass = $subclass->createSubClass($label2, $comment);
                $this->assertIsA($subSubClass, 'core_kernel_classes_Class');
                
                $foundSubclasses = $subclass->getSubClasses();
                $this->assertEqual(count($foundSubclasses), 1);
                
                //one identical triple allowed by language.
//                $this->assertTrue($subSubClass->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTestCase1.rdf#Prop1'), $subclass->getUri()));
//                $this->assertTrue($subSubClass->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTestCase1.rdf#Prop1'), $subclass->getUri()));
//                $this->assertTrue($subSubClass->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTestCase1.rdf#Prop1'), 'hello'));
//                $this->assertTrue($subSubClass->setPropertyValueByLg(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTestCase1.rdf#Prop1'), 'hello', 'EN'));
                
                $this->assertTrue($subSubClass->isSubClassOf($subclass));
                $parentClasses = $subSubClass->getParentClasses();
                $this->assertEqual(count($parentClasses), 1);
                $theParentClass = array_pop($parentClasses);
                $this->assertEqual($theParentClass->getUri(), $subclass->getUri());
                
                $this->assertTrue($subSubClass->delete());
                $this->assertTrue($subclass->delete());
        }
        
        public function testGetProperties(){
                $class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#List');
                $this->assertEqual(count($class->getProperties()), 1);
        }
        
        public function testSetInstance(){
                
                $class = new core_kernel_classes_Class(RDFS_CLASS);
                $label1 = 'new subclass 1';
                $label2 = 'new subclass 2';
                $comment = 'created for virtuoso unit test @ '.date('d-m-Y H:i:s');
                
                $subclass1 = $class->createSubClass($label1, $comment);
                $subclass2 = $class->createSubClass($label2, $comment);
                
                $this->assertIsA($subclass1, 'core_kernel_classes_Class');
                $this->assertIsA($subclass2, 'core_kernel_classes_Class');
                
                $label3 = 'new instance';
                $newInstance1 = $subclass1->createInstance($label3, $comment);
                
                $this->assertIsA($newInstance1, 'core_kernel_classes_Resource');
                $this->assertEqual($subclass1->countInstances(), 1);
                
                $newInstance2 = $subclass2->setInstance($newInstance1);
//                var_dump($newInstance1, $newInstance2, $subclass1, $subclass2);
                
                $this->assertIsA($newInstance1, 'core_kernel_classes_Resource');
                $this->assertEqual($subclass2->countInstances(), 1);
                $this->assertNotEqual($newInstance1->getUri(), $newInstance2->getUri());
                $this->assertEqual($newInstance1->getLabel(), $newInstance2->getLabel());
                
                $this->assertTrue($newInstance1->delete());
                $this->assertTrue($newInstance2->delete());
                $this->assertTrue($subclass1->delete());
                $this->assertTrue($subclass2->delete());
        }
        
        public function testSearchInstance(){
                $class = new core_kernel_classes_Class(RDFS_CLASS);
                $instance = $class->createInstance('instance for unit test', 'instance for unit test @ '.date('d-m-Y H:i:s'));
                $prop = new core_kernel_classes_Property(RDFS_COMMENT);
                $prop2 = new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOtestCase.rdf#property1');
                $prop3 = new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOtestCase.rdf#property2');
                $instance1 = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOtestCase.rdf#instance1');
                $instance2 = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOtestCase.rdf#instance2');
                $complexStringValue = "/taoDelivery/ItemDelivery/runner?itemUri=^itemUri&testUri=^testUri&deliveryUri=^deliveryUri&abs=*?.+?^$\\";//.+?^$
                //'*', '.', '+', '?', '^', '$'
                
                $instance->setPropertyValue($prop, 'comment1');
                $instance->setPropertyValueByLg($prop, 'comment2', 'EN');
                $instance->setPropertyValue($prop2, $instance1->getUri());
                $instance->setPropertyValueByLg($prop2, $instance2->getUri(), 'en');
                $instance->setPropertyValue($prop3, $complexStringValue);
                
                $propertyFilters = array(
                    $prop->getUri() => 'comment1',
                    $prop2->getUri() => $instance1->getUri()
                );
                //like(true), lang (''), chaining (or/and), recursive(false)
                $options = array('like'=>false);
                
                $foundInstances = $class->searchInstances($propertyFilters, $options);
                $this->assertFalse(empty($foundInstances));
                
                
                
                $propertyFilters = array(
                    $prop->getUri() => 'comment2',
                    $prop2->getUri() => $instance1->getUri()
                );
                $options = array('like'=>false, 'lang' => 'en');
                $foundInstances = $class->searchInstances($propertyFilters, $options);
                $this->assertFalse(empty($foundInstances));
                
                
                $propertyFilters = array(
                    $prop->getUri() => 'ent2',
                    $prop2->getUri() => $instance1->getUri()
                );
                $options = array('like'=>true, 'lang' => 'en');
                $foundInstances = $class->searchInstances($propertyFilters, $options);
                $this->assertFalse(empty($foundInstances));
                
                
                
                $propertyFilters = array(
                    $prop->getUri() => 'comment1'
                );
                $options = array('like'=>false, 'lang' => 'en');
                $foundInstances = $class->searchInstances($propertyFilters, $options);
                $this->assertTrue(empty($foundInstances));
                
                
                
                $propertyFilters = array(
                    $prop->getUri() => 'comment2',
                    $prop2->getUri() => $instance2->getUri()
                );
                $options = array('like'=>false, 'lang' => 'en');
                $foundInstances = $class->searchInstances($propertyFilters, $options);
                $this->assertTrue(empty($foundInstances));
                
                
                
                $propertyFilters = array(
                    $prop3->getUri() => $complexStringValue
                );
                $options = array();
                $foundInstances = $class->searchInstances($propertyFilters, $options);
                $this->assertFalse(empty($foundInstances));
                
                
                
                //delete instance:
                $this->assertTrue($instance->delete());
        }
        
        public function testCreateSuperUser(){
                $modelUri = common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri();
                $superUserUri = $modelUri.'superUser';
                $superUser = new core_kernel_classes_Resource($superUserUri);
                try{
                        if($superUser->exists()){
                                return true;
                        }
                }catch(Exception $e){
                        echo 'notice: caught exception when checking the super user: '.$e->getMessage();
                }
                
                $class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole');
                $newSuperUser = $class->createInstance('SuperUser', 'super user created during the Virtuoso Unit Test', '#superUser');
                
                $this->assertIsA($newSuperUser, 'core_kernel_classes_Resource');
                $this->assertEqual($newSuperUser->getUri(), $superUserUri);
                
                $newSuperUser->setPropertiesValues(array(
                        'http://www.tao.lu/Ontologies/generis.rdf#login' => 'tao',
                        'http://www.tao.lu/Ontologies/generis.rdf#password' => md5('tao'),
                        'http://www.tao.lu/Ontologies/generis.rdf#userDefLg' => 'http://www.tao.lu/Ontologies/TAO.rdf#LangEN',
                        'http://www.tao.lu/Ontologies/generis.rdf#userUILg' => 'http://www.tao.lu/Ontologies/TAO.rdf#LangEN'
                ));
        }
        
        public function _testExecProcedure(){
                
                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                
                $rootPath = (substr(ROOT_PATH, -1)=='/')? substr(ROOT_PATH,0,-1) : ROOT_PATH;
                
                $this->assertTrue($virtuoso->execProcedure('CALL loadTAOontology(?)', array($rootPath)));
                
        }
        
        
}
?>
