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


namespace qtism\data\expressions;

use qtism\common\enums\Enumeration;

/**
 * The class of Mathematical constants provided by QTI.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class MathEnumeration implements Enumeration {
	
	/**
	 * From IMS QTI:
	 * 
	 * The number π, the ratio of the circumference of a circle to its diameter.
	 * 
	 * @var float
	 */
	const PI = 0;
	
	/**
	 * From IMS QTI:
	 * 
	 * The number e, exp(1).
	 * 
	 * @var float
	 */
	const E = 1;
	
	public static function asArray() {
		return array(
			'PI' => self::PI,
			'E' => self::E		
		);
	}

	public static function getNameByConstant($constant) {
		switch ($constant) {
			case self::PI:
				return 'pi';
			break;
			
			case self::E:
				return 'e';
			break;
		}
	}
	
	public static function getConstantByName($name) {
		switch (strtolower($name)) {
			case 'pi':
				return self::PI;
			break;
			
			case 'e':
				return self::E;
			break;
		}
	}
}
