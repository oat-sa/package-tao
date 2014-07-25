<?php
abstract class DBConnection extends PDO {
	
	/**
	 * Contains the prepared statements for the current connection.
	 * @var array
	 */
	private $statementStore = array();
	
	protected $user;
	
	protected $pass;
	
	protected $host;
	
	protected $dbName;
	
	/**
	 * Creates a new extended PDO instance that represents a connection to a
	 * specific RDBMS.
	 */
	public function DBConnection($username, $password, $dbName, $host, $driver_options = array()){
		
		$this->user = $username;
		$this->pass = $password;
		$this->dbName = $dbName;
		$this->host = $host;
		
		foreach ($this->getExtraConfiguration() as $k => $v){
			$driver_options[$k] = $v;
		}

		$dsn = $this->getDSN();
		parent::__construct($dsn, $username, $password, $driver_options);
		$this->afterConnect();
	}
	
	/**
	 * Implement this method to return extra PDO configuration parameters
	 * to your database connection. If there are no specific requirements
	 * for your PDO connection, just return an empty array instead.
	 * 
	 * @return array Extra PDO configuration parameters.
	 */
	protected abstract function getExtraConfiguration();
	
	/**
	 * Behaviour to execute right after the database connection
	 * is established.
	 * 
	 * @return void
	 */
	protected abstract function afterConnect();
	
	/**
	 * Implement this method to create the DSN used by PDO at
	 * connection time e.g. ';charset=utf8' supported by MySQL.
	 * 
	 * @return string The DSN for your PDO Driver.
	 */
	protected abstract function getDSN();
	
	/**
	 * PDO::prepare function overload that implements a prepared statement store
	 * for better reuse of prepared statements in memory. Throws standard PDOExceptions.
	 * 
	 * @param string $statement A valid SQL query for the target RDBMS.
	 * @param array $driver_options This array contains one ore more key => value pairs for the PDOStatement to prepare.
	 */
	public function prepare($statement, $driver_options = array()){
		$key = $this->getStatementKey($statement);
		
		if (!empty($this->statementStore[$key])){
			// Already prepared, use it again.
			return $this->statementStore[$key];
		}
		else{
			// Not prepared yet, try to prepare it.
			try{
				$sth = parent::prepare($statement);
				$this->statementStore[$key] = $sth;
				return $sth;
			}
			catch (PDOException $e){
				// Simply rethrow to client code.
				throw $e;
			}	
		}
	}
	
	/**
	 * Returns the current statement store as an array.
	 * 
	 * @return An array of already prepared statements.
	 */
	public function getStatementStore(){
		return $this->statementStore;
	}
	
	/**
	 * Sets the current statement store. If no parameter given, it is
	 * simply reset.
	 * 
	 * @param array $statementStore The array to be used as the statement store.
	 * @return void
	 */
	public function setStatementStore($statementStore = array()){
		$this->statementStore = $statementStore;
	}
	
	/**
	 * Returns a unique identifier used by the statement store for
	 * a particular SQL statement.
	 * 
	 * @param string $statement An SQL statement.
	 * @return string The resulting unique key for the SQL statement.
	 */
	protected function getStatementKey($statement){
		return hash('crc32b', $statement);
	}
}
?>