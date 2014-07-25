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
 * Dedicated database wrapper used for database creation in
 * a MySQL context.
 * 
 * @see PDO
 * @author Bertrand CHEVRIER <bertrand.chevrier@tudor.lu>
 * @author Jerome BOGAERTS <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage install_utils
 *
 */
class tao_install_utils_MysqlDbCreator extends tao_install_utils_DbCreator{
	
	public function chooseSQLParsers(){
		$this->setSQLParser(new tao_install_utils_SimpleSQLParser());
		$this->setProcSQLParser(new tao_install_utils_MysqlProceduresParser());
	}
	
	/**
	 * Check if the database exists already
	 * @param string $name
	 */
	public function dbExists($dbName)
	{
		$result = $this->pdo->query('SHOW DATABASES');
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
		$result = $this->pdo->query('SHOW TABLES');
		
		while ($t = $result->fetchColumn(0)){
			$tables[] = $t;
		}

		foreach ($tables as  $t){
			$this->pdo->exec("DROP TABLE \"${t}\"");
		}
	}
	
	public function createDatabase($name){
		$this->pdo->exec('CREATE DATABASE "' . $name . '"');
		$this->setDatabase($name);
	}
	
	protected function afterConnect(){
		$this->pdo->exec('SET SESSION SQL_MODE="ANSI_QUOTES"');
	}
	
	protected function getExtraConfiguration(){
		return array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);	
	}
	
	protected function getDiscoveryDSN(){
		$driver = str_replace('pdo_', '', $this->driver);
		$dsn  = $driver . ':host=' . $this->host . ';charset=utf8';
		return $dsn;
	}
	
	protected function getDatabaseDSN(){
		$driver = str_replace('pdo_', '', $this->driver);
		$dbName = $this->dbName;
		$dsn  = $driver . ':dbname=' . $dbName . ';host=' . $this->host . ';charset=utf8';
		return $dsn;
	}
}
?>