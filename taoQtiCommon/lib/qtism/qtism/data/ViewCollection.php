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

use qtism\common\collections\IntegerCollection;
use InvalidArgumentException as InvalidArgumentException;

/**
 * A collection that aims at storing Views (View enumartion values).
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ViewCollection extends IntegerCollection {

	/**
	 * Check if $value is a valid View enumeration value.
	 * 
	 * @throws InvalidArgumentException If $value is not a valid View enumeration value.
	 */
	protected function checkType($value) {
		if (!in_array($value, View::asArray())) {
			$msg = "The ViewsCollection class only accept View enumeration values, '${value}' given.";
			throw new InvalidArgumentException($msg); 
		}
	}
}
