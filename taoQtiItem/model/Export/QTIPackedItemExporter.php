<?php
/**
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
 *               2013-2014 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\taoQtiItem\model\Export;

use oat\taoQtiItem\model\qti\Service;
use \core_kernel_classes_Resource;
use \ZipArchive;
use \DOMDocument;
use \tao_helpers_Uri;
use \taoItems_models_classes_TemplateRenderer;
use \tao_helpers_Display;
use \common_Exception;

class QTIPackedItemExporter extends AbstractQTIItemExporter {

    private $manifest;
    
    /**
	 * Creates a new instance of QtiPackedItemExporter for a particular item.
	 * 
	 * @param core_kernel_classes_Resource $item The item to be exported.
	 * @param ZipArchive $zip The ZIP archive were the files have to be exported.
	 * @param DOMDocument $manifest A Manifest to be reused to reference item components (e.g. auxilliary files).
	 */
	public function __construct(core_kernel_classes_Resource $item, ZipArchive $zip, DOMDocument $manifest = null) {
	    parent::__construct($item, $zip);
	    $this->setManifest($manifest);
	}
	
	public function getManifest() {
	    return $this->manifest;
	}
	
	public function setManifest(DOMDocument $manifest = null) {
	    $this->manifest = $manifest;
	}
	
	public function hasManifest() {
	    return $this->getManifest() !== null;
	}
	
	public function export($options = array()) {
		$report = parent::export($options);
		$this->exportManifest($options);
        return $report;
	}
	
	public function buildBasePath() {
	    return tao_helpers_Uri::getUniqueId($this->getItem()->getUri());
	}
	
	public function buildIdentifier() {
	    return tao_helpers_Uri::getUniqueId($this->getItem()->getUri());
	}
	
	/**
	 * Build, merge and export the IMS Manifest into the target ZIP archive.
	 * 
	 * @throws 
	 */
	public function exportManifest($options = array()) {
	    
	    $asApip = isset($options['apip']) && $options['apip'] === true;
	    
	    $base = $this->buildBasePath();
		$zipArchive = $this->getZip();
		$qtiFile = '';
		$qtiResources = array();
		
		for ($i = 0; $i < $zipArchive->numFiles; $i++) {
      		$fileName = $zipArchive->getNameIndex($i);
      		
      		if (preg_match("@^" . preg_quote($base) . "@", $fileName)) {
      			if (basename($fileName) == 'qti.xml') {
      				$qtiFile = $fileName;
      			}
      			else {
      				$qtiResources[] = $fileName;
      			}
      		}
 		}

		$qtiItemService = Service::singleton();
        
		//@todo add support of multi language packages
        $rdfItem = $this->getItem();
		$qtiItem = $qtiItemService->getDataItemByRdfItem($rdfItem);
		
		if (!is_null($qtiItem)) {
            
		    // -- Prepare data transfer to the imsmanifest.tpl template.
		    $qtiItemData = array();
		    
		    // alter identifier for export to avoid any "clash".
		    $qtiItemData['identifier'] = $this->buildIdentifier();
		    $qtiItemData['filePath'] = $qtiFile;
		    $qtiItemData['medias'] = $qtiResources;
		    $qtiItemData['adaptive'] = ($qtiItem->getAttributeValue('adaptive') === 'adaptive') ? true : false;
		    $qtiItemData['timeDependent'] = ($qtiItem->getAttributeValue('timeDependent') === 'timeDependent') ? true : false;
		    $qtiItemData['toolName'] = $qtiItem->getAttributeValue('toolVendor');
		    $qtiItemData['toolVersion'] = $qtiItem->getAttributeValue('toolVersion');
		    $qtiItemData['interactions'] = array();
            
		    foreach ($qtiItem->getInteractions() as $interaction) {
		        $interactionData = array();
		        $interactionData['type'] = $interaction->getQtiTag();
		        $qtiItemData['interactions'][] = $interactionData;
		    }
		    
		    // -- Build a brand new IMS Manifest.
		    $dir = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem')->getDir();
		    $tpl = ($asApip === false) ? $dir . 'model/qti/templates/imsmanifest.tpl.php' : $dir . 'model/qti/templates/imsmanifestApip.tpl.php';
		    
		    $templateRenderer = new taoItems_models_classes_TemplateRenderer($tpl, array(
		                    'qtiItems' 				=> array($qtiItemData),
		                    'manifestIdentifier'    => 'MANIFEST-' . tao_helpers_Display::textCleaner(uniqid('tao', true), '-')
		    ));
		    	
		    $renderedManifest = $templateRenderer->render();
		    $newManifest = new DOMDocument('1.0', TAO_DEFAULT_ENCODING);
		    $newManifest->loadXML($renderedManifest);
		    
		    if ($this->hasManifest()) {
		        // Merge old manifest and new one.
		        $dom1 = $this->getManifest();
		        $dom2 = $newManifest;
		        $dom2->loadXML($renderedManifest);
		        $resourceNodes = $dom2->getElementsByTagName('resource');
		        $resourcesNodes = $dom1->getElementsByTagName('resources');
		    
		        foreach ($resourcesNodes as $resourcesNode) {
		    
		            foreach ($resourceNodes as $resourceNode) {
		                $newResourceNode = $dom1->importNode($resourceNode, true);
		                $resourcesNode->appendChild($newResourceNode);
		            }
		        }
		    
		        // rendered manifest is now useless.
		        unset($dom2);
		    }
		    else {
		        // Brand new manifest.
		        $this->setManifest($newManifest);
		    }
		    
		    // -- Overwrite manifest in the current ZIP archive.
		    $zipArchive->addFromString('imsmanifest.xml', $this->getManifest()->saveXML());
		}
		else {
		    $itemLabel = $this->getItem()->getLabel();
		    throw new common_Exception("the item '${itemLabel}' involved in the export process has no content.");
		}
	}
}
