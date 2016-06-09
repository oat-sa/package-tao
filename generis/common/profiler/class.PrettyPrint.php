<?php
/*  
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
 */

class common_profiler_PrettyPrint{
	
	/**
     *
     * @param $mem
     * @return string
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public static function memory($mem){
		$returnValue = '';
		if ($mem < 1024){
			$returnValue = $mem.'B';
		}else if($mem < 1048576){
			$returnValue = round($mem/1024, 2).'KB';
		}else{
			$returnValue = round($mem/1048576, 2).'MB';
		}
		return $returnValue;
	}
	
	/**
     *
     * @param $mem
     * @return string
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public static function percentage($part, $total, $decimal = 1){
		return round($part/$total*100,1);
	}
}
