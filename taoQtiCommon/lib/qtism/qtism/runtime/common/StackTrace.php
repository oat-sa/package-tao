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

use qtism\common\collections\Stack;
use qtism\common\collections\AbstractCollection;
use \InvalidArgumentException;

/**
 * The StackTrace class is a Stack of StackTraceItem objects.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class StackTrace extends AbstractCollection implements Stack {
	
	/**
	 * Pop a StackTraceItem object from the StackTrace.
	 * 
	 * @return StackTraceItem|null A StackTraceItem object or null if there is nothing to pop.
	 */
	public function pop() {
		$data = &$this->getDataPlaceHolder();
		$val = array_pop($data);
		return $val;
	}
	
	/**
	 * Push a given StackTraceItem object on the StackTrace.
	 * 
	 * @param StackTraceItem $value A StackTraceItem object.
	 * @throws InvalidArgumentException If $value is not a StackTraceItem object.
	 */
	public function push($value) {
		$this->checkType($value);
		$data = &$this->getDataPlaceHolder();
		array_push($data, $value);
	}
	
	public function checkType($value) {
		if (!$value instanceof StackTraceItem) {
			$msg = "The StackTrace class only accepts to store StackTraceItem objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function __toString() {
		$str = '';
		$data = &$this->getDataPlaceHolder();
		
		foreach (array_keys($data) as $k) {
			$item = $data[$k];
			$str .= $item->getTraceMessage() . "\n";
		}
		
		return $str;
	}
}