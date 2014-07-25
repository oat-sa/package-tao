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
?>
<?php

/**
 * Dedicated database wrapper used for database creation.
 * 
 * @see PDO
 * @author Bertrand CHEVRIER <bertrand.chevrier@tudor.lu>
 * @author Jerome BOGAERTS <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage install_utils
 *
 */
abstract class tao_install_utils_DbCreator{
	
	/**
	 * @var sqlParser
	 */
	private $sqlParser;
	
	/**
	 * @var procSqlParser
	 */
	private $procSqlParser;
	
	/**
	 * @var PDO
	 */
	protected $pdo = null;
	
	/**
	 * @var driver
	 */
	protected $driver = '';
	
	/**
	 * @var user
	 */
	protected $user = '';
	
	/**
	 * @var pass
	 */
	protected $pass = '';
	
	/**
	 * @var host
	 */
	protected $host = '';
	
	/**
	 * @var dbName
	 */
	protected $dbName = '';

	/**
	 * @var $options
	 */
	protected $options = array();
	
	public function __construct( $host = 'localhost', $user = 'root', $pass = '', $driver = 'pdo_mysql', $dbName = ''){
		
		$this->driver = strtolower($driver);
		$this->user = $user;
		$this->pass = $pass;
		$this->host = $host;
		$this->dbName = $dbName;

		$this->chooseSQLParsers();
		
		try{
	        $dsn = $this->getDiscoveryDSN();
	        $this->options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_BOTH,
	        					   PDO::ATTR_PERSISTENT => false,
	        					   PDO::ATTR_EMULATE_PREPARES => false);
	        				 
	     	foreach ($this->getExtraConfiguration() as $k => $v){
	     		$this->options[$k] = $v;	
	     	}

	     	$this->pdo = new PDO($dsn, $this->user, $this->pass, $this->options);
			$this->afterConnect();
	     	
			//if the database exists already, connect to it
			if ($this->dbExists($dbName)){
                common_Logger::d('Switch to database ' . $dbName , 'INSTALL');
				$this->setDatabase($dbName);
			}
		}
		catch(PDOException $e){
			$this->pdo = null;
            common_Logger::e('Problems connecting to DSN = ' . $dsn , 'INSTALL');
            common_Logger::e($e->getMessage() . $e->getTraceAsString(), 'INSTALL');
			throw new tao_install_utils_Exception("Unable to connect to the database '${dbName}' with the provided credentials: " . $e->getMessage());
		}
	}
	
	public function getSQLParser(){
		return $this->sqlParser;
	}
	
	public function setSQLParser($sqlParser){
		$this->sqlParser = $sqlParser;
	}
	
	public function getProcSQLParser(){
		return $this->procSqlParser;	
	}
	
	public function setProcSQLParser($sqlParser){
		$this->procSqlParser = $sqlParser;
	}
	
	abstract public function chooseSQLParsers();
	
	/**
	 * Load a given SQL file containing simple statements.
	 * SQL files containing Stored Procedure or Function declarations
	 * are not supported. Use tao_install_utils_SpConnector instead.
	 * 
	 * @param string $file path to the SQL file
	 * @param array repalce variable to replace into the file: array keys are search with {} around
	 * @throws tao_install_utils_Exception
	 */
	public function load($file, $replace = array())
	{
		$parser = $this->getSQLParser();
		$parser->setFile($file);
		$parser->parse();
		
		//make replacements
		foreach ($parser->getStatements() as $statement){
			//make replacements
			$finalStatement = $statement;
			foreach($replace as $key => $value){
				$finalStatement = str_replace('{'.strtoupper($key).'}', $value, $statement);
			}

			$this->pdo->exec($finalStatement);
		}
	}
	
	public function loadProc($file){
		$oldParser = $this->getSQLParser();
		$this->setSQLParser($this->getProcSQLParser());
		$this->load($file);
		$this->setSQLParser($oldParser);
	}
	
	/**
	 * Check if the database exists already
	 * @param string $name
	 */
	abstract public function dbExists($dbName);
	
	/**
	 * Clean database by droping all tables
	 * @param string $name
	 */
	abstract public function cleanDb();
	
	/**
	 * Set up the database name
	 * @param string $name
	 * @throws tao_install_utils_Exception
	 */
	public function setDatabase($name)
	{
		// We have to reconnect with PDO :/
		try{
			$this->pdo = null;
			$dsn = $this->getDatabaseDSN();
			$this->pdo = new PDO($dsn, $this->user, $this->pass, $this->options);
			$this->afterConnect();
		}
		catch (PDOException $e){
            common_Logger::e('Problems connecting to DSN = ' . $dsn , 'INSTALL');
            common_Logger::e($e->getMessage() . $e->getTraceAsString(), 'INSTALL');
			throw new tao_install_utils_Exception("Unable to set database '${name}': " . $e->getMessage() . "");
		}
	}
	
	abstract public function createDatabase($name);
	
	public function execute($query){
		$this->pdo->exec($query);
	}
	
	/**
	 * Close the connection when the wrapper is destructed
	 */
	public function __destruct()
	{
		if(!is_null($this->pdo)){
			$this->pdo = null;
		}
	}
	
	abstract protected function afterConnect();
	
	public static function getClassNameForDriver($driver){
		$driverName = ucfirst($driver);
		$className = 'tao_install_utils_' . $driver . 'DbCreator';
		if (class_exists($className)){
			return $className;
		}
		else{
			$driverName = str_replace('pdo_', '', $driver);
			$driverName = ucfirst($driverName);
			$className = 'tao_install_utils_' . $driverName . 'DbCreator';
			if (class_exists($className)){
				return $className;
			}
			else{
				$msg  = "Unable to find the sub-class of 'tao_install_utils_DbCreator' ";
				$msg .= "related to the '${driver}' database driver.";
				throw new tao_install_utils_Exception($msg);
			}
		}
	}

	abstract protected function getExtraConfiguration();
	
	abstract protected function getDiscoveryDSN();

	abstract protected function getDatabaseDSN();
}
?>