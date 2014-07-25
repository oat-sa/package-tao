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


namespace qtism\data\expressions\operators;

use qtism\common\enums\BaseType;

class OperatorBaseType extends BaseType {
	
	/**
	 * Express that the operands can have any BaseType from the BaseType enumeration and
	 * can be different.
	 * 
	 * @var int
	 */
	const ANY = 12;
	
	/**
	 * Express that all the operands must have the same
	 * baseType.
	 * 
	 * @var int
	 */
	const SAME = 13;
	
	public static function asArray() {
		$values = BaseType::asArray();
		$values['ANY'] = self::ANY;
		$values['SAME'] = self::SAME;
		
		return $values;
	}
}
