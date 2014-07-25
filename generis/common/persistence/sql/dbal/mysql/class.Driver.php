<?php

class common_persistence_sql_dbal_mysql_Driver extends common_persistence_sql_dbal_Driver{


	/* (non-PHPdoc)
	 * @see common_persistence_sql_dbal_Driver::connect()
	 */
	public function connect($id, array $params) {
		$returnValue = parent::connect($id, $params);
		$this->exec('SET SESSION SQL_MODE=\'ANSI_QUOTES\';');
		return $returnValue;

	}
 
    
    
    /* (non-PHPdoc)
     * @see common_persistence_sql_Driver::getSchemaManager()
    */
    public function getSchemaManager()
    {
        return new common_persistence_sql_dbal_mysql_SchemaManager($this->connection->getSchemaManager());
    }
}