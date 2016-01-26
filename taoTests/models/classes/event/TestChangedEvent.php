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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */
namespace oat\taoTests\models\event;

use oat\oatbox\event\Event;
/**
 * Event fired whenever the state of a test session changes
 * in the context of a delivery execution
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
abstract class TestChangedEvent implements Event
{
    const EVENT_NAME = __CLASS__;
    
    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\event\Event::getName()
     */
    public function getName()
    {
        return self::EVENT_NAME;
    }
    
    /**
     * Returns the service call id of the test session
     * 
     * @return string
     */
    abstract public function getServiceCallId();
    
    /**
     * Returns a human readable description
     * of the test session in progress
     * 
     * @return string
     */
    abstract public function getNewStateDescription();
    
}
