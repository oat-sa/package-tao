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
 */

/**
 * Dbal Driver 
 * 
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 * @package generis
 *
 */
class common_persistence_sql_dbal_Driver implements common_persistence_sql_Driver
{

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * Connect to Dbal
     * 
     * @param string $id
     * @param array $params
     * @return \Doctrine\DBAL\Connection;
     */
    function connect($id, array $params)
    {
        if (isset($params['connection'])) {
            $connectionParams = $params['connection'];
        } else {
            $connectionParams = $params;
            $connectionParams['driver'] = str_replace('dbal_', '', $connectionParams['driver']);
        }
        $config = new \Doctrine\DBAL\Configuration();
//          $logger = new Doctrine\DBAL\Logging\EchoSQLLogger();
//          $config->setSQLLogger($logger);
        $this->connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        return new common_persistence_SqlPersistence($params, $this);
    }
    
    
   

    /**
     * (non-PHPdoc)
     * @see common_persistence_sql_Driver::getPlatForm()
     */
    public function getPlatForm(){
        return new common_persistence_sql_Platform($this->connection->getDatabasePlatform());
    }
    
    /**
     * (non-PHPdoc)
     * @see common_persistence_sql_Driver::getSchemaManager()
     */
    public function getSchemaManager()
    {
        return new common_persistence_sql_dbal_SchemaManager($this->connection->getSchemaManager());
    }
    
   
    /**
     * Execute the statement with provided params
     *
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param mixed $statement
     * @param array $params
     * @return integer number of affected row
     */
    public function exec($statement,$params = array())
    {
        return $this->connection->executeUpdate($statement,$params);
    }
    
    
    /**
     * Query  the statement with provided params
     * 
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param mixed $statement
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function query($statement,$params = array())
    {
        return $this->connection->executeQuery($statement,$params);
    }
    
    /**
     * Convenience access to PDO::quote.
     *
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param string $parameter The parameter to quote.
     * @param int $parameter_type A PDO PARAM_XX constant.
     * @return string The quoted string.
     */
    public function quote($parameter, $parameter_type = PDO::PARAM_STR){
        return $this->connection->quote($parameter, $parameter_type);
    }
    
    

    /**
     * (non-PHPdoc)
     * @see common_persistence_sql_Driver::insert()
     */
    public function insert($tableName, array $data){
        $cleanColumns = array();
        foreach ($data as $columnName => $value) {
            $cleanColumns[$this->getPlatForm()->quoteIdentifier($columnName)] = $value;
        }
        return $this->connection->insert($tableName, $cleanColumns);
    }
    
    /**
     * Convenience access to PDO::lastInsertId.
     *
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param string $name
     * @return string The quoted string.
     */
    public function lastInsertId($name = null){
        return $this->connection->lastInsertId($name);
    }

    

}
