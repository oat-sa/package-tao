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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
use oat\tao\helpers\form\elements\TreeAware;

/**
 * Short description of class tao_helpers_form_elements_Treeview
 *
 * @abstract
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
abstract class tao_helpers_form_elements_Treeview extends tao_helpers_form_elements_MultipleElement implements TreeAware
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute widget
     *
     * @access protected
     * @var string
     */
    protected $widget = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView';

    /**
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  core_kernel_classes_Class $range
     * @param  boolean $recursive
     * @return array
     */
    public function rangeToTree( core_kernel_classes_Class $range, $recursive = false)
    {
        $data = array();
        foreach($range->getSubClasses(false) as $rangeClass){
            $classData = array(
                'data' => $rangeClass->getLabel(),
                'attributes' => array(
                    'id' => tao_helpers_Uri::encode($rangeClass->getUri()),
                    'class' => 'node-instance'
                )
            );
            $children = $this->rangeToTree($rangeClass, true);
            if(count($children) > 0){
                $classData['state'] = 'closed';
                $classData['children'] = $children;
            }

            $data[] = $classData;
        }
        if(!$recursive){
            $returnValue = array(
                'data' => $range->getLabel(),
                'attributes' => array(
                    'id' => tao_helpers_Uri::encode($range->getUri()),
                    'class' => 'node-root'
                ),
                'children' => $data
            );
        }
        else{
            $returnValue = $data;
        }

        return $returnValue;
    }

}