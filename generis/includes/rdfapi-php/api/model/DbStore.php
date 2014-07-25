<?php
require_once RDFAPI_INCLUDE_DIR . 'constants.php';
require_once RDFAPI_INCLUDE_DIR . 'util/Object.php';
require_once RDFAPI_INCLUDE_DIR . 'util/DBConnection.php';
require_once RDFAPI_INCLUDE_DIR . 'model/DbModel.php';

// ----------------------------------------------------------------------------------
// Class: DbStore
// ----------------------------------------------------------------------------------

/**
 * DbStore is a persistent store of RDF data using relational database technology.
 * DbStore uses PDO as a Database abstraction layer,
 * which allows to connect to multiple databases in a portable manner.
 * This class also provides methods for creating tables for MsAccess, MySQL, and MS SQL Server.
 * If you want to use other databases, you will have to create tables by yourself
 * according to the abstract database schema described in the API documentation.
 *
 *
 *
 * @version  $Id: DbStore.php 560 2008-02-29 15:24:20Z cax $
 * @author   Radoslaw Oldakowski <radol@gmx.de>
 * @author   Daniel Westphal (http://www.d-westphal.de)
 *
 * @package model
 * @access	public
 */


class DbStore extends Object
{
    /**
    * Array with all supported database types
    *
    * @var array
    */
    public static $arSupportedDbTypes = array(
        "mysql",
        "pgsql"
    );

    /**
    * Database connection object
    *
    * @var     PDO
    * @access	private
    */
    var $dbConn;

    /**
    * Database driver name
    *
    * @var string
    */
    protected $driver = null;

    /**
    *   SparqlParser so we can re-use it
    *   @var Parser
    */
    var $queryParser = null;


/**
 * Constructor:
 * Set the database connection with the given parameters.
 *
 * @param   string   $dbDriver
 * @param   string   $host
 * @param   string   $dbName
 * @param   string   $user
 * @param   string   $password
 * @access	public
 */
 function DbStore ($dbDriver=RDFAPI_DB_DRIVER, $host=RDFAPI_DB_HOST, $dbName=RDFAPI_DB_NAME,
                   $user=RDFAPI_DB_USER, $password=RDFAPI_DB_PASSWORD) {

	// create a new PDO object
	$this->driver = strtolower($dbDriver);
	$options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM, // Best performance
		        	 PDO::ATTR_PERSISTENT => false,
		        	 PDO::ATTR_EMULATE_PREPARES => false);

	try{
		// We try to load the DBConnection class specialization for the
		// requested driver.
		$driver = $this->driver;
		$driverName = ucfirst($driver);
		$className = $driverName . 'DBConnection';
		$classFile = RDFAPI_INCLUDE_DIR . 'util/' . $className . '.php';
		if (file_exists($classFile)){
			require_once($classFile);
			$this->dbConn = new $className($user, $password, $dbName, $host, $options);
		}
		else{
			$driver = ucfirst(str_replace('pdo_', '', $this->driver));
			$className = $className = $driver . 'DBConnection';
			$classFile = $classFile = RDFAPI_INCLUDE_DIR . 'util/' . $className . '.php';
			if (file_exists($classFile)){
				require_once($classFile);
				$driver = strtolower($driver);
				$this->dbConn = new $className($user, $password, $dbName, $host, $options);	
			}
			else{
				throw new Exception("RDF-API: No DBConnection sub-class found for driver {$classFile}.");
			}
		}
	}
	catch (PDOException $e){
		throw new Exception("RDF-API: Cannot connect to database: " . $e->getMessage());
	}
 }



/**
 * List all DbModels stored in the database.
 *
 * @return  array
 * @throws	SqlError
 * @access	public
 */
 function listModels() {

   $recordSet = $this->dbConn->query('SELECT "modelURI", "baseURI"
                                         FROM "models"');
   if ($recordSet === false){
      $errmsg = $this->dbConn->errorInfo();
      $errmsg = $errmsg[0];
      trigger_error($errmsg, E_USER_ERROR);
   }
   else {
      $models = array();
      $i = 0;
      while ($row = $recordSet->fetch()) {

          $models[$i]['modelURI'] = $row[0];
          $models[$i]['baseURI'] = $row[1];

          ++$i; // ;) ...
      }
      return $models;
   }
 }


/**
 * Check if the DbModel with the given modelURI is already stored in the database
 *
 * @param   string   $modelURI
 * @return  boolean
 * @throws	SqlError
 * @access	public
 */
 function modelExists($modelURI) {

  if(preg_match("/#$/", $modelURI)){
  	$shortModelUri = substr($modelURI, 0, -1);
  }
  else{
  	$shortModelUri = $modelURI;
  	$modelURI .= '#';
  }
   
  $sth = $this->dbConn->prepare('SELECT COUNT(*) FROM "models" WHERE ("modelURI" = ? OR "modelURI" = ?)');
  $res = $sth->execute(array($modelURI,$shortModelUri));
  
  if ($res === false){
     $errmsg = $this->dbConn->errorInfo();
     $errmsg = $errmsg[0];
     trigger_error($errmsg, E_USER_ERROR);
  }
  else {
     $count = $sth->fetchColumn(0);
     $sth->closeCursor();
     return ($count > 0);
   }
 }



    /**
    * Returns the database connection object
    *
    * @return PDO Database object
    * @access public
    */
    function getDbConn()
    {
        return $this->dbConn;
    }


/**
 * Create a new instance of DbModel with the given $modelURI and
 * load the corresponding values of modelID and baseURI from the database.
 * Return FALSE if the DbModel does not exist.
 *
 * @param   string   $modelURI
 * @return  object DbModel
 * @access	public
 */
 function getModel($modelURI) {

   if (!$this->modelExists($modelURI))
      return FALSE;
   else {
	   if(preg_match("/#$/", $modelURI)){
	  	$shortModelUri = substr($modelURI, 0, -1);
	  }
	  else{
	  	$shortModelUri = $modelURI;
	  	$modelURI .= '#';
	  }
	  
      $sth = $this->dbConn->prepare('SELECT "modelURI", "modelID", "baseURI"
                                     FROM "models"
                                     WHERE ("modelURI" = ? OR "modelURI" = ? )');
	  $sth->execute(array($modelURI, $shortModelUri));
      $row = $sth->fetch();
      
      $dbModel = new DbModel($this->dbConn, $row[0], $row[1], $row[2]);
      $sth->closeCursor();
      return $dbModel;
   }
 }


/**
 * Create a new instance of DbModel with the given $modelURI
 * and insert the DbModel variables into the database.
 * Return FALSE if there is already a model with the given URI.
 *
 * @param   string   $modelURI
 * @param   string   $baseURI
 * @return  object DbModel
 * @throws  SqlError
 * @access	public
 */
 function getNewModel($modelURI, $baseURI=NULL) {

   if ($this->modelExists($modelURI))
      return FALSE;
   else {
      $modelID = $this->_createUniqueModelID();
		if(is_null($baseURI)){
			$baseURI = $modelURI;
			if(!preg_match("/#$/", $baseURI)){
				$baseURI .= '#';
			}
		}
		
      	$sth = $this->dbConn->prepare('INSERT INTO models ("modelID", "modelURI", "baseURI") VALUES (?,?,?)');
		$res = $sth->execute(array($modelID,$modelURI,$baseURI));
      	
		if ($res === false){
			$errmsg = $sth->errorInfo();
			$errmsg = $errmsg[0];
			trigger_error($errmsg, E_USER_ERROR);	
		}
		else{
			return new DbModel($this->dbConn, $modelURI, $modelID, $baseURI);	
		}
   }
 }


/**
 * Store a MemModel or another DbModel from a different DbStore in the database.
 * Return FALSE if there is already a model with modelURI matching the modelURI
 * of the given model.
 *
 * @param   object Model  &$model
 * @param   string $modelURI
 * @return  boolean
 * @access	public
 */
 function putModel($model, $modelURI=NULL) {

   if (!$modelURI) {
      if (is_a($model, 'MemModel'))
         $modelURI = 'DbModel-' .$this->_createUniqueModelID();
      else
         $modelURI = $model->modelURI;
   }else
      if ($this->modelExists($modelURI))
         return FALSE;


   $newDbModel = $this->getNewModel($modelURI, $model->getBaseURI());
   $newDbModel->addModel($model);
 }


/**
 * Close the DbStore.
 * !!! Warning: If you close the DbStore all active instances of DbModel from this
 * !!!          DbStore will lose their database connection !!!
 *
 * @access	public
 */
 function close() {

   $this->dbConn->close();
   unset($this);
 }


// =============================================================================
// **************************** private methods ********************************
// =============================================================================


/**
 * Create a unique ID for the DbModel to be insert into the models table.
 * This method was implemented because some databases do not support auto-increment.
 *
 * @return  integer
 * @access	private
 */
 function _createUniqueModelID() {

   $result = $this->dbConn->query('SELECT MAX("modelID") FROM "models"');
   $maxModelId = (int) $result->fetchColumn(0);
   $result->closeCursor();
   
   return ++$maxModelId;
 }

 /**
 * Create a unique ID for the dataset to be insert into the datasets table.
 * This method was implemented because some databases do not support auto-increment.
 *
 * @return  integer
 * @access	private
 */
 function _createUniqueDatasetID() {

   $result = $this->dbConn->query('SELECT MAX("datasetId") FROM "datasets"');
   $maxDatasetID = (int) $result->fetchColumn(0);
   $result->closeCursor();
   return ++$maxDatasetID;
 }



    /**
     * Sets up tables for RAP.
     * DOES NOT CHECK IF TABLES ALREADY EXIST
     *
     * @param string  $databaseType Database driver name (e.g. MySQL)
     *
     * @throws Exception If database type is unsupported
     * @access public
     **/
    public function createTables($databaseType = null)
    {
        $driver = $this->getDriver($databaseType);
        self::assertDriverSupported($driver);

        $createFunc = '_createTables_' . $driver;
        return $this->$createFunc();
    }//public function createTables($databaseType="MySQL")



    /**
    * Create tables and indexes for MsAccess database
    *
    * @return boolean true If all is ok
    *
    * @throws Exception
    */
    protected function _createTables_MsAccess()
    {
        $this->dbConn->startTrans();

        $this->dbConn->execute('CREATE TABLE models
                                (modelID long primary key,
                                    modelURI varchar not null,
                                    baseURI varchar)');

        $this->dbConn->execute('CREATE UNIQUE INDEX m_modURI_idx ON models (modelURI)');

        $this->dbConn->execute('CREATE TABLE statements
                                (modelID long,
                                    subject varchar,
                                    predicate varchar,
                                    object Memo,
                                    l_language varchar,
                                    l_datatype varchar,
                                    subject_is varchar(1),
                                    object_is varchar(1),
                                    primary key (modelID, subject, predicate, object,
                                    l_language, l_datatype))');

        $this->dbConn->execute('CREATE INDEX s_mod_idx ON statements (modelID)');
        $this->dbConn->execute('CREATE INDEX s_sub_idx ON statements (subject)');
        $this->dbConn->execute('CREATE INDEX s_pred_idx ON statements (predicate)');
        $this->dbConn->execute('CREATE INDEX s_obj_idx ON statements (object)');

        $this->dbConn->execute('CREATE TABLE namespaces
                            (modelID long,
                                namespace varchar,
                                prefix varchar,
                                primary key (modelID, namespace, prefix))');

        $this->dbConn->execute('CREATE INDEX n_name_idx ON namespaces (namespace)');
        $this->dbConn->execute('CREATE INDEX n_pref_idx ON namespaces (prefix)');

        $this->dbConn->execute("CREATE TABLE datasets
                            (datasetName varchar,
                                defaultModelUri varchar,
                                primary key (datasetName))");

        $this->dbConn->execute('CREATE INDEX nGS_idx1 ON datasets (datasetName)');


        $this->dbConn->execute("CREATE TABLE `dataset_model` (
                                    datasetName varchar,
                                    modelId long,
                                    graphURI varchar,
                                    PRIMARY KEY  (modelId,datasetName))");


        if (!$this->dbConn->completeTrans()) {
            throw new Exception($this->dbConn->errorMsg());
        }
        return true;
    }



    /**
    * Create tables and indexes for MySQL database
    *
    * @return boolean true If all is ok
    *
    * @throws Exception
    */
    function _createTables_MySQL()
    {

        $this->dbConn->startTrans();

        $this->dbConn->execute("CREATE TABLE models
                                (modelID bigint NOT NULL,
                                    modelURI varchar(255) NOT NULL,
                                    baseURI varchar(255) DEFAULT '',
                                    primary key (modelID))");

        $this->dbConn->execute('CREATE UNIQUE INDEX m_modURI_idx ON models (modelURI)');

        $this->dbConn->execute("CREATE TABLE statements
                                (modelID bigint NOT NULL,
                                    subject varchar(255) NOT NULL,
                                    predicate varchar(255) NOT NULL,
                                    object text,
                                    l_language varchar(255) DEFAULT '',
                                    l_datatype varchar(255) DEFAULT '',
                                    subject_is varchar(1) NOT NULL,
                                    object_is varchar(1) NOT NULL)");

        $this->dbConn->execute("CREATE TABLE namespaces
                                (modelID bigint NOT NULL,
                                    namespace varchar(255) NOT NULL,
                                    prefix varchar(255) NOT NULL,
                                    primary key (modelID,namespace))");

        $this->dbConn->execute("CREATE TABLE `dataset_model` (
                                    `datasetName` varchar(255) NOT NULL default '0',
                                    `modelId` bigint(20) NOT NULL default '0',
                                    `graphURI` varchar(255) NOT NULL default '',
                                    PRIMARY KEY  (`modelId`,`datasetName`))");

        $this->dbConn->execute("CREATE TABLE `datasets` (
                                    `datasetName` varchar(255) NOT NULL default '',
                                    `defaultModelUri` varchar(255) NOT NULL default '0',
                                    PRIMARY KEY  (`datasetName`),
                                    KEY `datasetName` (`datasetName`))");

        $this->dbConn->execute('CREATE INDEX s_mod_idx ON statements (modelID)');
        $this->dbConn->execute('CREATE INDEX n_mod_idx ON namespaces (modelID)');

        $this->dbConn->execute('CREATE INDEX s_sub_pred_idx ON statements
                                (subject(200),predicate(200))');

        $this->dbConn->execute('CREATE INDEX s_sub_idx ON statements (subject(200))');
        $this->dbConn->execute('CREATE INDEX s_pred_idx ON statements (predicate(200))');
        $this->dbConn->execute('CREATE INDEX s_obj_idx ON statements (object(250))');

        $this->dbConn->execute('CREATE FULLTEXT INDEX s_obj_ftidx ON statements (object)');

        if (!$this->dbConn->completeTrans()) {
            throw new Exception($this->dbConn->errorMsg());
        }
        return true;
    }



    /**
    * Creates tables on a MySQLi database
    */
    function _createTables_MySQLi()
    {
        return $this->_createTables_MySQL();
    }//function _createTables_MySQLi()



    /**
    * Create tables and indexes for MSSQL database
    *
    * @return boolean true If all is ok
    *
    * @throws Exception
    */
    function _createTables_MSSQL()
    {
        $this->dbConn->startTrans();

        $this->dbConn->execute("CREATE TABLE [dbo].[models] (
                                    [modelID] [int] NOT NULL ,
                                    [modelURI] [nvarchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
                                    [baseURI] [nvarchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL
                                    ) ON [PRIMARY]");

        $this->dbConn->execute("CREATE TABLE [dbo].[statements] (
                                    [modelID] [int] NOT NULL ,
                                    [subject] [nvarchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
                                    [predicate] [nvarchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
                                    [object] [text] COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
                                    [l_language] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
                                    [l_datatype] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
                                    [subject_is] [nchar] (1) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
                                    [object_is] [nchar] (1) COLLATE SQL_Latin1_General_CP1_CI_AS NULL
                                    ) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]");


        $this->dbConn->execute("CREATE TABLE [dbo].[namespaces] (
                                    [modelID] [int] NOT NULL ,
                                    [namespace] [nvarchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
                                    [prefix] [nvarchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
                                    ) ON [PRIMARY]");

        $this->dbConn->execute("ALTER TABLE [dbo].[models] WITH NOCHECK ADD
                                    CONSTRAINT [PK_models] PRIMARY KEY  CLUSTERED
                                    (
                                        [modelID]
                                    )  ON [PRIMARY] ");
        $this->dbConn->execute("ALTER TABLE [dbo].[namespaces] WITH NOCHECK ADD
                                    CONSTRAINT [PK_namespaces] PRIMARY KEY  CLUSTERED
                                    (
                                        [modelID],[namespace]
                                    )  ON [PRIMARY] ");

        $this->dbConn->execute("CREATE  INDEX [joint index on subject and predicate] ON [dbo].[statements]([subject], [predicate]) ON [PRIMARY]");


        if (!$this->dbConn->completeTrans()) {
            throw new Exception($this->dbConn->errorMsg());
        }
        return true;
    }



    /**
     * Checks if tables are setup for RAP
     *
     * @param   string  $databaseType
     * @throws Exception If database type is unsupported
     * @access public
     **/
    public function isSetup($databaseType = null)
    {
        $driver = $this->getDriver($databaseType);
        self::assertDriverSupported($driver);

        $issetupFunc = '_isSetup_' . $driver;
        return $this->$issetupFunc();
    }//public function isSetup($databaseType="MySQL")



    /**
    * Returns the driver for the database type.
    * You can pass NULL or omit the parameter to
    *  use the parameter from the dbstore constructor
    *
    * @param string $databaseType Database driver name (e.g. MySQL)
    *
    * @return string Database driver string (e.g. MySQL)
    */
    public function getDriver($databaseType = null)
    {
        if ($databaseType === null) {
            if ($this->driver === null) {
                //backward compatibility
                $databaseType = 'MySQL';
            } else {
                $databaseType = $this->driver;
            }
        }
        if (!self::isDriverSupported($databaseType)) {
            //check if it is a known driver in wrong case
            $arLowercases = array_map('strtolower', self::$arSupportedDbTypes);
            $arMapping    = array_combine($arLowercases, self::$arSupportedDbTypes);
            if (isset($arMapping[strtolower($databaseType)])) {
                $databaseType = $arMapping[strtolower($databaseType)];
            }
        }
        return $databaseType;
    }//public function getDriver($databaseType = null)



    /**
    * Returns if the given driver is supported
    *
    * @return boolean True if it supported, false if not
    */
    public static function isDriverSupported($databaseType)
    {
        return in_array($databaseType, self::$arSupportedDbTypes);
    }//public static function isDriverSupported($databaseType)



    /**
    * Checks if the given driver is supported and throws an
    * Exception if not.
    *
    * @param string $databaseType Database driver name (e.g. MySQL)
    *
    * @return true If it does not fail
    *
    * @throws Exception If the driver is not supported
    */
    public static function assertDriverSupported($databaseType)
    {
        if (!self::isDriverSupported($databaseType)) {
            throw new Exception(
                'Unsupported database type, only supported: '
                . implode(', ', self::$arSupportedDbTypes)
            );
        }
        return true;
    }//public static function assertDriverSupported($databaseType)



    /**
    * Checks if tables are setup for RAP (MySql)
    *
    * @throws SqlError
    * @access private
    **/
    function _isSetup_MySQL()
    {
        $recordSet = $this->dbConn->query("SHOW TABLES");
        if ($recordSet === false) {
        	$errmsg = $this->dbConn->errorInfo();
        	$errmsg = $errmsg[0];
        	trigger_error($errmsg, E_USER_ERROR);
        } else {
            $tables = array();
            while ($row = $recordSet->fetch()) {
                $tables[]= $row[0];
                if (isset($i)) {
                    ++$i;
                }
            }
            if (in_array("models",$tables) && in_array("statements",$tables)
             && in_array("namespaces",$tables)) {
                return true;
            }
        }
        return false;
    }//function _isSetup_MySQL()



    /**
    * Checks if tables are setup for RAP (MySQLi)
    *
    * @see _isSetup_MySQL()
    */
    function _isSetup_MySQLi()
    {
        return $this->_isSetup_MySQL();
    }//function _isSetup_MySQLi()



    /**
    * Checks if tables are setup for RAP (MsAccess)
    *
    * @throws SqlError
    * @access private
    **/
    function _isSetup_MsAccess()
    {
        $tables =& $this->dbConn->MetaTables();
        if (!$tables) {
            throw new Exception($this->dbConn->errorMsg());
        }
        if (count($tables) == 0) {
            return false;
        } else {
            if (in_array("models",$tables) && in_array("statements",$tables)
             && in_array("namespaces",$tables)) {
                return true;
            } else {
                return false;
            }
        }
    }//function _isSetup_MsAccess()



    /**
    * Checks if tables are setup for RAP (MSSQL)
    *
    * @throws SqlError
    * @access private
    **/
    function _isSetup_MSSQL()
    {
        $tables =& $this->dbConn->MetaTables();
        if (!$tables) {
            throw new Exception($this->dbConn->errorMsg());
        }
        if (count($tables) == 0) {
            return false;
        } else {
            if (in_array("models",$tables) && in_array("statements",$tables)
             && in_array("namespaces",$tables)){
                return true;
            } else {
                return false;
            }
        }
    }//function _isSetup_MSSQL()


 /**
 * Create a new instance of DatasetDb with the given $datasetName
 * and insert the DatasetDb variables into the database.
 * Return FALSE if there is already a model with the given URI.
 *
 * @param   $datasetName string
 * @return  object DatasetDB
 * @throws  SqlError
 * @access	public
 */
 function getNewDatasetDb($datasetName)
 {

 	require_once(RDFAPI_INCLUDE_DIR . PACKAGE_DATASET);

   if ($this->datasetExists($datasetName))
      return FALSE;
   else
   {
   		$defaultModelUri=uniqid('http://rdfapi-php/dataset_defaultmodel_');
   		$defaultModel=$this->getNewModel($defaultModelUri);

      	$rs = $this->dbConn->exec('INSERT INTO "datasets"
                                   VALUES (' .$this->dbConn->quote($datasetName) .',
                                   		   ' .$this->dbConn->quote($defaultModelUri).')');

      if ($res === false){
         $errmsg = $this->dbConn->errorInfo();
         $errmsg = $errmsg[0];
         trigger_error($errmsg, E_USER_ERROR);
      }
      else
		$return = new DatasetDb($this->dbConn, $this, $datasetName);
   		return ($return);
   }
 }

 /**
 * Check if the Dataset with the given $datasetName is already stored in the database
 *
 * @param   $datasetName string
 * @return  boolean
 * @throws	SqlError
 * @access	public
 */
function datasetExists($datasetName) {

   $res = $this->dbConn->execute('SELECT COUNT(*) FROM "datasets"
                                   WHERE "datasetName" = ' . $this->dbConn->quote($datasetName));
   if ($res === false){
      $errmsg = $this->dbConn->errorInfo();
      $errmsg = $errmsg[0];
      trigger_error($errmsg, E_USER_ERROR);
   }
   else {
   	  $count = (int) $res->fetchColumn(0);
      return ($count > 0);
   }
 }


 /**
 * Create a new instance of DatasetDb with the given $datasetName and
 * load the corresponding values from the database.
 * Return FALSE if the DbModel does not exist.
 *
 * @param   $datasetId string
 * @return  object DatasetDb
 * @access	public
 */
 function &getDatasetDb($datasetName) {
    require_once(RDFAPI_INCLUDE_DIR . PACKAGE_DATASET);

    if (!$this->datasetExists($datasetName)) {
        return FALSE;
    } else {
        $return = new DatasetDb($this->dbConn, $this, $datasetName);
        return ($return);
    }
 }

 /**
 * Create a new instance of namedGraphDb with the given $modelURI and graphName and
 * load the corresponding values of modelID and baseURI from the database.
 * Return FALSE if the DbModel does not exist.
 *
 * @param   $modelURI string
 * @param   $graphName string
 * @return  object NamedGraphMem
 * @access	public
 */
 function getNamedGraphDb($modelURI, $graphName)
 {
	require_once(RDFAPI_INCLUDE_DIR . PACKAGE_DATASET);

   if (!$this->modelExists($modelURI))
      return FALSE;
   else {
      $modelVars = $this->dbConn->query('SELECT "modelURI", "modelID", "baseURI"
                                            FROM "models"
                                            WHERE "modelURI" = ' . $this->dbConn->quote($modelURI));

      $row = $modelVars->fetch();
      $graph = new NamedGraphDb($this->dbConn, $row[0], $row[1], $graphName, $row[2]);
      $modelVars->closeCursor();
      
      return $graph;
   }
 }

 /**
 * Create a new instance of namedGraphDb with the given $modelURI and graphName
 * and insert the DbModel variables into the database (not the graphName. This
 * is only stored persistently, when added to dataset).
 * Return FALSE if there is already a model with the given URI.
 *
 * @param   $modelURI string
 * @param  	$graphName string
 * @param   $baseURI string
 * @return  object namedGraphDb
 * @throws  SqlError
 * @access	public
 */
 function getNewNamedGraphDb($modelURI, $graphName, $baseURI=NULL) {

   if ($this->modelExists($modelURI))
      return FALSE;
   else {
      $modelID = $this->_createUniqueModelID();

      $rs = $this->dbConn->exec('INSERT INTO models (modelID, modelURI, baseURI)
                                            VALUES (' .$modelID . ','
                                                  . $this->dbConn->quote($modelURI) . ','
                                                  . $this->dbConn->quote($baseURI) . ')');
      if ($rs === false){
         $errmsg = $this->dbConn->errorInfo();
         $errmsg = $errmsg[0];
         trigger_error($errmsg, E_USER_ERROR);
      }
      else{
         return new NamedGraphDb($this->dbConn, $modelURI, $modelID, $graphName, $baseURI);
      }
   }
 }

 /**
 * Removes the graph with all statements from the database.
 * Warning: A single namedGraph can be added to several datasets. So it'll be
 * removed from all datasets.
 *
 * @param   $modelURI string
 * @return  boolean
 * @throws  SqlError
 * @access	public
 */
 function removeNamedGraphDb($modelURI)
 {
	if (!$this->modelExists($modelURI)){
		return FALSE;
	}
	
	$mURI = $this->dbConn->quote($modelURI);
	$result = $this->dbConn->query('SELECT "modelID" FROM "models" WHERE "modelURI"=' . $mURI);
	$modelID = (int) $result->fetchColumn(0);
	$result->closeCursor();
	
	$this->dbConn->exec('DELETE FROM "models" WHERE modelID='.$modelID);
	$this->dbConn->exec('DELETE FROM "dataset_model" WHERE modelId='.$modelID);
	$this->dbConn->exec('DELETE FROM "statements" WHERE modelID='.$modelID);

	return true;
 }



    /**
    * Performs a SPARQL query against a model. The model is converted to
    * an RDF Dataset. The result can be retrived in SPARQL Query Results XML Format or
    * as an array containing the variables an their bindings.
    *
    * @param  string $query       Sparql query string
    * @param mixed $arModelIds    Array of modelIDs, or NULL to use all models
    * @param  string $resultform  Result form ('xml' for SPARQL Query Results XML Format)
    * @return string/array
    */
    function sparqlQuery($query, $arModelIds = null, $resultform = false)
    {
        $engine = $this->_prepareSparql($arModelIds);
        return $engine->queryModel(
            null,
            $this->_parseSparqlQuery($query),
            $resultform
        );
    }//function sparqlQuery($query,$resultform = false)



    /**
    *   Prepares everything for SparqlEngine-usage
    *   Loads the files, creates instances for SparqlEngine and
    *   Dataset...
    *
    *   @return SparqlEngineDb
    */
    function _prepareSparql($arModelIds)
    {
        require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb.php';
        return new SparqlEngineDb($this, $arModelIds);
    }//function _prepareSparql()



    /**
    *   Parses an query and returns the parsed form.
    *   If the query is not a string but a Query object,
    *   it will just be returned.
    *
    *   @param $query mixed String or Query object
    *   @return Query query object
    *   @throws Exception If $query is no string and no Query object
    */
    function _parseSparqlQuery($query)
    {
        if ($this->queryParser === null) {
            require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlParser.php';
            $this->queryParser = new SparqlParser();
        }
        return $this->queryParser->parse($query);
    }//function _parseSparqlQuery($query)

} // end: Class DbStore
?>