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
namespace qtism\common;

/**
 * An interface aiming at providing the same behaviour as
 * Oracle Java's Object.equals method.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @link http://docs.oracle.com/javase/7/docs/api/java/lang/Object.html#equals(java.lang.Object)
 */
interface Comparable {
	
	/**
	 * Indicates whether some object is equal to this one. (c.f. JavaSE doc)
	 * 
	 * @param mixed $obj An object to compare.
	 * @return boolean Whether the object to compare is equal to this one.
	 * @link http://docs.oracle.com/javase/7/docs/api/java/lang/Object.html#equals(java.lang.Object)
	 */
	public function equals($obj);
	
}