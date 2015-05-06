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
abstract class common_persistence_sql_SchemaManager {
    
    /**
     * HACK to set "PDO::MYSQL_ATTR_MAX_BUFFER_SIZE" for fileupload
     *  
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param unknown $name
     * @param unknown $value
     * @throws core_kernel_persistence_Exception
     */
    public function setAttribute($name,$value){
        throw new core_kernel_persistence_Exception('setattribute only availlable for mysql pdo implementation');
    } 
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    protected abstract function getSchemaManager();
    
    /**
     * Returns the column names of a given table
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string table
     * @return array
     */
    public function getColumnNames($table){
        return $this->getSchemaManager()->listTableColumns($table);
    }
    
    
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    public function createSchema(){
        $tables = $this->getSchemaManager()->listTables();
        return new \Doctrine\DBAL\Schema\Schema($tables,array(),$this->getSchemaManager()->createSchemaConfig());
    }
     
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param unknown $schema
     * @param unknown $tblname
     * @param unknown $column
     * @return unknown
     */
    public function addColumnToTable($schema,$tblname,$column){
        $newSchema = clone $schema;
        $table = $newSchema->getTable($tblname);
        $table->addColumn($column, "text",array("notnull" => false));
        return $newSchema;
    }
    
    /**
     * Returns an array of string containting the names of the tables contained
     * the currently selected database in the storage engine.
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return array
     */
    public  function getTables(){
        return $this->getSchemaManager()->listTableNames();
    }
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $tableName
     */
    public function getTableIndexes($tableName){
        return $this->getSchemaManager()->listTableIndexes($tableName);
    }
    
     /**
     * Create an index on a given table and selected columns. This method throws
     * in case of error.
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string indexName The name of the index to create.
     * @param  string tableName A table name
     * @param  array columns
     * @return void
     */
    public function createIndex($indexName, $tableName, $columns){
        $index = new \Doctrine\DBAL\Schema\Index($indexName,array_keys($columns));
        $table = new \Doctrine\DBAL\Schema\Table($tableName);
        $this->getSchemaManager()->createIndex($index,$table);
    }

    /**
     * The error code returned by PDO in when an Index already exists in a table
     * a given DBMS implementation.
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public abstract function getIndexAlreadyExistsErrorCode();
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public abstract function getColumnNotFoundErrorCode();

    
}
