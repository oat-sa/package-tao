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

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\classes\class.PgsqlDbWrapper.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 07.02.2013, 11:16:20 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Simple utility class that allow you to wrap the database connector.
 * You can retrieve an instance evreywhere using the singleton method.
 *
 * This database wrapper uses PDO.
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 */
require_once('core/kernel/classes/class.DbWrapper.php');

/* user defined includes */
// section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC1-includes begin
// section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC1-includes end

/* user defined constants */
// section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC1-constants begin
// section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC1-constants end

/**
 * Short description of class core_kernel_classes_PgsqlDbWrapper
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_PgsqlDbWrapper
    extends core_kernel_classes_DbWrapper
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getExtraConfiguration
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return array
     */
    public function getExtraConfiguration()
    {
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC3 begin
    	$returnValue = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC3 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getTableNotFoundErrorCode
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function getTableNotFoundErrorCode()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC5 begin
        $returnValue = '42P01';
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC5 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getColumnNotFoundErrorCode
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function getColumnNotFoundErrorCode()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC7 begin
        $returnValue = '42703';
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC7 end

        return (string) $returnValue;
    }

    /**
     * Short description of method afterConnect
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return void
     */
    public function afterConnect()
    {
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC9 begin
        $this->dbConnector->exec("SET NAMES 'UTF8'");
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC9 end
    }

    /**
     * Short description of method getTables
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return array
     */
    public function getTables()
    {
        $returnValue = array();

        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BCE begin
        $catalog = $this->dbConnector->quote(DATABASE_NAME);
        $schema = $this->dbConnector->quote('public');
        $type = $this->dbConnector->quote('BASE TABLE');
        $sql = 'SELECT "table_name" FROM "information_schema"."tables" WHERE
                "table_catalog" = ' . $catalog . ' AND table_schema = ' . $schema
            . ' AND "table_type" = ' . $type;
        	 
        $result = $this->query($sql);
        while($table = (string)$result->fetchColumn(0)){
        	$returnValue[] = $table;
        }
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BCE end

        return (array) $returnValue;
    }

    /**
     * Short description of method limitStatement
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string statement
     * @param  int limit
     * @param  int offset
     * @return string
     */
    public function limitStatement($statement, $limit, $offset = 0)
    {
        $returnValue = (string) '';

        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BD0 begin
        $statement .= " LIMIT ${limit} OFFSET ${offset}";
        $returnValue = $statement;
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BD0 end

        return (string) $returnValue;
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
        $returnValue = (string) '';

        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BD6 begin
        $returnValue = '42P07';
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BD6 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getDSN
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    protected function getDSN()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-1b0119e:13ad126c698:-8000:0000000000001BD9 begin
        $driver = str_replace('pdo_', '', SGBD_DRIVER);
        $dbName = DATABASE_NAME;
        $dbUrl = DATABASE_URL;
        
        $returnValue = $driver . ':dbname=' . $dbName . ';host=' . $dbUrl;
        // section 10-13-1-85-1b0119e:13ad126c698:-8000:0000000000001BD9 end

        return (string) $returnValue;
    }

    /**
     * Short description of method createIndex
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string indexName
     * @param  string tableName
     * @param  array columns
     * @return void
     */
    public function createIndex($indexName, $tableName, $columns)
    {
        // section 10-13-1-85-69bd0289:13adae4f080:-8000:0000000000001BF7 begin
        $sql = 'CREATE INDEX "' . $indexName . '" ON "' . $tableName . '" (';
        $colsSql = array();
        foreach ($columns as $n => $l){
        	// Index lengths is not available in postgres.
        	$colsSql[] = '"' . $n . '"';
        }
        
        $colsSql = implode(',', $colsSql);
        $sql .= $colsSql . ')';
        
        $this->exec($sql);
        // section 10-13-1-85-69bd0289:13adae4f080:-8000:0000000000001BF7 end
    }

    /**
     * Short description of method rebuildIndexes
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string tableName
     * @return void
     */
    public function rebuildIndexes($tableName)
    {
        // section 10-13-1-85-69bd0289:13adae4f080:-8000:0000000000001BFB begin
        $this->query('REINDEX TABLE "' . $tableName . '"');
        // section 10-13-1-85-69bd0289:13adae4f080:-8000:0000000000001BFB end
    }

    /**
     * Short description of method flush
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string tableName
     * @return void
     */
    public function flush($tableName)
    {
        // section 10-13-1-85-69bd0289:13adae4f080:-8000:0000000000001BFE begin
        // No flushing in postgres.
        return;
        // section 10-13-1-85-69bd0289:13adae4f080:-8000:0000000000001BFE end
    }

    /**
     * Short description of method getColumnNames
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string table
     * @return array
     */
    public function getColumnNames($table)
    {
        $returnValue = array();

        // section 10-13-1-85--7fbbcc7e:13b8a608d82:-8000:0000000000001DCF begin
        $quotedTable = $this->dbConnector->quote($table);
        $result = $this->query('SELECT "column_name" FROM "information_schema"."columns" WHERE "table_name" = ' . $quotedTable);
        
        while ($col = $result->fetchColumn()){
        	$returnValue[] = $col;	
        }
        // section 10-13-1-85--7fbbcc7e:13b8a608d82:-8000:0000000000001DCF end

        return (array) $returnValue;
    }

} /* end of class core_kernel_classes_PgsqlDbWrapper */

?>