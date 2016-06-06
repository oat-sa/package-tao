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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

error_reporting(E_ALL);


/**
 * Simple utility class that allow you to wrap the database connector.
 * You can retrieve an instance evreywhere using the singleton method.
 *
 * This database wrapper uses PDO.
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package generis
 
 */
class core_kernel_classes_DbWrapper
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * singleton
     *
     * @access private
     * @var DbWrapper
     */
    private static $instance = null;


    /**
     * The number of queries executed by the wrapper since its instantiation.
     *
     * @access private
     * @var int
     */
    private $nrQueries = 0;



    /**
     * The very last PDOStatement instance that was prepared by the wrapper.
     *
     * @access public
     * @var PDOStatement
     */
    public $lastPreparedExecStatement = null;


    /**
     * The number of statement reused in the statement store since the
     * of the wrapper.
     *
     * @access private
     * @var int
     */
    private $nrHits = 0;

    /**
     * The number of statements that could not be reused since the instantiation
     * the wrapper.
     *
     * @access private
     * @var int
     */
    private $nrMisses = 0;

    /**
     * debug mode
     *
     * @access public
     * @var boolean
     */
    public $debug = false;
    
    /**
     * 
     * @var common_persistence_SqlPersistence
     */
    private $persistence = null;

    /**
     * 
     * @var common_persistence_sql_Platform
     */
    private $platform = null;
    /**
     * 
     * @var common_persistence_sql_SchemaManager
     */
    private $schemaManager = null;
    /**
     * Entry point.
     * Enables you to retrieve staticly the DbWrapper instance.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @throws core_kernel_persistence_Exception
     * @return core_kernel_classes_DbWrapper
     */
    public static function singleton()
    {
        $returnValue = null;

        if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		$returnValue = self::$instance;



        return $returnValue;
    }

    /**
     * Initialize the storage engine connection
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_DbWrapper
     */
    private function __construct()
    {
        $this->persistence = common_persistence_SqlPersistence::getPersistence('default');
        //file_put_contents(ROOT_PATH.'sql.php', file_get_contents(ROOT_PATH.'sql.php.master'));
    }

    /**
     * Will throw an exception. Singleton instances must not be cloned.
     *
     * @access public
     * @author C�dric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_DbWrapper
     */
    public function __clone()
    {
        $returnValue = null;
		trigger_error('You cannot clone a singleton', E_USER_ERROR);
        return $returnValue;
    }

    /**
     * Returns the ammount of queries executed so far.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return int
     */
    public function getNrOfQueries()
    {
        return $this->nrQueries;
    }

    /**
     * Executes an SQL query on the storage engine. Should be used for SELECT
     * only.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string statement
     * @param  array params
     * @return PDOStatement
     */
    public function query($statement, $params = array())
    {
        $returnValue = null;

        
        $this->debug($statement);
        common_Profiler::queryStart();
//         $trace=debug_backtrace();
//         $caller=array_shift($trace);
//         $caller=array_shift($trace);
//         common_Logger::d('trace : '. $caller['function'] .$caller['class'] );
//         common_Logger::d($statement . implode('|', $params));
       	$sth = $this->persistence->query($statement,$params);
//        	$string = '$this->persistence->executeQuery('.common_Utils::toPHPVariableString($statement).','
//        			.common_Utils::toPHPVariableString($params).');$this->count++;'.PHP_EOL;
//        	file_put_contents(ROOT_PATH.'sql.php', $string,FILE_APPEND);
        common_Profiler::queryStop($statement, $params);
		
        if (!empty($sth)){
        	$returnValue = $sth;
        }

        $this->incrementNrOfQueries();
        return $returnValue;
    }

    /**
     * Executes a query on the storage engine. Should be only used for INSERT,
     * DELETE statements.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string statement
     * @param  array params
     * @return int
     */
    public function exec($statement, $params = array())
    {
        $this->debug($statement);
		
		common_Profiler::queryStart();

        $returnValue = $this->persistence->exec($statement,$params);

        common_Profiler::queryStop($statement, $params);
		
        $this->incrementNrOfQueries();
        return (int) $returnValue;
    }
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $tableName
     * @param array $data
     */
    public function insert($tableName, array $data){
    	$this->incrementNrOfQueries();
        return $this->persistence->insert($tableName,$data);

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
    public function getTables() {
        return $this->getSchemaManager()->getTables();
    }


    
    
    /**
     * Returns the column names of a given table
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string table
     * @return array
     */
    public function getColumnNames($table){
        return $this->getSchemaManager()->getColumnNames($table);
    }

    /**
     * Increments the number of queries executed so far.
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return void
     */
    protected function incrementNrOfQueries()
    {
        $this->nrQueries++;
    }

    /**
     * Returns the number of hits in the statements store.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return int
     */
    public function getNrOfHits()
    {
        return  $this->nrHits;
    }

    /**
     * Increment the number of hits in the statements store.
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     *
     */
    protected function incrementNrOfHits()
    {
        $this->nrHits++;
    }

    /**
     * Returns the number of misses in the statements store.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return int
     */
    public function getNrOfMisses()
    {
        return  $this->nrMisses;
    }

    /**
     * Increment the number of misses in the statements store.
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     *
     */
    protected function incrementNrOfMisses()
    {
        $this->nrMisses++;
    }

    /**
     * outputs a given statement in the logger.
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string statement
     *
     */
    protected function debug($statement)
    {
        if ($this->debug){
        	common_Logger::w($statement);
        }
    }

    /**
     * Appends the correct LIMIT statement depending on the implementation of
     * wrapper. For instance, limiting results in SQL statements are different
     * mySQL and postgres.
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string statement The statement to limit
     * @param  int limit Limit lower bound.
     * @param  int offset Limit upper bound.
     * @return string
     */
    public function limitStatement($statement, $limit, $offset = 0){
        return $this->getPlatform()->limitStatement($statement, $limit, $offset);
    }



    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * return common_persistence_sql_Platform
     */
    public function getPlatForm(){
        if($this->platform == null){
            $this->platform =  $this->persistence->getPlatForm();
        }
        return $this->platform;
    }
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * return common_persistence_sql_SchemaManager
     */
    public function getSchemaManager(){
        if($this->schemaManager == null){
            $this->schemaManager = $this->persistence->getSchemaManager();
        }
        return $this->schemaManager;
    }

    /**
     * The error code returned by PDO in when an Index already exists in a table
     * a given DBMS implementation.
     *
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function getIndexAlreadyExistsErrorCode(){
        return $this->getSchemaManager()->getIndexAlreadyExistsErrorCode();
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
     public function getColumnNotFoundErrorCode(){
         return $this->getSchemaManager()->getColumnNotFoundErrorCode();
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
     * @param  array columns An associative array that represents the columns on which the index applies. The keys of the array are the name of the columns, the values are the length of the data to index in the column. If there is no length limitation, set the value of the array cell to null.
     * @return void
     */
    public function createIndex($indexName, $tableName, $columns){
        return $this->getSchemaManager()->createIndex($indexName, $tableName, $columns);
    }

    /**
     * Rebuild the indexes of a given table. This method throws PDOExceptions in
     * of error.
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string tableName
     * @return void
     */
    public function rebuildIndexes($tableName){
        return $this->getSchemaManager()->rebuildIndexes($tableName);
    }

    /**
     * Flush a particular table (query cache, ...). This method throws
     * in case of error.
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string tableName
     * @return void
     */
    public function flush($tableName){
        return $this->getSchemaManager()->flush($tableName);
        
    }

    /**
     * Get the row count of a given table. The column to count is specified for
     * performance reasons.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string tableName The name of the table.
     * @param  string column The column name on wich the COUNT sql statement must be performed.
     * @return int
     */
    public function getRowCount($tableName, $column = 'id')
    {
        $sql = 'SELECT count("' . $column . '") FROM "' . $tableName . '"';
        $result = $this->persistence->query($sql);
        $returnValue = intval($result->fetchColumn(0));
        $result->closeCursor();
        return (int) $returnValue;
    }
    
    /**
     * Convenience access to lastInsertId.
     *
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param string $name
     * @return string The quoted string.
     */
    public function lastInsertId($name = null){
        return $this->persistence->lastInsertId($name);
    }
    
    /**
     * Convenience access to platForm quote.
     * 
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param string $parameter The parameter to quote.
     * @param int $parameter_type A PDO PARAM_XX constant.
     * @return string The quoted string.
     */
    public function quote($parameter){
    	return $this->persistence->quote($parameter);
    }
    
    public function quoteIdentifier($parameter){
        return $this->persistence->getPlatForm()->quoteIdentifier($parameter);
    }

}
