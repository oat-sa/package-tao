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
 * A Service implementation aiming at checking the existence and the validity of rights
 * of file system components, in other words files and directorties.
 * 
 * Please refer to tao/install/api.php for more information about how to call this service.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 
 */
class tao_install_services_CheckFileSystemComponentService 
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
    	
        $fsc = self::buildComponent($this->getData());
        $report = $fsc->check();                       
        $this->setResult(self::buildResult($this->getData(), $report, $fsc));
    }
    
    protected function checkData(){
    	$content = json_decode($this->getData()->getContent(), true);
        if (!isset($content['type']) || empty($content['type'])){
            throw new InvalidArgumentException("Missing data: 'type' must be provided.");
        }
        else if ($content['type'] !== 'CheckFileSystemComponent'){
            throw new InvalidArgumentException("Unexpected type: 'type' must be equal to 'CheckFileSystemComponent'.");
        }
        else if (!isset($content['value']) || empty($content['value']) || count($content['value']) == 0){
            throw new InvalidArgumentException("Missing data: 'value' must be provided as a not empty array.");
        }
        else if (!isset($content['value']['id']) || empty($content['value']['id'])){
        	throw new InvalidArgumentException("Missing data: 'id' must be provided.");
        }
        else if (!isset($content['value']['rights']) || empty($content['value']['rights'])){
            throw new InvalidArgumentException("Missing data: 'rights' must be provided.");
        }
        else if (!isset($content['value']['location']) || empty($content['value']['location'])){
            throw new InvalidArgumentException("Missing data: 'location' must be provided.");
        }
    }
    
    public static function buildComponent(tao_install_services_Data $data){
    	$content = json_decode($data->getContent(), true);
        $location = $content['value']['location'];
        $rights = $content['value']['rights'];
        $recursive = isset($content['value']['recursive']) && (bool) $content['value']['recursive'];
    	if (isset($content['value']['optional'])){
        	$optional = $content['value']['optional'];
        }
        else{
        	$optional = false;
        }
        
        return common_configuration_ComponentFactory::buildFileSystemComponent($location, $rights, $optional, $recursive);
    }
    
    public static function buildResult(tao_install_services_Data $data,
									   common_configuration_Report $report,
									   common_configuration_Component $component){

		$content = json_decode($data->getContent(), true);
        $rights = $content['value']['rights'];
        $id = $content['value']['id'];
        $root = dirname(__FILE__) . '/../../../';
        
        $data = array('type' => 'FileSystemComponentReport',
                      'value' => array('status' => $report->getStatusAsString(),
                                       'message' => $report->getMessage(),
        							   'id' => $id,
                                       'optional' => $component->isOptional(),
                                       'isReadable' => $component->isReadable(),
                                       'isWritable' => $component->isWritable(),
                                       'isExecutable' => $component->isExecutable(),
                                       'recursive' => $component->getRecursive(),
        							   'expectedRights' => $rights,
        							   'isFile' => is_file($root . $component->getLocation()),
        							   'location' => $component->getLocation()));	
        
        return new tao_install_services_Data(json_encode($data));						   	
	}
}
?>