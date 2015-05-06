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

class common_persistence_sql_dbal_oracle_Platform extends common_persistence_sql_Platform {
	
	/**
	 * @return common_persistence_sql_MultipleInsertsSqlHelper
	 */
	public function getMultipleInsertsSqlQueryHelper(){
		return new common_persistence_sql_dbal_oracle_MultipleInsertsSqlHelper();
	}
	/**
	 * (non-PHPdoc)
	 * @see common_persistence_sql_Platform::getNullString()
	 */
	public function getNullString(){
		return 'null';
	}
	/**
	 * (non-PHPdoc)
	 * @see common_persistence_sql_Platform::isNullCondition()
	 */
	public function isNullCondition($column){
		return $column . ' IS ' .$this->getNullString();
	}
	/**
	 * Oracle may return stream in case of LOB type, retreive string if so
	 * 
	 * @author "Lionel Lecaque, <lionel@taotesting.com>"
	 * @param string $text
	 */
	public function getPhpTextValue($text){
		$doctrineType = \Doctrine\DBAL\Types\Type::getType('text');
		return $doctrineType->convertToPHPValue($text, $this->dbalPlatform);
	}
	
	/**
	 *
	 * @author "Lionel Lecaque, <lionel@taotesting.com>"
	 * @param string $functionName
	 */
	public function getSqlFunction($functionName){
	    return "SELECT " . $functionName . '(?) from dual';
	}
	
// 	public function getObjectTypeCondition(){
// 		return 'to_char(object) ';
// 	}
}

?>