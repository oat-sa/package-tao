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
 * importhandler for RDF
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 * @subpackage models_classes_import
 */
class tao_models_classes_import_RdfImporter implements tao_models_classes_import_ImportHandler
{

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::getLabel()
     */
    public function getLabel() {
    	return __('RDF');
    }
    
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::getForm()
     */
    public function getForm() {
    	$form = new tao_models_classes_import_RdfImportForm();
    	return $form->getForm();
    }

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::import()
     */
    public function import($class, $form) {
    	
        $fileInfo = $form->getValue('source');
		$file = $fileInfo['uploaded_file'];
			
		//validate the file to import
		$parser = new tao_models_classes_Parser($file, array('extension' => 'rdf'));
			
		$parser->validate();
		if(!$parser->isValid()){
			$report = common_report_Report::createFailure(__('Nothing imported'));
			$report->add($parser->getReport());
			return $report;
		} else{
		
			//initialize the adapter
			$adapter = new tao_helpers_data_GenerisAdapterRdf();
			if($adapter->import($file, $class)){
				return common_report_Report::createSuccess(__('Data imported successfully'));
			} else{
				return common_report_Report::createFailure(__('Nothing imported'));
			}	
		}
    }

}

?>