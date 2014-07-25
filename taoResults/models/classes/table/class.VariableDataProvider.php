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

/**
 * tao - taoResults/models/classes/table/class.VariableDataProvider.php
 *
 * $Id$
 *
 * This file is part of tao.
 *
 * Automatically generated on 31.08.2012, 10:14:43 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoResults
 * @subpackage models_classes_table
 */


/**
 * include tao_models_classes_table_DataProvider
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/models/classes/table/interface.DataProvider.php');

/* user defined includes */
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000004006-includes begin
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000004006-includes end

/* user defined constants */
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000004006-constants begin
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000004006-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoResults
 * @subpackage models_classes_table
 */
class taoResults_models_classes_table_VariableDataProvider
        implements tao_models_classes_table_DataProvider
{
    /**
     * Short description of attribute cache
     *
     * @access public
     * @var array
     */
    public $cache = array();

    /**
     * Short description of attribute singleton
     *
     * @access public
     * @var VariableDataProvider
     */
    public static $singleton = null;



    // --- OPERATIONS ---

    /**
     * Short description of method prepare
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array resources  results
     * @param  array columns    variables
     * @return mixed
     */
    public function prepare($resources, $columns)
    {
        $resultsService = taoResults_models_classes_ResultsService::singleton();
        // section 127-0-1-1--920ca93:1397ba721e9:-8000:0000000000000C5B begin
        foreach($resources as $result){
            $vars = $resultsService->getVariables($result, new core_kernel_classes_Class(TAO_RESULT_VARIABLE));
            $cellData = array();
			foreach ($vars as $var) {
                $varData = $resultsService->getVariableData($var);
                
                if (is_array($varData["value"])) {
                    $varData["value"] = json_encode($varData["value"]);
                }
                
				//should be improved 
                $variableIdentifier = (string)$varData["identifier"];
                $itemResult = $resultsService->getItemResultFromVariable($var);
                $item = $resultsService->getItemFromItemResult($itemResult);
                if (get_class($item) == "core_kernel_classes_Resource") {
                $contextIdentifier = (string)$item->getUri();
                } else {
                $contextIdentifier = (string)$item->__toString();    
                }
                foreach ($columns as $column) {
					if (
                        $variableIdentifier == $column->getIdentifier()
                        and
                        $contextIdentifier == $column->getContextIdentifier()
                        ) {
							$value = (string)$varData["value"];
                            
						    //echo $varData["epoch"];
                            $epoch = $varData["epoch"];
                            //echo $epoch;
                            $readableTime = "";
						    //if ($epoch != "") {$readableTime = "@". date("F j, Y, g:i:s a",$varData["epoch"]);}
						    if ($epoch != "") {$readableTime = "@". tao_helpers_Date::displayeDate(tao_helpers_Date::getTimeStamp($epoch), tao_helpers_Date::FORMAT_VERBOSE);}
                            $this->cache[$varData["type"]->getUri()][$result->getUri()][$contextIdentifier.$variableIdentifier][(string)$epoch] =  array($value, $readableTime);
					}
				}
			}
		}

        // section 127-0-1-1--920ca93:1397ba721e9:-8000:0000000000000C5B end
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
        $returnValue = array();

        // section 127-0-1-1--920ca93:1397ba721e9:-8000:0000000000000C5D begin
        $vcUri = $column->getVariableClass()->getUri();
        if (isset($this->cache[$vcUri][$resource->getUri()][$column->getContextIdentifier().$column->getIdentifier()])) {
        	$returnValue = $this->cache[$vcUri][$resource->getUri()][$column->getContextIdentifier().$column->getIdentifier()];
            
        } else {
        	common_Logger::i('no data for resource: '.$resource->getUri().' column: '.$column->getIdentifier());
        }
        // section 127-0-1-1--920ca93:1397ba721e9:-8000:0000000000000C5D end
        return $returnValue;
    }
    
    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return taoResults_models_classes_table_VariableDataProvider
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--920ca93:1397ba721e9:-8000:0000000000000C69 begin
        if (is_null(self::$singleton)) {
        	self::$singleton = new self();
        }
        return self::$singleton;
        // section 127-0-1-1--920ca93:1397ba721e9:-8000:0000000000000C69 end

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    private function __construct()
    {
        // section 127-0-1-1--920ca93:1397ba721e9:-8000:0000000000000C6C begin
        // section 127-0-1-1--920ca93:1397ba721e9:-8000:0000000000000C6C end
    }

} /* end of class taoResults_models_classes_table_VariableDataProvider */

?>