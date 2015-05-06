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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author "Lionel Lecaque, <lionel@taotesting.com>"
 * @license GPLv2
 * @package generis
 
 *
 */

class common_persistence_sql_dbal_oracle_MultipleInsertsSqlHelper extends common_persistence_sql_AbstractMultipleInsertsSqlHelper {
	
	
	/**
	 * (non-PHPdoc)
	 * @see common_persistence_sql_AbstractMultipleInsertsSqlHelper::getFirstStaticPart()
	 */
	public function getFirstStaticPart($table , $columns = array()){
		return 'INSERT ALL ' ;
	}
	/**
	 * (non-PHPdoc)
	 * @see common_persistence_sql_AbstractMultipleInsertsSqlHelper::getValuePart()
	 */
	public function getValuePart($table , $columns = array(),$values = array()){
		$returnValue = 'INTO ' . $table;
		if(!empty($columns)){
			$returnValue .= ' (' . implode(',',$columns). ') VALUES ';
		}
		if(!empty($values)){
			$returnValue .= '('  ;
			foreach ($values as $val){
				//$value = $val != null ?  $val  : 'null';
				$returnValue .= $val . ',';
			}
			$returnValue = substr($returnValue, 0, strlen($returnValue) -1);
			//pls take care last char is removed
			$returnValue .= ') ';
			
		}
		return $returnValue;
	}
	/**
	 * (non-PHPdoc)
	 * @see common_persistence_sql_AbstractMultipleInsertsSqlHelper::getEndStaticPart()
	 */
	public function getEndStaticPart(){
		return ' SELECT null FROM dual';
	}
}

?>