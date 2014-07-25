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

require_once dirname(__FILE__) . '/../../generis/common/inc.extension.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';
define ('PATH_SAMPLE', dirname(__FILE__).'/samples/');
?>

<!DOCTYPE html>
<html>
<head>
	<title>QUnit Test Suite</title>
	<link rel="stylesheet" href="../../tao/test/qunit/qunit.css" type="text/css" media="screen">
	<!--<script type="text/javascript" src="https://getfirebug.com/firebug-lite.js"></script>-->
	<script type="application/javascript" src='../views/js/jquery-1.8.0.min.js'></script>
	<script type="application/javascript" src="../test/qunit/qunit.js"></script>
	<script type="application/javascript" src="../views/js/tao.ajaxWrapper.js"></script>

	<!-- -------------------------------------------------------------------------
	QTI DATA
	--------------------------------------------------------------------------->

	<script type="application/javascript">
        var testToRun = '*';
        //var testToRun = "Remote Parsing / Client Matching : Select Point";

        var testUnitFct = test;
        var asynctestUnitFct = asyncTest;
        test = function (label, func)
        {
            if (testToRun == "*"){
                testUnitFct (label, func);
            } else if (testToRun == label){
                testUnitFct (label, func);
            }
        }
        asyncTest = function (label, func)
        {
            if (testToRun == "*"){
                asynctestUnitFct (label, func);
            } else if (testToRun == label){
                asynctestUnitFct (label, func);
            }
        }

        isExpectedResult = function (result, success, type, data, message){
            equal(result.success, success, 'The request has been performed as expected');
            equal(result.type, type, 'Expected type');
            equal(result.message, message, 'Expected message');
            equal(result.data, data, 'Expected data');
        }

		test("Test default ajax response", function(){
            var successCallback = function(data, result){
                isExpectedResult(result, true, 'json', null, "");
            }

            tao.ajaxWrapper.ajax({
                'url'       : '<?=ROOT_URL?>/tao/test/ajaxWrapper/class.Foo.php?action=defaultAjaxResponse'
                , 'success' : successCallback
                , 'async'   : false
            });
        });

		test("Test classic ajax response", function(){
            var expectedData = "expected data";
            var expectedMessage = "expected message";
            var successCallback = function(data, result){
                isExpectedResult(result, true, 'json', expectedData, expectedMessage);
            }

            tao.ajaxWrapper.ajax({
                'url'       : '<?=ROOT_URL?>/tao/test/ajaxWrapper/class.Foo.php?action=jsonAjaxResponse'
                , 'success' : successCallback
                , 'async'   : false
            });
        });

		test("Test ajax wrapper with bad url", function(){
            tao.ajaxWrapper.ajax({
                'url'       : '<?=ROOT_URL?>/tao/test/ajaxWrapper/badUrl'
                , 'success' : function(data, result){
                    ok(false, "The success callback should not be fired");
                }
                , 'error' : function(result){
                    ok(true, "The error callback is well fired");
                }
                , 'async'   : false
            });
        });

		test("Test ajax wrapper with bad result", function(){
            var expectedData = "expected data";
            var expectedMessage = "expected message";
            tao.ajaxWrapper.ajax({
                'url'       : '<?=ROOT_URL?>/tao/test/ajaxWrapper/class.Foo.php?action=failedAjaxResponse'
                , 'success' : function(data, result){
                    ok(false, "The success callback should not be fired");
                }
                , 'error' : function(result){
                    ok(true, "The error callback is well fired");
                    isExpectedResult(result, false, 'json', expectedData, expectedMessage);
                }
                , 'async'   : false
            });
        });

		test("Test ajax expect exception", function(){
            var expectedMessage = "An expected test case exception occured";
            var expectedData = null;
            tao.ajaxWrapper.ajax({
                'url'       : '<?=ROOT_URL?>/tao/test/ajaxWrapper/class.Foo.php?action=exceptionAjaxResponse'
                , 'success' : function(data, result){
                    ok(false, "The success callback should not be fired");
                }
                , 'error' : function(result){
                    ok(true, "The error callback is well fired");
                    isExpectedResult(result, false, 'exception', expectedData, expectedMessage);
                }
                , 'async'   : false
            });
        });

        var globalVarToUpdate = 0;
        function defaultAjaxSuccessCallback(){
            globalVarToUpdate++;
        }

        test("Test classic ajax response with default callbacks", function(){
            tao.ajaxWrapper.addSuccessCallback(defaultAjaxSuccessCallback);
            tao.ajaxWrapper.addSuccessCallback(defaultAjaxSuccessCallback);

            var expectedData = "expected data";
            var expectedMessage = "expected message";
            var successCallback = function(data, result){
                isExpectedResult(result, true, 'json', expectedData, expectedMessage);
                globalVarToUpdate ++;
                equal(globalVarToUpdate, 3);
                //clean the wrapper
                 tao.ajaxWrapper.removeSuccessCallback(1);
                 tao.ajaxWrapper.removeSuccessCallback(1);
            }
            tao.ajaxWrapper.ajax({
                'url'       : '<?=ROOT_URL?>/tao/test/ajaxWrapper/class.Foo.php?action=jsonAjaxResponse'
                , 'success' : successCallback
                , 'async'   : false
            });
        });

	</script>

</head>
<body>
	<h1 id="qunit-header">QUnit Test Suite</h1>
	<h2 id="qunit-banner"></h2>
	<div id="qunit-testrunner-toolbar"></div>
	<h2 id="qunit-userAgent"></h2>
	<ol id="qunit-tests"></ol>
	<div id="qunit-fixture">test markup</div>
</body>
</html>