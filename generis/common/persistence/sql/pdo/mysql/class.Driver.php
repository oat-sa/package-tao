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
 * @package generis
 */


class common_persistence_sql_pdo_mysql_Driver
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
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return array
     */
    protected function getExtraConfiguration()
    {
        
    	$returnValue = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        

        return (array) $returnValue;
    }



    /**
     * Short description of method afterConnect
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return void
     */
    protected function afterConnect()
    {
        
        $this->exec('SET SESSION SQL_MODE=\'ANSI_QUOTES\';');
        
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

        $params = $this->getParams();
        $driver = str_replace('pdo_', '', $params['driver']);
        $dbName = $params['dbname'];
        $dbUrl = $params['host'];
        $dbPort = isset($params['port']) ? ';port='.$params['port'] : '';
        
        $returnValue = $driver . ':dbname=' . $dbName . ';host=' . $dbUrl . $dbPort .';charset=utf8';
        

        return (string) $returnValue;
    }


    /* (non-PHPdoc)
     * @see common_persistence_sql_pdo_Driver::getSchemaManager()
    */
    public function getPlatform(){
        if($this->platform == null){
            $this->platform = new common_persistence_sql_Platform(new \Doctrine\DBAL\Platforms\MySqlPlatform());
        }
        return $this->platform;
    }
    
    /* (non-PHPdoc)
     * @see common_persistence_sql_pdo_Driver::getSchemaManager()
     */
    public function getSchemaManager(){
        if($this->schemamanger == null){
            $this->schemamanger = new common_persistence_sql_pdo_mysql_SchemaManager($this);
        }
        return $this->schemamanger;
    }



}