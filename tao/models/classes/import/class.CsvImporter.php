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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * Basic import of csv files
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_models_classes_import_CsvImporter extends \oat\tao\model\import\CsvAbstractImporter implements tao_models_classes_import_ImportHandler
{
	const OPTION_POSTFIX = '_O';

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::getLabel()
     */
    public function getLabel() {
    	return __('CSV');
    }
    
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::getForm()
     */
    public function getForm() {
    	$form = empty($_POST['source']) && empty($_POST['importFile'])
    	    ? new tao_models_classes_import_CsvUploadForm()
    	    : $this->createImportFormContainer();
    	return $form->getForm();
    }

    /**
     * Constructs the Import form container
     * In need of a major refactoring, which will
     * probably involve refactoring the Form engine as well
     */
	private function createImportFormContainer(){
	    
	    $sourceContainer = new tao_models_classes_import_CsvUploadForm();
	    $sourceForm = $sourceContainer->getForm();
	    foreach($sourceForm->getElements() as $element) {
	        $element->feed();
	    }
	    
	    if (isset($_POST['importFile'])) {
        	$file = $_POST['importFile'];
		} else {
		    $sourceForm->getElement('source')->feed();
    		$fileInfo = $sourceForm->getValue('source');
    	    $file = $fileInfo['uploaded_file'];
	    }
	    
		$properties = array(tao_helpers_Uri::encode(RDFS_LABEL) => __('Label'));
		$rangedProperties = array();

		$classUri = \tao_helpers_Uri::decode($_POST['classUri']);
		$class = new core_kernel_classes_Class($classUri);
		$classProperties = $this->getClassProperties($class);

		foreach($classProperties as $property){
			if(!in_array($property->getUri(), $this->getExludedProperties())){
				//@todo manage the properties with range
				$range = $property->getRange();
				$properties[tao_helpers_Uri::encode($property->getUri())] = $property->getLabel();
				
				if($range instanceof core_kernel_classes_Resource && $range->getUri() != RDFS_LITERAL){
					$rangedProperties[tao_helpers_Uri::encode($property->getUri())] = $property->getLabel();
				}
			}
		}
		
		//load the csv data from the file (uploaded in the upload form) to get the columns
		$csv_data = new tao_helpers_data_CsvFile($sourceForm->getValues());
		$csv_data->load($file);

		$values = $sourceForm->getValues();
		$values[tao_helpers_data_CsvFile::FIRST_ROW_COLUMN_NAMES] = !empty($values[tao_helpers_data_CsvFile::FIRST_ROW_COLUMN_NAMES]);
		$values['importFile'] = $file;
	    $myFormContainer = new tao_models_classes_import_CSVMappingForm($values, array(
			'class_properties'  		=> $properties,
			'ranged_properties'			=> $rangedProperties,
			'csv_column'				=> $this->getColumnMapping($csv_data, $sourceForm->getValue(tao_helpers_data_CsvFile::FIRST_ROW_COLUMN_NAMES)),
			tao_helpers_data_CsvFile::FIRST_ROW_COLUMN_NAMES	=> $sourceForm->getValue(tao_helpers_data_CsvFile::FIRST_ROW_COLUMN_NAMES),
		));
		return $myFormContainer;
	}
	
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::import()
     */
    public function import($class, $form) {

		$options = $form->getValues();
		$options['file'] = $options['importFile'];

		// Clean "csv_select" values from form view.
		// Transform any "csv_select" in "csv_null" in order to
		// have the same importation behaviour for both because
		// semantics are the same.
		$map = $form->getValues('property_mapping');
		$newMap = array();
		
		foreach($map as $k => $m) {
			if ($m !== 'csv_select') {
				$newMap[$k] = $map[$k];
			}
			else {
				$newMap[$k] = 'csv_null';
			}
			$newMap[$k]= str_replace(self::OPTION_POSTFIX, '', $newMap[$k]);
		    common_Logger::d('map: ' . $k . ' => '. $newMap[$k]);
		}
		$options['map'] = $newMap;

		$staticMap = array();
		foreach ($form->getValues('ranged_property') as $propUri => $value) {
            if (strpos($propUri, tao_models_classes_import_CSVMappingForm::DEFAULT_VALUES_SUFFIX) !== false){
    			$cleanUri = str_replace(tao_models_classes_import_CSVMappingForm::DEFAULT_VALUES_SUFFIX, '', $propUri);
    			$staticMap[$cleanUri] = $value;
            }
		}
		$options['staticMap'] = array_merge($staticMap, $this->getStaticData());
		return parent::importFile($class, $options);

    }
}
