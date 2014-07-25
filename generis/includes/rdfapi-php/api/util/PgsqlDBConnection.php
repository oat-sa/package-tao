<?php
class PgsqlDBConnection extends DBConnection{
	
	protected function getExtraConfiguration(){
		return array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
	}
	
	protected function afterConnect(){
		$this->exec("SET NAMES 'UTF8'");
	}
	
	protected function getDSN(){
		$host = $this->host;
		$dbName = $this->dbName;
		return "pgsql:host=${host};dbname=${dbName}";
	}
}
?>