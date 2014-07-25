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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */

namespace oat\generisHard\models\hardapi;

/**
 * The TableManager class contains the logic to deal with tables in Hard SQL
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generisHard
 
 */
class TableManager
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute name
     *
     * @access protected
     * @var string
     */
    protected $name = '';

    /**
     * Short description of attribute _tables
     *
     * @access private
     * @var array
     */
    private static $_tables = array();

    /**
     * Table name strict mode.
     *
     * @access private
     * @var boolean
     */
    private $strict = false;

    // --- OPERATIONS ---

    /**
     * Creates a new instance of TableManager.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name The name of the table you have to deal with.
     * @param  boolean strict Use or not table name strict mode.
     * @return mixed
     */
    public function __construct($name, $strict = true)
    {
        

    	$this->strict = $strict;
    	$this->setName($name);

		if(count(self::$_tables) == 0){
			$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
			$result = $dbWrapper->query('SELECT DISTINCT "table" FROM class_to_table');
			while($row = $result->fetch()){
				self::$_tables[] = $row['table'];
				self::$_tables[] = $row['table'] . 'props';
			}
		}
        
    }

    /**
     * Indicates if the table with the name you provided at instanciation time
     * within the database.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function exists()
    {
        $returnValue = (bool) false;

        
		$returnValue = in_array($this->name, self::$_tables);

        

        return (bool) $returnValue;
    }

    /**
     * Create a new table with the columns names givien in argument and the name
     * with the constructor call.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array columns An array of associative arrays.
+ key 'multi' (boolean) describes if the property is multi valued or not.
+ key 'foreign' (boolean) states that the column references remote resources (vs. literal) or not.
+ key 'name' gives the name of the column to create.
     * @return boolean
     */
    public function create($columns = array())
    {
        $returnValue = (bool) false;

        
		
		if(!$this->exists() && !empty($this->name)){
			$dbWrapper = \core_kernel_classes_DbWrapper::singleton();

			//build the query to create the main table
			
			$schema = new \Doctrine\DBAL\Schema\Schema() ;
				
			$table = $schema->createTable($dbWrapper->quoteIdentifier($this->name));
			$table->addColumn("id", "integer",array("notnull" => true,"autoincrement" => true));
			$table->setPrimaryKey(array("id"));
            $table->addColumn("uri", "string",array("length" => 255, "notnull" => true));
            $table->addOption('engine' , 'MyISAM');

			
			foreach($columns as $column){
				if(isset($column['name'])){
					if(!isset($column['multi'])){

						$table->addColumn($dbWrapper->quoteIdentifier($column['name']), "text",array("notnull" => false));
					}
				}
			}
			// create table index
			$table->addIndex(array("uri"),"idx_" . $this->name);
			
    			
			$multiPro = $schema->createTable($dbWrapper->quoteIdentifier($this->name.'props'));
			$multiPro->addColumn("id", "integer",array("notnull" => true,"autoincrement" => true));
			$multiPro->addColumn("property_uri", "string",array("length" => 255));
			$multiPro->addColumn('property_value', "text",array("notnull" => false));
			$multiPro->addColumn("property_foreign_uri", "string",array("length" => 255,"notnull" => false));
			$multiPro->addColumn("l_language", "string",array("length" => 10,"notnull" => false));
			$multiPro->addColumn("instance_id", "integer",array("notnull" => true));
			$multiPro->setPrimaryKey(array("id"));
			$multiPro->addOption('engine' , 'MyISAM');

			
			//$multiPro->addIndex(array("l_language"),"idx_props_l_language");
			$multiPro->addIndex(array("property_uri"),"idx_props_property_uri");
			$multiPro->addIndex(array("property_foreign_uri"),"idx_props_property_foreign_uri");
			$multiPro->addIndex(array("instance_id"),"idx_props_instance_id");
			
			//$query = $schema->toSql();
			
           
            $query = $dbWrapper->getPlatform()->schemaToSql($schema);
            


			self::$_tables[] = "{$this->name}props";
			
			// Create multiples properties table indexes
			try{
			    foreach ($query as $q){
			         $dbWrapper->exec($q);
			    }
			}
			catch(\Exception $e){
				if($e->getCode() != $dbWrapper->getIndexAlreadyExistsErrorCode() && $e->getCode() != '00000'){
					//the user may not have the right to create the table index or it already exists.
					throw new \Exception("Unable to create the multiples properties table indexes  {$this->name} : " .$e->getMessage());
				}
				else {
				    \common_Logger::i('index already exist ' . $e->getMessage());
				    
				}
			}
			
			//auto reference
			self::$_tables[] = $this->name;
			$returnValue = true;
				
		}
		
        

        return (bool) $returnValue;
    }

    /**
     * Remove the table with the name provided at instanciation time from the
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function remove()
    {
        $returnValue = (bool) false;

        
		$name = $this->getName(); 
		if(!empty($name) && $this->exists()){
			
			$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
			//remove the multi properties table
			try{
			   
				$dbWrapper->exec('DROP TABLE '. $dbWrapper->quoteIdentifier($name.'props'));
				$tblKey = array_search("{$name}props", self::$_tables);
				if($tblKey !== false){
					unset(self::$_tables[$tblKey]);
				}

				//remove the table
				$dbWrapper->exec('DROP TABLE '.$dbWrapper->quoteIdentifier($name));
				
				$tblKey = array_search($name, self::$_tables);
				if($tblKey !== false){
					unset(self::$_tables[$tblKey]);
				}
				
				$returnValue = true;
			}
			catch (\PDOException $e){
			    \common_Logger::e('sommething go wrong when removing table : ' . $name);
				$returnValue = false;
			}
		}
		else{
			if (empty($name)){
				throw new LogicException("The 'name' field must be set before trying to remove a table.");
			}
			// else, it does nothing because the table actually do not exist.
			// it will return false.
		}

        

        return (bool) $returnValue;
    }

    /**
     * Provides the name of the currently targeted table.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getName()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->name;
        

        return (string) $returnValue;
    }

    /**
     * Set the name of the table you want the TableManager to deal with.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name The name of the table you want to deal with.
     * @return void
     */
    public function setName($name)
    {
        
    	if($this->isStrict() && !preg_match("/^_[0-9a-zA-Z\-_]{4,}$/", $name)){
			throw new Exception("Dangerous table name '$name' . Table name must begin by a underscore, followed  with only alphanumeric, - and _ characters are allowed.");
		}
		else{
			$this->name = $name;
		}
        
    }

    /**
     * Get the columns from the currently targeted table that aim at containing
     * values. If the targeted table is not a "base" table, a
     * is thrown.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getPropertyColumns()
    {
        $returnValue = array();

        
        $name = $this->getName();
        if ($this->isBaseTable()){
        	$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        	$rawColumns = $dbWrapper->getColumnNames($name);
        	$propertyColumns = array();
        	foreach ($rawColumns as $col){
        	    $colName = $col->getName();
        	    if(!in_array($colName,array('id', 'uri'))){
        	        $propertyColumns[] = $colName;
        	    }
        	}
       	
        	foreach ($propertyColumns as $pC){
        		$longName = Utils::getLongName($pC);
        		if (!empty($longName)){
					$returnValue[$pC] = $longName;
        		}
        		else{
        			throw new Exception("Unable to resolve property URI corresponding to column '${name}'.'${pC}'.");	
        		}
        	}
        }
        

        return (array) $returnValue;
    }

    /**
     * Obtain the 'properties table' name bound to the current 'base table'. An
     * will be thrown if the current table is not a 'base table' or if the
     * 'properties table' does not exist.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getPropertiesTable()
    {
        $returnValue = (string) '';

        
        $name = $this->getName();
        if ($this->isBaseTable()){
        	$propsTableName = $name . 'props';
        	$tblmanager = new self($propsTableName);
        	if ($tblmanager->exists()){
        		$returnValue = $propsTableName;	
        	}
        	else{
        		throw new Exception("The 'properties table' '${propsTableName}' does not exist.");	
        	}
        }
        else{
        	throw new Exception("The current table '${name}' is not a 'base table'.");	
        }
        

        return (string) $returnValue;
    }

    /**
     * Obtain the 'base table' name corresponding to the current table.
     * will be thrown
     * - if the current table does not exist.
     * - if the current table is not a 'properties table'
     * - if there is no corresponding 'base table'
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getBaseTable()
    {
        $returnValue = (string) '';

        
        $name = $this->getName();
        if ($this->isPropertiesTable()){
        	$baseTableName = preg_replace("/props$/i", '', $name);
        	$tblmgr = new self($baseTableName);
        	if ($tblmgr->exists()){
        		$returnValue = $baseTableName;	
        	}
        	else{
        		throw new Exception("The 'base table' '${baseTableName}' does not exist.");	
        	}
        }
        else{
        	throw new Exception("The current table '${name}' is not a 'properties table'.");
        }
        

        return (string) $returnValue;
    }

    /**
     * Check if the current table is a 'base table'.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function isBaseTable()
    {
        $returnValue = (bool) false;

        
        $name = $this->getName();
        if ($this->exists()){
        	$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        	$rawColumns = $dbWrapper->getColumnNames($this->getName());

        	$returnValue = (count(array_intersect(array_keys($rawColumns), array('id', 'uri'))) == 2);
        }
        else{
        	throw new Exception("Table '${name}' does not exist.");	
        }
        

        return (bool) $returnValue;
    }

    /**
     * Check if the current table is a 'properties table'.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function isPropertiesTable()
    {
        $returnValue = (bool) false;

        
    	$name = $this->getName();
        if ($this->exists()){
        	$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        	$rawColumns = $dbWrapper->getColumnNames($this->getName());
        	$expectedColumns = array('id', 'property_uri', 'property_value', 'property_foreign_uri',
        							 'l_language', 'instance_id');
        	$returnValue = (count(array_intersect($rawColumns, $expectedColumns)) == 6);
        }
        else{
        	throw new Exception("Table '${name}' does not exist.");	
        }
        

        return (bool) $returnValue;
    }

    /**
     * Makes you able to know if the current table has a given column or not.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name A column name.
     * @return boolean
     */
    public function hasColumn($name)
    {
        $returnValue = (bool) false;

        
        $tblName = $this->getName();
        if ($this->exists()){
        	$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        	$columns = $dbWrapper->getColumnNames($tblName);
        	$returnValue = in_array($name, $columns);
        }
        else{
        	throw new Exception("Table '${tblName}' does not exist in database.");	
        }
        
        

        return (bool) $returnValue;
    }

    /**
     * States if the current table manager is in strict mode or not.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function isStrict()
    {
        $returnValue = (bool) false;

        
        $returnValue = $this->strict;
        

        return (bool) $returnValue;
    }

    /**
     * Create an additional column to the table. The column description is an
     * array where keys have the following roles:
     * + 'name' (string) the name of the column to create
     * + 'multi' (boolean) states the column contains multi values or not
     * + 'foreign' (boolean) describes if the column references foreign
     * or not.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array column
     * @return boolean
     */
    public function addColumn($column)
    {
        $returnValue = (bool) false;

        
        $tblname = $this->getName();
        if (!empty($column)){
        	if (!empty($column['name'])){
        		$colname = $column['name'];
        		
        		$multi = true;
        		if (isset($column['multi'])){
        			$multi = $column['multi'];
        		}
        		
        		$foreign = false;
        		if (isset($column['foreign'])){
        			$foreign = $column['foreign'];	
        		}
        		
        		// Lets deal with this column description.
        		if (true === $multi){
        			// There is nothing to do. It will be handled
        			// by the properties table.
        			$returnValue = true;	
        		}
        		else{
        			$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        			$actualSchema = $dbWrapper->getSchemaManager()->createSchema();
        			$newSchema = $dbWrapper->getSchemaManager()->addColumnToTable($actualSchema,$tblname, $dbWrapper->quoteIdentifier($column['name']));
        			$sql = $dbWrapper->getPlatForm()->getMigrateSchemaSql($actualSchema,$newSchema); 
        			
        			try{
        				
        				$dbWrapper->exec(current($sql));
        				$returnValue = true;
        			}
        			catch (\PDOException $e){
        				throw new Exception("An error occured while adding column '${colname}' to table '${tblname}': " . $e->getMessage());
        			}
        		}
        	}
        	else{
        		throw new InvalidArgumentException("No column name provided.");
        	}
        }
        else{
        	throw new InvalidArgumentException("Cannot add a column from an empty array.");	
        }
        

        return (bool) $returnValue;
    }

    /**
     * Removes an existing column from the table.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name The name of the column to remove from the table.
     * @return boolean
     */
    public function removeColumn($name)
    {
        $returnValue = (bool) false;

        
        $dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        if (!empty($name)){
        	$tblname = $this->getName();
        	$sql = 'ALTER TABLE "' . $tblname . '" DROP COLUMN ' . $dbWrapper->quoteIdentifier($name);

        	try{
        		$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        		$dbWrapper->exec($sql);
        		$returnValue = true;
        	}
        	catch (\PDOException $e){
        		throw new Exception("An error occured while removing column '${name}' from table '${tblname}': " . $e->getMessage());	
        	}
        }
        else{
        	throw new InvalidArgumentException("Empty column name provided.");	
        }
        

        return (bool) $returnValue;
    }

}
