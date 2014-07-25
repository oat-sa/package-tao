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
class tao_models_classes_nsImExport_NamespaceExporter
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

			//export by namespace

			$nsManager = common_ext_NamespaceManager::singleton();

			$namespaces = array();
			foreach($formValues['rdftpl'] as $key => $value){
				if(preg_match("/^ns_/", $key)){
					$modelID = (int)str_replace('ns_', '', $key);
					if($modelID > 0){
						$ns = $nsManager->getNamespace($modelID);
						if($ns instanceof common_ext_Namespace){
							$namespaces[] = (string)$ns;
						}
					}
				}
			}
			if(count($namespaces) > 0){
				$rdf = $api->exportXmlRdf($namespaces);
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