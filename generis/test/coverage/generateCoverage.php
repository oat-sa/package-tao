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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
require_once dirname(__FILE__) . '/../GenerisTestRunner.php';
$testSuite = new TestSuite('Generis unit tests');

//get the test into each extensions
$tests = array_merge(
    GenerisTestRunner::findTest((dirname(__FILE__).'/../'))
	,GenerisTestRunner::findTest(dirname(__FILE__).'/../common')
	,GenerisTestRunner::findTest(dirname(__FILE__).'/../rules')
);

//create the test sutie
foreach($tests as $i => $testCase){

    //TODO disable for release, remove after
    if(strpos($testCase, 'VirtuosoImplTestCase.php')== false 
    		&& strpos($testCase, 'SubscriptionsServiceTestCase.php') == false
    && strpos($testCase, 'PDOWrapperTestCase.php') == false){
       $testSuite->addFile($testCase);
    }
}

//add versioning disabled test case
//$testSuite->addFile(dirname(__FILE__).'/../versioning/VersioningDisabledTestCase.php');


//add the reporter regarding the context
if(PHP_SAPI == 'cli'){
	$reporter = new TextReporter();
}
else{
	$reporter =  new HtmlReporter();
}
error_reporting(0);
require_once  PHPCOVERAGE_HOME. "CoverageRecorder.php";
require_once PHPCOVERAGE_HOME . "reporter/HtmlCoverageReporter.php";

$includePaths = array(ROOT_PATH.'generis/core',ROOT_PATH.'generis/common',ROOT_PATH.'generis/helpers');
$excludePaths = array(ROOT_PATH.'generis/common/conf',ROOT_PATH.'generis/common/exception');
$covReporter = new HtmlCoverageReporter("Code Coverage Report Generis", "", PHPCOVERAGE_REPORTS."generis/");
$cov = new CoverageRecorder($includePaths, $excludePaths, $covReporter);
//run the unit test suite
$cov->startInstrumentation();
error_reporting(E_ALL);
$testSuite->run($reporter);
error_reporting(0);
$cov->stopInstrumentation();

$cov->generateReport();
$covReporter->printTextSummary(PHPCOVERAGE_REPORTS.'generis_coverage.txt');
?>