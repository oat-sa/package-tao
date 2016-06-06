<?php
require_once dirname(__FILE__) . '/../../tao/includes/class.Bootstrap.php';
require_once dirname(__FILE__) . '/../model/RdsResultStorage.php';

if (PHP_SAPI == 'cli') {
    $_SERVER['HTTP_HOST'] = 'http://localhost';
    $_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__) . '/../../..';
}

$bootStrap = new BootStrap('taoOutcomeRds');
$bootStrap->start();
$timeTot = 0;
for ($a = 0; $a < 10; $a++) {

    $time_start = microtime(true);

//done ahead of your code
    $rs = new \oat\taoOutcomeRds\model\RdsResultStorage();
//    $rs = taoResults_models_classes_ResultsService::singleton();
//optional control on Ids
    for ($i = 0; $i < 1000; $i++) {

        // store results value (id, test taker, delivery)
        $deliverIdentifier = "myCoolId#" . $i . $time_start;
        $rs->storeRelatedTestTaker($deliverIdentifier, "MyTestTaker#" . $i);
        $rs->storeRelatedDelivery($deliverIdentifier, "MyDelivery#" . $i);
//        $deliveryResult = $rs->storeDeliveryResult($deliverIdentifier);
//        $rs->storeTestTaker($deliveryResult, "MyTestTaker#".$i);
//        $rs->storeDelivery($deliveryResult,"MyDelivery#".$i);

        $test = "myTestid" . $i;

        // Create ItemVariables then store them
        for ($j = 0; $j < 10; $j++) {
            if ($j < 3) {
                $itemVariable = new taoResultServer_models_classes_OutcomeVariable();
                $itemVariable->setNormalMaximum("123" . $i . $j);
                $itemVariable->setNormalMinimum("Maximum" . $i . $j);
                $itemVariable->setValue("MyValue" . $i . $j);
                $itemVariable->setIdentifier("identifier" . $i . $j);
                $itemVariable->setCardinality("single");
                $itemVariable->setBaseType("float");

            } else {
                $itemVariable = new taoResultServer_models_classes_ResponseVariable();
                $itemVariable->setCandidateResponse("[Jerome Awesome]");
                $itemVariable->setCorrectResponse("[nop,plop]");
                $itemVariable->setIdentifier("identifier" . $i . $j);
                $itemVariable->setCardinality("single");
                $itemVariable->setBaseType("pair");
            }
            $callIdItem = "An identifier of the execution of an item occurence for a test taker#" . $j;
            $item = "anotherId#" . $j;


            $rs->storeItemVariable($deliverIdentifier, $test, $item, $itemVariable, $callIdItem);
//            $rs->storeItemVariable($deliveryResult, $test, $item, $itemVariable, $callIdItem);
        }

        $callIdTest = "Great Test Id#134589";
        $testVariable = new taoResultServer_models_classes_ResponseVariable();
        $testVariable->setIdentifier("identifier" . $i);
        $testVariable->setCardinality("single");
        $testVariable->setBaseType("pair");
        $rs->storeTestVariable($deliverIdentifier, $test, $testVariable, $callIdTest);
//        $rs->storeTestVariable($deliveryResult, $test, $testVariable, $callIdTest);

    }

    $time_end = microtime(true);
    $time = $time_end - $time_start;

    echo "\n$time\n";
    $timeTot += $time;

}

echo "$timeTot\n";

