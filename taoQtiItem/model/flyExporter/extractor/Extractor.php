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

use oat\oatbox\PhpSerializable;

/**
 * Extractor of item data
 *
 * Interface Extractor
 * @package oat\taoQtiItem\model\simpleExporter
 */
interface Extractor extends PhpSerializable
{
    const DEFAULT_PROPERTY_DELIMITER = '|';

    /**
     * Item to export, could load relative info like xml
     *
     * @param \core_kernel_classes_Resource $item
     * @return mixed
     */
    public function  setItem(\core_kernel_classes_Resource $item);

    /**
     * Add column to extract with associate config
     *
     * @param $column
     * @param array $config
     * @return mixed
     */
    public function addColumn($column, array $config);

    /**
     * Run process by extracting data following columns
     *
     * @return mixed
     */
    public function run();

    /**
     * Return generated data
     *
     * @return mixed
     */
    public function getData();
}