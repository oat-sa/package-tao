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
namespace oat\oatbox\event;

use common_ext_ExtensionsManager;
/**
 * A generic event used for events that are defined by name alone
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class GenericEvent implements Event
{
    /**
     * Event name
     * 
     * @var string
     */
    private $eventName;
    
    /**
     * Parameters of the event
     * 
     * @var array()
     */
    private $params;
    
    /**
     * Create a new generic event based on an event name
     * with optional parameters
     * 
     * @param string $eventName
     * @param array $params
     */
    public function __construct($eventName, $params = array()) {
        $this->eventName = (string)$eventName;
        $this->params = $params;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\event\Event::getName()
     */
    public function getName() {
        return $this->eventName;
    }

    /**
     * Get parameters
     * @return array
     */
    public function getParams() {
        return $this->params;
    }
}
