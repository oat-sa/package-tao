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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 */
require_once dirname(__FILE__).'/../includes/raw_start.php';

/**
 * 
 * @author Bertrand Chevrier <bertrand.chevrier@tudor.lu>
 *
 */
class SaSManager{
	
	/**
	 * @var boolean
	 */
	protected $outputModeWeb = true;
	
	/**
	 * @var array
	 */
	protected $services = array();
	
	/**
	 * @var array
	 */
	protected $processVars = array();
	
	/**
	 * @var array
	 */
	protected $formalParams = array();
	
	/**
	 * Constructor
	 */
	public function __construct(){
		
		if(PHP_SAPI == 'cli'){
			$this->outputModeWeb = false;
		}
	}
	
	/**
	 * get the sas file definition in every extensions
	 * @return array
	 */
	protected function getSasFiles(){
		
		$sasFiles = array();
		$extensionsManager = common_ext_ExtensionsManager::singleton();
		foreach($extensionsManager->getInstalledExtensions() as $extension){
			$filePath = $extension->getDir() . '/models/services/sas.xml';
			if(file_exists($filePath)){
				$sasFiles[$extension->getId()] = $filePath;
			}
		}
		
		return $sasFiles;
	}
	
	/**
	 * Parse the sas xml file and populate the services and processVar attributes
	 * @param string $file path
	 */
	protected function parseSasFile($extensionName, $file){
		
		$xml = simplexml_load_file($file);
		$services = array();
		if($xml instanceof SimpleXMLElement){
			foreach($xml->service as $service){
				
				$loc = $service->location;
				$url = (string)$loc["url"];
				if(count($loc->param) > 0 && !preg_match("/(\?|\&)$/", $url)){
					$url .= '?';
				}
				
				$formalParamsIn = array();
				foreach($loc->param as $param){
					if(isset($param['key'])){
						
						$url .= ((string)$param['key']) . '=';
						if(isset($param['value'])) {
							$url .= (string)$param['value'];		

							//set the processVars
							if(!in_array((string)$param['value'], $this->processVars)){
								$this->processVars[] = (string)$param['value'];
							}	

							$formalParamsIn[(string)$param['key']] = (string)$param['value']; 
							
							//set the formatParams
							$key = (string)$param['key'].(string)$param['value'];
							if(!array_key_exists($key, $this->formalParams)){
								$this->formalParams[$key] = array(
									'name'			=> (string)$param['key'],
									'processVar'	=> ( preg_match("/^\^/", (string)$param['value'])) ? (string)$param['value'] : false,
									'constant'		=> (!preg_match("/^\^/", (string)$param['value'])) ? (string)$param['value'] : false 
								);
							}
						}
						else{
							$url .= "^".((string)$param['key']);
						}
						$url .= "&";
						
					}
				}
				if(isset($service->return)){
					foreach($service->return->param as $param){
						if(isset($param['key'])) {
							$code = "^".(string)$param['key'];
							if(!in_array($code, $this->processVars)){
								$this->processVars[] = $code;
							}		
						}
					}
				}
				$services[] = array(
					'name' 			=> (string)$service->name,
					'description' 	=> (string)$service->description,
					'url'			=>	$url,
					'params'		=> $formalParamsIn
				);
			}
			$this->services[$extensionName] = $services;
		}
	}
	
	/**
	 * Utility method to (unCamelize -> un camelize) a string
	 * @param string $input
	 * @return string
	 */
	protected static function unCamelize($input){
		$matches = array();
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
		$output = $matches[0];
		foreach ($output as &$match) {
			$match = ($match == strtoupper($match)) ? strtolower($match) : ucfirst($match);
		}
		return implode(' ', $output);
	}
	
	
	/**
	 * @param string $message
	 */
	protected function log($message = ''){
		if($this->outputModeWeb){
			echo "{$message}</br>";
		}
		else{
			echo "{$message}\n";
		}
	}
}

?>