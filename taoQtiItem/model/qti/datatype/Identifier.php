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

use oat\taoQtiItem\model\qti\datatype\Identifier;
use oat\taoQtiItem\model\qti\datatype\Datatype;

/**
 * The basic Identifier data type
 *
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10722
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
class Identifier extends Datatype
{
	
	/**
	 * The identifier datatype must hold directly the reference to the referenced object
	 * Indeed, QTI allow identical identifiers for different qti elements 
	 * (e.g. a choice and a feedback can share the same identifier)
	 * 
	 * @param oat\taoQtiItem\model\qti\IdentifiedElement $value
	 * @return boolean
	 */
	public static function validate($value){
		return !is_null(self::fix($value));
	}
	
	/**
	 * Check if the identifier string is correct per the QTI standard
	 * 
	 * @param string $identifier
	 * @return boolean
	 */
	public static function checkIdentifier($identifier){
		return preg_match('/^[_a-z]{1}[a-z0-9-._]{0,31}$/ims', $identifier);
	}
	
	public static function fix($value){
		
		$returnValue = null;
		
		foreach(static::getAllowedClasses() as $class){
			if($value instanceof $class){
				$returnValue = $value;
				break;
			}
		}
		
		return $returnValue;
	}
	
	/**
	 * Define the array of authorized QTI element classes
	 * 
	 * @return array
	 */
	public static function getAllowedClasses(){
		return array(
			'oat\\taoQtiItem\\model\\qti\\IdentifiedElement'
		);
	}
	
	public function getValue(){
		
		$returnValue = null;
		
		if(!is_null($this->value)){
			$returnValue = $this->value->getIdentifier();
		}
		
		return $returnValue;
	}
	
	public function getReferencedObject(){
		
		$returnValue = null;
		
		if(!is_null($this->value)){
			$returnValue = $this->value;
		}
		
		return $returnValue;
	}
	
}