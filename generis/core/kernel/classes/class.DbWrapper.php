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
 * @package core
 * @subpackage kernel_classes
 */
abstract class core_kernel_classes_DbWrapper
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
     * An established PDO connection object.
     *
     * @access public
     * @var PDO
     */
    public $dbConnector = null;

    /**
     * The number of queries executed by the wrapper since its instantiation.
     *
     * @access private
     * @var int
     */
    private $nrQueries = 0;

    /**
     * States if the last statement executed by the wrapper was a prepared
     * or not.
     *
     * @access public
     * @var boolean
     */
    public $preparedExec = false;

    /**
     * The very last PDOStatement instance that was prepared by the wrapper.
     *
     * @access public
     * @var PDOStatement
     */
    public $lastPreparedExecStatement = null;

    /**
     * A prepared statement store used by the prepare() method to reuse
     * at most.
     *
     * @access public
     * @var array
     */
    public $statements = array();

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

    // --- OPERATIONS ---

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
			$driver = strtolower(SGBD_DRIVER);
			$driverName = ucfirst($driver);
			$className = 'core_kernel_classes_' . $driverName .'DbWrapper';
			
			if (class_exists($className)){
				self::$instance = new $className;
			}
			else{
				// maybe a 'pdo_' named driver?
				$driverName = str_replace('pdo_', '', $driver);
				$driverName = ucfirst($driverName);
				$className = $className = 'core_kernel_classes_' . $driverName .'DbWrapper';
				
				if (class_exists($className)){
					self::$instance = new $className();	
				}
				else
				{
					$driver = SGBD_DRIVER;
					$msg = "Unable to load the DBWrapper sub-class related to the '${driver}' database driver.";
					throw new core_kernel_persistence_Exception($msg);
				}
			}
            

        }
        $returnValue = self::$instance;



        return $returnValue;
    }

    /**
     * Initialize the storage engine connection
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @throws PDOException
     * @return core_kernel_classes_DbWrapper
     */
    private function __construct()
    {

        $connLimit = 3; // Max connection attempts.
        $counter = 0; // Connection attemps counter.
        
        while (true){
	        $dsn = $this->getDSN();
	        $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_BOTH,
	        				 PDO::ATTR_PERSISTENT => false,
	        				 PDO::ATTR_EMULATE_PREPARES => false);
	        				 
	     	
	        foreach ($this->getExtraConfiguration() as $k => $v){
	        	$options[$k] = $v;
	        }
	       
	        try{
	        	$this->dbConnector = @new PDO($dsn, DATABASE_LOGIN, DATABASE_PASS, $options);
	        	$this->afterConnect();	
		        // We are connected. Get out of the loop.
		        break;
	        }
	        catch (PDOException $e){
	        	$this->dbConnector = null;
	        	$counter++;
	        	
	        	if ($counter == $connLimit){
	        		// Connection attempts exceeded.
	        		throw $e;
	        	}
	        }
        }

    }

    /**
     * Used to close the database connection on destruction
     *
     * @access public
     * @author C�dric Alfonsi, <cedric.alfonsi@tudor.lu>
     *
     */
    public function __destruct()
    {
    	if(!is_null($this->dbConnector)){
    		$this->dbConnector = null;
    	}
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
        $this->preparedExec = false;
        
        $this->debug($statement);
        common_Profiler::queryStart();
		
        if (count($params) > 0){
        	$sth = $this->dbConnector->prepare($statement);
        	$sth->execute($params);
        }
        else{
        	$sth = $this->dbConnector->query($statement);
        }
		
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
        if (count($params) > 0){
        	$sth = $this->dbConnector->prepare($statement);
        	$this->preparedExec = true;
        	$this->lastPreparedExecStatement = $sth;
        	$sth->execute($params);
        	$returnValue = $sth->rowCount();
        }
        else{
        	$this->preparedExec = false;
        	try {
        	    $returnValue = $this->dbConnector->exec($statement);
        	} catch (PDOException $e) {
        	    common_Logger::w('Error in statement: '.$statement);
        	    throw $e;
        	}
        }
        common_Profiler::queryStop($statement, $params);
		
        $this->incrementNrOfQueries();
        return (int) $returnValue;
    }

    /**
     * Creates a prepared PDOStatement.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string statement
     * @return PDOStatement
     */
    public function prepare($statement)
    {
		common_Profiler::queryStart();
		
        $returnValue = null;
        $this->preparedExec = false;
        $this->debug($statement);
        $returnValue = $this->getStatement($statement);
        $this->incrementNrOfQueries();
		
		common_Profiler::queryStop($statement);
        return $returnValue;
    }

    /**
     * Returns the last error code generated by the wrapped PDO object. Please
     * the PDOStatement::errorCode method for prepared statements.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function errorCode()
    {
        if ($this->preparedExec == false){
    		$returnValue = $this->dbConnector->errorCode();
    	}
    	else{
    		$returnValue = $this->lastPreparedExecStatement->errorCode();
    	}
        return (string) $returnValue;
    }

    /**
     * Returns the last error message of the PDO object wrapped by this class.
     * call PDOStatement::errorMessage for prepared statements.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function errorMessage()
    {


        if ($this->preparedExec == false){
    		$info = $this->dbConnector->errorInfo();
    	}
    	else{
    		$info = $this->lastPreparedExecStatement->errorInfo();
    	}
    	
    	if (!empty($info[2])){
    		$returnValue = $info[2];
    	}
    	else if (!empty($info[1])){
            $returnValue = 'Driver error: ' . $info[1];
    	}
    	else if (!empty($info[0])){
    		$returnValue = 'SQLSTATE: ' . $info[0];
    	}
        else{
            $returnValue = 'No error message to display.';
        }

        return (string) $returnValue;
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
    public abstract function getTables();

    /**
     * Returns the column names of a given table
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string table
     * @return array
     */
    public abstract function getColumnNames($table);

    /**
     * Get a statement in the statement store regarding the provided statement.
     * it could not be found, NULL is returned.
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string statement
     * @return PDOStatement
     */
    protected function getStatement($statement)
    {
        $key = $this->getStatementKey($statement);
    	$sth = null;
    	
    	if (!empty($this->statements[$key])){
    		$sth = $this->statements[$key];
    	}
    	else{
    		$sth = $this->dbConnector->prepare($statement);
    		$this->statements[$key] = $sth;
    	}
    	
    	return $sth;

    }

    /**
     * Get the key of a given statement stored in the statements store.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string statement
     * @return string
     */
    public function getStatementKey($statement)
    {
        return hash('crc32b', $statement);
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
    public abstract function limitStatement($statement, $limit, $offset = 0);

    /**
     * Retrieve Extra Configuration for the driver
     *
     * @abstract
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return array
     */
    protected abstract function getExtraConfiguration();

    /**
     * The error code returned by PDO in when a table is not found in a query
     * a given DBMS implementation.
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public abstract function getTableNotFoundErrorCode();

    /**
     * Returns the error code corresponding to a column not found in a query
     * on a given DBMS implementation.
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public abstract function getColumnNotFoundErrorCode();

    /**
     * Should contain any instructions that must be executed right after the
     * to a given DBMS implementation.
     *
     * @abstract
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return void
     */
    protected abstract function afterConnect();

    /**
     * The error code returned by PDO in when an Index already exists in a table
     * a given DBMS implementation.
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public abstract function getIndexAlreadyExistsErrorCode();

    /**
     * Returns the DSN to Connect with PDO to the database.
     *
     * @abstract
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    protected abstract function getDSN();

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
    public abstract function createIndex($indexName, $tableName, $columns);

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
    public abstract function rebuildIndexes($tableName);

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
    public abstract function flush($tableName);

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
        $result = $this->dbConnector->query($sql);
        $returnValue = intval($result->fetchColumn(0));
        $result->closeCursor();
        return (int) $returnValue;
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
    	return $this->dbConnector->quote($parameter, $parameter_type);
    }

}
