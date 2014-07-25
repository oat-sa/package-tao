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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * The ParserFactory provides some methods to build the QTI_Data objects from an
 * element.
 * SimpleXML is used as source to build the model.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_ManifestParserFactory
{
	
    /**
     * Enables you to build the QTI_Resources from a manifest xml data node
     * Content Packaging 1.1)
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  SimpleXMLElement source
     * @return array
     * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_intgv2p0.html#section10003
     */
    public static function getResourcesFromManifest( SimpleXMLElement $source)
    {
        $returnValue = array();

		//check of the root tag
		if($source->getName() != 'manifest'){
			throw new Exception("incorrect manifest root tag");
		}
			
		$resourceNodes = $source->xpath("//*[name(.)='resource']");
		foreach($resourceNodes as $resourceNode){
			$type = (string) $resourceNode['type'];
			if(taoQTI_models_classes_QTI_Resource::isAllowed($type)){
					
				$id = (string) $resourceNode['identifier'];
				$href = (isset($resourceNode['href'])) ? (string) $resourceNode['href'] : '';
					
				$auxFiles = array();
				$xmlFiles = array();
				foreach($resourceNode->file as $fileNode){
					$fileHref = (string) $fileNode['href'];
					if(preg_match("/\.xml$/", $fileHref)){
						if(empty($href)){
							$xmlFiles[] = $fileHref;
						}
					}
					else{
						$auxFiles[] = $fileHref;
					}
				}
					
				if(count($xmlFiles) == 1 && empty($href)){
					$href = $xmlFiles[0];
				}
				$resource = new taoQTI_models_classes_QTI_Resource($id, $href);
				$resource->setAuxiliaryFiles($auxFiles);
					
				$returnValue[] = $resource;
			}
		}

        return (array) $returnValue;
    }
	
}/* end of class taoQTI_models_classes_QTI_ManifestParserFactory */