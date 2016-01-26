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
 * Copyright (c) (original work) 2015 Open Assessment Technologies SA
 * 
 */
namespace oat\generis\test\oatbox;

use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\generis\model\data\permission\implementation\FreeAccess;
use oat\oatbox\event\EventManager;
use Prophecy\Argument;
use Prophecy\Prediction\CallTimesPrediction;
use oat\oatbox\event\GenericEvent;

class EmptyClass
{
    public function testfunction($event) {
        
    }
}

class EventManagerTest extends GenerisPhpUnitTestRunner
{
    public function testInit()
    {
        $eventManager = new EventManager();
        $this->assertInstanceOf('oat\\oatbox\\event\\EventManager', $eventManager);
        
        return $eventManager;
        //no cleanup required, not persisted
    }

    /**
     * @depends testInit
     */
    public function testAttachOne($eventManager)
    {
        $callable = $this->prophesize('oat\\generis\\test\\oatbox\\EmptyClass');
        $callable->testfunction(Argument::any())->should(new CallTimesPrediction(1));
        
        $eventManager->attach('testEvent', array($callable->reveal(), 'testfunction'));
        $eventManager->trigger('testEvent');
    }
    
    /**
     * @depends testInit
     */
    public function testAttachMultiple($eventManager)
    {
        $callable = $this->prophesize('oat\\generis\\test\\oatbox\\EmptyClass');
        $callable->testfunction(Argument::any())->should(new CallTimesPrediction(2));
    
        $eventManager->attach(array('testEvent1','testEvent2'), array($callable->reveal(), 'testfunction'));
        $eventManager->trigger('testEvent1');
        $eventManager->trigger('testEvent2');
        $eventManager->trigger('testEvent3');
    }
    
    /**
     * @depends testInit
     */
    public function testTriggerEventObj($eventManager)
    {
        $genericEvent = new GenericEvent('objEvent', array('param1' => '1'));
        
        $callable = $this->prophesize('oat\\generis\\test\\oatbox\\EmptyClass');
        $callable->testfunction($genericEvent)->should(new CallTimesPrediction(1));
        
    
        $eventManager->attach($genericEvent->getName(), array($callable->reveal(), 'testfunction'));
        $eventManager->trigger($genericEvent);
    }
    
}