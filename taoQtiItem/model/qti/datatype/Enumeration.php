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

use oat\taoQtiItem\model\qti\datatype\Enumeration;
use oat\taoQtiItem\model\qti\datatype\Datatype;
use oat\taoQtiItem\model\qti\exception\QtiModelException;

/**
 * Enumeration is a data type that contains a precise list of values
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
abstract class Enumeration extends Datatype
{
	public static function validate($value){
		return (bool)in_array($value, static::getEnumeration());
	}
	
	public static function fix($value){
		
		$returnValue = null;
		
		$enum = static::getEnumeration();
		foreach($enum as $element){
			if(strcasecmp($value, $element) == 0){
				$returnValue = $element;
				break;
			}
		}
		
		return $returnValue;
	}
	
	/**
	 * Returns the array enumeration all available elements
	 * 
	 * @return array
	 */
	public static function getEnumeration(){
        throw new QtiModelException('Method to be implemented by and called from inherited classes');
    }
	
    public function setValue($value){
        if(!empty($value)){
            parent::setValue($value);
        }
    }
} 
