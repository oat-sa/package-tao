<?php
/*  
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
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - tao/models/classes/table/class.StaticColumn.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 31.08.2012, 16:22:59 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes_table
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_models_classes_table_Column
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/models/classes/table/class.Column.php');

/**
 * include tao_models_classes_table_DataProvider
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/models/classes/table/interface.DataProvider.php');

/* user defined includes */
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BAD-includes begin
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BAD-includes end

/* user defined constants */
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BAD-constants begin
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BAD-constants end

/**
 * Short description of class tao_models_classes_table_StaticColumn
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes_table
 */
class tao_models_classes_table_StaticColumn
    extends tao_models_classes_table_Column
        implements tao_models_classes_table_DataProvider
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute value
     *
     * @access public
     * @var string
     */
    public $value = '';

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
        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BDA begin
        // nothing to do
        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BDA end
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

        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BDE begin
        $returnValue = $column->value;
        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BDE end

        return (string) $returnValue;
    }

    /**
     * Short description of method fromArray
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array array
     * @return tao_models_classes_table_StaticColumn
     */
    protected static function fromArray($array)
    {
        $returnValue = null;

        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BF5 begin
        $returnValue = new self($array['label'], $array['val']);
        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BF5 end

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string label
     * @param  string value
     * @return mixed
     */
    public function __construct($label, $value)
    {
        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BAF begin
        parent::__construct($label);
        $this->value = $value;
        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BAF end
    }

    /**
     * Short description of method toArray
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function toArray()
    {
        $returnValue = array();

        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BC0 begin
        $returnValue = parent::toArray();
        $returnValue['val'] = $this->value;
        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BC0 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getDataProvider
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return tao_models_classes_table_DataProvider
     */
    public function getDataProvider()
    {
        $returnValue = null;

        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BE4 begin
        $returnValue = $this;
        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BE4 end

        return $returnValue;
    }

} /* end of class tao_models_classes_table_StaticColumn */

?>