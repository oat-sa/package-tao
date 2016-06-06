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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\tao\model\import;
/**
 * Abstract class the describe a csv import
 *
 * @access public
 * @author Antoine Robin <antoine.robin@vesperiagroup.com
 * @package tao
 
 */
abstract class CsvAbstractImporter
{

	protected $validators = array();

    
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
	protected function getClassProperties($clazz)
	{
		$topLevelClass = new \core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
		$classProperties = \tao_models_classes_TaoService::singleton()->getClazzProperties($clazz, $topLevelClass);

		return $classProperties;
	}

	/**
	 * @param \tao_helpers_data_CsvFile $csv_data
	 * @param boolean $firstRowAsColumnName
	 * @return array
	 */
	protected function getColumnMapping($csv_data, $firstRowAsColumnName = true)
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
		if ($firstRowAsColumnName && null != $csv_data->getColumnMapping()) {
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


    /**
     * Additional mapping values but that comes from another source than the CSV file.
     * It enables you to define a mapping that will to work along with the CSV mapping.
     *
     * @return the mapping in the same form than the staticData (uri -> val/uri)
     */
    public function getStaticMap()
    {
        return array();
    }


	/**
	 * @param \core_kernel_classes_Class $class where data will be imported
	 * @param array $options contains parameters under key => value format
	 *	file => required
	 *	map => required
	 *	callbacks => optional
	 *	field_delimiter => optional
	 *  field_encloser => optional
	 *  first_row_column_names => optional
	 *  multi_values_delimiter => optional
	 *  onResourceImported => optional
	 *	staticMap => optional
	 * @return \common_report_Report
	 */
	public function importFile($class, $options) {

        if(!isset($options['staticMap']) || !is_array($options['staticMap'])){
            $options['staticMap'] = $this->getStaticData();
        } else {
            $options['staticMap'] = array_merge($options['staticMap'], $this->getStaticData());
        }
		$options = array_merge($options, $this->getAdditionAdapterOptions());

		// Check if we have a proper UTF-8 file.
		if (@preg_match('//u', file_get_contents($options['file'])) === false) {
			return new \common_report_Report(\common_report_Report::TYPE_ERROR, __("The imported file is not properly UTF-8 encoded."));
        }


		$adapter = new \tao_helpers_data_GenerisAdapterCsv($options);
		$adapter->setValidators($this->getValidators());

		//import it!
        $report = $adapter->import($options['file'], $class);


		if ($report->getType() == \common_report_Report::TYPE_SUCCESS) {
            @unlink($options['file']);
            $report->setData($adapter->getOptions());
		}
		return $report;
	}

}
