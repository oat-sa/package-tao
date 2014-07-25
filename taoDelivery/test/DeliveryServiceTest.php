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
require_once dirname(__FILE__) . '/../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class DeliveryServiceTest extends TaoPhpUnitTestRunner {
	
    /**
     * @var taoDelivery_models_classes_DeliveryTemplateService
     */
	protected $deliveryService = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TaoPhpUnitTestRunner::initTest();
		$this->deliveryService = taoDelivery_models_classes_DeliveryTemplateService::singleton();
	}
	
	public function testService(){
		$this->assertIsA($this->deliveryService, 'taoDelivery_models_classes_DeliveryTemplateService');
	}
	
	public function testCreateInstance(){
		$delivery = $this->deliveryService->createInstance(new core_kernel_classes_Class(TAO_DELIVERY_CLASS), 'UnitTestDelivery2');
		$this->assertIsA($delivery, 'core_kernel_classes_resource');
		
		//check if the default delivery server exists:
		$defaultDeliveryServer = $delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
		$this->assertNotNull($defaultDeliveryServer);
		
		$defaultServer = taoResultServer_models_classes_ResultServerAuthoringService::singleton()->getDefaultResultServer();
		if(!is_null($defaultDeliveryServer)){
			$this->assertEquals($defaultDeliveryServer->getUri(), $defaultServer->getUri());
		}
		
		$this->deliveryService->deleteInstance($delivery);
		$this->assertFalse($delivery->exists());
		$this->assertNull($delivery->getOnePropertyValue(new core_kernel_classes_Property(RDF_TYPE)));
	}
	
	public function testCreateDeleteClass() {
	    $deliveryClass = $this->deliveryService->createSubClass($this->deliveryService->getRootClass(), 'UnitTestDeliveryClass');
	    $this->assertIsA($deliveryClass, 'core_kernel_classes_class');
		$this->assertTrue($deliveryClass->exists());
	    
	    $delivery = $this->deliveryService->createInstance($deliveryClass, 'UnitTestDelivery3');
		$this->assertIsA($delivery, 'core_kernel_classes_resource');
		$this->assertTrue($delivery->exists());
		$this->assertEquals(1, count($delivery->getTypes()));
		$this->assertTrue($delivery->isInstanceOf($deliveryClass));
		$this->deliveryService->deleteInstance($delivery);
		$this->assertFalse($delivery->exists());
		
		 
		$this->deliveryService->deleteDeliveryClass($deliveryClass);
	    $this->assertFalse($deliveryClass->exists());
	}

}

