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
# include TestRunner
require 'PHPUnit/Autoload.php';


//create the test sutie
$testSuite = new PHPUnit_Framework_TestSuite('TAO Functional Test Suite (tao extension)');

$testSuite->addTestFile(dirname(__FILE__) . '/SeleniumBackendMainTestCase.php');

if (PHP_SAPI == 'cli') {
	PHPUnit_TextUI_TestRunner::run($testSuite);
}
else {
	// Do not output anything before the report in Server mode.
	// We only want the HTML.
	ob_start();
	
	$reportFileName = tempnam(sys_get_temp_dir(), 'tao');
	PHPUnit_TextUI_TestRunner::run($testSuite, array('junitLogfile' => $reportFileName,
													 'verbose', false));
	
	// Get the report and transform the XML to an HTML readable report.
	$xmlDoc = new DOMDocument();
	$xslDoc = new DOMDocument();
	
	$xmlDoc->load($reportFileName);
	$xslDoc->load(dirname(__FILE__) . '/includes/phpunit-noframes.xsl');
	
	$xsl = new XSLTProcessor();
	$xsl->importStyleSheet($xslDoc);
	
	ob_clean();
	
	echo $xsl->transformToXML($xmlDoc);
	unlink($reportFileName);
}
?>