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
 * SQL Server Database Creator
 *
 * @author lionel.lecaque@tudor.lu
 * @package tao
 * @subpackage install_utils
 *
 */
class tao_install_utils_SqlsrvDbCreator extends tao_install_utils_DbCreator
{

    protected function getDsn($host)
    {
        return $this->driver . ':server=' . $host . $this->getExtraDSN();
    }



    public function chooseSQLParsers()
    {
        $this->setSQLParser(new tao_install_utils_SimpleSQLParser());
        //TODO $this->setProcSQLParser();
    }

    /**
     * Check if the database exists already
     * @param string $name
     */
    public function dbExists($dbName)
    {
        $dsn = $this->getDiscoveryDSN() . ';database=master';
        $pdo = new PDO($dsn,$this->user,$this->pass);
        $result = $pdo->query('SELECT name FROM "sysdatabases"');
        $databases = array();
        while($db = $result->fetchColumn(0)){
            $databases[] = $db;
        }
        if (in_array($dbName, $databases)){
            return true;
        }
        return false;
    }

    /**
     * Clean database by droping all tables
     * @param string $name
     */
    public function cleanDb()
    {
        $tables = array();
        $result = $this->pdo->query('SELECT TABLE_NAME FROM information_schema.tables');
        while ($t = $result->fetchColumn(0)){
            $tables[] = $t;
        }

        foreach ($tables as  $t){
            $this->pdo->exec("DROP TABLE \"${t}\"");
        }
    }

    public function createDatabase($name)
    {
        $this->pdo->exec('CREATE DATABASE "' . $name . '"');
        $this->setDatabase($name);
    }

    protected function afterConnect()
    {
        $this->pdo->exec("SET NAMES 'UTF8'");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }

    protected function getExtraConfiguration()
    {
        return array();
    }

    protected function getExtraDSN()
    {
        return ';';
    }

    protected function getDiscoveryDSN()
    {
        $driver = str_replace('pdo_', '', $this->driver);
        $dsn  = $driver . ':server=' . $this->host;
        return $dsn;
    }

    protected function getDatabaseDSN()
    {
        $driver = str_replace('pdo_', '', $this->driver);
        $dbName = $this->dbName;
        $dsn  = $driver . ':server=' . $this->host . ';database=' . $dbName;
        return $dsn;
    }

}
