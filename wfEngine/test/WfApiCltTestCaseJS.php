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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
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
	<script type="application/javascript" src='../../tao/views/js/jquery-1.8.0.min.js'></script>
    <script type="application/javascript" src="../../tao/test/qunit/qunit.js"></script>

    <script type="application/javascript" src="../views/js/wfApi/src/constants.js"></script>
    <script type="application/javascript" src="../views/js/wfApi/src/context.js"></script>
    <script type="application/javascript" src="../views/js/wfApi/src/api.js"></script>
    <script type="application/javascript" src="../views/js/wfApi/src/wfApi.js"></script>
    <script type="application/javascript" src="../views/js/wfApi/src/ProcessDefinition.js"></script>
    <script type="application/javascript" src="../views/js/wfApi/src/ProcessExecution.js"></script>
    <script type="application/javascript" src="../views/js/wfApi/src/ActivityExecution.js"></script>
    <script type="application/javascript" src="../views/js/wfApi/src/Variable.js"></script>
    <script type="application/javascript" src="../views/js/wfApi/src/RecoveryContext.js"></script>

	<!-- -------------------------------------------------------------------------
	QTI DATA
	--------------------------------------------------------------------------->

	<script type="application/javascript">
	$(function(){
		var processDefinitionUri = null;
		var processExecutionUri = null;
        var testToRun = '*';
        //var testToRun = "Remote Parsing / Client Matching : Select Point";

        var testUnitFct = test;
        var asynctestUnitFct = asyncTest;
        test = function (label, func) {
            if (testToRun == "*"){
                testUnitFct (label, func);
            } else if (testToRun == label){
                testUnitFct (label, func);
            }
        };
        asyncTest = function (label, func) {
            if (testToRun == "*"){
                asynctestUnitFct (label, func);
            } else if (testToRun == label){
                asynctestUnitFct (label, func);
            }
        };


/* ****************************************************************************
	Test the wfEngine API by :
    1. create the process definition sample
    2. instantiate the process execution
    3. go to the next activity
    4. pause the process
	5. resume the process
	6. go to the previous acitivty
	7. cancel the process
	8. delete the process
	9. delete the process definition sample
******************************************************************************* */

		// CREATE PROCESS DEFINITION SAMPLE
        asyncTest('Create process definition sample', function(){
			$.ajax({
				'url' : root_url+'/wfEngine/test/wfApi/createProcessSamples.php?create=1'
				, type : 'GET'
                , async : true
				, 'dataType' : 'json'
				, 'success' : function(response){
					processDefinitionUri = response[0];
					ok(true, 'The sample process definition has been created');
					start();
				}, 'error' : function(){
					ok(false, 'Unable to create the sample process definition');
				}
			});
        });

		// Instantiate new process
        asyncTest('Instantiate the process definition sample', function(){
			wfApi.ProcessDefinition.initExecution(processDefinitionUri, function(data){
				processExecutionUri = data['processExecutionUri'];
				ok(true, 'The process execution has been instantiated (process definition : '+processDefinitionUri+', process execution : '+processExecutionUri+')');
				start();
			}
			, function(){
				ok(false, 'Unable to instantiate the process execution (process definition : '+processDefinitionUri+')');
				start();
			});
        });

		// next process
        asyncTest('Next the process execution sample', function(){
			wfApi.ProcessExecution.next(processExecutionUri, function(data){
				ok(true, 'The cursor has been moved to the next activity (process execution : '+processExecutionUri+')');
				start();
			}
			, function(){
				ok(false, 'Unable to go to the next activity (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// Pause the process
        asyncTest('Pause the process definition sample', function(){
			wfApi.ProcessExecution.pause(processExecutionUri, function(data){
				ok(true, 'The process execution has been paused  (process execution : '+processExecutionUri+')');
				start();
			}
			, function(){
				ok(false, 'Unable to pause the process execution (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// Resume the process
        asyncTest('Resume the process definition sample', function(){
			wfApi.ProcessExecution.resume(processExecutionUri, function(data){
				ok(true, 'The process execution has been resumed (process execution : '+processExecutionUri+')');
				start();
			}
			, function(){
				ok(false, 'Unable to resume the process execution (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// previous process
        asyncTest('Previous the process execution sample', function(){
			wfApi.ProcessExecution.previous(processExecutionUri, function(data){
				ok(true, 'The cursor has been moved to the previous activity (process execution : '+processExecutionUri+')');
				start();
			}
			, function(){
				ok(false, 'Unable to go to the previous activity (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// Cancel process
        asyncTest('Cancel the process execution sample', function(){
			wfApi.ProcessExecution.cancel(processExecutionUri, function(data){
				ok(true, 'The process has been canceled (process execution : '+processExecutionUri+')');
				start();
			}, function(){
				ok(false, 'Unable to cancel the process (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// Delete process
        asyncTest('Delete the process execution sample', function(){
			wfApi.ProcessExecution.delete(processExecutionUri, function(data){
				ok(true, 'The process has been deleted (process execution : '+processExecutionUri+')');
				start();
			}, function(){
				ok(false, 'Unable to delete the process (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// DELETE PROCESS DEFINITION SAMPLE
        asyncTest('Clean process definition sample', function(){
			$.ajax({
				'url' : root_url+'/wfEngine/test/wfApi/createProcessSamples.php?clean=1'
				, type : 'GET'
                , async : true
				, 'dataType' : 'json'
				, 'success' : function(response){
					ok(true, 'The sample process definition has been cleaned');
					start();
				}, 'error' : function(){
					ok(false, 'Unable to clean the sample process definition');
				}
			});
        });

/* ****************************************************************************
	Test the wfEngine API by :
    1. create the process definition sample
    2. instantiate the process execution
    3. go to previous (expect an error)
	4. go over the last activities (expect an error)
	5. go to previous (expect an error, the process is finished)
	6. cancel the process execution (we can cancel an already canceled activity)
	7. delete the process execution
	8. Try to pause (expect an error)
	9. Try to resume (expect an error)
	10. Try to go to next (expect an error)
	11. Try to go to previous (expect an error)
	12. delete the process definition sample
******************************************************************************* */

		// CREATE PROCESS DEFINITION SAMPLE
        asyncTest('Create process definition sample (2)', function(){
			//coment
			$.ajax({
				'url' : root_url+'/wfEngine/test/wfApi/createProcessSamples.php?create=1'
				, type : 'GET'
                , async : true
				, 'dataType' : 'json'
				, 'success' : function(response){
					processDefinitionUri = response[0];
					ok(true, 'The sample process definition has been created');
					start();
				}, 'error' : function(){
					ok(false, 'Unable to create the sample process definition');
				}
			});
        });

		// Instantiate new process
        asyncTest('Instantiate the process definition sample', function(){
			wfApi.ProcessDefinition.initExecution(processDefinitionUri, function(data){
				processExecutionUri = data['processExecutionUri'];
				ok(true, 'The process execution has been instantiated  (process definition : '+processDefinitionUri+')');
				start();
			}
			, function(){
				ok(false, 'Unable to instantiate the process execution (process definition : '+processDefinitionUri+')');
				start();
			});
        });

		// previous process
        asyncTest('Previous the process execution sample', function(){
			// here we expect false
			wfApi.ProcessExecution.previous(processExecutionUri
			, function(){
				ok(false, 'The cursor has been moved to the previous activity (process execution : '+processExecutionUri+')');
				start();
			}
			, function(data){
				ok(true, 'Unable to go to the previous activity (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// go over the last activity
        asyncTest('Go over the last process\' activity', function(){
			for(var i =0; i<4; i++){
				// here we expect false
				wfApi.ProcessExecution.next(processExecutionUri
				, function(){
					ok(true, 'The cursor has been moved to the next activity (process execution : '+processExecutionUri+')');
				}
				, function(data){
					ok(false, 'Unable to go to the next activity (process execution : '+processExecutionUri+')');
				},
				{'async':true});
			}

			// here we expect false
			wfApi.ProcessExecution.next(processExecutionUri
			, function(){
				ok(false, 'The cursor has been moved to the next activity (process execution : '+processExecutionUri+')');
				start();
			}
			, function(data){
				ok(true, 'Unable to go to the next activity (process execution : '+processExecutionUri+')');
				start();
			},
			{'async':true});
        });

		// Previous process
        asyncTest('Previous the process execution sample', function(){
			wfApi.ProcessExecution.previous(processExecutionUri, function(data){
				ok(false, 'The cursor has been moved to the previous activity (process execution : '+processExecutionUri+')');
				start();
			}, function(){
				ok(true, 'Unable to move the cursor to the previous activity, the process is finished (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// Cancel process
        asyncTest('Cancel the process execution sample', function(){
			wfApi.ProcessExecution.cancel(processExecutionUri, function(data){
				ok(true, 'The process has been canceled. We can canceled an already finished process (process execution : '+processExecutionUri+')');
				start();
			}, function(){
				ok(false, 'Unable to cancel the process (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// Delete process
        asyncTest('Delete the process execution sample', function(){
			wfApi.ProcessExecution.delete(processExecutionUri, function(data){
				ok(true, 'The process has been deleted (process execution : '+processExecutionUri+')');
				start();
			}, function(){
				ok(false, 'Unable to delete the process, the process is finished (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// Pause process
        asyncTest('Pause the process execution sample', function(){
			wfApi.ProcessExecution.pause(processExecutionUri, function(data){
				ok(false, 'The process has been paused (process execution : '+processExecutionUri+')');
				start();
			}, function(){
				ok(true, 'Unable to pause the process, the process is deleted (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// Resume process
        asyncTest('Resume the process execution sample', function(){
			wfApi.ProcessExecution.resume(processExecutionUri, function(data){
				ok(false, 'The process has been resumed (process execution : '+processExecutionUri+')');
				start();
			}, function(){
				ok(true, 'Unable to resume the process, the process is deleted (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// Next process
        asyncTest('Next the process execution sample', function(){
			wfApi.ProcessExecution.next(processExecutionUri, function(data){
				ok(false, 'The cursor has been moved to the next activity (process execution : '+processExecutionUri+')');
				start();
			}, function(){
				ok(true, 'Unable to move the cursor to the next activity, the process is deleted (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// Previous process
        asyncTest('Previous the process execution sample', function(){
			wfApi.ProcessExecution.previous(processExecutionUri, function(data){
				ok(false, 'The cursor has been moved to the previous activity (process execution : '+processExecutionUri+')');
				start();
			}, function(){
				ok(true, 'Unable to move the cursor to the previous activity, the process is deleted (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// DELETE PROCESS DEFINITION SAMPLE
        asyncTest('Clean process definition sample', function(){
			$.ajax({
				'url' : root_url+'/wfEngine/test/wfApi/createProcessSamples.php?clean=1'
				, type : 'GET'
                , async : true
				, 'dataType' : 'json'
				, 'success' : function(response){
					ok(true, 'The sample process definition has been cleaned');
					start();
				}, 'error' : function(){
					ok(false, 'Unable to clean the sample process definition');
				}
			});
        });

/* ****************************************************************************
	Test the wfEngine API by :
    1. create the process definition sample
    2. instantiate the process execution
	3. go over the last activities (expect an error)
	4. Resume
	5. Go to previous
	6. Try to go to next
	7. cancel the process execution
	8. delete the process execution
	9. delete the process definition sample
******************************************************************************* */

		// CREATE PROCESS DEFINITION SAMPLE
        asyncTest('Create process definition sample (2)', function(){
			//coment
			$.ajax({
				'url' : root_url+'/wfEngine/test/wfApi/createProcessSamples.php?create=1'
				, type : 'GET'
                , async : true
				, 'dataType' : 'json'
				, 'success' : function(response){
					processDefinitionUri = response[0];
					ok(true, 'The sample process definition has been created');
					start();
				}, 'error' : function(){
					ok(false, 'Unable to create the sample process definition');
				}
			});
        });

		// Instantiate new process
        asyncTest('Instantiate the process definition sample', function(){
			wfApi.ProcessDefinition.initExecution(processDefinitionUri, function(data){
				processExecutionUri = data['processExecutionUri'];
				ok(true, 'The process execution has been instantiated  (process definition : '+processDefinitionUri+')');
				start();
			}
			, function(){
				ok(false, 'Unable to instantiate the process execution (process definition : '+processDefinitionUri+')');
				start();
			});
        });

		// go over the last activity
        asyncTest('Go over the last process\' activity', function(){
			for(var i =0; i<4; i++){
				// here we expect false
				wfApi.ProcessExecution.next(processExecutionUri
				, function(){
					ok(true, 'The cursor has been moved to the next activity (process execution : '+processExecutionUri+')');
				}
				, function(data){
					ok(false, 'Unable to go to the next activity (process execution : '+processExecutionUri+')');
				});
			}

			// here we expect false
			wfApi.ProcessExecution.next(processExecutionUri
			, function(){
				ok(false, 'The cursor has been moved to the next activity (process execution : '+processExecutionUri+')');
				start();
			}
			, function(data){
				ok(true, 'Unable to go to the next activity (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// Resume the process
        asyncTest('Resume the process definition sample', function(){
			wfApi.ProcessExecution.resume(processExecutionUri, function(data){
				ok(true, 'The process execution has been resumed (process execution : '+processExecutionUri+')');
				start();
			}
			, function(){
				ok(false, 'Unable to resume the process execution (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// previous process
        asyncTest('Previous the process execution sample', function(){
			wfApi.ProcessExecution.previous(processExecutionUri, function(data){
				ok(true, 'The cursor has been moved to the previous activity (process execution : '+processExecutionUri+')');
				start();
			}
			, function(){
				ok(false, 'Unable to go to the previous activity (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// next process
        asyncTest('Next the process execution sample', function(){
			wfApi.ProcessExecution.next(processExecutionUri, function(data){
				ok(true, 'The cursor has been moved to the next activity (process execution : '+processExecutionUri+')');
				start();
			}
			, function(){
				ok(false, 'Unable to go to the next activity (process execution : '+processExecutionUri+')');
				start();
			});
        });

		// Cancel process
        asyncTest('Cancel the process execution sample', function(){
			wfApi.ProcessExecution.cancel(processExecutionUri, function(data){
				ok(true, 'The process has been canceled (process execution : '+processExecutionUri+')');
				start();
			}, function(){
				ok(false, 'Unable to cancel the process (process execution : '+processExecutionUri+')');
				start();
			});
        });
/*
		// Delete process
        asyncTest('Delete the process execution sample', function(){
			wfApi.ProcessExecution.delete(processExecutionUri, function(data){
				ok(true, 'The process has been deleted (process execution : '+processExecutionUri+')');
				start();
			}, function(){
				ok(false, 'Unable to delete the process (process execution : '+processExecutionUri+')');
				start();
			});
        });
*/
		// DELETE PROCESS DEFINITION SAMPLE
        asyncTest('Clean process definition sample', function(){
			$.ajax({
				'url' : root_url+'/wfEngine/test/wfApi/createProcessSamples.php?clean=1'
				, type : 'GET'
                , async : true
				, 'dataType' : 'json'
				, 'success' : function(response){
					ok(true, 'The sample process definition has been cleaned');
					start();
				}, 'error' : function(){
					ok(false, 'Unable to clean the sample process definition');
				}
			});
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
