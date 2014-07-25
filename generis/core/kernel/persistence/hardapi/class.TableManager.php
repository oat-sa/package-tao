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
?>
<?php

error_reporting(E_ALL);

/**
 * The TableManager class contains the logic to deal with tables in Hard SQL
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardapi
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015A3-includes begin
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015A3-includes end

/* user defined constants */
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015A3-constants begin
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015A3-constants end

/**
 * The TableManager class contains the logic to deal with tables in Hard SQL
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardapi
 */
class core_kernel_persistence_hardapi_TableManager
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
        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015AA begin

    	$this->strict = $strict;
    	$this->setName($name);

		if(count(self::$_tables) == 0){
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
			$result = $dbWrapper->query('SELECT DISTINCT "table" FROM class_to_table');
			while($row = $result->fetch()){
				self::$_tables[] = $row['table'];
				self::$_tables[] = $row['table'] . 'Props';
			}
		}
        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015AA end
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

        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015AD begin

		$returnValue = in_array($this->name, self::$_tables);

        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015AD end

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

        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015AF begin
		
		if(!$this->exists() && !empty($this->name)){
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
				
			//build the query to create the main table
			$query = 'CREATE TABLE "'.$this->name.'" (
						"id" SERIAL,
						PRIMARY KEY ("id"),
						"uri" VARCHAR(255) NOT NULL';
			
			foreach($columns as $column){
				if(isset($column['name'])){
					if(!isset($column['multi'])){
						$query .= ', "'.$column['name'].'"';
						$query .= " TEXT";
					}
				}
			}
			
			$query .= ')/*!ENGINE = MYISAM, DEFAULT CHARSET=utf8*/;';

			$dbWrapper->exec($query);
			
			// create table index
			$dbWrapper->createIndex('idx_' . $this->name, $this->name, array('uri' => null));
				
			//always create the multi prop table
			$query = 'CREATE TABLE "'.$this->name.'Props" (
				"id" SERIAL,
				"property_uri" VARCHAR(255),
				"property_value" TEXT,
				"property_foreign_uri" VARCHAR(255),
				"l_language" VARCHAR(5),
				"instance_id" int NOT NULL ,
				PRIMARY KEY ("id")';
                                
			$query .= ")/*!ENGINE = MYISAM, DEFAULT CHARSET=utf8*/;";
				
			$dbWrapper->exec($query);

			self::$_tables[] = "{$this->name}Props";
			
			// Create multiples properties table indexes
			try{
				$dbWrapper->createIndex('idx_props_l_language', $this->name . 'Props', array('l_language' => null));
				$dbWrapper->createIndex('idx_props_property_uri', $this->name . 'Props', array('property_uri' => null));
				$dbWrapper->createIndex('idx_props_foreign_property_uri', $this->name . 'Props', array('property_foreign_uri' => null));
				$dbWrapper->createIndex('idx_props_instance_id', $this->name . 'Props', array('instance_id' => null));
			}
			catch(PDOException $e){
				if($e->getCode() != $dbWrapper->getIndexAlreadyExistsErrorCode() && $e->getCode() != '00000'){
					//the user may not have the right to create the table index or it already exists.
					throw new core_kernel_persistence_hardapi_Exception("Unable to create the multiples properties table indexes  {$this->name} : " .$e->getMessage());
				}
			}
			
			//auto reference
			self::$_tables[] = $this->name;
			$returnValue = true;
				
		}
		
        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015AF end

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

        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015B9 begin
		$name = $this->getName(); 
		if(!empty($name) && $this->exists()){
			
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();

			//remove the multi properties table
			try{
				$dbWrapper->exec('DROP TABLE "'.$name.'Props"');
				$tblKey = array_search("{$name}Props", self::$_tables);
				if($tblKey !== false){
					unset(self::$_tables[$tblKey]);
				}
				
				//remove the table
				$result = $dbWrapper->exec('DROP TABLE "'.$name.'";');
				$tblKey = array_search($name, self::$_tables);
				if($tblKey !== false){
					unset(self::$_tables[$tblKey]);
				}
				
				$returnValue = true;
			}
			catch (PDOException $e){
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

        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015B9 end

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

        // section 10-13-1-85-4976cd9b:13b8f671e92:-8000:0000000000001DD9 begin
        $returnValue = $this->name;
        // section 10-13-1-85-4976cd9b:13b8f671e92:-8000:0000000000001DD9 end

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
        // section 10-13-1-85-4976cd9b:13b8f671e92:-8000:0000000000001DDC begin
    	if($this->isStrict() && !preg_match("/^_[0-9a-zA-Z\-_]{4,}$/", $name)){
			throw new core_kernel_persistence_hardapi_Exception("Dangerous table name '$name' . Table name must begin by a underscore, followed  with only alphanumeric, - and _ characters are allowed.");
		}
		else{
			$this->name = $name;
		}
        // section 10-13-1-85-4976cd9b:13b8f671e92:-8000:0000000000001DDC end
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

        // section 10-13-1-85-4976cd9b:13b8f671e92:-8000:0000000000001DE1 begin
        $name = $this->getName();
        if ($this->isBaseTable()){
        	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        	$rawColumns = $dbWrapper->getColumnNames($name);
        	$propertyColumns = array_diff($rawColumns, array('id', 'uri'));
        	
        	foreach ($propertyColumns as $pC){
        		$longName = core_kernel_persistence_hardapi_Utils::getLongName($pC);
        		if (!empty($longName)){
					$returnValue[$pC] = $longName;
        		}
        		else{
        			throw new core_kernel_persistence_hardapi_Exception("Unable to resolve property URI corresponding to column '${name}'.'${pC}'.");	
        		}
        	}
        }
        // section 10-13-1-85-4976cd9b:13b8f671e92:-8000:0000000000001DE1 end

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

        // section 10-13-1-85-60c76063:13b8f97825a:-8000:0000000000001DE5 begin
        $name = $this->getName();
        if ($this->isBaseTable()){
        	$propsTableName = $name . 'Props';
        	$tblmanager = new core_kernel_persistence_hardapi_TableManager($propsTableName);
        	if ($tblmanager->exists()){
        		$returnValue = $propsTableName;	
        	}
        	else{
        		throw new core_kernel_persistence_hardapi_Exception("The 'properties table' '${propsTableName}' does not exist.");	
        	}
        }
        else{
        	throw new core_kernel_persistence_hardapi_Exception("The current table '${name}' is not a 'base table'.");	
        }
        // section 10-13-1-85-60c76063:13b8f97825a:-8000:0000000000001DE5 end

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

        // section 10-13-1-85-60c76063:13b8f97825a:-8000:0000000000001DE9 begin
        $name = $this->getName();
        if ($this->isPropertiesTable()){
        	$baseTableName = preg_replace("/Props$/i", '', $name);
        	$tblmgr = new core_kernel_persistence_hardapi_TableManager($baseTableName);
        	if ($tblmgr->exists()){
        		$returnValue = $baseTableName;	
        	}
        	else{
        		throw new core_kernel_persistence_hardapi_Exception("The 'base table' '${baseTableName}' does not exist.");	
        	}
        }
        else{
        	throw new core_kernel_persistence_hardapi_Exception("The current table '${name}' is not a 'properties table'.");
        }
        // section 10-13-1-85-60c76063:13b8f97825a:-8000:0000000000001DE9 end

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

        // section 10-13-1-85-60c76063:13b8f97825a:-8000:0000000000001DED begin
        $name = $this->getName();
        if ($this->exists()){
        	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        	$rawColumns = $dbWrapper->getColumnNames($this->getName());
        	$returnValue = (count(array_intersect($rawColumns, array('id', 'uri'))) == 2);
        }
        else{
        	throw new core_kernel_persistence_hardapi_Exception("Table '${name}' does not exist.");	
        }
        // section 10-13-1-85-60c76063:13b8f97825a:-8000:0000000000001DED end

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

        // section 10-13-1-85-60c76063:13b8f97825a:-8000:0000000000001DF1 begin
    	$name = $this->getName();
        if ($this->exists()){
        	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        	$rawColumns = $dbWrapper->getColumnNames($this->getName());
        	$expectedColumns = array('id', 'property_uri', 'property_value', 'property_foreign_uri',
        							 'l_language', 'instance_id');
        	$returnValue = (count(array_intersect($rawColumns, $expectedColumns)) == 6);
        }
        else{
        	throw new core_kernel_persistence_hardapi_Exception("Table '${name}' does not exist.");	
        }
        // section 10-13-1-85-60c76063:13b8f97825a:-8000:0000000000001DF1 end

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

        // section 10-13-1-85--7fc9b7e9:13ba8f5f383:-8000:0000000000001E09 begin
        $tblName = $this->getName();
        if ($this->exists()){
        	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        	$columns = $dbWrapper->getColumnNames($tblName);
        	$returnValue = in_array($name, $columns);
        }
        else{
        	throw new core_kernel_persistence_hardapi_Exception("Table '${tblName}' does not exist in database.");	
        }
        
        // section 10-13-1-85--7fc9b7e9:13ba8f5f383:-8000:0000000000001E09 end

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

        // section 10-13-1-85--7fc9b7e9:13ba8f5f383:-8000:0000000000001E1A begin
        $returnValue = $this->strict;
        // section 10-13-1-85--7fc9b7e9:13ba8f5f383:-8000:0000000000001E1A end

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

        // section 10-13-1-85--2619f144:13ba9352ee6:-8000:0000000000001E1A begin
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
        			$sql = 'ALTER TABLE "' . $tblname . '" ADD COLUMN "' . $colname . '" TEXT';
        			try{
        				$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        				$dbWrapper->exec($sql);
        				$returnValue = true;
        			}
        			catch (PDOException $e){
        				throw new core_kernel_persistence_hardapi_Exception("An error occured while adding column '${colname}' to table '${tblname}': " . $e->getMessage());
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
        // section 10-13-1-85--2619f144:13ba9352ee6:-8000:0000000000001E1A end

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

        // section 10-13-1-85--2619f144:13ba9352ee6:-8000:0000000000001E1E begin
        if (!empty($name)){
        	$tblname = $this->getName();
        	$sql = 'ALTER TABLE "' . $tblname . '" DROP COLUMN "' . $name . '"';

        	try{
        		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        		$dbWrapper->exec($sql);
        		$returnValue = true;
        	}
        	catch (PDOException $e){
        		throw new core_kernel_persistence_hardapi_Exception("An error occured while removing column '${name}' from table '${tblname}': " . $e->getMessage());	
        	}
        }
        else{
        	throw new InvalidArgumentException("Empty column name provided.");	
        }
        // section 10-13-1-85--2619f144:13ba9352ee6:-8000:0000000000001E1E end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_hardapi_TableManager */

?>