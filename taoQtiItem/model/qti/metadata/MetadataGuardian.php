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

namespace oat\taoQtiItem\model\qti\metadata;

/**
 * MetadataGuardion interface.
 * 
 * All classes claiming to be able to identify an item being imported (using its associated metadata)
 * as an item already stored in the item bank must implement this interface.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
interface MetadataGuardian
{
    /**
     * Check whether or not an item is already stored in the item bank.
     * 
     * This method takes in input an array of $metadataValues that will be inspected by the implementation
     * to check whether or not, an item being imported is currently stored in the item bank.
     * 
     * @param array $metadataValues An array of MetadataValue objects that were previously identified to belong to a given item.
     * @return false|\core_kernel_classes_Resource An ontology resource describing an item already in the database or false if not already in the database.
     */
    public function guard(array $metadataValues);
}