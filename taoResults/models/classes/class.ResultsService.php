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
 * Copyright (c) 2013 Open Assessment Technologies S.A.
 * 
 */
/**
 * Short description of class taoResults_models_classes_ResultsService
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoResults
 * @subpackage models_classes
 */
class taoResults_models_classes_ResultsService
     extends tao_models_classes_ClassService
{
    public function getRootClass()
    {	
		return new core_kernel_classes_Class(TAO_DELIVERY_RESULT);
    }
    /**
     * return all variable for taht deliveryResults (uri identifiers) 
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource deliveryResult
     * @param core_kernel_classes_Class to restrict to a specific class of variables
     * @return array
     */
    public function getVariables( core_kernel_classes_Resource $deliveryResult, $variableClass = null)
    {
        $returnValue = array();
        // section 127-0-1-1-16e239f7:13925739ce2:-8000:0000000000003B74 begin
        foreach ($this->getItemResultsFromDeliveryResult($deliveryResult) as $itemResult){
            $itemResultVariables = $this->getVariablesFromItemResult($itemResult, $variableClass);
            $returnValue = array_merge($itemResultVariables, $returnValue);
        }
        // section 127-0-1-1-16e239f7:13925739ce2:-8000:0000000000003B74 end
        return (array) $returnValue;
    }
    /**
    * @param  Resource deliveryResult
    * @param core_kernel_classes_Class to restrict to a specific class of variables
    * @return array
    */
    public function getVariablesFromItemResult(core_kernel_classes_Resource $itemResult, $variableClass = null){
            if (is_null($variableClass)) {
                $variableClass = new core_kernel_classes_Class(TAO_RESULT_VARIABLE);
            }
            $itemResultVariables = $variableClass->searchInstances(
        	array(PROPERTY_RELATED_ITEM_RESULT	=> $itemResult->getUri()),
        	array('recursive' => true, 'like' => false)
            );
            return  $itemResultVariables;
        
    }
    /**
     * Return the corresponding delivery 
     * @param core_kernel_classes_Resource $deliveryResult
     * @return core_kernel_classes_Resource delviery
     * @author Patrick Plichart, <patrick@taotesting.com>
     */
    public function getDelivery(core_kernel_classes_Resource $deliveryResult){
	return $deliveryResult->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_RESULT_OF_DELIVERY));
    }

    /**
     * Returns all itemResults related to the delvieryResults
     * @param core_kernel_classes_Resource $deliveryResult
     * @return array core_kernel_classes_Resource
     **/
    public function getItemResultsFromDeliveryResult(core_kernel_classes_Resource $deliveryResult){
        $type = new core_kernel_classes_Class(ITEM_RESULT);
        $returnValue = $type->searchInstances(
        	array(PROPERTY_RELATED_DELIVERY_RESULT	=> $deliveryResult->getUri())
        );
        return $returnValue;
    }
    
    
    public function getItemResultFromVariable(core_kernel_classes_Resource $variable){
           $relatedItemResult = new core_kernel_classes_Property(PROPERTY_RELATED_ITEM_RESULT);
           $itemResult = $variable->getUniquePropertyValue($relatedItemResult);
           return $itemResult;
    }
    public function getItemFromItemResult(core_kernel_classes_Resource $itemResult){
            $relatedItem = new core_kernel_classes_Property(PROPERTY_RELATED_ITEM);
            $item = $itemResult->getUniquePropertyValue($relatedItem);
           return $item;
    }

    public function getItemFromVariable(core_kernel_classes_Resource $variable){
            return $this->getItemFromItemResult($this->getItemResultFromVariable($variable));
    }

      public function getVariableValue($variableUri){
            $variable = new core_kernel_classes_Resource($variableUri);
            return $variable->getUniquePropertyValue(new core_kernel_classes_Property(RDF_VALUE));
    }
    
    /**
     *
     * @param core_kernel_classes_Resource $deliveryResult
     * @param string $filter 'lastSubmitted', 'firstSubmitted'
     * @return type
     */
    public function getItemVariableDataStatsFromDeliveryResult(core_kernel_classes_Resource $deliveryResult, $filter = null){
            $variablesData = $this->getItemVariableDataFromDeliveryResult($deliveryResult, $filter);
            $numberOfResponseVariables = 0;
            $numberOfCorrectResponseVariables = 0;
            $numberOfInCorrectResponseVariables = 0;
            $numberOfUnscoredResponseVariables = 0;
             $numberOfOutcomeVariables = 0;
            //$numberOfOutcomeVariables = $numberOfOutcomeVariables - ($numberOfCorrectOutcomeVariables + $numberOfInCorrectOutcomeVariables);
            foreach ($variablesData as $itemVariables) {

                foreach ($itemVariables['sortedVars'] as $variableType=>$variables) {
                        foreach ($variables as $variableIdentifier => $observations){
                            foreach ($observations as $var) {
                                $type = $variableType;
                                $correctResponse = current($var[PROPERTY_RESPONSE_VARIABLE_CORRECTRESPONSE]);

                                    if ($type == CLASS_RESPONSE_VARIABLE){
                                    $numberOfResponseVariables++;
                                        if ($correctResponse and (get_class($correctResponse)=='core_kernel_classes_Resource')) {
                                            if ($correctResponse->getUri() == GENERIS_TRUE) {
                                            $numberOfCorrectResponseVariables++;
                                            } else {
                                                if ($correctResponse->getUri() == GENERIS_FALSE) {
                                                    $numberOfInCorrectResponseVariables++;
                                                } else { //an unknown core_kernel_classes_Resource
                                                    $numberOfUnscoredResponseVariables++;
                                                }
                                            }
                                        } else {
                                                    $numberOfUnscoredResponseVariables++;
                                        }
                                    }
                                     else {
                                     $numberOfOutcomeVariables++;
                                     }
                            }
                        }
                }
            }
            $stats = array(
                "nbResponses" => $numberOfResponseVariables,
                "nbCorrectResponses" => $numberOfCorrectResponseVariables,
                "nbIncorrectResponses" => $numberOfInCorrectResponseVariables,
                "nbUnscoredResponses" =>  $numberOfUnscoredResponseVariables,
                "data" =>  $variablesData 
            );
            
            return $stats;
    }
    /**
     *  prepare a data set as an associative array, service intended to populate gui controller
     * @param string $filter 'lastSubmitted', 'firstSubmitted'
     */
    public function getItemVariableDataFromDeliveryResult(core_kernel_classes_Resource $deliveryResult, $filter){
            
            $itemResults = $this->getItemResultsFromDeliveryResult($deliveryResult);
            $variablesByItem = array();
            foreach ($itemResults as $itemResult){
                $relatedItem = $this->getItemFromItemResult($itemResult);
                 if (get_class($relatedItem)=="core_kernel_classes_Literal") {
                $itemIdentifier = $relatedItem->__toString();
                $itemLabel = $relatedItem->__toString();
                $itemModel = "unknown";
                 } else{
                $itemIdentifier =  $relatedItem->getUri();
                $itemLabel =  $relatedItem->getLabel();
                $itemModel =  $relatedItem->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
                $variablesByItem[$itemIdentifier]['itemModel'] = $itemModel->getLabel();
                }
                foreach ($this->getVariablesFromItemResult($itemResult) as $variable) {
                    $variableDescription = $variable->getPropertiesValues(array(
                        PROPERTY_IDENTIFIER, RDF_VALUE, RDF_TYPE, PROPERTY_RESPONSE_VARIABLE_CORRECTRESPONSE, PROPERTY_VARIABLE_EPOCH
                    ));
                   $relatedItem = $this->getItemFromVariable($variable);
                   if (get_class($relatedItem)=="core_kernel_classes_Literal") {
                        $itemIdentifier = $relatedItem->__toString();
                        $itemLabel = $relatedItem->__toString();
                         $variablesByItem[$itemIdentifier]['itemModel'] = "unknown";
                   } else{
                        $itemIdentifier =  $relatedItem->getUri();
                        $itemLabel =  $relatedItem->getLabel();
                        $itemModel =  $relatedItem->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
                        $variablesByItem[$itemIdentifier]['itemModel'] = $itemModel->getLabel();
                   }
                $type = current($variableDescription[RDF_TYPE])->getUri();
                $variableIdentifier = current( $variableDescription[PROPERTY_IDENTIFIER])->__toString();
                $epoch = current($variableDescription[PROPERTY_VARIABLE_EPOCH])->__toString();
                $variableDescription["uri"] = $variable->getUri();
                $variableDescription["epoch"] =  array(tao_helpers_Date::displayeDate(tao_helpers_Date::getTimeStamp($epoch), tao_helpers_Date::FORMAT_VERBOSE));
                
                $correctResponse = current($variableDescription[PROPERTY_RESPONSE_VARIABLE_CORRECTRESPONSE]);
                if ($correctResponse and (get_class($correctResponse)=='core_kernel_classes_Resource')) {
                            if ($correctResponse->getUri() == GENERIS_TRUE) {
                                $variableDescription["isCorrect"] = "correct";
                            } else {
                                if ($correctResponse->getUri() == GENERIS_FALSE) {
                                    $variableDescription["isCorrect"] = "incorrect";
                                } else { //an unknown core_kernel_classes_Resource
                                    $variableDescription["isCorrect"] = "unscored";
                                }
                            }
                } else {
                    $variableDescription["isCorrect"] = "unscored";
                }
                
                $variablesByItem[$itemIdentifier]['sortedVars'][$type][$variableIdentifier][$epoch] = $variableDescription;
                $variablesByItem[$itemIdentifier]['label'] = $itemLabel;
                }
            }
                //sort by epoch and filter
                foreach ($variablesByItem as $itemIdentifier =>$itemVariables){
                   
                    foreach ($itemVariables['sortedVars'] as $variableType=>$variables) {
                        foreach ($variables as $variableIdentifier => $observation) {

                            uksort($variablesByItem[$itemIdentifier]['sortedVars'][$variableType][$variableIdentifier], "self::sortTimeStamps" );
                           
                            //print_r($observation);

                            switch ($filter){
                                case "lastSubmitted":{
                                        $variablesByItem[$itemIdentifier]['sortedVars'][$variableType][$variableIdentifier] =
                                            array(array_pop( $variablesByItem[$itemIdentifier]['sortedVars'][$variableType][$variableIdentifier]));
                                        break;}
                                case "firstSubmitted":{
                                        $variablesByItem[$itemIdentifier]['sortedVars'][$variableType][$variableIdentifier] =
                                            array(array_shift( $variablesByItem[$itemIdentifier]['sortedVars'][$variableType][$variableIdentifier]));
                                        break;
                                        
                                }
                            }
                        }
                    }
                }

        return $variablesByItem;
    }
    static function sortTimeStamps($a, $b){
        list($usec, $sec) = explode(" ", $a);
        $floata = ((float)$usec + (float)$sec);
        list($usec, $sec) = explode(" ", $b);
        $floatb = ((float)$usec + (float)$sec);
        common_Logger::i($a." ".$floata);
        common_Logger::i($b. " ".$floatb);
        //the callback is expecting an int returned, for the case where the difference is of less than a second
        //intval(round(floatval($b) - floatval($a),1, PHP_ROUND_HALF_EVEN));
        if ((floatval($floata) - floatval($floatb)) > 0) {
            return 1;
        } elseif ((floatval($floata) - floatval($floatb)) < 0) {
            return -1;
        } else {
            return 0;
        }
    }
    /**
     * return all variables linked to the delviery result and that are not linked to a particular itemResult
     */
    public function getVariableDataFromDeliveryResult(core_kernel_classes_Resource $deliveryResult){

            $variablesData = array();

            $variableClass = new core_kernel_classes_Class(TAO_RESULT_VARIABLE);
            $variables = $variableClass->searchInstances(
        	array(PROPERTY_RELATED_DELIVERY_RESULT	=> $deliveryResult->getUri()),
        	array('recursive' => true, 'like' => false)
            );
            foreach ($variables as $variable) {
            $variablesData[] = $variable->getPropertiesValues(array(
            PROPERTY_IDENTIFIER, RDF_VALUE, RDF_TYPE, PROPERTY_RESPONSE_VARIABLE_CORRECTRESPONSE, PROPERTY_VARIABLE_EPOCH
            ));

            }
            return $variablesData;
    }
    /**
     * returns the test taker related to the delivery
     *
     * @author Patrick Plichart, <patrick.plichart@taotesting.com>
     */
    public function getTestTaker( core_kernel_classes_Resource $deliveryResult)
    {
        $propResultOfSubject = new core_kernel_classes_Property(PROPERTY_RESULT_OF_SUBJECT);
        return $deliveryResult->getUniquePropertyValue($propResultOfSubject);
    }

    /**************************************/

    /**
     *
     * @param string $deliveryResultIdentifier
     * @return core_kernel_classes_resource
     * @throws common_exception_Error
     */
    public function storeDeliveryResult($deliveryResultIdentifier = null){

        $deliveryResultClass = new core_kernel_classes_Class(TAO_DELIVERY_RESULT);
        if (is_null($deliveryResultIdentifier)) {
            $deliveryResult = $deliveryResultClass->createInstanceWithProperties(array(
					RDFS_LABEL					=> uniqid(),
                    PROPERTY_IDENTIFIER	=> $deliveryResultIdentifier
				));
            $deliveryResult->editPropertyValues(new core_kernel_classes_Property(PROPERTY_IDENTIFIER), $deliveryResult->getUri());
            return $deliveryResult;
        }

        
        $deliveryResults = $deliveryResultClass->searchInstances(array(
	        	PROPERTY_IDENTIFIER	=> $deliveryResultIdentifier
	        ));
         if (count( $deliveryResults) > 1) {
	        	throw new common_exception_Error('More than 1 deliveryResult for the corresponding Id '.$deliveryResultIdentifier);
	        } elseif (count($deliveryResults) == 1) {
	        	$returnValue = array_shift($deliveryResults);
				common_Logger::d('found Delivery Result after search for '.$deliveryResultIdentifier);
	        } else {

				$returnValue = $deliveryResultClass->createInstanceWithProperties(array(
					RDFS_LABEL					=> uniqid(),
                    PROPERTY_IDENTIFIER	=> $deliveryResultIdentifier
				));
				common_Logger::d('spawned Delivery Result for '.$deliveryResultIdentifier);
	        }
            return $returnValue;
    }
    /**
    * @param string testTakerIdentifier (uri recommended)
    */
    public function storeTestTaker(core_kernel_classes_Resource $deliveryResult, $testTakerIdentifier) {
        $propResultOfSubject = new core_kernel_classes_Property(PROPERTY_RESULT_OF_SUBJECT);
        $deliveryResult->editPropertyValues($propResultOfSubject, $testTakerIdentifier);
    }
    /**
    * @param string deliveryIdentifier (uri recommended)
    */
    public function storeDelivery(core_kernel_classes_Resource $deliveryResult, $deliveryIdentifier) {
        $propResultOfDelivery = new core_kernel_classes_Property(PROPERTY_RESULT_OF_DELIVERY);
        $deliveryResult->editPropertyValues($propResultOfDelivery, $deliveryIdentifier);
    }
    /**
    * Submit a specific Item Variable, (ResponseVariable and OutcomeVariable shall be used respectively for collected data and score/interpretation computation)
    * @param string test (uri recommended)
    * @param string item (uri recommended)
    * @param taoResultServer_models_classes_ItemVariable itemVariable
    * @param string callId an id for the item instanciation
    */
    /*todo dependency due to object*/
    public function storeItemVariable(core_kernel_classes_Resource $deliveryResult, $test, $item, taoResultServer_models_classes_Variable $itemVariable, $callId){
        //lookup for ItemResult already set with this identifier (callId), creates it otherwise
        $itemResult = $this->getItemResult($deliveryResult, $callId, $test, $item);
        $storedVariable = $this->storeVariable($itemVariable);
        $storedVariable->setPropertyValue(new core_kernel_classes_Property(PROPERTY_RELATED_ITEM_RESULT), $itemResult->getUri());
    }
    
    private function storeVariable($itemVariable) {
            switch (get_class($itemVariable)) {
            case "taoResultServer_models_classes_OutcomeVariable":{
                $outComeVariableClass = new core_kernel_classes_Class(CLASS_OUTCOME_VARIABLE);
                $returnValue = $outComeVariableClass->createInstanceWithProperties(array(
                    PROPERTY_IDENTIFIER	=> $itemVariable->getIdentifier(),
                    PROPERTY_VARIABLE_CARDINALITY   => $itemVariable->getCardinality(),
                    PROPERTY_VARIABLE_BASETYPE      => $itemVariable->getBaseType(),
                    PROPERTY_OUTCOME_VARIABLE_NORMALMAXIMUM => $itemVariable->getNormalMaximum(),
                    PROPERTY_OUTCOME_VARIABLE_NORMALMINIMUM => $itemVariable->getNormalMinimum(),
                    RDF_VALUE						=> serialize($itemVariable->getValue()),
                    PROPERTY_VARIABLE_EPOCH		=> microtime()
                ));

                break;}
            case "taoResultServer_models_classes_ResponseVariable":{
                $responseVariableClass = new core_kernel_classes_Class(CLASS_RESPONSE_VARIABLE);
                       if (is_null($itemVariable->getCorrectResponse())) {
                    $isCorrect = "";
                }
                else {
                    if ($itemVariable->getCorrectResponse()) {
                    $isCorrect = GENERIS_TRUE;

                    } else
                    {
                        $isCorrect = GENERIS_FALSE;
                     }
                }
                $returnValue = $responseVariableClass->createInstanceWithProperties(array(
                    PROPERTY_IDENTIFIER	=> $itemVariable->getIdentifier(),
                    PROPERTY_VARIABLE_CARDINALITY   => $itemVariable->getCardinality(),
                    PROPERTY_VARIABLE_BASETYPE      => $itemVariable->getBaseType(),
                    //put as rdf#boolean
                    PROPERTY_RESPONSE_VARIABLE_CORRECTRESPONSE => $isCorrect,
                    //PROPERTY_RESPONSE_VARIABLE_CANDIDATERESPONSE=> $itemVariable->getCandidateResponse(),
                    RDF_VALUE						=> serialize($itemVariable->getCandidateResponse()),
                    PROPERTY_VARIABLE_EPOCH		=> microtime()
                ));
                break;}
              case "taoResultServer_models_classes_TraceVariable":{
                $traceVariableClass = new core_kernel_classes_Class(CLASS_TRACE_VARIABLE);
                $returnValue = $traceVariableClass->createInstanceWithProperties(array(
                    PROPERTY_IDENTIFIER	=> $itemVariable->getIdentifier(),
                    PROPERTY_VARIABLE_CARDINALITY   => $itemVariable->getCardinality(),
                    PROPERTY_VARIABLE_BASETYPE      => $itemVariable->getBaseType(),
                    RDF_VALUE						=> serialize($itemVariable->getTrace()), //todo store a file
                    PROPERTY_VARIABLE_EPOCH		=> microtime()
                ));

                break;}
            default:{throw new common_exception_Error("The variable class is not supported");break;}
            }
            return $returnValue;
            //$returnValue->setPropertyValue(new core_kernel_classes_Property(PROPERTY_RELATED_DELIVERY_RESULT), $delvieryResult->getUri());
           
    }
    public function storeTestVariable(core_kernel_classes_Resource $deliveryResult, $test, taoResultServer_models_classes_Variable $itemVariable, $callId) {
        $storedVariable = $this->storeVariable($itemVariable);
        $storedVariable->setPropertyValue(new core_kernel_classes_Property(PROPERTY_RELATED_DELIVERY_RESULT), $deliveryResult->getUri());
    }

    public function getItemResult(core_kernel_classes_Resource $deliveryResult, $callId, $test, $item) {
        $itemResultsClass = new core_kernel_classes_Class(ITEM_RESULT);
        $itemResults = $itemResultsClass->searchInstances(array(
	        	PROPERTY_IDENTIFIER	=> $callId
	        ),array('like' => false));
         if (count( $itemResults) > 1) {
	        	throw new common_exception_Error('More then 1 itemResult for the corresponding Id '.$deliveryResultIdentifier);
	        } elseif (count($itemResults) == 1) {
	        	$returnValue = array_shift($itemResults);
				common_Logger::d('found Item Result after search for '.$callId);
	        } else {
				$returnValue = $itemResultsClass->createInstanceWithProperties(array(
					RDFS_LABEL					=> $callId,
                    PROPERTY_IDENTIFIER	=> $callId,
                    PROPERTY_RELATED_ITEM    => $item,
                    PROPERTY_RELATED_TEST   => $test,
                    PROPERTY_RELATED_DELIVERY_RESULT => $deliveryResult->getUri()
				));
				common_Logger::d('spawned Item Result for '.$callId);
	        }
            return $returnValue;
    }

    /** Submit a complete Item result
    *
    * @param taoResultServer_models_classes_ItemResult itemResult
    * @param string callId an id for the item instanciation
    */
//    public function setItemResult($item, taoResultServer_models_classes_ItemResult $itemResult, $callId ) {}
//    public function setTestResult($test, taoResultServer_models_classes_TestResult $testResult, $callId){}

    public function setTestVariable(core_kernel_classes_Resource $deliveryResult, $test, taoResultServer_models_classes_Variable $testVariable, $callId){
    }



    /**************************************/








	/**
     * Short description of method deleteResult
     *
   
     */
    public function deleteResult( core_kernel_classes_Resource $result)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000204D begin
		if(!is_null($result)){

            $itemResults = $this->getItemResultsFromDeliveryResult($result);
            $variables = $this->getVariables($result);
            foreach  ($itemResults as $itemResult){
                $itemResult->delete();
            }
            foreach  ($variables as $variable){
                $variable->delete();
            }

			$returnValue = $result->delete();
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000204D end

        return (bool) $returnValue;
    }
    /**
     * Short description of method deleteResultClass
     *
     */
    public function deleteResultClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;
     
		if(!is_null($clazz)){
				$returnValue = $clazz->delete();
		}
       return (bool) $returnValue;
    }



    /***********************************************/
    /** CANDIDATE FOR DELETION *********************/
    /***********************************************/


    

    /**
     * Short description of method storeResponse
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource deliveryResult
     * @param  Resource activityExecution
     * @param  string identifier
     * @param  string value
     * @return core_kernel_classes_Resource
     */
    public function storeResponse( core_kernel_classes_Resource $deliveryResult,  core_kernel_classes_Resource $activityExecution, $identifier, $value)
    {
        $returnValue = null;

        // section 127-0-1-1-6befba6b:1394401f373:-8000:0000000000003B7E begin
        $returnValue = $this->storeVariable(
        	new core_kernel_classes_Class(TAO_RESULT_RESPONSE)
        	, $deliveryResult, $activityExecution, $identifier, $value
        );
        // section 127-0-1-1-6befba6b:1394401f373:-8000:0000000000003B7E end

        return $returnValue;
    }

    /**
     * Short description of method storeGrade
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource deliveryResult
     * @param  Resource activityExecution
     * @param  string identifier
     * @param  string value
     * @return core_kernel_classes_Resource
     */
    public function storeGrade( core_kernel_classes_Resource $deliveryResult,  core_kernel_classes_Resource $activityExecution, $identifier, $value)
    {
        $returnValue = null;

        // section 127-0-1-1-6befba6b:1394401f373:-8000:0000000000003B80 begin
        $returnValue = $this->storeVariable(
        	new core_kernel_classes_Class(TAO_RESULT_GRADE)
        	, $deliveryResult, $activityExecution, $identifier, $value
        );
        // section 127-0-1-1-6befba6b:1394401f373:-8000:0000000000003B80 end

        return $returnValue;
    }

    /********************************************/
    /***** YET TO BE REVIEWED********************/
    /********************************************/
        /**
     * Retrieves all score variables pertaining to the deliveryResult
     *
     * @access public
     * @author Patrick Plichart, <patrick.plichart@taotesting.com>
     * @param  Resource deliveryResult
     * @return array
     */
    public function getScoreVariables( core_kernel_classes_Resource $deliveryResult)
    {
        return $this->getVariables($deliveryResult, new core_kernel_classes_Class(CLASS_OUTCOME_VARIABLE));
    }
/**
     * Retrieves information about the variable, including or not the related item (slower)
     * @access public
     * @author Patrick Plichart, <patrick.plichart@taotesting.com>
     * @param  Resource variable
     * @return array simple associative$returnValue = taoTests_models_classes_TestAuthoringService::singleton()->getItemByActivity($activityClass);
     */
    public function getVariableData( core_kernel_classes_Resource $variable, $getItem = false)
    {
        $returnValue = array();
        $propValues = $variable->getPropertiesValues(array(
					RDF_TYPE,
					PROPERTY_IDENTIFIER,
					PROPERTY_VARIABLE_EPOCH,
					RDF_VALUE,
                    PROPERTY_VARIABLE_CARDINALITY,
                    PROPERTY_VARIABLE_BASETYPE

				));
        
    	$returnValue["value"] = (string)unserialize(current($propValues[RDF_VALUE])->__toString());
    	$returnValue["identifier"] = current($propValues[PROPERTY_IDENTIFIER])->__toString();
        $returnValue["type"] = current($propValues[RDF_TYPE]);
        $returnValue["epoch"] = current($propValues[PROPERTY_VARIABLE_EPOCH])->__toString();
        if (count($propValues[PROPERTY_VARIABLE_CARDINALITY]) > 0){
        $returnValue["cardinality"] = current($propValues[PROPERTY_VARIABLE_CARDINALITY])->__toString();
        }
       if (count($propValues[PROPERTY_VARIABLE_BASETYPE]) > 0){
        $returnValue["basetype"] = current($propValues[PROPERTY_VARIABLE_BASETYPE])->__toString();
        }
        if ($getItem) {$returnValue["item"] = $this->getItemFromVariable($variable);}
	//$returnValue["epoch"] = $variable->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_VARIABLE_EPOCH));
        return (array) $returnValue;
    }
    /**
     * To be reviewed as it implies a dependency towards taoSubjects
     * @param core_kernel_classes_Resource $deliveryResult
     */
    public function getTestTakerData(core_kernel_classes_Resource $deliveryResult) {
        $testTaker = $this->gettestTaker($deliveryResult);
        if (get_class($testTaker)=='core_kernel_classes_Literal') {
             return $testTaker;
        }
        else {
        $propValues =  $testTaker->getPropertiesValues(array(
					RDFS_LABEL,
                    PROPERTY_USER_LOGIN,
					PROPERTY_USER_FIRSTNAME,
					PROPERTY_USER_LASTNAME,
                    PROPERTY_USER_MAIL,
				));
        }
        return $propValues;

    }

} /* end of class taoResults_models_classes_ResultsService */

?>