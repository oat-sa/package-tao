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
 * A MetadataExtractor implements the mechanics to extract all metadata values from a given source.
 * 
 * A MetadataExtractor implementation could extract MetadataValue objects from various kind of sources, such
 * as IMS Manifest XML Files, Ontologies, QTI Items, QTI Tests, ...
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @see MetadataValue The MedataValue interface, describing objects extracted and returned by a MetadataExtractor.
 */
interface MetadataExtractor
{
    /**
     * Extract the metadata values from a given source.
     * Please see the documentation of the MetadataValue interface for in depth information
     * about what a metadata value actually is.
     * 
     * The return value of this method is an associative array. Each key is a Resource Identifier and
     * each value for a key is an array of MetadataValue object that belongs to the resource identified
     * by Resource Identifier.
     * 
     * If no MetadataValue objects could be infered from the $source, an empty array is returned.
     * 
     * @param mixed $source The source you want to extract MetaDataValue objects from.
     * @throws MetadataExtractionException If something goes wrong during the extraction process.
     * @return MetadataValue[] An associative array where MetadataValue objects are regrouped by Resource Identifier.
     */
    public function extract($source);
}