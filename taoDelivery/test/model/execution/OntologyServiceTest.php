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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoDelivery\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use common_ext_ExtensionsManager;
use taoDelivery_models_classes_execution_ServiceProxy;

class OntologyServiceTest extends TaoPhpUnitTestRunner
{
    protected function setUp()
    {
        parent::setUp();
        // load constants
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
    }
    
    public function testSetState()
    {
        $service = new \taoDelivery_models_classes_execution_OntologyService();
        $this->assertInstanceOf('taoDelivery_models_classes_execution_Service', $service);
        
        $assembly = new \core_kernel_classes_Resource('fake');
        $deliveryExecution = $service->initDeliveryExecution($assembly, 'fakeUser');
        
        $this->assertInstanceOf('taoDelivery_models_classes_execution_DeliveryExecution', $deliveryExecution);
        
        $success = $deliveryExecution->setState('http://uri.com/fake#State');
        $this->assertTrue($success);
        
        $state = $deliveryExecution->getState();
        $this->assertEquals('http://uri.com/fake#State', $state->getUri());
        
        $success = $deliveryExecution->setState('fakeState');
        $this->assertTrue($success);
        
        $state = $deliveryExecution->getState();
        $this->assertEquals('fakeState', $state->getUri());
        
        $success = $deliveryExecution->setState('fakeState');
        $this->assertFalse($success);
        
        $deliveryExecution->delete();
    }
}
