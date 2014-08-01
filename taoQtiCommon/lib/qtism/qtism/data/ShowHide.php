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
 * The ShowHide enumeration.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ShowHide implements Enumeration {
	
	const SHOW = 0;
	
	const HIDE = 1;
	
	public static function asArray() {
		return array(
			'SHOW' => self::SHOW,
			'HIDE' => self::HIDE		
		);
	}
	
	public static function getConstantByName($name) {
		switch (strtolower($name)) {
			case 'show':
				return self::SHOW;
			break;
			
			case 'hide':
				return self::HIDE;
			break;
			
			default:
				return false;
			break;
		}
	}
	
	public static function getNameByConstant($constant) {
		switch ($constant) {
			case self::SHOW:
				return 'show';
			break;
			
			case self::HIDE:
				return 'hide';
			break;
			
			default:
				return false;
			break;
		}
	}
}
