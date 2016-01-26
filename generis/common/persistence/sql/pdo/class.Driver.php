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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */




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
abstract class common_persistence_sql_pdo_Driver implements common_persistence_sql_Driver
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---


    /**
     * An established PDO connection object.
     *
     * @access public
     * @var PDO
     */
    private $dbConnector = null;

  
    /**
     * States if the last statement executed by the wrapper was a prepared
     * or not.
     *
     * @access public
     * @var boolean
     */
    private $preparedExec = false;

    /**
     * The very last PDOStatement instance that was prepared by the wrapper.
     *
     * @access public
     * @var PDOStatement
     */
    private $lastPreparedExecStatement = null;

    /**
     * A prepared statement store used by the prepare() method to reuse
     * at most.
     *
     * @access public
     * @var array
     */
    private $statements = array();

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

    private $params;

    // --- OPERATIONS ---

    /**
     * Connect the SQL driver
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return common_persistence_SqlPersistence
     */
    public function connect($id, array $params)
    {
        $returnValue = null;
        $this->params = $params;
        $driver = $params['driver'];
        $dbLogin = $params['user'];
        $dbpass = $params['password'];
        //remove pdo to add UpperCase ClassName

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
                    $this->dbConnector = @new PDO($dsn, $dbLogin, $dbpass, $options);
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
            
            $returnValue =  new common_persistence_SqlPersistence($params,$this);;

        return $returnValue;
    }


    /**
     *  HACK to set "PDO::MYSQL_ATTR_MAX_BUFFER_SIZE" for fileupload
     *  add attribute to the connection
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $name
     * @param string $value
     * @throws PDOException
     */
    public function setAttribute($name,$value){
        try{
            common_Logger::d('setattri ' . $name . ' => ' . $value);
            $this->dbConnector->setAttribute($name, $value);
            
        } catch (PDOException $e){
            common_Logger::e('Fail to set attribute ' . $name . ' with value ' . $value);
            throw $e;
        }
    
    }
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param unknown $tableName
     * @param array $data
     */
    public function insert($tableName, array $data){
    
        $cols = array();
        $placeholders = array();
        
        foreach ($data as $columnName => $value) {
            $cols[] = $this->getPlatForm()->quoteIdentifier($columnName);
            $placeholders[] = '?';
        }
        
        $query = 'INSERT INTO ' . $tableName
        . ' (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $placeholders) . ')';
        
        return $this->exec($query, array_values($data));
        
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

       
        if (count($params) > 0){
        	$sth = $this->dbConnector->prepare($statement);
        	$sth->execute($params);
        }
        else{
        	$sth = $this->dbConnector->query($statement);
        }
        
		
        if (!empty($sth)){
        	$returnValue = $sth;
        }

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
	
        $returnValue = null;
        $this->preparedExec = false;
        $returnValue = $this->getStatement($statement);

		
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
    private function errorCode()
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
    private function errorMessage()
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
     * Returns the number of hits in the statements store.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return int
     */
    private function getNrOfHits()
    {
        return  $this->nrHits;
    }



    /**
     * Returns the number of misses in the statements store.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return int
     */
    private function getNrOfMisses()
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
     * Retrieve Extra Configuration for the driver
     *
     * @abstract
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return array
     */
    protected abstract function getExtraConfiguration();




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
     * Returns the DSN to Connect with PDO to the database.
     *
     * @abstract
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    protected abstract function getDSN();

    
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
    
    /**
     * Convenience access to PDO::lastInsertId.
     *
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param string $name
     * @return string The quoted string.
     */
    public function lastInsertId($name = null){
        return $this->dbConnector->lastInsertId($name);
    }

    public function getParams()
    {
        return $this->params;
    }
	

}
