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
 * @author Joel Bout, <joel@taotesting.com>
 * @license GPLv2
 * @package generis
 *
 */
class helpers_ExtensionHelper{

    /**
     * Based on a list of extensions we generate an array of missing extensions 
     * 
     * @param array $extensions
     * @return array array of missing extensions ids
     */
    public static function getMissingExtensionIds($extensions) {
        $inList = array();
        foreach ($extensions as $ext) {
            $inList[] = $ext->getId();
        }
        $missing = array();
        foreach ($extensions as $ext) {
            foreach ($ext->getDependencies() as $extId => $version) {
                if (!in_array($extId, $inList) && !in_array($extId, $missing)) {
                    $missing[] = $extId; 
                }
            }
        }
        return $missing;
    }
    
    /**
     * Sorts a list of extensions by dependencies,
     * starting with independent extensions  
     * 
     * @param array $extensions
     * @throws common_exception_Error
     * @return array
     */
    public static function sortByDependencies($extensions) {
        $sorted = array();
        $unsorted = array();
        foreach ($extensions as $ext) {
            $unsorted[$ext->getId()] = array_keys($ext->getDependencies());
        }
        while (!empty($unsorted)) {
            $before = count($unsorted);
            foreach (array_keys($unsorted) as $id) {
                $missing = array_diff($unsorted[$id], $sorted);
                if (empty($missing)) {
                    $sorted[] = $id;
                    unset($unsorted[$id]);
                }
            }
            if (count($unsorted) == $before) {
                throw new common_exception_Error('Unable to resolve extension dependencies');
            }
        }
        
        $returnValue = array();
        foreach ($sorted as $id) {
            foreach ($extensions as $ext) {
                if ($ext->getId() == $id) {
                    $returnValue[$id] = $ext;
                }
            }
        }
        return $returnValue;
    }
    
    public static function sortById($extensions) {
        usort($extensions, function($a, $b) {
            return strcasecmp($a->getId(),$b->getId());
        });
        return $extensions;
    }
    
    /**
     * Whenever or not the extension is required by other installed extensions
     * 
     * @param common_ext_Extension $extension
     * @return boolean
     */
    public static function isRequired(common_ext_Extension $extension) {
        foreach (common_ext_ExtensionsManager::singleton()->getInstalledExtensions() as $ext) {
            foreach ($ext->getDependencies() as $extId => $version) {
                if ($extId == $extension->getId()) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Whenever or not the extension is required to be enabled
     * by other enabled extensions
     * 
     * @param common_ext_Extension $extension
     * @return boolean
     */
    public static function mustBeEnabled(common_ext_Extension $extension) {
        foreach (common_ext_ExtensionsManager::singleton()->getEnabledExtensions() as $ext) {
            foreach ($ext->getDependencies() as $extId => $version) {
                if ($extId == $extension->getId()) {
                    return true;
                }
            }
        }
        return false;
    }
    
}