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

use oat\taoQtiItem\model\qti\datatype\Datatype;
use oat\taoQtiItem\model\qti\exception\QtiModelException;
use oat\taoQtiItem\model\qti\datatype\DatatypeException;

/**
 * It is the top class of every basic data type used in QTI
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
abstract class Datatype
{
	protected $value = null;
	
	public function __construct($value = null){
		$this->setValue($value);
	}
	
	public function __toString(){
		return (string) $this->getValue();
	}
	
	public function selfCheck(){
		return static::validate($this->value);
	}

	public static function validate($value){
        throw new QtiModelException('the method "validate" must be implemented by '.get_called_class());
    }
	
	public static function fix($value){
        throw new QtiModelException('the method "fix" must be implemented by '.get_called_class());
    }
	
	/**
	 * Return the base type representation of the value
	 * 
	 * @return mixed
	 */
	public function getValue(){
		
		$returnValue = null;
		
		if(!is_null($this->value)){
			$returnValue = $this->value;
		}
		
		return $returnValue;
	}
	
	public function setValue($value){
		if(static::validate($value)){
			$this->value = $value;
		}elseif(!is_null(static::fix($value))){
			$this->value = static::fix($value);
		}else{
			throw new DatatypeException('cannot set invalid value to datatype '.get_class($this).':'.$value);
		}
	}

}
