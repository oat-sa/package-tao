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
namespace qtism\common\collections;

use InvalidArgumentException as InvalidArgumentException;
use qtism\common\utils\Format as Format;

/**
 * A collection that aims at storing string values.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class IdentifierCollection extends StringCollection {

	/**
	 * Check if $value is a valid QTI Identifier.
	 * 
	 * @throws InvalidArgumentException If $value is not a valid QTI Identifier.
	 */
	protected function checkType($value) {
		if (gettype($value) !== 'string') {
			$msg = "IdentifierCollection class only accept string values, '" . gettype($value) . "' given.";
			throw new InvalidArgumentException($msg);
		}
		else if (!Format::isIdentifier($value)) {
			$msg = "IdentifierCollection class only accept valid QTI Identifiers, '${value}' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function __toString() {
	    $strArray = array();
	    $dataPlaceHolder = &$this->getDataPlaceHolder();
	    
	    foreach (array_keys($dataPlaceHolder) as $k) {
	        $strArray[] = $dataPlaceHolder[$k];
	    }
	    
	    return implode(',', $strArray);
	}
}