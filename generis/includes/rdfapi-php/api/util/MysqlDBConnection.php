<?php
class MysqlDBConnection extends DBConnection{
	
	protected function getExtraConfiguration(){
		return array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
	}
	
	protected function afterConnect(){
		$this->exec("SET SESSION SQL_MODE='ANSI_QUOTES'");
	}
	
	protected function getDSN(){
		$host = $this->host;
		$dbName = $this->dbName;
		return "mysql:host=${host};dbname=${dbName};charset=utf8";
	}
}
?>