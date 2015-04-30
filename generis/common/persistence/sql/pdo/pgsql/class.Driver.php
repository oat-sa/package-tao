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
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package generis
 */
class common_persistence_sql_pdo_pgsql_Driver
    extends common_persistence_sql_pdo_Driver
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---
    private $platform = null;
    private $schemamanger = null;
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
        
    	$returnValue = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        

        return (array) $returnValue;
    }

    /* (non-PHPdoc)
     * @see common_persistence_sql_pdo_Driver::getSchemaManager()
    */
    public function getSchemaManager(){
        if($this->schemamanger == null){
            $this->schemamanger = new common_persistence_sql_pdo_pgsql_SchemaManager($this);
        }
        return $this->schemamanger;
    }

    /* (non-PHPdoc)
     * @see common_persistence_sql_pdo_Driver::getSchemaManager()
    */
    public function getPlatform(){
        if($this->platform == null){
            $this->platform = new common_persistence_sql_Platform(new \Doctrine\DBAL\Platforms\PostgreSqlPlatform());
        }
        return $this->platform;
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
        
        $this->exec("SET NAMES 'UTF8'");
        
    }


    public function lastInsertId($name = null)
    {
        if ($name === 'models') {
            $name = 'models_modelid_seq';
        } else {
            if ($name === 'variables_storage') {
                $name = 'variables_storage_variable_id_seq';
            } else {
                $name .= '_id_seq';
            }
        }

        return parent::lastInsertId($name);
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
        
        $params = $this->getParams();
        $driver = str_replace('pdo_', '', $params['driver']);
        $dbName = $params['dbname'];
        $dbUrl = $params['host'];
        
        return $driver . ':dbname=' . $dbName . ';host=' . $dbUrl;
        
    }

}