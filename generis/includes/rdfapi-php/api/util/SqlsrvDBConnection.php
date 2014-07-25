<?php
class SqlSrvDBConnection extends DBConnection{

    protected function getExtraConfiguration(){
        return array();
    }

    protected function afterConnect(){
        $this->exec("SET NAMES 'UTF8'");
        $this->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }

    protected function getExtraDSN(){
        return ';';
    }

    protected function getDSN(){
        $host = $this->host;
        $dbName = $this->dbName;
        return 'sqlsrv:Server='. $host . ';Database='.$dbName;
    }
}
?>