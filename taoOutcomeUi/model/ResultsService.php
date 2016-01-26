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
 * Copyright (c) 2013 Open Assessment Technologies S.A.
 *
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoOutcomeUi
 */

namespace oat\taoOutcomeUi\model;

use \common_Exception;
use \common_Logger;
use \common_cache_FileCache;
use \common_exception_Error;
use \core_kernel_classes_Class;
use \core_kernel_classes_DbWrapper;
use \core_kernel_classes_Property;
use \core_kernel_classes_Resource;
use oat\taoResultServer\models\classes\ResultManagement;
use \tao_helpers_Date;
use \tao_models_classes_ClassService;
use oat\taoOutcomeUi\helper\Datatypes;
use oat\taoDelivery\model\execution\DeliveryExecution;

class ResultsService extends tao_models_classes_ClassService {

    /**
     *
     * @var \taoResultServer_models_classes_ReadableResultStorage
     */
    private $implementation = null;

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_ClassService::getRootClass()
     */
    public function getRootClass() {
        return new core_kernel_classes_Class(TAO_DELIVERY_RESULT);
    }

    public function setImplementation(ResultManagement $implementation){
        $this->implementation = $implementation;
    }

    /**
     * @return ResultManagement
     * @throws common_exception_Error
     */
    public function getImplementation(){
        if($this->implementation == null){
            throw new \common_exception_Error('No result storage defined');
        }
        return $this->implementation;
    }

    /**
     * return all variable for that deliveryResults (uri identifiers)
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  \taoDelivery_models_classes_execution_DeliveryExecution deliveryResult
     * @param boolean $flat a flat array is returned or a structured delvieryResult-ItemResult-Variable
     * @return array
     */
    public function getVariables(\taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult, $flat = true) {
        $variables = array();
        //this service is slow due to the way the data model design  
        //if the delvieryResult related execution is finished, the data is stored in cache.

        $serial = 'deliveryResultVariables:'.$deliveryResult->getIdentifier();
        if (common_cache_FileCache::singleton()->has($serial)) {
            $variables = common_cache_FileCache::singleton()->get($serial);
        } else {
           foreach ($this->getItemResultsFromDeliveryResult($deliveryResult) as $itemResult) {
                $itemResultVariables = $this->getVariablesFromObjectResult($itemResult);
                $variables[$itemResult] = $itemResultVariables;
           }
           foreach ($this->getTestsFromDeliveryResult($deliveryResult) as $testResult) {
                $testResultVariables = $this->getVariablesFromObjectResult($testResult);
                $variables[$testResult] = $testResultVariables;
           }
           //overhead for cache handling, the data is stored only when the underlying deliveryExecution is finished
           try {
                $status = $deliveryResult->getState();
                if ($status->getUri()== DeliveryExecution::STATE_FINISHIED ) {
                    common_cache_FileCache::singleton()->put($variables, $serial);
                }

           }catch (common_Exception $e) {
               common_Logger::i("List of variables of results of ".$deliveryResult->getIdentifier()." could not be reliable cached due to an unfinished execution");
           }


        }
         if ($flat) {
                $returnValue = array();
                foreach ($variables as $key => $itemResultVariables) {
                $newKeys = array();
                $oldKeys = array_keys($itemResultVariables);
                foreach($oldKeys as $oldKey){
                    $newKeys[] = $key.'_'.$oldKey;
                }
                $itemResultVariables = array_combine($newKeys, array_values($itemResultVariables));
                $returnValue = array_merge($itemResultVariables, $returnValue);
                }
            } else {
                $returnValue = $variables;
            }


        return (array) $returnValue;
    }

    /**
     * @param  string $itemResult
     * @return array
     */
    public function getVariablesFromObjectResult($itemResult) {
        return $this->getImplementation()->getVariables($itemResult);
    }

    /**
     * Return the corresponding delivery
     * @param \taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult
     * @return core_kernel_classes_Resource delviery
     * @author Patrick Plichart, <patrick@taotesting.com>
     */
    public function getDelivery(\taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult) {
        return new core_kernel_classes_Resource($this->getImplementation()->getDelivery($deliveryResult->getIdentifier()));
    }

    /**
     * Returns all label of itemResults related to the delvieryResults
     * @param \taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult
     * @return array string uri
     * */
    public function getItemResultsFromDeliveryResult(\taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult) {
        return $this->getImplementation()->getRelatedItemCallIds($deliveryResult->getIdentifier());
    }

    /**
     * Returns all label of itemResults related to the delvieryResults
     * @param \taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult
     * @return array string uri
     * */
    public function getTestsFromDeliveryResult(\taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult) {
        return $this->getImplementation()->getRelatedTestCallIds($deliveryResult->getIdentifier());
    }

    /**
     *
     * @param string $itemCallId
     * @param array $itemVariables already retrieved variables
     * @return \core_kernel_classes_Resource
     */
    public function getItemFromItemResult($itemCallId, $itemVariables = array())
    {
        $item = null;

        if(empty($itemVariables)){
            $itemVariables = $this->getImplementation()->getVariables($itemCallId);
        }

        //get the first variable (item are the same in all)
        $tmpItems = array_shift($itemVariables);

        //get the first object
        if(!is_null($tmpItems[0]->item)){
            $item = new core_kernel_classes_Resource($tmpItems[0]->item);
        }
        return $item;
    }

    /**
     *
     * @param string $test
     * @return \core_kernel_classes_Resource
     */
    public function getVariableFromTest($test) {
        $returnTest = null;
        $tests = $this->getImplementation()->getVariables($test);

        //get the first variable (item are the same in all)
        $tmpTests = array_shift($tests);

        //get the first object
        if(!is_null($tmpTests[0]->test)){
            $returnTest = new core_kernel_classes_Resource($tmpTests[0]->test);
        }
        return $returnTest;
    }

    /**
     *
     * @param string $variableUri
     * @return string
     *
     */
    public function getVariableCandidateResponse($variableUri) {
        return $this->getImplementation()->getVariableProperty($variableUri, 'candidateResponse');
    }

    /**
     *
     * @param string $variableUri
     * @return string
     */
    public function getVariableBaseType($variableUri) {
        return $this->getImplementation()->getVariableProperty($variableUri, 'baseType');
    }

    /**
     *
     * @param \taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult
     * @param string $filter 'lastSubmitted', 'firstSubmitted'
     * @return array ["nbResponses" => x,"nbCorrectResponses" => y,"nbIncorrectResponses" => z,"nbUnscoredResponses" => a,"data" => $variableData]
     */
    public function getItemVariableDataStatsFromDeliveryResult(\taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult, $filter = null) {
        $numberOfResponseVariables = 0;
        $numberOfCorrectResponseVariables = 0;
        $numberOfInCorrectResponseVariables = 0;
        $numberOfUnscoredResponseVariables = 0;
        $numberOfOutcomeVariables = 0;
        $variablesData = $this->getItemVariableDataFromDeliveryResult($deliveryResult, $filter);
        foreach ($variablesData as $itemVariables) {
            foreach($itemVariables['sortedVars'] as $key => $value){
                if($key == CLASS_RESPONSE_VARIABLE){
                    foreach($value as $variable){
                        $variable = array_shift($variable);
                        $numberOfResponseVariables++;
                        switch($variable['isCorrect']){
                            case 'correct':
                                $numberOfCorrectResponseVariables++;
                                break;
                            case 'incorrect':
                                $numberOfInCorrectResponseVariables++;
                                break;
                            case 'unscored':
                                $numberOfUnscoredResponseVariables++;
                                break;
                            default:
                                common_Logger::w('The value '.$variable['isCorrect'].' is not a valid value');
                                break;
                        }
                    }
                }
                else{
                    $numberOfOutcomeVariables++;
                }

            }
        }
        $stats = array(
            "nbResponses" => $numberOfResponseVariables,
            "nbCorrectResponses" => $numberOfCorrectResponseVariables,
            "nbIncorrectResponses" => $numberOfInCorrectResponseVariables,
            "nbUnscoredResponses" => $numberOfUnscoredResponseVariables,
            "data" => $variablesData
        );
        return $stats;
    }
    /**
     *  prepare a data set as an associative array, service intended to populate gui controller
     *
     * @param \taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult
     * @param string $filter 'lastSubmitted', 'firstSubmitted'
     *
     * @return array
     */
    public function getItemVariableDataFromDeliveryResult(\taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult, $filter)
    {

        $undefinedStr = __('unknown'); //some data may have not been submitted           

        $itemCallIds = $this->getItemResultsFromDeliveryResult($deliveryResult);
        $variablesByItem = array();
        foreach ($itemCallIds as $itemCallId) {
            $itemVariables = $this->getVariablesFromObjectResult($itemCallId);
            try {
                common_Logger::d("Retrieving related Item for item call " . $itemCallId . "");
                $relatedItem = $this->getItemFromItemResult($itemCallId, $itemVariables);
            } catch (common_Exception $e) {
                common_Logger::w("The item call '" . $itemCallId . "' is not linked to a valid item. (deleted item ?)");
                $relatedItem = null;
            }
            if (get_class($relatedItem) == "core_kernel_classes_Literal") {
                $itemIdentifier = $relatedItem->__toString();
                $itemLabel = $relatedItem->__toString();
                $itemModel = $undefinedStr;
            } elseif (get_class($relatedItem) == "core_kernel_classes_Resource") {
                $itemIdentifier = $relatedItem->getUri();
                $itemLabel = $relatedItem->getLabel();

                try {
                    common_Logger::d("Retrieving related Item model for item " . $relatedItem->getUri() . "");
                    $itemModel = $relatedItem->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
                    $variablesByItem[$itemIdentifier]['itemModel'] = $itemModel->getLabel();
                } catch (common_Exception $e) { //a resource but unknown
                    $variablesByItem[$itemIdentifier]['itemModel'] = $undefinedStr;
                }
            } else {
                $itemIdentifier = $undefinedStr;
                $itemLabel = $undefinedStr;
                $variablesByItem[$itemIdentifier]['itemModel'] = $undefinedStr;
            }
            foreach ($itemVariables as $variable) {
                //retrieve the type of the variable
                $variableTemp = $variable[0]->variable;
                $variableDescription = array();
                $type = get_class($variableTemp);


                $variableIdentifier = $variableTemp->getIdentifier();

                $variableDescription["uri"] = $variable[0]->uri;
                $variableDescription["var"] = $variableTemp;

                if (method_exists($variableTemp, 'getCorrectResponse') && !is_null($variableTemp->getCorrectResponse())) {
                    if($variableTemp->getCorrectResponse() >= 1){
                        $variableDescription["isCorrect"] = "correct";
                    }
                    else{
                        $variableDescription["isCorrect"] = "incorrect";
                    }
                }
                else{
                    $variableDescription["isCorrect"] = "unscored";
                }

                $variablesByItem[$itemIdentifier]['sortedVars'][$type][$variableIdentifier][$variableTemp->getEpoch()] = $variableDescription;
                $variablesByItem[$itemIdentifier]['label'] = $itemLabel;
            }
        }
        //sort by epoch and filter
        foreach ($variablesByItem as $itemIdentifier => $itemVariables) {

            foreach ($itemVariables['sortedVars'] as $variableType => $variables) {
                foreach ($variables as $variableIdentifier => $observation) {

                    uksort($variablesByItem[$itemIdentifier]['sortedVars'][$variableType][$variableIdentifier], "self::sortTimeStamps");

                    switch ($filter) {
                        case "lastSubmitted": {
                                $variablesByItem[$itemIdentifier]['sortedVars'][$variableType][$variableIdentifier] = array(array_pop($variablesByItem[$itemIdentifier]['sortedVars'][$variableType][$variableIdentifier]));
                                break;
                            }
                        case "firstSubmitted": {
                                $variablesByItem[$itemIdentifier]['sortedVars'][$variableType][$variableIdentifier] = array(array_shift($variablesByItem[$itemIdentifier]['sortedVars'][$variableType][$variableIdentifier]));
                                break;
                            }
                    }
                }
            }
        }

        return $variablesByItem;
    }
    /**
     *
     * @param string $a epoch
     * @param string $b epoch
     * @return number
     */
    public static function sortTimeStamps($a, $b) {
        list($usec, $sec) = explode(" ", $a);
        $floata = ((float) $usec + (float) $sec);
        list($usec, $sec) = explode(" ", $b);
        $floatb = ((float) $usec + (float) $sec);

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
     *
     * @param \taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult
     * @return array
     */
    public function getVariableDataFromDeliveryResult(\taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult) {

        $variables = $this->getImplementation()->getVariables($deliveryResult->getIdentifier());
        $variablesData = array();
        foreach($variables as $variable){
            if($variable[0]->callIdTest != ""){
                $variablesData[] = $variable[0]->variable;
            }
        }
        return $variablesData;
    }

    /**
     * returns the test taker related to the delivery
     *
     * @param \taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult
     * @return \core_kernel_classes_Resource
     */
    public function getTestTaker(\taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult) {
        $testTaker = $this->getImplementation()->getTestTaker($deliveryResult->getIdentifier());
        return new core_kernel_classes_Resource($testTaker);
    }

    /**
     * Delete a delivery result
     *
     * @param string $deliveryResultIdentifier
     * @return boolean
     */
     public function deleteResult($deliveryResultIdentifier) {
        return $this->getImplementation()->deleteResult($deliveryResultIdentifier);
    }


    /**
     * Return the file data associate to a variable
     * @param $variableUri
     * @return array file data
     * @throws \core_kernel_persistence_Exception
     */
    public function getVariableFile($variableUri) {
        //distinguish QTI file from other "file" base type
        $baseType = $this->getVariableBaseType($variableUri);

        // https://bugs.php.net/bug.php?id=52623 ; 
        // if the constant for max buffering, mysqlnd or similar driver
        // is being used without need to adapt buffer size as it is atutomatically adapted for all the data. 
        if (core_kernel_classes_DbWrapper::singleton()->getPlatForm()->getName() == 'mysql') {
            if (defined("PDO::MYSQL_ATTR_MAX_BUFFER_SIZE")) {
                $maxBuffer = (is_int(ini_get('upload_max_filesize'))) ? (ini_get('upload_max_filesize')* 1.5) : 10485760 ;
                core_kernel_classes_DbWrapper::singleton()->getSchemaManager()->setAttribute(\PDO::MYSQL_ATTR_MAX_BUFFER_SIZE,$maxBuffer);
            }
        }

        switch ($baseType) {
            case "file": {
                    $value = $this->getVariableCandidateResponse($variableUri);
                    common_Logger::i(var_export(strlen($value), true));
                    $decodedFile = Datatypes::decodeFile($value);
                    common_Logger::i("FileName:");
                    common_Logger::i(var_export($decodedFile["name"], true));
                    common_Logger::i("Mime Type:");
                    common_Logger::i(var_export($decodedFile["mime"], true));
                    $file = array(
                        "data" => $decodedFile["data"],
                        "mimetype" => "Content-type: " . $decodedFile["mime"],
                        "filename" => $decodedFile["name"]);
                    break;
                }
            default: { //legacy files
                    $file = array(
                        "data" => $this->getVariableCandidateResponse($variableUri),
                        "mimetype" => "Content-type: text/xml",
                        "filename" => "trace.xml");
                }
        }
        return $file;
    }

    /**
     * To be reviewed as it implies a dependency towards taoSubjects
     * @param \taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult
     * @return array test taker properties values
     */
    public function getTestTakerData(\taoDelivery_models_classes_execution_DeliveryExecution $deliveryResult) {
        $testTaker = $this->gettestTaker($deliveryResult);
        if (get_class($testTaker) == 'core_kernel_classes_Literal') {
            return $testTaker;
        } else {
            $propValues = $testTaker->getPropertiesValues(array(
                RDFS_LABEL,
                PROPERTY_USER_LOGIN,
                PROPERTY_USER_FIRSTNAME,
                PROPERTY_USER_LASTNAME,
                PROPERTY_USER_MAIL,
            ));
        }
        return $propValues;
    }

    /**
     *
     * @param \core_kernel_classes_Resource $delivery
     * @return \taoResultServer_models_classes_ReadableResultStorage
     * @throws \core_kernel_persistence_Exception
     * @throws common_exception_Error
     */
    public function getReadableImplementation(\core_kernel_classes_Resource $delivery) {

        if(is_null($delivery)){
            throw new \common_exception_Error(__('This delivery doesn\'t exists'));
        }

        $deliveryResultServer = $delivery->getOnePropertyValue(new \core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));

        if(is_null($deliveryResultServer)){
            throw new \common_exception_Error(__('This delivery has no Result Server'));
        }

        $resultServerModel = $deliveryResultServer->getPropertyValues(new \core_kernel_classes_Property(TAO_RESULTSERVER_MODEL_PROP));

        if(is_null($resultServerModel)){
            throw new \common_exception_Error(__('This delivery has no readable Result Server'));
        }

        foreach($resultServerModel as $model){
            $model = new \core_kernel_classes_Class($model);
            /** @var $implementationClass \core_kernel_classes_Literal*/
            $implementationClass = $model->getOnePropertyValue(new \core_kernel_classes_Property(TAO_RESULTSERVER_MODEL_IMPL_PROP));
            if (!is_null($implementationClass)
                && class_exists($implementationClass->literal) && in_array('taoResultServer_models_classes_ReadableResultStorage',class_implements($implementationClass->literal))) {
                $className = $implementationClass->literal;
                if (!class_exists($className)) {
                    throw new \common_exception_Error('readable resultinterface implementation '.$className.' not found');
                }
                return new $className();
            }
        }

        throw new \common_exception_Error(__('This delivery has no readable Result Server'));
    }
}
