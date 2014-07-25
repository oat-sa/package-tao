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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * tao - taoResults/models/classes/table/class.VariableColumn.php
 *
 * $Id$
 *
 * This file is part of tao.
 *
 * Automatically generated on 31.08.2012, 16:23:31 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoResults
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

/* user defined includes */
// section 127-0-1-1--228e2cb4:13971ca3814:-8000:0000000000000C40-includes begin
// section 127-0-1-1--228e2cb4:13971ca3814:-8000:0000000000000C40-includes end

/* user defined constants */
// section 127-0-1-1--228e2cb4:13971ca3814:-8000:0000000000000C40-constants begin
// section 127-0-1-1--228e2cb4:13971ca3814:-8000:0000000000000C40-constants end

/**
 * Short description of class taoResults_models_classes_table_VariableColumn
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoResults
 * @subpackage models_classes_table
 */
abstract class taoResults_models_classes_table_VariableColumn
    extends tao_models_classes_table_Column
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute classActivity
     *
     * @access public
     * @var Resource
     */
    public $classActivity = null;

    /**
     * Short description of attribute identifier
     *
     * @access public
     * @var string
     */
    public $identifier = '';

   
    // --- OPERATIONS ---

    /**
     * Short description of method fromArray
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array array
     * @return taoResults_models_classes_table_VariableColumn
     */
    protected static function fromArray($array)
    {
        $returnValue = null;

        // section 127-0-1-1--920ca93:1397ba721e9:-8000:0000000000000C74 begin
        
        $contextId = $array['contextId'];
        $contextLabel = $array['contextLabel'];
        $variableIdentifier =  $array['variableIdentifier'];
		$returnValue = new static($contextId, $contextLabel, $variableIdentifier);
        // section 127-0-1-1--920ca93:1397ba721e9:-8000:0000000000000C74 end

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource classActivity
     * @param  string identifier
     * @return mixed
     */
    public function __construct( $contextIdentifier,$contextLabel, $identifier)
    {
        parent::__construct( $contextLabel. "-" .$identifier);
        $this->identifier = $identifier;
        $this->contextIdentifier = $contextIdentifier;
        $this->contextLabel = $contextLabel;
        // section 127-0-1-1--228e2cb4:13971ca3814:-8000:0000000000000C5C end
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

        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000004004 begin
        $returnValue = taoResults_models_classes_table_VariableDataProvider::singleton();
        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000004004 end

        return $returnValue;
    }

    /**
     * Short description of method getContextIdentifier
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public function getContextIdentifier()
    {
        $returnValue = null;

        // section 127-0-1-1--920ca93:1397ba721e9:-8000:0000000000000C6E begin
        return $this->contextIdentifier;
        // section 127-0-1-1--920ca93:1397ba721e9:-8000:0000000000000C6E end

        return $returnValue;
    }

    /**
     * Short description of method getIdentifier
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getIdentifier()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--920ca93:1397ba721e9:-8000:0000000000000C70 begin
        return $this->identifier;
        // section 127-0-1-1--920ca93:1397ba721e9:-8000:0000000000000C70 end

        return (string) $returnValue;
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

        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000004002 begin
        $returnValue = parent::toArray();
        //$returnValue['ca'] = "deprecated";
        $returnValue['contextId'] = $this->contextIdentifier;
        $returnValue['contextLabel'] = $this->contextLabel;
        $returnValue['variableIdentifier'] = $this->identifier;
        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000004002 end

        return (array) $returnValue;
    }

} /* end of abstract class taoResults_models_classes_table_VariableColumn */

?>