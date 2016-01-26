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
 * A Service implementation aiming at calling a custom check.
 * 
 * Please refer to tao/install/api.php for more information about how to call this service
 *
 *  @access public
  * @author Jerome Bogaerts, <jerome@taotesting.com>
  * @package tao
  
 */
class tao_install_services_CheckCustomService 
	extends tao_install_services_Service
	implements tao_install_services_CheckService
	{
    
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
        $name = $content['value']['name'];
        $extension = $content['value']['extension'];
        $check = self::buildComponent($this->getData());
        
        if ($check !== null){
            $report = $check->check();                   
            $this->setResult(self::buildResult($this->getData(), $report, $check));    
        }
        else{
            throw new tao_install_services_UnknownCustomCheckException($name, $extension);
        }
    }
    
    protected function checkData(){
    	$content = json_decode($this->getData()->getContent(), true);
        if (!isset($content['type']) || empty($content['type'])){
            throw new InvalidArgumentException("Missing data: 'type' must be provided.");
        }
        else if ($content['type'] !== 'CheckCustom'){
            throw new InvalidArgumentException("Unexpected type: 'type' must be equal to 'CheckCustom'.");
        }
        else if (!isset($content['value']) || empty($content['value']) || !is_array($content['value']) || count($content['value']) == 0){
            throw new InvalidArgumentException("Missing data: 'value' must be provided as a not empty array.");
        }
        else if (!isset($content['value']['id']) || empty($content['value']['id'])){
        	throw new InvalidArgumentException("Missing data: 'id' must be provided.");	
        }
        else if (!isset($content['value']['name']) || empty($content['value']['name'])){
            throw new InvalidArgumentException("Missing data: 'name' must be provided.");
        }
        else if (!isset($content['value']['extension']) || empty($content['value']['extension'])){
            throw new InvalidArgumentException("Missing data: 'extension' must be provided");
        }
        else if (isset($content['value']['parameters'])){
            // If parameters are given to the custom check...
            if (empty($content['value']['parameters']) || !is_array($content['value']['parameters']) || count($content['value']['parameters']) == 0){
                throw new InvalidArgumentException("Missing data: 'parameters' must be provided as a not empty array.");
            }
        }
    }
    
    public static function buildComponent(tao_install_services_Data $data){
    	$content = json_decode($data->getContent(), true);
        $name = $content['value']['name'];
        
    	if (isset($content['value']['optional'])){
        	$optional = $content['value']['optional'];
        }
        else{
        	$optional = false;
        }
        $extension = $content['value']['extension'];
        
        try{
            return common_configuration_ComponentFactory::buildCustom($name, $extension, $optional);
        }
        catch (common_configuration_ComponentFactoryException $e){
        	return null;
        }
    }
    
    public static function buildResult(tao_install_services_Data $data,
									   common_configuration_Report $report,
									   common_configuration_Component $component){

		$content = json_decode($data->getContent(), true);
        
        $id = $content['value']['id'];
        $extension = $content['value']['extension'];

        $data = array('type' => 'CheckCustomReport',
                      'value' => array('status' => $report->getStatusAsString(),
                                       'message' => $report->getMessage(),
                                       'name' => $component->getName(),
                                       'extension' => $extension,
                                       'optional' => $component->isOptional(),
            						   'id' => $id));
                                           
        return new tao_install_services_Data(json_encode($data));
	}
}
?>