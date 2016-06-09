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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoQtiItem\model\flyExporter\extractor;

/**
 * Extract given column of item ontology data
 *
 * Class OntologyExtractor
 * @package oat\taoQtiItem\model\simpleExporter
 */
class OntologyExtractor implements Extractor
{
    /**
     * Item to export
     * @var \core_kernel_classes_Resource
     */
    protected $item;

    /**
     * Request columns
     * @var array
     */
    protected $columns = [];

    /**
     * Output of data
     * @var array
     */
    protected $data = [];

    /**
     * Set item to export
     *
     * @param \core_kernel_classes_Resource $item
     * @return Extractor $this
     */
    public function setItem(\core_kernel_classes_Resource $item)
    {
        $this->item = $item;
    }

    /**
     * Add column to export
     * Check if core_kernel_classes_Property exists into $config
     *
     * @param $column
     * @param array $config
     * @throws ExtractorException
     * @return Extractor $this
     */
    public function addColumn($column, array $config)
    {
        if (!isset($config['property'])) {
            throw new ExtractorException('Property config is missing.');
        }

        $property = new \core_kernel_classes_Property($config['property']);
        if (!$property->exists()) {
            throw new ExtractorException('Property config is not a valid property uri.');
        }
        $config['property'] = $property;

        $this->columns[$column] = $config;
        return $this;
    }

    /**
     * Get ontology values of requested item properties
     *
     * @return $this
     * @throws \Exception
     */
    public function run()
    {
        $this->data = [];

        if (empty($this->item) || !($this->item instanceof \core_kernel_classes_Resource)) {
            throw new ExtractorException('Export item not set.');
        }

        $properties = [];
        foreach ($this->columns as $config) {
            $properties[] = $config['property'];
        }
        $values = $this->item->getPropertiesValues($properties);

        foreach ($this->columns as $column => $config) {
            try {
                $data = [];
                foreach ($values[$config['property']->getUri()] as $itemValue) {
                    if (is_array($itemValue)) {
                        array_walk($itemValue, function (&$value) {
                            $resource = new \core_kernel_classes_Resource($value);
                            $value = $resource->getLabel();
                        });

                        if (isset($config['delimiter'])) {
                            $delimiter = $config['delimiter'];
                        } else {
                            $delimiter = self::DEFAULT_PROPERTY_DELIMITER;
                        }
                        $data[] = explode($delimiter, $itemValue);
                        continue;
                    }

                    $data[] = ($itemValue instanceof \core_kernel_classes_Resource)? $itemValue->getLabel() : (string)$itemValue;
                }
            } catch (\Exception $e) {
                \common_Logger::e('ERROR on column ' . $column . ' : ' . $e->getMessage());
                $data = ['N/A'];
            }

            $this->data[$column] = implode(self::DEFAULT_PROPERTY_DELIMITER, $data);
        }
        $this->columns = [];
        return $this;
    }

    /**
     * Return formatted output
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get human readable declaration class
     * @return string
     */
    public function __toPhpCode()
    {
        return 'new '.get_class($this).'()';
    }
}