<?php

class common_persistence_sql_dbal_mysql_SchemaManager extends common_persistence_sql_dbal_SchemaManager{
    
    
    /**
     * 
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $indexName
     * @param string $tableName
     * @param array($columnName => $size) $columns
     */
    public function createIndex($indexName, $tableName, $columns){
        $indexCol = array();
        foreach ($columns as $col => $size){
            if(is_int($size)){
                $indexCol[] = $col . "(".$size .")";
            }
            else {
                $indexCol[] =$col;
            }
        }
        
        $index = new \Doctrine\DBAL\Schema\Index($indexName,$indexCol);     
        $this->getSchemaManager()->createIndex($index,$tableName);
    }
    
    /**
     * Short description of method getIndexAlreadyExistsErrorCode
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function getIndexAlreadyExistsErrorCode()
    {
        return '42000';
    }
    /**
     * (non-PHPdoc)
     * @see common_persistence_sql_dbal_SchemaManager::getColumnNotFoundErrorCode()
     */
    public function getColumnNotFoundErrorCode(){
        return '42S22';
    }
    
}