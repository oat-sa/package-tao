<?php
set_time_limit(0);
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';

//get the test into each extensions
$tests = TaoTestRunner::getTests(array('wfAuthoring'));

//create the test sutie
$testSuite = new TestSuite('workflow authoring unit tests');
foreach($tests as $testCase){
    
	//TODO disable for release, remove after
    if(strpos($testCase, 'TranslationProcessExecutionTestCase.php')== false){
       $testSuite->addFile($testCase);
    }
}    

//add the reporter regarding the context
if(PHP_SAPI == 'cli'){
	$reporter = new XmlTimeReporter();
}
else{
	$reporter = new HtmlReporter();
}
//run the unit test suite
$testSuite->run($reporter);
?>