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
 * A Service implementation aiming at installing the software.
 * 
 * Please refer to tao/install/api.php for more information about how to call this service.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 
 */
class tao_install_services_InstallService extends tao_install_services_Service{
    
    /**
     * Creates a new instance of the service.
     * @param tao_install_services_Data $data The input data to be handled by the service.
     * @throws InvalidArgumentException If the input data structured is malformed or is missing data.
     */
    public function __construct(tao_install_services_Data $data){
        parent::__construct($data);
    }
    
    /**
     * Executes the main logic of the service.
     * @return tao_install_services_Data The result of the service execution.
     */
    public function execute(){
        $content = json_decode($this->getData()->getContent(), true);
		
		//instantiate the installator
		try{
			set_error_handler(array(get_class($this), 'onError'));
			$installer = new tao_install_Installator(array(
				'root_path' 	=> TAO_INSTALL_PATH,
				'install_path'	=> dirname(__FILE__) . '/../../install'
			));
			
			// For the moment, we force English as default language.
			$content['value']['module_lang'] = 'en-US';
			// fallback until ui is ready
			if (!isset($content['value']['file_path'])) { 
                $content['value']['file_path'] =  TAO_INSTALL_PATH.'data'.DIRECTORY_SEPARATOR;
			}
			$installer->install($content['value']);
            
            $installationLog = $installer->getLog();
            $message = (isset($installationLog['e']) || isset($installationLog['f']) || isset($installationLog['w'])) ?
                'Installation complete (warnings occurred)' : 'Installation successful.';
            
            $report = array(
                'type' => 'InstallReport',
                'value' => array(
                    'status' => 'valid',
                    'message' => $message,
                    'log' => $installationLog
                )
            );
			$this->setResult(new tao_install_services_Data(json_encode($report)));
			
			restore_error_handler();
		} catch(Exception $e) {
			$report = array(
                'type' => 'InstallReport',
				'value' => array(
                    'status' => 'invalid',
				    'message' => $e->getMessage() . ' in ' . $e->getFile() . ' at line ' . $e->getLine(),
                    'log' => $installer->getLog()
                )
            );
			$this->setResult(new tao_install_services_Data(json_encode($report)));
			
			restore_error_handler();
		}
    }
    
    public static function onError($errno, $errstr, $errfile, $errline){
    	common_Logger::w($errfile . ':' . $errline . ' - ' . $errstr, 'INSTALL');
        switch ($errno) {
            case E_ERROR:
                throw new tao_install_utils_Exception($errfile . ':' .$errline . ' - ' . $errstr);
                break;
            default:
                return true;
        }
    }
    
    protected function checkData(){
    	$content = json_decode($this->getData()->getContent(), true);
        if (!isset($content['type']) || empty($content['type']) || $content['type'] != 'Install'){
            throw new InvalidArgumentException("Unexpected type: 'type' must be equal to 'Install'.");
        }
        else if (!isset($content['value']) || empty($content['value'])){
            throw new InvalidArgumentException("Missing data: 'value' must be provided.");
        }
        else if (!isset($content['value']['db_host']) || empty($content['value']['db_host'])){
        	throw new InvalidArgumentException("Missing data: 'db_host' must be provided.");
        }
    	else if (!isset($content['value']['db_user']) || empty($content['value']['db_user'])){
        	throw new InvalidArgumentException("Missing data: 'db_user' must be provided.");
        }
    	else if (!isset($content['value']['db_host']) || empty($content['value']['db_host'])){
        	throw new InvalidArgumentException("Missing data: 'db_host' must be provided.");
        }
        else if (!isset($content['value']['db_pass'])){
        	throw new InvalidArgumentException("Missing data: 'db_pass' must be provided.");
        }
    	else if (!isset($content['value']['db_driver']) || empty($content['value']['db_driver'])){
        	throw new InvalidArgumentException("Missing data: 'db_driver' must be provided.");
        }
    	else if (!isset($content['value']['db_name']) || empty($content['value']['db_name'])){
        	throw new InvalidArgumentException("Missing data: 'db_name' must be provided.");
        }
    	else if (!isset($content['value']['module_namespace']) || empty($content['value']['module_namespace'])){
        	throw new InvalidArgumentException("Missing data: 'module_namespace' must be provided.");
        }
    	else if (!isset($content['value']['module_url']) || empty($content['value']['module_url'])){
        	throw new InvalidArgumentException("Missing data: 'module_url' must be provided.");
        }
    	else if (!isset($content['value']['module_lang']) || empty($content['value']['module_lang'])){
        	throw new InvalidArgumentException("Missing data: 'module_lang' must be provided.");
        }
    	else if (!isset($content['value']['module_mode']) || empty($content['value']['module_mode'])){
        	throw new InvalidArgumentException("Missing data: 'module_mode' must be provided.");
        }
    	else if (!isset($content['value']['import_local']) || ($content['value']['import_local'] !== false && $content['value']['import_local'] !== true)){
        	throw new InvalidArgumentException("Missing data: 'import_local' must be provided.");
        }
    	else if (!isset($content['value']['user_login']) || empty($content['value']['user_login'])){
        	throw new InvalidArgumentException("Missing data: 'user_login' must be provided.");
        }
    	else if (!isset($content['value']['user_pass1']) || empty($content['value']['user_pass1'])){
        	throw new InvalidArgumentException("Missing data: 'user_pass1' must be provided.");
        }
        else if (!isset($content['value']['instance_name']) || empty($content['value']['instance_name'])){
        	throw new InvalidArgumentException("Missing data: 'instance_name' must provided.");
        }
    }
}
?>