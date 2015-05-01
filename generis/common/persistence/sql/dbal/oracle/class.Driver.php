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
 class common_persistence_sql_dbal_oracle_Driver extends common_persistence_sql_dbal_Driver{
	
	/**
	 * 
	 * @author "Lionel Lecaque, <lionel@taotesting.com>"
	 * @param string $id
	 * @param array $params
	 * @return Doctrine\DBAL\Connection
	 */
	public function connect($id, array $params) {

		
		$params['wrapperClass'] = 'Doctrine\DBAL\Portability\Connection';
		$params['portability'] = \Doctrine\DBAL\Portability\Connection::PORTABILITY_ALL;
		$params['fetch_case'] = PDO::CASE_LOWER;
		$returnValue = parent::connect($id, $params);
		return $returnValue;
	
	}
	
	/* (non-PHPdoc)
	 * @see common_persistence_sql_Driver::getPlatForm()
	*/
	public function getPlatForm(){
		return new common_persistence_sql_dbal_oracle_Platform($this->connection->getDatabasePlatform());
	}
	
	/**
	 * For unknown reasons quote not implemented in PDO 
	 * (non-PHPdoc)
	 * @see common_persistence_sql_dbal_Driver::quote()
	 */
	public function quote($parameter, $parameter_type = PDO::PARAM_STR){
		return "'".$parameter."'";
	}
	
}