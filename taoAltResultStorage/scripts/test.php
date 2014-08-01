<?php
require_once dirname(__FILE__) . '/../includes/raw_start.php';

//done ahead of your code 
$rs = new taoResultServer_models_classes_ResultServerStateFull();
$rs->initResultServer("http://www.tao.lu/Ontologies/taoAltResultStorage.rdf#KeyValueResultStorage");
//optional control on Ids
$id = $rs->spawnResult("myCoolId");
$rs->storeRelatedTestTaker("Jerome the big guy");
$rs->storeRelatedDelivery("Jerome's super delivery");



//you are doing this
$itemVariable = new taoResultServer_models_classes_ResponseVariable();
$itemVariable->setCandidateResponse("[Jerome Awesome]");
$itemVariable->setCardinality("single");
$itemVariable->setIdentifier("unittest_identifier");
$itemVariable->setBaseType("pair");
$callIdItem = "An identifier of the execution of an item occurence for a test taker";

$rs->storeItemVariable("sometest identifier", "someidentifier", $itemVariable, $callIdItem);

//and youn want to do this 

$variables = $rs->getVariables($callIdItem);


$variable = $rs->getVariable($callIdItem,"unittest_identifier");
print_r($variable);
$testtaker = $rs->getTestTaker($id);
print_r($testtaker);
$delivery = $rs->getDelivery($id);
print_r($delivery);
//consider results may have been broadcasted to different storages, 
//thus if one of them does not contain the readable interface, 
//you don't have an exception globally but partial data. 