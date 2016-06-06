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
namespace qtism\runtime\tests;

use qtism\common\collections\AbstractCollection;
use InvalidArgumentException as InvalidArgumentException;
use \OutOfBoundsException;

/**
 * A collection that aims at storing Route objects.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SelectableRouteCollection extends AbstractCollection {

	/**
	 * Check if $value is a Route object
	 * 
	 * @throws InvalidArgumentException If $value is not a Route object.
	 */
	protected function checkType($value) {
		if (!$value instanceof SelectableRoute) {
			$msg = "SelectableRouteCollection class only accept SelectableRoute objects, '" . gettype($value) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Swap Route at position $key1 with the Route
	 * at position $key2.
	 *
	 * @param int $position1 A RouteItem position.
	 * @param int $position2 A RouteItem position.
	 * @throws OutOfBoundsException If $position1 or $position2 are not poiting to any Route.
	 */
	public function swap($position1, $position2) {
	    $routes = &$this->getDataPlaceHolder();
	
	    if (isset($routes[$position1]) === false) {
	        $msg = "No Route object at position '${position1}'.";
	        throw new OutOfBoundsException($msg);
    	}
    	
    	if (isset($routes[$position2]) === false) {
    	    $msg = "No Route object at position '${position2}'.";
    	    throw new OutOfBoundsException($msg);
    	}
    	
    	$temp = $routes[$position2];
    	$routes[$position2] = $routes[$position1];
    	$routes[$position1] = $temp;
	}
	
	/**
	 * Insert the SelectableRoute object $route at $position.
	 *
	 * @param SelectableRoute $route A SelectableRoute object.
	 * @param integer $position An integer index where $route must be placed.
	 */
	public function insertAt(SelectableRoute $route, $position) {
	    $data = &$this->getDataPlaceHolder();
	    if ($position === 0) {
	        array_unshift($data, $route);
	    }
	    else if ($position === (count($data))) {
	        array_push($data, $route);
	    }
	    else {
	        array_splice($data, $position, 0, array($route));
	    }
	    
        // Make sure indexes are rebased.
        $data = array_values($data);
	}
}