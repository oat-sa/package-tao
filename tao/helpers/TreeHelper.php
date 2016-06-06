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

namespace oat\tao\helpers;

use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use tao_helpers_Uri;

/**
 * Utility class to support building tree rendering component
 */
class TreeHelper
{
    /**
     * Returns the nodes to open in order to display
     * all the listed resources to be visible
     *
     * @param array $uris list of resources to show
     * @param core_kernel_classes_Class $rootNode root node of the tree
     * @return array array of the uris of the nodes to open
     */
    public static function getNodesToOpen($uris, core_kernel_classes_Class $rootNode) {
        // this array is in the form of
        // URI to test => array of uris that depend on the URI
        $toTest = array();
        foreach($uris as $uri){
            $resource = new core_kernel_classes_Resource($uri);
            foreach ($resource->getTypes() as $type) {
                $toTest[$type->getUri()] = array();
            }
        }
        $toOpen = array($rootNode->getUri());
        while (!empty($toTest)) {
            reset($toTest);
            list($classUri, $depends) = each($toTest);
            unset($toTest[$classUri]);
            if (in_array($classUri, $toOpen)) {
                $toOpen = array_merge($toOpen, $depends);
            } else {
                $class = new core_kernel_classes_Class($classUri);
                /** @var core_kernel_classes_Class $parent */
                foreach ($class->getParentClasses(false) as $parent) {
                    if ($parent->getUri() === RDFS_CLASS) {
                        continue;
                    }
                    if (!isset($toTest[$parent->getUri()])) {
                        $toTest[$parent->getUri()] = array();
                    }
                    $toTest[$parent->getUri()] = array_merge(
                        $toTest[$parent->getUri()],
                        array($classUri),
                        $depends
                    );
                }
            }
        }
        return $toOpen;
    }

    /**
     * generis tree representation of a resource node
     *
     * @param core_kernel_classes_Resource $resource
     * @param core_kernel_classes_Class $class
     *
     * @return array
     */
    public static function buildResourceNode(core_kernel_classes_Resource $resource, core_kernel_classes_Class $class) {
        $label = $resource->getLabel();
        $label = empty($label) ? __('no label') : $label;

        return array(
            'data' 	=> _dh($label),
            'type'	=> 'instance',
            'attributes' => array(
                'id' => tao_helpers_Uri::encode($resource->getUri()),
                'class' => 'node-instance',
                'data-uri' => $resource->getUri(),
                'data-classUri' => $class->getUri()
            )
        );
    }

}