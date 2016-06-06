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
 * This file contains the TAO Install REST API.
 * 
 * It provides services aiming at checking the current configuration
 * of the web serser hosting the API. The following services are implemented.
 * 
 * The service to be called is indicated by the top-level 'type' attribute of the
 * sent JSON data structure. It must contain the name of one of the services
 * described below.
 * 
 * If a server-side error occurs, a 500 like HTTP status code will be returned with
 * an approriate message in the response body.
 * 
 * If the service cannot be chosen from the top-level 'type' attribute, a 404 HTTP
 * status code will be returned with an approriate message in the response body.
 * 
 * 
 * CheckPHPConfig (POST)
 * ---------------------
 * Inspects the request body to find JSON encoded data that represents
 * a collection of PHP configuration checks.
 * 
 * It takes as input a collection of JSON data structures used in the following
 * web services of this API: CheckPHPRuntime, CheckPHPINIValue, CheckPHPExtension,
 * CheckFileSystemComponent. It returns a JSON data structure containing a type
 * equals to 'ReportCollection' with a value attribute containing a collection
 * of reports that are usually used in the list of services seen above.
 * 
 * 
 * 
 * Example of call:
 * {"type": "CheckPHPConfig",
 *  "value": [{"type": "CheckPHPExtension", "value": {"name": "gd", "optional": false}},
 *           {"type": "CheckPHPExtension", "value": {"name": "curle", "optional": false}}]}
 * 
 * will return:
 * {"type": "ReportCollection",
 *  "value": {"type": "PHPExtensionReport", "value": {"status": "valid", "message": "PHP Extension 'gd' is loaded.", "optional": false, "name": "gd"}},
 *           {"type": "PHPExtensionReport", "value": {"status": "unknown", "message": "PHP Extension 'curle' could not be found.", "optional": false, "name": "curle"}}}
 * 
 * 
 * CheckPHPExtension (POST)
 * ------------------------
 * Inspects the resquest body to find a JSON encoded data that represents
 * a PHP extension check. With this service, you can know if a PHP Extension is
 * installed or not on the server-side.
 * 
 * Example of call:
 * {"type": "CheckPHPExtension", value": {"name": "gd","optional":false}}
 * 
 * will return:
 * {"type":"PHPExtensionReport",
 *  "value":{"status":"valid","message":"PHP Extension 'gd' is loaded.","optional":false,"name":"gd"}}
 * 
 * + The value->status attribute contains 'valid' if the extension is loaded or 'unknown' if the extension is
 * not loaded.
 * 
 * 
 * CheckPHPDatabaseDriver (POST)
 * ------------------------
 * Inspects the resquest body to find a JSON encoded data that represents
 * a PHP Database Driver check. With this service, you can know if a PHP Database Driver is
 * installed or not on the server-side.
 * 
 * Example of call:
 * {"type": "CheckPHPDatabaseDriver", value": {"name": "mysqli","optional":true}}
 * 
 * will return:
 * {"type":"PHPDatabaseDriverReport", 
 *  "value":{"status":"valid","message":"Database Driver 'mysql' is available.","optional":true,"name":"mysql"}}
 * 
 * + The value->status attribute contains 'valid' if the db driver is loaded or 'unknown' if the dbdriver is
 * not loaded.
 * 
 * 
 * CheckPHPINIValue (POST)
 * -----------------------
 * Inspects the request body to find a JSON encoded data structure that represents
 * a PHP INI value check. By calling this service, you are able to known if a PHP INI value
 * as the execpted value on the server-side.
 * 
 * Example of call:
 * {"type": "CheckPHPINIValue",
 *  "value": {"name": "short_open_tag", "value": "0", "optional":false}}
 * 
 * will return:
 * {"type":"PHPINIValueReport",
 *  "value":{"status":"invalid",
 *           "message":"PHP Configuration Option 'short_open_tag' = '1' has an unexpected value.",
 *           "expectedValue":"0",
 *           "value":"1",
 *           "name":"short_open_tag",
 *           "optional":false}}
 * 
 * + The value->value attribute must be a string.
 * + The value->status attribute contains 'valid' if the expected PHP INI value is found, 'invalid' if it is not found
 * and 'unknown' if no value can be retrieved for requested PHP INI Variable.
 * 
 * 
 * CheckPHPRuntime (POST)
 * ----------------------
 * Takes a JSON encoded data structure as input and use it to check
 * if the PHP runtime exists and is between a min and max PHP standardized version number.
 * 
 * Example of call:
 * {"type": "CheckPHPRuntime",
 *  "value": {"min": "5.3.2", "max": "5.3.9", "optional":false}}
 * 
 * will return:
 * {"type":"PHPRuntimeReport",
 *  "value":{"status":"valid",
 *  "message":"PHP Version (5.3.9) is between 5.3.2 and 5.3.9.",
 *  "min":"5.3.2","value":"5.3.9",
 *  "max":"5.3.9"}}
 * 
 * + The value->status will be 'valid' if the PHP Runtime version is between value->min and value->max, 'invalid' in any
 * other case.
 * + The value->min attribute is not mandatory if the value->max attribute is set. In this case, there is no minimal version.
 * + The value->max attribute is not mandatory if the value->min attribute is set. In this case, theire is no maximal version.
 * 
 * 
 * CheckFileSystemComponent (POST)
 * -------------------------------
 * Will look for a JSON encoded data structure as input in the request body. It will
 * use this information to check if a TAO file system component (file or directory) exists 
 * and has the correct system rights (read|write|execute).
 * 
 * Example of call:
 * {"type": "CheckFileSystemComponent",
 * "value": {"location": "tao/install", "rights": "rw", "optional": true, "name": "tao_install_directory"}}
 * 
 * + The value->rights attribute contains a string were 'r' states that the file/directory must be readable,
 * 'w' states that the file/directory must writable and 'x' states that the file/directory must be
 * executable.
 * + The value->location attribute is a relative path from the TAO root directory on your web server to
 * the file you want to test existence and rights.
 * + The value->name attribute is just there for client convenience to distinguish about which file/directory
 * is the result.
 * 
 * will return:
 * {"type":"FileSystemComponentReport",
 *  "value":["status": "valid",
 *           "message": "File system component 'tao_install_directory' is compliant with expected rights (rw).'",
 *           "name": "tao_install_directory",
 *           "optional": true,
 *           "isReadable": true,
 *           "isWritable": true,
 *           "isExecutable": false]}
 * 
 * + The value->status attribute will be 'valid' if the file/directory exists and the expected rights are correct.
 * + The value->status attribute will be 'invalid' if the file/directory exists but the expected rights are incorrect.
 * + The value->status attribute will be 'unknown' if the file/directory does not exist or is not accessible with the rights
 * of the hosts has.
 * 
 * 
 * CheckCustom (POST)
 * ----------------------
 * Takes a JSON encoded data structure as input and use it to run a custom
 * check for a particular extension.
 * 
 * Example of call:
 * {"type": "CheckCustom","value": {"name": "ModRewrite", "extension" : "tao", "optional": true}}
 * 
 * will return:
 * {"type": "CheckCustomReport",
 *  "value":{"status": "valid",
 *           "message":"Apache mod_rewrite is enabled.",
 *           "name":"ModRewrite",
 *           "extension":"tao",
 *           "optional":true}}
 * 
 * + The value->status value depends on the implementation of the requested check.
 * 
 * 
 * CheckDatabaseConnection (POST)
 * ----------------------------------
 * Takes a JSON data structure as input and use it to run a Database Connection Check
 * against a particular host, driver, user, password.
 * 
 * Example of call:
 * {"type": "CheckDatabaseConnection",
 *  "value": {"driver": "mysql",
 *            "user": "root",
 *            "password": "",
 *            "host": "localhost",
 *            "optional": false,
 *			  "overwrite": true,
 *			  "database": "db1",
 *            "name": "db_connection"}}
 * 
 * will return:
 * {"type": "DatabaseConnectionReport",
 *  "value": {"status": "valid",
 *            "message": "Database connection successfully established with driver 'mysql'.",
 *            "optional": false,
 *            "name": "db_connection"}}
 */

// use unicode.
header('Content-Type:text/html; charset=UTF-8');

// clear sessions.
session_start();
session_destroy(); 
 
// initialize what we need.
require_once('init.php');

try{
    
    // Deal with the 'CheckProtocol' service first.
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['type'] == 'Sync'){
           
        $data = new tao_install_services_Data(array('type' => 'Sync'));
        $service = new tao_install_services_SyncService($data);
        
        // Execute service.
        $service->execute();
        $result = $service->getResult();
        $contentType = $result->getMimeType();
        $charset = $result->getEncoding();
        header("Content-Type:${contentType}; charset=${charset}", 200);
        echo $result->getContent();
        die();
    }
    
    $service = null;
    $data = null;
    if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    	$input = $_GET;	
    }
    else{
    	$rawInput = file_get_contents('php://input');
    	$input = @json_decode($rawInput, true);
    }
    
    if ($input == null){
        throw new tao_install_api_MalformedRequestBodyException("Unable to parse request body as valid JSON.");
    }        
    else if (!isset($input['type']) || empty($input['type'])){
        throw new tao_install_api_InvalidAPICallException("No 'type' attribute found in request body.");
    }
    else{
        
    	if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    		switch($input['type']){
    			case 'CheckPHPConfig':
					$data = new tao_install_services_Data(json_encode($input));
	                $class = new ReflectionClass('tao_install_services_' . $input['type'] . 'Service');
	                $service = $class->newInstance($data);
    			break;
    			
    			default:
	                // Unknown service.
	                throw new tao_install_services_UnknownServiceException($input['type']);
	            break;
    		}
    	}
    	else{
	    	switch ($input['type']){
	            case 'CheckPHPConfig':
	            case 'CheckPHPRuntime':
	            case 'CheckPHPINIValue':
	            case 'CheckPHPExtension':
	            case 'CheckPHPDatabaseDriver': 
	            case 'CheckFileSystemComponent':
	            case 'CheckDatabaseConnection':
                case 'CheckTAOForgeConnection':
	            case 'CheckCustom':
	            case 'Install':
	                $data = new tao_install_services_Data($rawInput);
	                $class = new ReflectionClass('tao_install_services_' . $input['type'] . 'Service');
	                $service = $class->newInstance($data);
	            break;
	            
	            default:
	                // Unknown service.
	                throw new tao_install_services_UnknownServiceException($input['type']);
	            break;
	        }	
    	}
    
        // Execute service.
        $service->execute();
        $result = $service->getResult();
        $contentType = $result->getMimeType();
        $charset = $result->getEncoding();
        header("Content-Type:${contentType}; charset=${charset}", 200);
        echo $result->getContent();    
    }
}
catch (tao_install_services_UnknownServiceException $e){
    $serviceName = $e->getServiceName(); 
    header('HTTP/1.0 404 Not Found');
    header('Content-Type:text; charset=UTF-8');
    echo "The requested service '${serviceName}' does not exist.";
}
catch (tao_install_api_MalformedRequestBodyException $e){
    header("HTTP/1.0 400 Bad Request");
    header("Content-Type:text; charset=UTF-8");
    echo "Request body could not be parsed as valid JSON.\n";
    echo "Is your JSON data correctly formatted? You can check it on http://www.jslint.com, the JavaScript Quality Tool.\n";
    echo "Make also sure your request body is UTF-8 encoded.";
}
catch (tao_install_api_InvalidAPICallException $e){
    header("HTTP/1.0 400 Bad Request");
    header("Content-Type:text; charset=UTF-8");
    echo $e->getMessage();
}
catch (Exception $e){
    header('HTTP/1.0 500 Internal Server Error');
    header('Content-Type:text; charset=UTF-8');
    echo "Fatal error: ". $e->getMessage();
}
$_SESSION = array();
@session_destroy();
?>