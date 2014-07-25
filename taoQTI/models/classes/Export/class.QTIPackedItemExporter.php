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
class taoQTI_models_classes_Export_QTIPackedItemExporter extends taoQTI_models_classes_Export_QTIItemExporter {

	/**
	 * Store the manifest of teh previous items exported
	 * @var string
	 */
	protected static $manifest = '';
	
	/**
	 * Reset the manifest
	 */
	public function resetManifest(){
		self::$manifest = '';
	}
	
	/**
	 * @see taoItems_models_classes_exporter_QTIItemExporter::export()
	 * Export the manifest in addition
	 */
	public function export($options = array()){
		parent::export($options);
		$this->exportManifest();
	}
	
	/**
	 * Build, merge and export the IMS Manifest
	 */
	public function exportManifest(){

		$base = tao_helpers_Uri::getUniqueId($this->getItem()->getUri());
		$zipArchive = $this->getZip();
		if(!is_null($zipArchive)){
			
			$qtiFile = '';
			$qtiResources = array();
			for($i = 0; $i < $zipArchive->numFiles; $i++){
          		$fileName = $zipArchive->getNameIndex($i);
          		if(preg_match("/^$base/", $fileName)){
          			if(basename($fileName) == 'qti.xml'){
          				$qtiFile = $fileName;
          			}else{
          				$qtiResources[] = $fileName;
          			}
          		}
     		}
     		if(!empty($qtiFile)){
     			$qtiItemService = taoQTI_models_classes_QTI_Service::singleton();
     			$qtiItem = $qtiItemService->getDataItemByRdfItem($this->getItem());//@todo add support of multi language packages
     			if(!is_null($qtiItem)){
	     			$templateRenderer = new taoItems_models_classes_TemplateRenderer(ROOT_PATH.'/taoQTI/models/classes/QTI/templates/imsmanifest.tpl.php', array(
						'qtiItem' 				=> $qtiItem,
						'qtiFilePath'			=> $qtiFile,
						'medias'				=> $qtiResources,
						'manifestIdentifier'	=> 'QTI-MANIFEST-'.tao_helpers_Display::textCleaner($qtiItem->getIdentifier(), '-')
		        	));
		        	$renderedManifest = $templateRenderer->render();
		        	if(self::$manifest == ''){
		        		self::$manifest = $renderedManifest;
		        	}
		        	else{
		        		$dom1 = new DOMDocument();
		        		$dom1->loadXML(self::$manifest);
		        		
		        		$dom2 = new DOMDocument();
		        		$dom2->loadXML($renderedManifest);
		        		$resourceNodes = $dom2->getElementsByTagName('resource');
		        		
		        		$resourcesNodes = $dom1->getElementsByTagName('resources');
		        		foreach($resourcesNodes as $resourcesNode){
		        			foreach($resourceNodes as $resourceNode){
		        				$newResourceNode = $dom1->importNode($resourceNode, true);
		        				$resourcesNode->appendChild($newResourceNode);
		        			}
		        		}
		        		self::$manifest = $dom1->saveXML();
		        		
		        	}
		        	
		        	$zipArchive->addFromString('imsmanifest.xml', self::$manifest);
     			}
     		}
			
		}
	}
	
}
?>