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
 * Copyright (c) 2002-2008 (original work) 2014 Open Assessment Technologies SA
 * 
 */

namespace oat\generis\model\data\event;

use oat\oatbox\event\Event;
/**
 * transitory class to manage the ontology driver
 * instead of managing full models, it only handles the rdfs interfaces
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class ResourceCreated implements Event
{
    private $resource;
    
	function __construct(\core_kernel_classes_Resource $resource)
	{
	    $this->resource = $resource;
	}
    
	function getResource()
	{
	    return $this->resource;
	}

	function getName()
	{
	    return __CLASS__;
	}
}