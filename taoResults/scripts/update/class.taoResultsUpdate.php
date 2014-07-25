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
 * Description of taoResultsUpdate
 *
 * @author plichart
 */

/**
 *
 * @author Patrick plichart, <patrick@taotesting.com>
 * @package taoResults
 * @subpackage test
 */
class taoResults_scripts_update_taoResultsUpdate extends tao_scripts_Runner {

    public function preRun(){
        if (self::isMigrated()) {
            $this->err("taoResults seems already migrated, script aborted", true);
        }
    }

    public function run(){
        self::migrateAllResults();
    }

    public function postRun(){
    }


    
	private static function migrateAllResults(){
       
		$oldResultClass = new core_kernel_classes_Class("http://www.tao.lu/Ontologies/TAOResult.rdf#DeliveryResult");
        $oldResults = $oldResultClass->getInstances();
        foreach ($oldResults as $oldResult) {
                self::migrate($oldResult);
            }
        }
    private static function migrate(core_kernel_classes_Resource $oldResult){
        //$testTaker = $oldResult();
        //delivery
            //PROPERTY_RESULT_OF_DELIVERY
        $oldVariableClass = new core_kernel_classes_Class("http://www.tao.lu/Ontologies/TAOResult.rdf#Variable");
        $oldVariableInstances = $oldVariableClass->searchInstances(
            array('http://www.tao.lu/Ontologies/TAOResult.rdf#memberOfDeliveryResult' => $oldResult->getUri()),
            array("recursive" => true)
            );
        self::log("Found ".count($oldVariableInstances). "to be migrated about the result ".$oldResult->getUri()." ") ;
        foreach ($oldVariableInstances as $oldVariableInstance) {
            self::migrateVariable($oldResult, $oldVariableInstance);
        }

        self::migrateModel();
    }
    private static function log($msg){
        //self::log($msg;
        //echo $msg."\n";
        common_Logger::i($msg);
    }
    private static function migrateVariable(core_kernel_classes_Resource $oldResult, $oldVariableInstance){
         $CallIdItem = self::getRelatedItemAndCallId($oldVariableInstance);
         $itemResult = self::getItemResult($oldResult,$CallIdItem[0], $CallIdItem[1]);
         $oldIdentifier = $oldVariableInstance->getUniquePropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOResult.rdf#variableIdentifier'));
         $oldValue = "";
            try {
                $oldValue = $oldVariableInstance->getUniquePropertyValue(new core_kernel_classes_Property(RDF_VALUE))->literal;
            } catch (Exception $e) {
                self::log("No value/More than one value found in variable ... ".$oldVariableInstance->getUri()."");
            }
        //PROPERTY_VARIABLE_EPOCH converted from time to microtime
        $oldEpoch = $oldVariableInstance->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_VARIABLE_EPOCH));
        $newEpoch = "111111 ".$oldEpoch;
        self::log("Changing ... ".$oldVariableInstance->getUri(). " epoch to ".$newEpoch."");;
        $oldVariableInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_VARIABLE_EPOCH), array($newEpoch) );

        $oldType = $oldVariableInstance->getUniquePropertyValue(new core_kernel_classes_Property(RDF_TYPE));
        
        //links the variable to the itemResult
        self::log("Changing ... ".$oldVariableInstance->getUri(). " related item result to ".$itemResult->getUri()."");
        $oldVariableInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_RELATED_ITEM_RESULT), array($itemResult->getUri()) );
        self::log("Changing ... ".$oldVariableInstance->getUri(). " identifier to ".$oldIdentifier ."");
        $oldVariableInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_IDENTIFIER), array($oldIdentifier));
        self::log("Removing ...".$oldVariableInstance->getUri(). " variableOrigin"."");
        $oldVariableInstance->removePropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOResult.rdf#variableOrigin'));
         self::log("Removing ...".$oldVariableInstance->getUri(). " memberOfDeliveryResult"."");
        $oldVariableInstance->removePropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOResult.rdf#memberOfDeliveryResult'));
        self::log("Removing ...".$oldVariableInstance->getUri(). " variableIdentifier"."");
        $oldVariableInstance->removePropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOResult.rdf#variableIdentifier'));

        //grade variable class changed from http://www.tao.lu/Ontologies/TAOResult.rdf#GradeVariable to http://www.tao.lu/Ontologies/TAOResult.rdf#OutcomeVariable
        if ($oldType->getUri() == "http://www.tao.lu/Ontologies/TAOResult.rdf#GradeVariable") {
            $oldVariableInstance->editPropertyValues(new core_kernel_classes_Property(RDF_TYPE), array("http://www.tao.lu/Ontologies/TAOResult.rdf#OutcomeVariable"));
            self::log("Changing ...".$oldVariableInstance->getUri(). " type (GRADE)"."");
        } else
        {
            
        }
        //values are now stored serialized
       self::log( "Changing ... ".$oldVariableInstance->getUri(). " value to ".$oldValue."");
        $oldVariableInstance->editPropertyValues(new core_kernel_classes_Property(RDF_VALUE), array(serialize($oldValue)) );

        //set default values
        //PROPERTY_VARIABLE_CARDINALITY   => $itemVariable->getCardinality(),
        //PROPERTY_VARIABLE_BASETYPE      => $itemVariable->getBaseType(),

        //set outcome default values
        //PROPERTY_OUTCOME_VARIABLE_NORMALMAXIMUM => $itemVariable->getNormalMaximum(),
        //PROPERTY_OUTCOME_VARIABLE_NORMALMINIMUM => $itemVariable->getNormalMinimum(),

         //set response default
        //PROPERTY_RESPONSE_VARIABLE_CORRECTRESPONSE => $isCorrect,

    }

    private static function getRelatedItemAndCallId(core_kernel_classes_Resource $oldVariableInstance){
            $activityInstanceOrigin = $oldVariableInstance->getUniquePropertyValue(new core_kernel_classes_Property("http://www.tao.lu/Ontologies/TAOResult.rdf#variableOrigin"));
            $callID= $activityInstanceOrigin->getUri();
            $activity  = $activityInstanceOrigin->getUniquePropertyValue(new core_kernel_classes_Property("http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsExecutionOf"));
            //$callInteractiveService = $activity->getUniquePropertyValue(new core_kernel_classes_Property("http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivitiesInteractiveServices"));
            //check all interactive services:
            $returnValue = "ItemRemoved";
			foreach ($activity->getPropertyValuesCollection(new core_kernel_classes_Property("http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivitiesInteractiveServices"))->getIterator() as $iService){
				if($iService instanceof core_kernel_classes_Resource){
                    $serviceDefinition = $iService->getUniquePropertyValue(new core_kernel_classes_Property("http://www.tao.lu/middleware/wfEngine.rdf#PropertyCallOfServicesServiceDefinition"));
					if(!is_null($serviceDefinition)){

						if($serviceDefinition->getUri() == "http://www.tao.lu/Ontologies/TAODelivery.rdf#ServiceItemRunner"){
							foreach($iService->getPropertyValuesCollection(new core_kernel_classes_Property("http://www.tao.lu/middleware/wfEngine.rdf#PropertyCallOfServicesActualParameterin"))->getIterator() as $actualParam){
								
								$formalParam = $actualParam->getUniquePropertyValue(new core_kernel_classes_Property("http://www.tao.lu/middleware/wfEngine.rdf#PropertyActualParametersFormalParameter"));
                                try {
                                    if($formalParam->getUniquePropertyValue(new core_kernel_classes_Property("http://www.tao.lu/middleware/wfEngine.rdf#PropertyFormalParametersName")) == 'itemUri'){
                                        $item = $actualParam->getOnePropertyValue(new core_kernel_classes_Property("http://www.tao.lu/middleware/wfEngine.rdf#PropertyActualParametersConstantValue"));

                                        if(!is_null($item)){
                                            $returnValue = $item->getUri();
                                            break(2);
                                        }
                                    }
                                } catch (exception $e) {
                                    //the current formal parameter does not reference the Item
                                }
							}
						}
					}
				}
            }
            return array($callID, $returnValue);
    }
    private static function getItemResult(core_kernel_classes_Resource $result, $callId, $item) {
        return taoResults_models_classes_ResultsService::singleton()->getItemResult($result, $callId, "tao2.4 Test", $item);
    }

    private static function migrateModel(){

        //import the new ontology model 
        $basepath = dirname(__FILE__) . '/../../';
        $newModelLocation = $basepath. '/models/ontology/taoresult.rdf';
        $targetNameSpace = "http://www.tao.lu/Ontologies/TAOResult.rdf";
        try {
       core_kernel_impl_ApiModelOO::singleton()->importXmlRdf($targetNameSpace, $newModelLocation);
        } catch (Exception $e)
        {
            self::log("Exception raised while importing the model :".$e->getMessage()."");
        }
        //remove the class defintion http://www.tao.lu/Ontologies/TAOResult.rdf#GradeVariable
        $gradeVariable =new core_kernel_classes_Resource("http://www.tao.lu/Ontologies/TAOResult.rdf#GradeVariable");
        $gradeVariable->delete();
        //remove the property http://www.tao.lu/Ontologies/TAOResult.rdf#variableIdentifier
        $prop =new core_kernel_classes_Resource("http://www.tao.lu/Ontologies/TAOResult.rdf#variableIdentifier");
        $prop->delete();
        //remove the property http://www.tao.lu/Ontologies/TAOResult.rdf#memberOfDeliveryResult
        $prop =new core_kernel_classes_Resource("http://www.tao.lu/Ontologies/TAOResult.rdf#memberOfDeliveryResult");
        $prop->delete();
        //remove the property http://www.tao.lu/Ontologies/TAOResult.rdf#variableOrigin
        $prop =new core_kernel_classes_Resource("http://www.tao.lu/Ontologies/TAOResult.rdf#variableOrigin");
        $prop->delete();
    }
    /**
     *
     * @return boolean
     */
    private static function isMigrated(){
        /*
        $gradeVariable = new core_kernel_classes_Resource("http://www.tao.lu/Ontologies/TAOResult.rdf#GradeVariable");
        $typeOf = $gradeVariable->getPropertyValues(new core_kernel_classes_Property(RDF_TYPE));
        var_dump($typeOf);
        if ((count($typeOf)) != 1) {
            return true;
        }
        $itemResultsClass = new core_kernel_classes_Class(ITEM_RESULT);
        $typeOf = $itemResultsClass->getPropertyValues(new core_kernel_classes_Property(RDF_TYPE));
        var_dump($typeOf);
        if ((count($itemResultsClass)) != 1) {
            return true;
        }*/


        return false;
    }
}   
?>