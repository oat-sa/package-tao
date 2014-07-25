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
require_once dirname(__FILE__) . '/../../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../../../tao/includes/raw_start.php';

Bootstrap::loadConstants ('tao');
Bootstrap::loadConstants ('filemanager');
Bootstrap::loadConstants ('taoItems');
Bootstrap::loadConstants ('taoGroups');
Bootstrap::loadConstants ('taoTests');
Bootstrap::loadConstants ('taoResults');
Bootstrap::loadConstants ('wfEngine');
Bootstrap::loadConstants ('taoDelivery');

$testSuite = new TestSuite('Hardify Unit Test Case');
$testSuite->addFile(dirname(__FILE__) . '/../../../tao/test/dataTest/MassInsertTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/HardifyTestCase.php');

//load generis test case
$testSuite->addFile(dirname(__FILE__) . '/../CollectionTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/../FileTestCase.php');
//$testSuite->addFile(dirname(__FILE__) . '/../ModelsRightTestCase.php');//policies in hard does not respect model rights
$testSuite->addFile(dirname(__FILE__) . '/../NamespaceTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/../PropertyTestCase.php');
//$testSuite->addFile(dirname(__FILE__) . '/../ResourceTestCase.php');//the test case still uses references to old api (setStatements ..). These references have to be refactored with the new persistence layer
$testSuite->addFile(dirname(__FILE__) . '/../UserServiceTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/../UtilsTestCase.php');

//load other extensions' test cases
$tests = array_merge(
	TaoTestRunner::getTests(array('tao'))
	, TaoTestRunner::getTests(array('taoItems'))
	, TaoTestRunner::getTests(array('taoTests'))
	, TaoTestRunner::getTests(array('taoSubjects'))
	, TaoTestRunner::getTests(array('taoResults'))
	, TaoTestRunner::getTests(array('taoDelivery'))
	, TaoTestRunner::getTests(array('taoGroups'))
	, TaoTestRunner::getTests(array('wfEngine'))
	, TaoTestRunner::getTests(array('filemanager'))
);
foreach($tests as $i => $testCase){	
	//TODO disable for release, remove after
    if(strpos($testCase, 'VirtuosoImplTestCase.php')== false
    	&& strpos($testCase, 'VirtuosoImplTestCase.php')== false){
       $testSuite->addFile($testCase);
    }
}
   
$testSuite->addFile(dirname(__FILE__) . '/UnhardifyTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/../../../tao/test/dataTest/CleanMassInsertTestCase.php');

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