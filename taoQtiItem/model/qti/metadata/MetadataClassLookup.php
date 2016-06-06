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
 * MetadataClassLookup interface.
 * 
 * All classes claiming at being able to lookup for a target import Ontology Class
 * a given item must go to from its metadata must implement this interface.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
interface MetadataClassLookup 
{    
    /**
     * Target import Onotology class lookup.
     * 
     * The implementations of this method will try to find an appropriate class
     * an item must be imported to from its metadata values.
     * 
     * @param array $metadataValues An array of MetadataValue objects.
     * @return \core_kernel_classes_Class 
     */
    public function lookup(array $metadataValues);
}
