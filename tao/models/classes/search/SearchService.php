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

use oat\tao\model\menu\MenuService;

/**
 * Search service
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class SearchService
{	
    const CONFIG_KEY = 'search';
    
    /**
     * 
     */
    static public function getSearchImplementation() 
    {
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        $impl = $ext->getConfig(self::CONFIG_KEY);
        
        if ($impl === false || !$impl instanceof Search) {
            throw new \common_exception_Error('No valid Search implementation found');
        }
        
        return $impl;
    }

    /**
     * Store the search implementation to use
     * 
     * @param Search $impl
     */
    static public function setSearchImplementation(Search $impl) 
    {
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        $ext->setConfig(self::CONFIG_KEY, $impl);
    }
    
    /**
     * Runs a full reindexing of the resources
     * 
     * @return int nr of resources indexed
     */
    static public function runIndexing() 
    {
        $iterator = new \core_kernel_classes_ResourceIterator(self::getIndexedClasses());
        return self::getSearchImplementation()->index($iterator);
    }
    
    /**
     * returns the root classes to index
     * 
     * @return array
     */
    static protected function getIndexedClasses() 
    {
        $classes = array();
        foreach (MenuService::getAllPerspectives() as $perspective) {
            foreach ($perspective->getChildren() as $structure) {
                foreach ($structure->getTrees() as $tree) {
                    $rootNode = $tree->get('rootNode');
                    if (!empty($rootNode)) {
                        $classes[$rootNode] = new \core_kernel_classes_Class($rootNode);
                    }
                }
            }
        }
        
        return array_values($classes);
    }
}
