<?php
/**
 * 
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
 * 
 * Create procedure for uri provider
 *
 * @author Lionel Lecaque, lionel@taotesting.com
 */
class tao_install_utils_ProceduresCreator{
    
    /**
     * 
     * @var unknown
     */
    private $connection;
    /**
     * 
     * @var tao_install_utils_SimpleSQLParser
     */
    private $sqlParser;
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $driver
     * @param unknown $connection
     * @throws tao_install_utils_SQLParsingException
     */
    public function __construct($driver, $connection){
        
        $this->connection = $connection;
        switch ($driver) {
        	case 'pdo_mysql':{
        	    $this->setSQLParser(new tao_install_utils_MysqlProceduresParser());
        	    break;
        	}
        	case 'pdo_pgsql' : {
        	    $this->setSQLParser(new tao_install_utils_PostgresProceduresParser());
        	    break;
        	}
        	case 'pdo_oci' : {
        	    $this->setSQLParser(new tao_install_utils_CustomProceduresParser());
        	    break;
        	}
        	case 'pdo_sqlsrv' : {
        		$this->setSQLParser(new tao_install_utils_CustomProceduresParser());
        		break;
        	}
        	default: {
        	    throw new tao_install_utils_SQLParsingException('Could not find Parser for driver ' . $driver);
        	}
        }        
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return tao_install_utils_SimpleSQLParser
     */
    private function getSQLParser(){
        return $this->sqlParser;
    }
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param tao_install_utils_SimpleSQLParser $sqlParser
     */
    private function setSQLParser($sqlParser){
        $this->sqlParser = $sqlParser;
    }
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $file
     */
    public function load($file){
        $parser = $this->getSQLParser();
        $parser->setFile($file);
        $parser->parse();

        foreach ($parser->getStatements() as $statement){
            $this->connection->executeUpdate($statement);
           
        }
    }

}