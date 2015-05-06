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
namespace qtism\runtime\common;

use qtism\common\collections\AbstractCollection;
use qtism\runtime\common\Variable;
use InvalidArgumentException as InvalidArgumentException;

/**
 * A collection that aims at storing runtime Variable objects.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class VariableCollection extends AbstractCollection {

	/**
	 * Check if $value is a valid Variable object.
	 * 
	 * @throws InvalidArgumentException If $value is not a Variable object.
	 */
	protected function checkType($value) {
		if (!$value instanceof Variable) {
			$msg = "The VariableCollection class only accept Variable objects, '${value}' given.";
			throw new InvalidArgumentException($msg);
		}
	}
}