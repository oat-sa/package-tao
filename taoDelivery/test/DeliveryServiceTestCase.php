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
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class DeliveryServiceTestCase extends UnitTestCase {
	
	protected $deliveryService = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TaoTestRunner::initTest();
		$this->deliveryService = taoDelivery_models_classes_DeliveryService::singleton();
	}
	
	public function testService(){
		$this->assertIsA($this->deliveryService, 'taoDelivery_models_classes_DeliveryService');
	}
	
	public function testCreateInstance(){
		$delivery = $this->deliveryService->createInstance(new core_kernel_classes_Class(TAO_DELIVERY_CLASS), 'UnitTestDelivery2');
		$this->assertIsA($delivery, 'core_kernel_classes_resource');
		
		//check if the default delivery server exists:
		$defaultDeliveryServer = $delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
		$this->assertNotNull($defaultDeliveryServer);
		
		if(!is_null($defaultDeliveryServer)){
			$this->assertEqual($defaultDeliveryServer->getUri(), TAO_DELIVERY_DEFAULT_RESULT_SERVER);
		}
		
		$this->deliveryService->deleteInstance($delivery);
		$this->assertFalse($delivery->exists());
		$this->assertNull($delivery->getOnePropertyValue(new core_kernel_classes_Property(RDF_TYPE)));
	}

}

