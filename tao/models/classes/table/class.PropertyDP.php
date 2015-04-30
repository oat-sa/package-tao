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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class tao_models_classes_table_PropertyDP
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
class tao_models_classes_table_PropertyDP
        implements tao_models_classes_table_DataProvider
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute singleton
     *
     * @access public
     * @var PropertyDP
     */
    public static $singleton = null;

    /**
     * Short description of attribute cache
     *
     * @access public
     * @var array
     */
    public $cache = array();

    // --- OPERATIONS ---

    /**
     * Short description of method prepare
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array resources
     * @param  array columns
     * @return mixed
     */
    public function prepare($resources, $columns)
    {
        
        
    }

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Column column
     * @return string
     */
    public function getValue( core_kernel_classes_Resource $resource,  tao_models_classes_table_Column $column)
    {
        $returnValue = (string) '';

        
        $result = $resource->getOnePropertyValue($column->getProperty());
        $returnValue = $result instanceof core_kernel_classes_Resource ? $result->getLabel() : (string)$result;
        

        return (string) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return tao_models_classes_table_PropertyDP
     */
    public static function singleton()
    {
        $returnValue = null;

        
        if (is_null(self::$singleton)) {
        	self::$singleton = new self();
        }
        $returnValue = self::$singleton;
        

        return $returnValue;
    }

}

?>