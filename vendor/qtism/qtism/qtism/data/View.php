<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *   
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package 
 */


namespace qtism\data;

use qtism\common\enums\Enumeration;

/**
 * The View enumeration.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class View implements Enumeration {
	
	const AUTHOR = 0;
	
	const CANDIDATE = 1;
	
	const PROCTOR = 2;
	
	const SCORER = 3;
	
	const TEST_CONSTRUCTOR = 4;
	
	const TUTOR = 5;
	
	/**
	 * Get the possible values of the enumaration as an array.
	 * 
	 * @return array An array of integer constants.
	 */
	public static function asArray() {
		return array(
			'AUTHOR' => self::AUTHOR,
			'CANDIDATE' => self::CANDIDATE,
			'PROCTOR' => self::PROCTOR,
			'SCORER' => self::SCORER,
			'TEST_CONSTRUCTOR' => self::TEST_CONSTRUCTOR,
			'TUTOR' => self::TUTOR
		);
	}
	
	/**
	 * Get a constant name by its value.
	 * 
	 * @param integer $constant The constant value from the View enumeration.
	 * @return string|boolean The name of the constant or false it if could not be resolved.
	 */
	public static function getNameByConstant($constant) {
		switch ($constant) {
			case self::AUTHOR:
				return 'author';
			break;
			
			case self::CANDIDATE:
				return 'candidate';
			break;
			
			case self::PROCTOR:
				return 'proctor';
			break;
			
			case self::SCORER:
				return 'scorer';
			break;
			
			case self::TEST_CONSTRUCTOR:
				return 'testConstructor';
			break;
			
			case self::TUTOR:
				return 'tutor';
			break;
			
			default:
				return false;
			break;
		}
	}
	
	/**
	 * Get the constant value from its name.
	 * 
	 * @param string $name The name of the constant you want to retrieve the value.
	 * @return integer|boolean The value of the related constant or false if the name could not be resolved. 
	 */
	public static function getConstantByName($name) {
		switch(strtolower($name)) {
			case 'author':
				return self::AUTHOR;
			break;
			
			case 'candidate':
				return self::CANDIDATE;
			break;
			
			case 'proctor':
				return self::PROCTOR;
			break;
			
			case 'scorer':
				return self::SCORER;
			break;
			
			case 'testconstructor':
				return self::TEST_CONSTRUCTOR;
			break;
			
			case 'tutor':
				return self::TUTOR;
			break;
			
			default:
				return false;
			break;
		}
	}
}
