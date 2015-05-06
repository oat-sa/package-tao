<?php
/**
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
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 *  
 *
 */
namespace qtism\common\enums;

interface Enumeration {
	
	/**
	 * Return the possible values of the enumeration as an array.
	 * 
	 * @return An associative array where keys are constant names (as they appear in the code) and values are constant values.
	 */
	public static function asArray();
	
	/**
	 * Get a constant value by its name. If $name does not match any of the value
	 * of the enumeration, false is returned.
	 * 
	 * @param integer|false $name The value relevant to $name or false if not found.
	 */
	public static function getConstantByName($name);
	
	/**
	 * Get a constant name by its value. If $constant does not match any of the names
	 * of the enumeration, false is returned.
	 * 
	 * @param string|false $constant The relevant name or false if not found.
	 */
	public static function getNameByConstant($constant);
}