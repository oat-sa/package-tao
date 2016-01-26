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

namespace oat\taoOutcomeUi\model;

use \Exception;
use \common_Exception;

/**
 * TAO - taoResults/models/classes/class.StatisticsService.php
 * extracts dataSet with statistics from taoResults
 * 
 *
 * @author Patrick Plichart, <patrick.plichart@taotesting.com>
 * @package taoOutcomeUi
 */
class StatisticsService

     extends ResultsService
{
	/**
	* returns  a data set containing results data using and using an associative array
	* with basic statistics related to a delivery class. 
	* 
	* @author Patrick Plichart, <patrick.plichart@taotesting.com>
	* @param core_kernel_classes_Class deliveryClass the current class selection
	* @return array an assocaitive array containing global statistics and per variable statistics
	*/
	public function extractDeliveryDataSet($deliveryClass){
        $deliveryDataSet = array(
            "nbExecutions" => 0, // Number of collected executions of the delivery
            "nbMaxExpectedExecutions" => 0, // Number of Test asturias albenizTasturias albenizakers
            "nbMaxExecutions" => 0, // Number of Executions tokens granted
            "statisticsPerVariable" => array(), // an array containing variables as keys, collected and computed data ["statisticsPerTest"]=>array()
            "statistics" => array()
        );
        
        $deliveryResults = $this->getImplementation()->getAllTestTakerIds();
        if (count($deliveryResults) == 0) {
            throw new common_Exception(__('The class you have selected contains no results to be analysed, please select a different class'));
        }
        $deliveryDataSet["nbExecutions"] = count($deliveryResults);
        $statisticsGroupedPerVariable = array();
        
        $statisticsGrouped = array(
            "sum" => 0,
            "#" => 0
        );
        foreach ($deliveryResults as $deliveryResult) {
            $de = \taoDelivery_models_classes_execution_ServiceProxy::singleton()->getDeliveryExecution($deliveryResult["deliveryResultIdentifier"]);
            $testTaker = $this->getTestTaker($de);
            if (get_class($testTaker) == 'core_kernel_classes_Literal') {
                $testTakerIdentifier = $testTaker->__toString();
                $testTakerLabel = $testTaker->__toString();
            } else {
                $testTakerIdentifier = $testTaker->getUri();
                $testTakerLabel = $testTaker->getLabel();
            }
            
            $statisticsGrouped["distinctTestTaker"][$testTakerIdentifier] = $testTakerLabel;
            $scoreVariables = $this->getVariables($de);
            try {
                $relatedDelivery = $this->getDelivery($de);
                $deliveryDataSet["deliveries"][$relatedDelivery->getUri()] = $relatedDelivery->getLabel();
            } catch (Exception $e) {
                $deliveryDataSet["deliveries"]["undefined"] = "Unknown Delivery";
            }
            
            foreach ($scoreVariables as $variable) {
                $variableData = (array)(array_shift($variable));
                $activityIdentifier = "";
                $activityNaturalId = "";
                
                if (isset($variableData["item"])) {
                    $item = new \core_kernel_classes_Resource($variableData["item"]);
                    $activityIdentifier = $item->getUri();
                    $activityNaturalId = $item->getLabel();
                }
                $variableIdentifier = $activityIdentifier . $variableData["variable"]->getIdentifier();
                if (! (isset($statisticsGroupedPerVariable[$variableIdentifier]))) {
                    $statisticsGroupedPerVariable[$variableIdentifier] = array(
                        "sum" => 0,
                        "#" => 0
                    );
                }
                
                // we should parametrize if we consider multiple executions of the same test taker or not, here all executions are considered
                $statisticsGroupedPerVariable[$variableIdentifier]["data"][] = $variableData["variable"]->getValue();
                $statisticsGroupedPerVariable[$variableIdentifier]["sum"] += $variableData["variable"]->getValue();
                $statisticsGroupedPerVariable[$variableIdentifier]["#"] += 1;
                $statisticsGroupedPerVariable[$variableIdentifier]["naturalid"] = $activityNaturalId . " (" . $variableData["variable"]->getIdentifier() . ")";
                $statisticsGrouped["data"][] = $variableData["variable"]->getValue();
                $statisticsGrouped["sum"] += $variableData["variable"]->getValue();
                $statisticsGrouped["#"] += 1;
            }
        }
        // compute basic statistics
        $statisticsGrouped["avg"] = $statisticsGrouped["sum"] / $statisticsGrouped["#"];
        // number of different type of variables collected
        $statisticsGrouped["numberVariables"] = sizeOf($statisticsGroupedPerVariable);
        // compute the deciles scores for the complete delivery
        $statisticsGrouped = $this->computeQuantiles($statisticsGrouped, 10);
        // computing average, std and distribution for every single variable
        foreach ($statisticsGroupedPerVariable as $variableIdentifier => $data) {
            ksort($statisticsGroupedPerVariable[$variableIdentifier]["data"]);
            // compute the total populationa verage score for this variable
            $statisticsGroupedPerVariable[$variableIdentifier]["avg"] = $statisticsGroupedPerVariable[$variableIdentifier]["sum"] / $statisticsGroupedPerVariable[$variableIdentifier]["#"];
            $statisticsGroupedPerVariable[$variableIdentifier] = $this->computeQuantiles($statisticsGroupedPerVariable[$variableIdentifier], 10);
        }
        
        ksort($statisticsGrouped["data"]);
        natsort($statisticsGrouped["distinctTestTaker"]);
        
        $deliveryDataSet["statistics"] = $statisticsGrouped;
        $deliveryDataSet["statisticsPerVariable"] = $statisticsGroupedPerVariable;
        
        return $deliveryDataSet;
		}
	/**
	 * computeQuantiles (deprecated)
	 * @param array $statisticsGrouped
	 * @param int $split
	 * @author Patrick Plichart, <patrick.plichart@taotesting.com>
	 * @return array
	 */
	protected function computeQuantiles($statisticsGrouped, $split = 10){
		//if ($statisticsGrouped["#"]< $split) {throw new common_Exception(__('The number of observations is too low').' #'.$statisticsGrouped["#"].'/'.$split);}
		//in case the number of observations is below the quantile size we lower it.
		//$split = min(array($split,$statisticsGrouped["#"]));
		
		$slotSize = $statisticsGrouped["#"] / $split; //number of observations per slot
		sort($statisticsGrouped["data"]);		                      
			//sum all values for the slotsize
            $slot = 0 ; 
			$i=1;
                        foreach ($statisticsGrouped["data"] as $key => $value){
                                if (($i) > $slotSize && (!($slot+1==$split))) {$slot++;$i=1;}
                                if (!(isset($statisticsGrouped["splitData"][$slot]))) {
                                $statisticsGrouped["splitData"][$slot] = array("sum" => 0, "avg" =>0, "#"=> 0);
                                }
                                $statisticsGrouped["splitData"][$slot]["sum"] += $value;
				$statisticsGrouped["splitData"][$slot]["#"] ++;
				$i++;
                        }
                        //compute the average for each slot
                        foreach ( $statisticsGrouped["splitData"] as $slot => $struct){
                                $statisticsGrouped["splitData"][$slot]["avg"] = 
                                $statisticsGrouped["splitData"][$slot]["sum"] /  $statisticsGrouped["splitData"][$slot]["#"];
                        }
		return $statisticsGrouped;	
		}



} 

?>