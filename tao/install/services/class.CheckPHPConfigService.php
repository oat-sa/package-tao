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
 * A Service implementation aiming at checking a series of configurable things
 * such as PHP Extensions, PHP INI Values, PHP Runtime, File system,...
 * 
 * Please refer to tao/install/api.php for more information about how to call this service.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 
 */
class tao_install_services_CheckPHPConfigService extends tao_install_services_Service{
    
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
		// contains an array of 'component', associated input 'data' 
		// and service 'class'.
    	$componentToData = array();
    	
        $content = json_decode($this->getData()->getContent(), true);
        if (self::getRequestMethod() == 'get'){
            // We extract the checks to perform from the manifests
        	// depending on the distribution.
            $content['value'] = tao_install_utils_ChecksHelper::getRawChecks($content['extensions']);
        }
        
        // Deal with checks to be done.
        $collection = new common_configuration_ComponentCollection();
        foreach ($content['value'] as $config){
        	$class = new ReflectionClass('tao_install_services_' . $config['type'] . 'Service');
        	$buildMethod = $class->getMethod('buildComponent');
        	$args = new tao_install_services_Data(json_encode($config));
        	$component = $buildMethod->invoke(null, $args);
        	$collection->addComponent($component);
        	
        	if (!empty($config['value']['silent']) && is_bool($config['value']['silent'])){
        		$collection->silent($component);
        	}
        	
        	$componentToData[] = array('component' => $component, 
        							   'id' => $config['value']['id'],
        							   'data' => $args,
        							   'class' => $class);
        }
        
        // Deal with the dependencies.
        foreach ($content['value'] as $config){
        	if (!empty($config['value']['dependsOn']) && is_array($config['value']['dependsOn'])){
        		foreach ($config['value']['dependsOn'] as $d){
        			// Find the component it depends on and tell the ComponentCollection.
        			$dependent = self::getComponentById($componentToData, $config['value']['id']);
        			$dependency = self::getComponentById($componentToData, $d);
        			if (!empty($dependent) && !empty($dependency)){
        				$collection->addDependency($dependent, $dependency);
        			}
        		}
        	}
        }
        
        
        // Deal with results to be sent to the client.
        $resultValue = array();
        $reports = $collection->check();
        foreach($reports as $r){
        	$component = $r->getComponent();
        	
        	
        	// For the retrieved component, what was the associated data and class ?
        	$associatedData = null;
        	$class = null;
        	foreach ($componentToData as $ctd)
        	{
        		if ($component == $ctd['component']){
        			$associatedData = $ctd['data'];
        			$class = $ctd['class'];
        		}
        	}
        	
        	$buildMethod = $class->getMethod('buildResult');
        	$serviceResult = $buildMethod->invoke(null, $associatedData, $r, $component);
        	$resultValue[] = $serviceResult->getContent();
        }
        
        // Sort by 'optional'.
        usort($resultValue, array('tao_install_services_CheckPHPConfigService' , 'sortReports'));
        
        
        $resultData = json_encode(array('type' => 'ReportCollection',
            'value' => '{RETURN_VALUE}'));
        
        $resultData = str_replace('"{RETURN_VALUE}"', '[' . implode(',', $resultValue) . ']', $resultData);
        $this->setResult(new tao_install_services_Data($resultData));
    }
    
    /**
     * Report sorting function.
     * @param string $a JSON encoded report.
     * @param string $b JSON encoded report.
     * @return boolean Comparison result.
     */
    private static function sortReports ($a, $b){
    	$a = json_decode($a, true);
    	$b = json_decode($b, true);
    	
    	if ($a['value']['optional'] == $b['value']['optional']){
    		return 0;
    	}
    	else{
    		return ($a['value']['optional'] < $b['value']['optional']) ? -1 : 1;
    	}
    }
    
    protected function checkData(){
    	$content = json_decode($this->getData()->getContent(), true);
        if (!isset($content['type']) || empty($content['type'])){
            throw new InvalidArgumentException("Missing data: 'type' must be provided.");
        }
        else if ($content['type'] !== 'CheckPHPConfig'){
            throw new InvalidArgumentException("Unexpected type: 'type' must be equal to 'CheckPHPConfig'.");
        }
        
        
        if (self::getRequestMethod() !== 'get'){
	        if (!isset($content['value']) || empty($content['value']) || count($content['value']) == 0){
	            throw new InvalidArgumentException("Missing data: 'value' must be provided as a not empty array.");
	        }
	        else{
	            $acceptedTypes = array('CheckPHPExtension', 'CheckPHPINIValue', 'CheckPHPRuntime', 'CheckPHPDatabaseDriver', 'CheckFileSystemComponent', 'CheckCustom');
	            
	            foreach ($content['value'] as $config){
	                if (!isset($config['type']) || empty($config['type']) || !in_array($config['type'], $acceptedTypes)){
	                    throw new InvalidArgumentException("Missing data: configuration 'type' must provided.");
	                }
	                else{
	                	$className = 'tao_install_services_' . $config['type'] . 'Service';
	                	$data = new tao_install_services_Data(json_encode($config));
	                	call_user_func($className . '::checkData', $data);
	                }
	            }
	        }
        }
    }
    
    /**
     * Returns a component stored in an array of array. The searched key is 'id'. If matched,
     * the component instance is returned. Otherwise null.
     * 
     * @param array $componentToData
     * @param string $id
     * @return common_configuration_Component
     */
    public static function getComponentById(array $componentToData, $id){
    	foreach ($componentToData as $ctd){
    		if ($ctd['id'] == $id){
    			return $ctd['component'];
    		}
    	}
    	
    	return null;
    }
}
?>