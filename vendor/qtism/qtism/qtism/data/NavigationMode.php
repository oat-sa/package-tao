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
 * The Navigation Mode enumeration.
 * 
 * From IMS QTI:
 * 
 * The navigation mode determines the general paths that the candidate may take.
 * A testPart in linear mode restricts the candidate to attempt each item in turn.
 * Once the candidate moves on they are not permitted to return. A testPart in
 * nonlinear mode removes this restriction - the candidate is free to navigate
 * to any item in the test at any time. Test delivery systems are free to implement
 * their own user interface elements to facilitate navigation provided they honour
 * the navigation mode currently in effect. A test delivery system may implement
 * nonlinear mode simply by providing a method to step forward or backwards through
 * the test part.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class NavigationMode implements Enumeration {
	
	const LINEAR = 0;
	
	const NONLINEAR = 1;
	
	public static function asArray() {
		return array(
			'LINEAR' => self::LINEAR,
			'NONLINEAR' => self::NONLINEAR
		);
	}
	
	public static function getConstantByName($name) {
		switch (strtolower($name)) {
			case 'linear':
				return self::LINEAR;
			break;
			
			case 'nonlinear':
				return self::NONLINEAR;
			break;
			
			default:
				return false;
			break;
		}
	}
	
	public static function getNameByConstant($constant) {
		switch ($constant) {
			case self::LINEAR:
				return 'linear';
			break;
			
			case self::NONLINEAR:
				return 'nonlinear';
			break;
			
			default:
				return false;
			break;
		}
	}
}
