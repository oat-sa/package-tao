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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */
namespace oat\tao\model\search;

use core_kernel_classes_Class;
/**
 * Index service
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class IndexService
{	
    /**
     * Create a new index
     * 
     * @param \core_kernel_classes_Property $property
     * @param unknown $identifier
     * @param \core_kernel_classes_Resource $tokenizer
     * @param unknown $isFuzzyMatching
     * @param unknown $isDefaultSearchable
     * @return \oat\tao\model\search\Index
     */
    static public function createIndex(\core_kernel_classes_Property $property, $identifier, \core_kernel_classes_Resource $tokenizer, $isFuzzyMatching, $isDefaultSearchable)
    {
        $class = new \core_kernel_classes_Class(Index::RDF_TYPE);
        $existingIndex = self::getIndexById($identifier);
        if (!is_null($existingIndex)) {
            throw new \common_Exception('Index '.$identifier.' already in use');
        }
        // verify identifier is unused
        $resource = $class->createInstanceWithProperties(array(
            RDFS_LABEL => $identifier,
            INDEX_PROPERTY_IDENTIFIER => $identifier,
            INDEX_PROPERTY_TOKENIZER => $tokenizer,
            INDEX_PROPERTY_FUZZY_MATCHING => $isFuzzyMatching ? GENERIS_TRUE : GENERIS_FALSE,
            INDEX_PROPERTY_DEFAULT_SEARCH => $isDefaultSearchable ? GENERIS_TRUE : GENERIS_FALSE
        ));
        $property->setPropertyValue(new \core_kernel_classes_Property(INDEX_PROPERTY), $resource);
        return new Index($resource);
    }
    
    /**
     * Get an index by its unique index id
     * 
     * @param string $identifier
     * @throws \common_exception_InconsistentData
     * @return \oat\tao\model\search\Index
     */
    static public function getIndexById($identifier) {
        
        $indexClass = new core_kernel_classes_Class(Index::RDF_TYPE);
        $resources = $indexClass->searchInstances(array(
                INDEX_PROPERTY_IDENTIFIER => $identifier
            ),array('like' => false)
        );
        if (count($resources) > 1) {
            throw new \common_exception_InconsistentData("Several index exist with the identifier ".$identifier);
        }
        return count($resources) > 0
            ? new Index(array_shift($resources))
            : null;
    }
    
    /**
     * Get all indexes of a property
     * 
     * @param \core_kernel_classes_Property $property
     * @return multitype:\oat\tao\model\search\Index
     */
    static public function getIndexes(\core_kernel_classes_Property $property) {
        $indexUris = $property->getPropertyValues(new \core_kernel_classes_Property(INDEX_PROPERTY));
        $indexes = array();
        
        foreach ($indexUris as $indexUri) {
            $indexes[] = new Index($indexUri);
        }
        
        return $indexes;
    }
    
    /**
     * Get the Search Indexes of a given $class.
     * 
     * The returned array is an associative array where keys are the Property URI
     * the Search Index belongs to, and the values are core_kernel_classes_Resource objects
     * corresponding to Search Index definitions.
     * 
     * @param \core_kernel_classes_Class $class
     * @param boolean $recursive Whether or not to look for Search Indexes that belong to sub-classes of $class. Default is true.
     * @return Index[] An array of Search Index to $class.
     */
    static public function getIndexesByClass(\core_kernel_classes_Class $class, $recursive = true)
    {
        $returnedIndexes = array();
        
        // Get properties to the root class hierarchy.
        $properties = $class->getProperties(true);
        
        foreach ($properties as $prop) {
            $propUri = $prop->getUri();
            $indexes = self::getIndexes($prop);
            
            if (count($indexes) > 0) {
                if (isset($returnedIndexes[$propUri]) === false) {
                    $returnedIndexes[$propUri] = array();
                }
                
                foreach ($indexes as $index) {
                    $returnedIndexes[$propUri][] = new Index($index->getUri());
                }
            }
        }
        
        return $returnedIndexes;
    }
}
