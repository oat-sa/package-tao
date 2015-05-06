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
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoResults
 
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
        
        
        foreach($resources as $result){
            $itemresults = $resultsService->getVariables($result, new core_kernel_classes_Class(TAO_RESULT_VARIABLE), false);
            $cellData = array();
            foreach ($itemresults as $itemResultUri=>$vars) {
                $item = $resultsService->getItemFromItemResult(new core_kernel_classes_Resource($itemResultUri));
                if (get_class($item) == "core_kernel_classes_Resource") {
                   $contextIdentifier = (string)$item->getUri();
                   } else {
                   $contextIdentifier = (string)$item->__toString();    
                   }
                foreach ($vars as $var) {
                    $varData = $resultsService->getVariableData($var);
                    if (is_array($varData["value"])) {
                        $varData["value"] = json_encode($varData["value"]);
                    }
                    $variableIdentifier = (string)$varData["identifier"];                   
                    foreach ($columns as $column) {
                        if (
                            $variableIdentifier == $column->getIdentifier()
                            and
                            $contextIdentifier == $column->getContextIdentifier()
                            ) {
                            $value = (string)$varData["value"];
                            $epoch = $varData["epoch"];
                            $readableTime = "";
                            if ($epoch != "") {
                                $readableTime = "@". tao_helpers_Date::displayeDate(tao_helpers_Date::getTimeStamp($epoch), tao_helpers_Date::FORMAT_VERBOSE);
                            }
                            $this->cache[$varData["type"]->getUri()][$result->getUri()][$contextIdentifier.$variableIdentifier][(string)$epoch] =  array($value, $readableTime);
                            }
                    }

                }
            }
    }
           

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

        
        $vcUri = $column->getVariableClass()->getUri();
        if (isset($this->cache[$vcUri][$resource->getUri()][$column->getContextIdentifier().$column->getIdentifier()])) {
        	$returnValue = $this->cache[$vcUri][$resource->getUri()][$column->getContextIdentifier().$column->getIdentifier()];
            
        } else {
        	common_Logger::d('no data for resource: '.$resource->getUri().' column: '.$column->getIdentifier());
        }
        
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

        
        if (is_null(self::$singleton)) {
        	self::$singleton = new self();
        }
        return self::$singleton;
        

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
        
        
    }

}