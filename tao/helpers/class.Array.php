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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Utility class on Arrays.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 */
class tao_helpers_Array
{
    /**
     * Sort an associative array on a key criterion. Uses sort or asort PHP
     * functions to implement the sort depending on the value of the descending
     * parameter.
     *
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array input The associative array to sort.
     * @param  string field The key criterion.
     * @param  boolean (optional, default = Ascending sort) descending Descending or Ascending order.
     * @return array An associative array.
     */
    public static function sortByField($input, $field, $descending = false)
    {
        $returnValue = array();
		
		$sorted = array();
		foreach($input as $key => $value ){
			$sorted[$key] = $value[$field];
		}

		if($descending){
			arsort($sorted);
		}
		else{
			asort($sorted);
		}

		foreach ($sorted as $key => $value ){
			$returnValue[$key] = $input[$key];
		}

        return (array) $returnValue;
    }
    
    /**
     * remove duplicate from array of objects implementing the __equal() function
     * 
     * @param array $array
     * @return array $array
     */
    public static function array_unique($array) {
        $keys = array_keys($array);
        $toDrop = array();
        for ($i = count($keys) - 1; $i >=0 ; $i-- ) {
            for ($j = $i - 1; $j >=0 ; $j--) {
                if ($array[$keys[$i]]->__equals($array[$keys[$j]])) {
                    $toDrop[] = $keys[$i];
                    break;
                }
            }
        }
        foreach ($toDrop as $key) {
            unset($array[$key]);
        }
        return $array;
    }

}
