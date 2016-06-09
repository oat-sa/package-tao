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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoQtiItem\model\qti\metadata;

/**
 * A MetadataInjector implements the mechanics to inject all metadata values from a given list of MetadataValue objects
 * into a given target.
 * 
 * A MetadataInjection implementation could inject MetadataValue objects in various kind of sources, such
 * as IMS Manifest XML Files, Ontologies, QTI Items, QTI Tests, ...
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @see MetadataValue The MedataValue interface, describing metadata values to be injected into a given target.
 */
interface MetadataInjector
{
    /**
     * Inject a set of metadata values into a given $target. 
     * 
     * Please see the documentation of the MetadataValue interface for in depth information
     * about what a metadata value actually is.
     * 
     * It is the responsibility of the implementation to throw an exception if the datatype of the $target
     * argument is not suitable.
     * 
     * @param mixed $target The target where you want to inject some metadata values.
     * @param MetadataValue[] $values The metadata values to be injected.
     * @throws MetadataInjectionException If something goes wrong during the injection process.
     */
    public function inject($target, array $values);
}