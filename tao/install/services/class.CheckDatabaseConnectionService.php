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

/**
 * A Service implementation aiming at checking that it is possible to connect to
 * a database with particular driver, user and password.
 * 
 * Please refer to tao/install/api.php for more information about how to call this service.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 
 */
class tao_install_services_CheckDatabaseConnectionService 
	extends tao_install_services_Service
	implements tao_install_services_CheckService
	{
    
    /**
     * Creates a new instance of the service.
     * @param tao_install_services_Data $data The input data to be handled by the service.
     * @throws InvalidArgumentException If the input data structured is malformed or is missing data.
     */
    public function __construct($data){
        parent::__construct($data);
    }
    
    /**
     * Executes the main logic of the service.
     * @return tao_install_services_Data The result of the service execution.
     */
    public function execute(){
        $ext = self::buildComponent($this->getData());
        $report = $ext->check();            
        $this->setResult(self::buildResult($this->getData(), $report, $ext));
    }

    /**
     * Custom error handler to prevent noisy output at bad connection time.
     * @return boolean
     */
    public static function onError($errno, $errstr, $errfile, $errline){
        // Do not call PHP internal error handler !
        return true;
    }
    
    protected function checkData(){
    	$content = json_decode($this->getData()->getContent(), true);
        if (!isset($content['type']) || empty($content['type']) || $content['type'] != 'CheckDatabaseConnection'){
            throw new InvalidArgumentException("Unexpected type: 'type' must be equal to 'CheckDatabaseConnection'.");
        }
        else if (!isset($content['value']) || empty($content['value'])){
            throw new InvalidArgumentException("Missing data: 'value' must be provided.");
        }
        else if (!isset($content['value']['driver']) || empty($content['value']['driver'])){
            throw new InvalidArgumentException("Missing data: 'driver' must be provided.");
        }
        else if (!isset($content['value']['user']) || empty($content['value']['user'])){
            throw new InvalidArgumentException("Missing data: 'user' must be provided.");
        }
        else if (!isset($content['value']['password'])){
            throw new InvalidArgumentException("Missing data: 'password' must be provided.");
        }
        else if (!isset($content['value']['host']) || empty($content['value']['host'])){
            throw new InvalidArgumentException("Missing data: 'host' must be provided.");
        }
		else if (!isset($content['value']['overwrite']) || !is_bool($content['value']['overwrite'])){
			throw new InvalidArgumentException("Missing data: 'overwrite' must be provided.");
		}
		else if (!isset($content['value']['database']) || empty($content['value']['database'])){
			throw new InvalidArgumentException("Missing data: 'database' must be provided.");
		}
    }
    
    public static function buildComponent(tao_install_services_Data $data){
    	$content = json_decode($data->getContent(), true);
        $driver = $content['value']['driver'];
    	if (isset($content['value']['optional'])){
        	$optional = $content['value']['optional'];
        }
        else{
        	$optional = false;
        }
        
        // Try such a driver. Because the provided driver name should
        // comply with a PHP Extension name (e.g. mysql, pgsql), we test its
        // existence.
        return common_configuration_ComponentFactory::buildPHPDatabaseDriver($driver, $optional);
    }
    
    public static function buildResult(tao_install_services_Data $data,
									   common_configuration_Report $report,
									   common_configuration_Component $component){

		$content = json_decode($data->getContent(), true);
        $driver = $content['value']['driver'];
        $user = $content['value']['user'];
        $password = $content['value']['password'];
        $host = $content['value']['host'];
		$overwrite = $content['value']['overwrite'];
		$database = $content['value']['database'];
		$message = '';
        
        if ($report->getStatus() == common_configuration_Report::VALID){
            // Great the driver is there, we can try a connection.
            try{
                set_error_handler(array('tao_install_services_CheckDatabaseConnectionService', 'onError'));
                //$dbCreatorClassName = tao_install_utils_DbCreator::getClassNameForDriver($driver);
                //$dbCreator = new $dbCreatorClassName($host, $user, $password, $driver);
                
                $dbConfiguration = array(
                		'driver' => $driver,
                		'host' => $host,
                		'dbname' => $database,
                		'user' => $user,
                		'password' => $password
                );
                
                $dbCreator = new tao_install_utils_DbalDbCreator($dbConfiguration);
                
				// If we are here, we are connected.
				if ($overwrite == false && $dbCreator->dbExists($database)){
					$message = "A database with name '${database}' already exists.";
					$status = 'invalid-overwrite';
				}
				else{
					$message = "Database connection successfully established with '${host}' using driver '${driver}'.";
					$status = 'valid';
				}
				
				restore_error_handler();
            }
            catch(Exception $e){
                
                if ($e instanceof PDOException && $driver === 'pdo_mysql' && $e->getCode() === 1049) {
                    // mysql -> 1049 = unknown DB -> let the last step of the installer create it if possible.
                    $message = "Database unknown, database creation process delayed.";
                    $status = 'valid';
                }
                else {
                    $message = "Unable to connect to database '${database}' at '${host}' using driver '${driver}': " . $e->getMessage();
                    $status = 'invalid-noconnection';
                }
                
                
                
                restore_error_handler();
            }
        }
        else{
            // No driver found.
            $status = 'invalid-nodriver';
            $message = "Database driver '${driver}' is not available.";
        } 
        
        
        $value = array('status' => $status,
                       'message' => $message,
                       'optional' => $component->isOptional(),
                       'name' => $component->getName());
                       
                       
        $data = array('type' => 'DatabaseConnectionReport',
                      'value' => $value);
        
        return new tao_install_services_Data(json_encode($data));
	}
}
?>