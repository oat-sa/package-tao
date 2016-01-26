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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti\attribute;

use oat\taoQtiItem\model\qti\attribute\BaseStringInteraction;
use oat\taoQtiItem\model\qti\attribute\Attribute;

/**
 * The BaseStringInteraction attribute
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
class BaseStringInteraction extends Attribute
{
	
	static protected $name = 'base';
	static protected $type = 'oat\\taoQtiItem\\model\\qti\\datatype\\Integer';
	static protected $defaultValue = 10;
	static protected $required = false;

}