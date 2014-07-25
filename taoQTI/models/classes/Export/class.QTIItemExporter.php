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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
class taoQTI_models_classes_Export_QTIItemExporter extends taoItems_models_classes_ItemExporter {

	/**
	 * Overriden export fro QTI items
	 * @see taoItems_models_classes_ItemExporter::export()
	 */
	public function export($options = array()) {
		
		$zipToRoot = isset($options['zipToRoot'])?(bool)$options['zipToRoot']:false;
		
		//add the data location
		$location = $this->getItemLocation();
		if(is_dir(realpath($location))){
			
			$basenameLocation = $zipToRoot?'':tao_helpers_Uri::getUniqueId($this->getItem()->getUri());
			
			$this->addFile($location, $basenameLocation);
			
			//get the local resources and add them
			$addedResources = 0;
			$resources = $this->getResources();
            
			foreach($resources as $resource){
				$resourceLocation = str_replace(ROOT_URL, ROOT_PATH, $resource);
                
                if(preg_match('/(.)+\/filemanager\/views\/data\//i', $resource)){
                    
                    $resourceLocation = preg_replace('/(.)+\/filemanager\/views\/data\//i', ROOT_PATH . '/filemanager/views/data/', $resource);
                    $this->addFile($resourceLocation, $basenameLocation.'/res/'.basename($resourceLocation));
                    $addedResources++;
                    
                }else if(file_exists($resourceLocation)){
                    
					$this->addFile($resourceLocation, $basenameLocation.'/res/'.basename($resourceLocation));
					$addedResources++;
				}
				//in case of dynamic media service
				else if(preg_match("/taoItems\/Items\/getMediaResource\?path=/", $resource)){
                    
					$path = urldecode(substr($resource, strpos($resource, '?path=') + 6));
					$path = substr($path, 0, strrpos($path, '&'));
                    
					if(preg_match('/(.)+\/filemanager\/views\/data\//i', $path)){
						//check if the file is linked to the file manager
						$resourceLocation = preg_replace('/(.)+\/filemanager\/views\/data\//i', ROOT_PATH . '/filemanager/views/data/', $path);
						$this->addFile($resourceLocation, $basenameLocation.'/res/'.basename($resourceLocation));
						$addedResources++;
					}
                }
			}
			
			//change the content of the item XML by linking the local resources 
			if($addedResources > 0){
				
				$dataFile = (string) $this->getItemModel()->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_DATAFILE_PROPERTY));
				$content = $this->getItemService()->getItemContent($this->getItem());
				foreach($resources as $resource){
					$content = str_replace(dirname($resource), 'res', $content);
				}
				$this->getZip()->addFromString($basenameLocation.'/'.$dataFile, $content);
			}
		}
	}
	
	/**
	 * Extract the local resources access from an URL  
	 * @return array the list of resources URLs
	 */
	public function getResources(){
		
		$returnValue = array();
		
		try{//Parse data to get img src by the media service URL
			
			$content = $this->getItemService()->getItemContent($this->getItem());
			$localResourcePattern = preg_quote(ROOT_URL, '/');
			
			$doc = new DOMDocument;
			if($doc->loadXML($content)){
				
				$tags 		= array('img', 'object');
				$srcAttr 	= array('src', 'data');
				$xpath = new DOMXpath($doc);
				$query = implode(' | ', array_map(create_function('$a', "return '//*[name(.)=\\''.\$a.'\\']';"), $tags));
				foreach($xpath->query($query) as $element) {
					foreach($srcAttr as $attr){
						if($element->hasAttribute($attr)){
							$source = trim($element->getAttribute($attr));
							if(preg_match("/^$localResourcePattern/", $source)){
								$returnValue[] = $source;
							}
						}
					}
				}
			}
		}
		catch(DOMException $de){ 
			//we render it anyway
		}
		
		return $returnValue;
	}
	
}
?>