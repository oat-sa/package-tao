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
 *
 */


/**
 * Persistence base on SQL
 * 
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 * @package generis 
 *
 */
class common_persistence_SqlPersistence extends common_persistence_Persistence
{

    /**
     * 
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param unknown $statement
     * @param unknown $params
     */
    public function exec($statement,$params = array())
    {
        return $this->getDriver()->exec($statement,$params);
    }

    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @return common_persistence_sql_SchemaManager
     */
    public function getSchemaManager(){
        return $this->getDriver()->getSchemaManager();
    }
    
    /**
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @return common_persistence_sql_Platform
     */
    public function getPlatForm(){
        return $this->getDriver()->getPlatform();
    }
    
    /**
     * 
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param unknown $tableName
     * @param array $data
     */
    public function insert($tableName, array $data)
    {
        return $this->getDriver()->insert($tableName,$data);
    }

    /**
     * 
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param unknown $statement
     * @param unknown $params
     */
    public function query($statement,$params= array())
    {
        return $this->getDriver()->query($statement,$params);
    }
    

    /**
     * Convenience access to quote.
     *
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param string $parameter The parameter to quote.
     * @param int $parameter_type A PDO PARAM_XX constant.
     * @return string The quoted string.
     */
    public function quote($parameter, $parameter_type = PDO::PARAM_STR){
        return $this->getDriver()->quote($parameter, $parameter_type);
    }
    
    
    /**
     * Convenience access to lastInsertId.
     *
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param string $name
     * @return string The quoted string.
     */
      public function lastInsertId($name = null){
          return $this->getDriver()->lastInsertId($name);
      }
}
