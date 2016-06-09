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
 * This interface has to be implemented by any software component which wants to represent
 * metadata values e.g. Metadata found in an IMS Manifest File, an Ontology, ...
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
interface MetadataValue
{
    /**
     * Returns a descriptive path aiming at representing the hierarchy of concepts to be traversed
     * to identify the metadata value. 
     * 
     * For instance, you would like to represent a metadata value about the name of a pet. Its path
     * could be the following:
     * 
     * <code>
     * array('species', 'dogs', 'pet', 'name');
     * </code>
     * 
     * Any metadata value using these paths can be identified has names belonging to pets, which are
     * animals among the various species in the world.
     * 
     * @return array An array of strings representing the descriptive path to the metadata attribute.
     */
    public function getPath();
    
    /**
     * Get the language of the intrinsic metadata value. If no particular language is specified,
     * this method returns an empty string.
     * 
     * @return string
     */
    public function getLanguage();
    
    /**
     * Returns an identifier which is unique, describing to whom (e.g. a QTI Item, an Ontology Resource, ...) the intrinsic
     * metadata value belongs to.
     * 
     * @return string
     */
    public function getResourceIdentifier();
    
    /**
     * Get the the intrinsic value of the metadata e.g. a pet name.
     * 
     * @return string
     */
    public function getValue();
}