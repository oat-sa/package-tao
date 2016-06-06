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

namespace oat\taoQtiItem\model\qti\metadata\classLookups;

use oat\taoQtiItem\model\qti\metadata\MetadataClassLookup;

/**
 * LabelClassLookup is an implementation of MetadataClassLookup.
 * 
 * Will lookup for an Item class with a particular label depending
 * on a metadata value with path array('http://www.w3.org/2000/01/rdf-schema#label').
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class LabelClassLookup implements MetadataClassLookup {
    
    public function lookup(array $metadataValues) {
        $lookup = false;
        
        foreach ($metadataValues as $metadataValue) {
            
            $path = $metadataValue->getPath();
            $expectedPath = array(
                RDFS_LABEL
            );
            
            if ($path === $expectedPath) {
                // Check for such a value in database...
                $prop = new \core_kernel_classes_Property(RDFS_LABEL);
                $class = new \core_kernel_classes_Class(RDFS_CLASS);
                $instances = $class->searchInstances(array($prop->getUri() => $metadataValue->getValue()), array('like' => false, 'recursive' => true));
                
                if (count($instances) > 0) {
                    $lookup = new \core_kernel_classes_Class(reset($instances));
                    break;
                }
            }
        }
        
        return $lookup;
    }
}