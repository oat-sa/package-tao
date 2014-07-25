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
 * Short description of class taoQTI_models_classes_ItemModel
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 * @subpackage models_classes_Export
 */
class tao_models_classes_export_RdfExporter implements tao_models_classes_export_ExportHandler
{

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_export_ExportHandler::getLabel()
     */
    public function getLabel() {
    	return __('RDF');
    }
    
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_export_ExportHandler::getExportForm()
     */
    public function getExportForm(core_kernel_classes_Resource $resource) {
        if ($resource instanceof core_kernel_classes_Class) {
            $formData= array('class' => $resource);
        } else {
            $formData= array('instance' => $resource);
        }
    	$form = new tao_models_classes_export_RdfExportForm($formData);
    	return $form->getForm();
    }
    
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_export_ExportHandler::export()
     */
    public function export($formValues, $destination) {
    	$file = null;
    	if(isset($formValues['rdftpl']) && isset($formValues['filename'])){

			$rdf = '';

			//file where we export
			$name = $formValues['filename'].'_'.time().'.rdf';
			$path = tao_helpers_File::concat(array($destination, $name));
			if(!tao_helpers_File::securityCheck($path, true)){
				throw new Exception('Unauthorized file name');
			}
			$api = core_kernel_impl_ApiModelOO::singleton();

			//export by instances

			$instances = array();
			foreach($formValues['rdftpl'] as $key => $value){
				if(preg_match("/^instance_/", $key)){
					$instances[] = tao_helpers_Uri::decode(str_replace('instance_', '', $key));
				}
			}
			if(count($instances) > 0){
				$xmls = array();
				foreach($instances as $instanceUri){
					$xmls[] = $api->getResourceDescriptionXML($instanceUri);
				}

				if(count($xmls) == 1){
					$rdf = $xmls[0];
				}
				else if(count($xmls) > 1){

					//merge the xml of each instances...

					$baseDom = new DomDocument();
					$baseDom->formatOutput = true;
					$baseDom->loadXML($xmls[0]);

					for($i = 1; $i < count($xmls); $i++){

						$xmlDoc = new SimpleXMLElement($xmls[$i]);
						foreach($xmlDoc->getNamespaces() as $nsName => $nsUri){
							if(!$baseDom->documentElement->hasAttribute('xmlns:'.$nsName)){
								$baseDom->documentElement->setAttribute('xmlns:'.$nsName, $nsUri);
							}
						}
						$newDom = new DOMDocument();
						$newDom->loadXml($xmls[$i]);
						foreach($newDom->getElementsByTagNameNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', "Description") as $desc){
							$newNode = $baseDom->importNode($desc, true);
							$baseDom->documentElement->appendChild($newNode);
						}
					}

					$rdf = $baseDom->saveXml();
				}
			}

			//save it
			if(!empty($rdf)){
				if(file_put_contents($path, $rdf)){
					$file = $path;
				}
			}
		}
		return $file;
    }

}

?>