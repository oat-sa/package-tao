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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA;
 *
 *
 */
namespace oat\ontoBrowser\helpers;

/**
 * Utility of display methods
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class Display
{
	
	private static $mapcache = null;
	
    /**
     * enable you to cut a long string and end it with [...] and add an hover
     * to display the complete string on mouse over.
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string input
     * @param  int maxLength
     * @return string
     */
    public static function reverseConstantLookup($value)
    {
    	if (is_null(self::$mapcache)) {
    		// use of categorised constants caused crashes
	    	$consts = get_defined_constants();
	    	self::$mapcache = array();
	    	foreach ($consts as $key => $test) {
	    		if (is_string($test)) {
	    			self::$mapcache[$test] = $key; 
	    		}
	    	}
    	}
    	return isset(self::$mapcache[$value]) ? self::$mapcache[$value] : $value;
    }
    
}