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



/**
 * Test class for Class.
 *
 * @author lionel.lecaque@tudor.lu
 * @package test
 */


class SubscriptionsServiceTestCase extends UnitTestCase {

	private $subcriptionInst;
	private $maskInst;
	private $subscriptionUrl =  'http://tao21.localdomain/generis/';
	private $subscriptionResoourceUrl = 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item';

	public function setUp(){
        GenerisTestRunner::initTest();

	}



	public function testGetSubscriptions(){

		$subscptionClass = new core_kernel_classes_Class(CLASS_SUBCRIPTION);
		$this->subcriptionInst = $subscptionClass->createInstance('testSubcription','testSubcription');
		$maskClass = new core_kernel_classes_Class(CLASS_MASK);
		$this->maskInst = $maskClass->createInstance('testMask','testMask');
		$object = new core_kernel_classes_Resource($this->subscriptionResoourceUrl);
		$this->maskInst->setPropertyValue(new core_kernel_classes_Property(PROPERTY_MASK_PREDICATE),RDF_TYPE);
		$this->maskInst->setPropertyValue(new core_kernel_classes_Property(PROPERTY_MASK_PREDICATE),RDFS_LABEL);
		$this->maskInst->setPropertyValue(new core_kernel_classes_Property(PROPERTY_MASK_OBJECT),$object->getUri());
		$this->subcriptionInst->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SUBCRIPTION_URL),$this->subscriptionUrl);
		$this->subcriptionInst->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SUBCRIPTION_MASK),$this->maskInst->getUri());
		$subcriptions = core_kernel_subscriptions_Service::singleton()->getSubscriptions(null,new core_kernel_classes_Property(RDF_TYPE),$object);

		$this->assertTrue(in_array($this->subcriptionInst->getUri(),$subcriptions));


	}
	 
	public function testGetInstancesFromSubscription(){
		$object = new core_kernel_classes_Class($this->subscriptionResoourceUrl);
		$instances = core_kernel_subscriptions_Service::singleton()->getInstancesFromSubscription($this->subcriptionInst,$object);
		var_dump($instances);
		$this->fail('not imp yet');
	}
	 
	public function testGetPropertyValuesFromSubscription(){
		$items = $this->maskInst->getPropertyValues(new core_kernel_classes_Property(PROPERTY_MASK_SUBJECT));
		$labelProp = new core_kernel_classes_Property(RDFS_LABEL);
		foreach ($items as $item){
			$resource = new core_kernel_classes_Resource($item);
			$value = core_kernel_subscriptions_Service::singleton()->getPropertyValuesFromSubscription($this->subcriptionInst,$resource,$labelProp);
			var_dump($value);
		}


		$this->fail('not imp yet');
	}

	public function testGetInstances(){
		return;
		$itemClass = new core_kernel_classes_Class($this->subscriptionResoourceUrl);
		$labelProp = new core_kernel_classes_Property(RDFS_LABEL);
		$items = $itemClass->getInstances();
		 
		foreach ($items as $item){
			var_dump($item->getPropertyValues($labelProp));
			var_dump($item->getPropertyValuesCollection($labelProp));
		}
		 
		$this->fail('not imp yet');
	}

	public function testClean(){
		$this->subcriptionInst->delete();
		$this->maskInst->delete();

	}

}