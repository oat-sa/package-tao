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
require_once dirname(__FILE__) . '/../../../tao/test/TaoTestRunner.php';

//get the test into each extensions
$tests = TaoTestRunner::getTests(array('wfAuthoring'));

//create the test sutie
$testSuite = new TestSuite('wfAuthoring unit tests');
foreach($tests as $testCase){
	$testSuite->addFile($testCase);
}    

//add the reporter regarding the context
if(PHP_SAPI == 'cli'){
	$reporter = new TextReporter();
}
else{
	$reporter = new HtmlReporter();
}

error_reporting(0);
require_once  PHPCOVERAGE_HOME. "/CoverageRecorder.php";
require_once PHPCOVERAGE_HOME . "/reporter/HtmlCoverageReporter.php";
//run the unit test suite
$includePaths = array(ROOT_PATH.'wfAuthoring/models',ROOT_PATH.'wfAuthoring/helpers');
$excludePaths = array();
$covReporter = new HtmlCoverageReporter("Code Coverage Report wfAuthoring", "", PHPCOVERAGE_REPORTS."/wfAuthoring");
$cov = new CoverageRecorder($includePaths, $excludePaths, $covReporter);
//run the unit test suite
$cov->startInstrumentation();
error_reporting(E_ALL);
$testSuite->run($reporter);
error_reporting(0);
$cov->stopInstrumentation();
$cov->generateReport();
$covReporter->printTextSummary(PHPCOVERAGE_REPORTS.'/wfAuthoring_coverage.txt');
?>