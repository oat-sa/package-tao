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
 * 
 */
require_once dirname(__FILE__) . '/../../generis/test/GenerisPhpUnitTestRunner.php';

use oat\generisHard\models\switcher\Switcher;
use oat\generisHard\models\hardapi\ResourceReferencer;
use oat\generisHard\models\proxy\PersistenceProxy;

/**
 * Test class for Class.
 * 
 * @author lionel.lecaque@tudor.lu
 * @package test
 */


class HardClassTest extends GenerisPhpUnitTestRunner {
	protected $object;
	
	protected function setUp(){

        GenerisPhpUnitTestRunner::initTest();
        $this->installExtension('generisHard');
		$this->object = new core_kernel_classes_Class(RDFS_RESOURCE);
		$this->object->debug = __METHOD__;
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
        
        common_Logger::d('starting hardify');
        //Test the search instances on the hard impl
        $switcher = new Switcher();
        $switcher->hardify($sub1Clazz, $options);
        unset ($switcher); //Required to update cache data
        common_Logger::d('done hardify');
        
        $propertyFilter = array(
            RDFS_LABEL => 'test case instance'
        );
        $instances = $sub1Clazz->searchInstances($propertyFilter, array('recursive'=>true));
        $this->assertTrue(array_key_exists($sub1ClazzInstance->getUri(), $instances));
        $this->assertTrue(array_key_exists($sub2ClazzInstance->getUri(), $instances));
        $this->assertTrue(array_key_exists($sub3ClazzInstance->getUri(), $instances));
        
        $switcher = new Switcher();
        $switcher->unhardify($sub1Clazz, $options);
        unset ($switcher); //Required to update cache data
        //Test the search instances on a shared model (smooth + hard)
        //Disable recursivity on hardify, and hardify the sub2Clazz
        
        $switcher = new Switcher();
        $options['recursive'] = false;
        $switcher->hardify($sub2Clazz, $options);
        unset ($switcher); //Required to update cache data
        
        $instances = $sub1Clazz->searchInstances($propertyFilter, array('recursive'=>true));
        $this->assertTrue(array_key_exists($sub1ClazzInstance->getUri(), $instances));
        $this->assertTrue(array_key_exists($sub2ClazzInstance->getUri(), $instances));
        $this->assertTrue(array_key_exists($sub3ClazzInstance->getUri(), $instances));
        
        try{
            $instances = $sub1Clazz->searchInstances($propertyFilter, array('recursive'=>true, 'offset'=>1));
            $this->assertTrue(false);
        }catch(common_Exception $e){
            $this->assertTrue(true);
        }
        
        $switcher = new Switcher();
        $switcher->unhardify($sub2Clazz, $options);
        unset ($switcher); //Required to update cache data

        //clean data test
        foreach($sub1Clazz->getInstances(true) as $instance){ $instance->delete(); }
        $sub1Clazz->delete(true);
        $sub2Clazz->delete(true);
        $sub3Clazz->delete(true);
    }
    
	
	public function testSearchInstancesHard($hard = true){
		
		if(!$hard) return;
		
		PersistenceProxy::forceMode(PERSISTENCE_HARD);
		
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#Languages');
		if(ResourceReferencer::singleton()->isClassReferenced($class)){
		
			$propertyFilter = array(
				RDFS_LABEL => 'English'
			);
			$options = array('like' => false, 'recursive' => 0);
					
			$languagesDependantProp = $class->searchInstances($propertyFilter, $options);
			
			$found = count($languagesDependantProp);
			$this->assertTrue($found > 0);
			
			$propertyFilter = array(
				RDF_VALUE => 'EN',
				RDF_TYPE	=> 'http://www.tao.lu/Ontologies/TAO.rdf#Languages'
			);
			$languagesDependantProp = $class->searchInstances($propertyFilter, $options);
			$nfound = count($languagesDependantProp);
			$this->assertTrue($nfound > 0);
			$this->assertEquals($found, $nfound);
			
		}
		
		PersistenceProxy::restoreImplementation();
		
	}
	
	public function testSearchInstancesVeryHard($hard=true){
		
		if(!$hard) return;
		
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		if(ResourceReferencer::singleton()->isClassReferenced($class)){
			
			//test simple search:
			$propertyFilter = array(
				'http://www.tao.lu/Ontologies/generis.rdf#login' => 's1',
				'http://www.tao.lu/Ontologies/generis.rdf#password'	=> 'e10adc3949ba59abbe56e057f20f883e'
			);
			$options = array('like' => false, 'recursive' => 0);
			$languagesDependantProp = $class->searchInstances($propertyFilter, $options);
			$nfound = count($languagesDependantProp);
			$this->assertTrue($nfound > 0);
			
			//test like option
			$propertyFilter = array(
				'http://www.tao.lu/Ontologies/generis.rdf#login' => '%s1',
				'http://www.tao.lu/Ontologies/generis.rdf#password'	=> 'e10adc3949ba59abbe56e057f20f883e'
			);
			$options = array('like' => true, 'recursive' => 0);
			$languagesDependantProp = $class->searchInstances($propertyFilter, $options);
			$likeFound = count($languagesDependantProp);
			$this->assertTrue($likeFound > 0);
			$this->assertEquals($nfound, $likeFound);
			
			//test reference resource prop value:
			$propertyFilter = array(
				'http://www.tao.lu/Ontologies/generis.rdf#login' => '%s1',
				'http://www.tao.lu/Ontologies/generis.rdf#password'	=> 'e10adc3949ba59abbe56e057f20f883e',
				'http://www.tao.lu/Ontologies/generis.rdf#userDefLg' => '%FR%'
			);
			
			$options = array('like' => true, 'recursive' => false);
			//test language filter (property 'http://www.tao.lu/Ontologies/generis.rdf#userDefLg' must be set to language dependent first!):
			// $options['lang'] = 'EN';
			
			$languagesDependantProp = $class->searchInstances($propertyFilter, $options);
			$refFound = count($languagesDependantProp);
			$this->assertTrue($refFound > 0);
			$this->assertEquals($nfound, $refFound);
		}
	}
}