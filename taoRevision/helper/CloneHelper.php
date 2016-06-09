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
 */

namespace oat\taoRevision\helper;

use core_kernel_classes_Property;

class CloneHelper
{
    static public function deepCloneTriples($triples) {

        $clones = array();
        foreach ($triples as $original) {
            $triple = clone $original;
            if ($triple->predicate == 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent') {
                // manually copy item content
                $triple->object = self::cloneItemContent($triple->object);
            } elseif (self::isFileReference($triple)) {
                $triple->object = self::cloneFile($triple->object);
            }
            $clones[] = $triple;
        }
        return $clones;
    }
    
    static public function isFileReference(\core_kernel_classes_Triple $triple) {
        $prop = new \core_kernel_classes_Property($triple->predicate);
        $range = $prop->getRange();
        $rangeUri = is_null($range) ? '' : $range->getUri(); 
        switch ($rangeUri) {
        	case CLASS_GENERIS_FILE :
        	    return true;
        	case RDFS_RESOURCE :
        	    $object = new \core_kernel_classes_Resource($triple->object);
        	    return $object->hasType(new \core_kernel_classes_Class(CLASS_GENERIS_FILE));
        	default :
        	    return false;
        }
    }
    
    static protected function cloneItemContent($itemContentUri) {
        \common_Logger::i('clone itemcontent '.$itemContentUri);
        $fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
        $file = new \core_kernel_versioning_File($itemContentUri);
        $sourceDir = dirname($file->getAbsolutePath());
    
        $newFile = $file->getRepository()->spawnFile($sourceDir);
        $newFile->editPropertyValues($fileNameProp, $file->getPropertyValues($fileNameProp));
    
        return $newFile->getUri();
    }

    static protected function cloneFile($fileUri)
    {
        \common_Logger::i('clone file ' . $fileUri);

        $file = new \core_kernel_versioning_File($fileUri);

        $newFile = $file->getRepository()->spawnFile($file->getAbsolutePath(), $file->getLabel(), function($originalName) {
            return md5($originalName + microtime());
        });

        return $newFile->getUri();
    }
}
