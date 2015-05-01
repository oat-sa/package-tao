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

namespace oat\taoQtiItem\model\qti\datatype;

use oat\taoQtiItem\model\qti\datatype\Boolean;
use oat\taoQtiItem\model\qti\datatype\Datatype;

/**
 * The basic boolean datatype
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
class Boolean extends Datatype
{
	
	public static function validate($value){
		return is_bool($value);
	}
	
	public static function fix($value){
		$returnValue = null;
		
		if(strcasecmp($value, 'false') == 0){
			$returnValue = false;
		}else if(!is_array($value) && !is_object($value)){
			$returnValue = (bool)$value;//try casting to boolean type
		}
		
		return $returnValue;
	}

}