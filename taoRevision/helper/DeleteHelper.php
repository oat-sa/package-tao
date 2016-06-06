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
use core_kernel_classes_Resource;
use oat\generis\model\data\ModelManager;

class DeleteHelper
{
    static public function deepDelete(\core_kernel_classes_Resource $resource) {
        foreach ($resource->getRdfTriples() as $triple) {
            self::deleteDependencies($triple);
        }
        $resource->delete();
    }
        
    static public function deepDeleteTriples($triples) {
        $rdf = ModelManager::getModel()->getRdfInterface();
        foreach ($triples as $triple) {
            self::deleteDependencies($triple);
            $rdf->remove($triple);            
        }
    }
    
    static protected function deleteDependencies(\core_kernel_classes_Triple $triple) {
        if ($triple->predicate == 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent') {
            $file = new \core_kernel_versioning_File($triple->object);
            if ($file->exists()) {
                $sourceDir = dirname($file->getAbsolutePath());
                $file->delete();
                \tao_helpers_File::delTree($sourceDir);
            }
        } elseif (CloneHelper::isFileReference($triple)) {
            $file = new \core_kernel_versioning_File($triple->object);
            if ($file->exists()) {
                $file->delete();
            }
        }
    }    
}
