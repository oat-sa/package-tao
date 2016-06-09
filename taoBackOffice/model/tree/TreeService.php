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
namespace oat\taoBackOffice\model\tree;

use tao_helpers_Uri;
use tao_models_classes_ClassService;
use core_kernel_classes_Class;
use core_kernel_classes_Property;

/**
 * Class TreeService
 */
class TreeService extends tao_models_classes_ClassService
{

    const CLASS_URI = 'http://www.tao.lu/Ontologies/TAO.rdf#Tree';

    const PROPERTY_CHILD_OF = 'http://www.tao.lu/Ontologies/TAO.rdf#isChildOf';

    public function getRootClass()
    {
        return new core_kernel_classes_Class( self::CLASS_URI );
    }

    /**
     * used to build visjs visualization
     *
     * @param core_kernel_classes_Class $tree
     * @param callable|null $labelProcessor
     *
     * @return array
     */
    public function getFlatStructure( core_kernel_classes_Class $tree, $labelProcessor = null )
    {
        $returnValue = array(
            'nodes' => array(),
            'edges' => array()
        );

        $childOf = new \core_kernel_classes_Property( self::PROPERTY_CHILD_OF );
        foreach ($tree->getInstances() as $node) {
            $returnValue['nodes'][] = array(
                'id'    => $node->getUri(),
                'label' => is_callable( $labelProcessor ) ? $labelProcessor( $node->getLabel() ) : $node->getLabel(),
            );
            foreach ($node->getPropertyValues( $childOf ) as $childUri) {
                $returnValue['edges'][] = array(
                    'from' => $childUri,
                    'to'   => $node->getUri()
                );
            }
        }

        return $returnValue;
    }

    /**
     * Used to build jsTree visualization widget
     *
     * @param array $nodes
     *
     * @return array
     */
    public function getNestedStructure( array $nodes )
    {
        $childOf = new \core_kernel_classes_Property( self::PROPERTY_CHILD_OF );
        $tmpTree = array();

        foreach ($nodes as $node) {
            $nodeData = array(
                'data'       => $node->getLabel(),
                'parent'     => 0,
                'attributes' => array(
                    'id'    => tao_helpers_Uri::encode( $node->getUri() ),
                    'class' => 'node-instance',
                )
            );

            foreach ($node->getPropertyValues( $childOf ) as $childUri) {
                $nodeData['parent'] = tao_helpers_Uri::encode( $childUri );
            }

            if (isset( $nodeData['parent'] )) {
                $tmpTree[$nodeData['parent']][] = $nodeData;
            }

        }

        $tree = self::createTree( $tmpTree, array_shift( $tmpTree ) );

        return $tree;
    }

    /**
     * get all the tree classes
     *
     * @return array
     */
    public function getTrees()
    {
        $returnValue = array();

        foreach ($this->getRootClass()->getSubClasses( false ) as $tree) {
            $returnValue[] = $tree;
        }

        return $returnValue;
    }

    /**
     * @param $list
     * @param $parent
     *
     * @return array
     */
    protected static function createTree( $list, $parent )
    {
        $tree = array();
        foreach ($parent as $k => $node) {
            if (isset( $list[$node['attributes']['id']] )) {
                $node['children'] = self::createTree( $list, $list[$node['attributes']['id']] );
                $node['state']    = 'open';
            }
            $tree[] = $node;
        }

        return $tree;
    }

}