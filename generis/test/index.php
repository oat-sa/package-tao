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
error_reporting(E_ALL);
require_once dirname(__FILE__).'/../common/inc.extension.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';
require_once INCLUDES_PATH.'/ClearFw/core/simpletestRunner/_main.php';
$testSuite = new TestSuite('Generis unit tests');

//get the test into each extensions
$tests = array_merge(
    TestRunner::findTest(dirname(__FILE__))
	,TestRunner::findTest(dirname(__FILE__).'/common')
	,TestRunner::findTest(dirname(__FILE__).'/rules')
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
//$testSuite->addFile(dirname(__FILE__).'/versioning/VersioningDisabledTestCase.php');


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