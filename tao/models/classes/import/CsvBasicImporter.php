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
 * Basic import of csv files
 *
 * @access public
 * @author Antoine Robin, <antoine.robin@vesperiagroup.com>
 * @package tao
 */
class CsvBasicImporter extends CsvAbstractImporter
{
    const OPTION_POSTFIX = '_O';

    public function import($class, $options)
    {
        return parent::importFile($class, $options);

    }

    /**
     * @param \core_kernel_classes_Class $class
     * @param string $file
     * @param array $options
     * @return array
     */
    public function getCsvMapping($class, $file, $options)
    {
        $properties = $this->getClassProperties($class);
        $csv_data = new \tao_helpers_data_CsvFile($options);
        $csv_data->load($file);
        $firstRowAsColumnNames = (isset($options[\tao_helpers_data_CsvFile::FIRST_ROW_COLUMN_NAMES]))?$options[\tao_helpers_data_CsvFile::FIRST_ROW_COLUMN_NAMES]:false;
        $headers = $this->getColumnMapping($csv_data, $firstRowAsColumnNames);
        $modifiedHeader = $headers;
        array_walk($modifiedHeader, function(&$value){
            $value = str_replace(' ', '', strtolower($value));
        });
        $properties[] = new \core_kernel_classes_Property(RDFS_LABEL);
        $map = array();
        /** @var \core_kernel_classes_Property $property */
        foreach($properties as $property){
            if(!in_array($property->getUri(), $this->getExludedProperties())){
                $propertiesMap[$property->getUri()] = $property->getLabel();

                //map properties in many ways
                //look for label (lower case without spaces)
                //look for uri (without namespace)
                if (
                    ($index = array_search(str_replace(' ', '', strtolower($property->getLabel())),$modifiedHeader)) !== false
                    || ($index = array_search(substr(strtolower($property->getUri()), strpos($property->getUri(), '#') + 1),$modifiedHeader)) !== false
                ) {
                    $map[$property->getUri()] = $index;
                //look for label or uri with eventually one error
                } else {
                    $maximumError = 1;
                    $closest = null;
                    foreach ($modifiedHeader as $index => $header) {
                        $levLabel = levenshtein(strtolower($property->getLabel()), $header);
                        $levUri = levenshtein(substr(strtolower($property->getUri()), strpos($property->getUri(), '#') + 1), $header);

                        if ($levLabel <= $maximumError || $levUri <= $maximumError) {
                            $closest  = $index;
                            break;
                        }
                    }
                    if(!is_null($closest)){
                        $map[$property->getUri()] = $closest;
                    }
                }
            }
        }
        $csvMap = array(
            'classProperties'   => $propertiesMap,
            'headerList'        => $headers,
            'mapping'           => $map
        );

        return $csvMap;
    }

    public function getDataSample($file, $options = array(), $size = 5, $associative = true){
        $csv_data = new \tao_helpers_data_CsvFile($options);
        $csv_data->load($file);

        $count = min($size, $csv_data->count());
        $data = array();
        for($i = 0; $i < $count; $i++){
            $data[] = $csv_data->getRow($i, $associative);
        }
        return $data;
    }

}
