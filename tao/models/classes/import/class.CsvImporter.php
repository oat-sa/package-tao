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
class tao_models_classes_import_CsvImporter implements tao_models_classes_import_ImportHandler
{
	const OPTION_POSTFIX = '_O';

	protected $validators = array();
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

		$classProperties = $this->getClassProperties();

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
			'csv_column'				=> $this->getColumnMapping($csv_data, $sourceForm),
			tao_helpers_data_CsvFile::FIRST_ROW_COLUMN_NAMES	=> $sourceForm->getValue(tao_helpers_data_CsvFile::FIRST_ROW_COLUMN_NAMES),
		));
		return $myFormContainer;
	}
	
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::import()
     */
    public function import($class, $form) {
	
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
		
		$options = $form->getValues();
		$options['map'] = $newMap;
		$staticMap = array();
		foreach ($form->getValues('ranged_property') as $propUri => $value) {
            if (strpos($propUri, tao_models_classes_import_CSVMappingForm::DEFAULT_VALUES_SUFFIX) !== false){
    			$cleanUri = str_replace(tao_models_classes_import_CSVMappingForm::DEFAULT_VALUES_SUFFIX, '', $propUri);
    			$staticMap[$cleanUri] = $value;
            }
		}
		$options['staticMap'] = array_merge($staticMap, $this->getStaticData());
		$options = array_merge($options, $this->getAdditionAdapterOptions());

		$adapter = new tao_helpers_data_GenerisAdapterCsv($options);
		$adapter->setValidators($this->getValidators());

		//import it!
		$report = $adapter->import($form->getValue('importFile'), $class);
		if ($report->getType() == common_report_Report::TYPE_SUCCESS) {
			@unlink($form->getValue('importFile'));
		}
		return $report;
    }
    
    /**
     * Returns an array of the Uris of the properties
     * that should not be importable via CVS
     * 
     * Can be overriden by CsvImporters that are adapted to
     * import resources of a specific class 
     * 
     * @return array
     */
    protected function getExludedProperties() {
        return array();
    }
    
    /**
     * Returns an key => value array of properties
     * to be set on the new resources
     * 
     * Can be overriden by CsvImporters that are adapted to
     * import resources of a specific class 
     * 
     * @return array
     */
    protected function getStaticData() {
        return array();
    }
    
    /**
     * Returns aditional options to be set to the
     * GenericAdapterCsv
     * 
     * Can be overriden by CsvImporters that are adapted to
     * import resources of a specific class 
     * 
     * @see tao_helpers_data_GenerisAdapterCsv
     * @return array
     */
    protected function getAdditionAdapterOptions() {
        return array();
    }

	/**
	 * @return array
	 */
	private function getClassProperties()
	{
		$classUri = tao_helpers_Uri::decode($_POST['classUri']);
		$clazz = new core_kernel_classes_Class($classUri);

		$topLevelClass = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
		$classProperties = tao_models_classes_TaoService::singleton()->getClazzProperties($clazz, $topLevelClass);

		return $classProperties;
	}

	/**
	 * @param $csv_data
	 * @param $sourceForm
	 * @return array
	 */
	private function getColumnMapping($csv_data, $sourceForm)
	{
		//build the mapping form
		if (!$csv_data->count()) {
			return array();
		}

		// 'class properties' contains an associative array(str:'propertyUri' => 'str:propertyLabel') describing properties belonging to the target class.
		// 'ranged properties' contains an associative array(str:'propertyUri' => 'str:propertyLabel')  describing properties belonging to the target class and that have a range.
		// 'csv_column' contains an array(int:columnIndex => 'str:columnLabel') that will be used to create the selection of possible CSV column to map in views.
		// 'csv_column' might have NULL values for 'str:columnLabel' meaning that there was no header row with column names in the CSV file.

		// Format the column mapping option for the form.
		if ($sourceForm->getValue(tao_helpers_data_CsvFile::FIRST_ROW_COLUMN_NAMES) && null != $csv_data->getColumnMapping()) {
			// set the column label for each entry.
			// $csvColMapping = array('label', 'comment', ...)

			return $csv_data->getColumnMapping();
		} else {
			// set an empty value for each entry of the array
			// to describe that column names are unknown.
			// $csvColMapping = array(null, null, ...)

			return array_fill(0, $csv_data->getColumnCount(), null);
		}

	}

	/**
	 * @return array
	 */
	public function getValidators()
	{
		return $this->validators;
	}

	/**
	 * @param array $validators
	 */
	public function setValidators($validators)
	{
		$this->validators = $validators;
	}

}
