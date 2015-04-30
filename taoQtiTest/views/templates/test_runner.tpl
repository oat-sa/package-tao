<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>QTI 2.1 Test Driver</title>
				<link rel="stylesheet" href="<?= Template::css('tao-main-style.css', 'tao') ?>"/>
                <link rel="stylesheet" href="<?= Template::css('test_runner.css') ?>"/>
                <script type="text/javascript" src="<?= Template::js('lib/require.js', 'tao') ?>"></script>
                
                <script type="text/javascript">
                (function(){
                    requirejs.config({waitSeconds : <?=get_data('client_timeout')?> });
                    require(['<?=get_data('client_config_url')?>'], function(){
                        require(['taoQtiTest/controller/runtime/testRunner', 'mathJax'], function(testRunner, MathJax){
                            if(MathJax){ 
                                MathJax.Hub.Configured();
                            }
                            testRunner.start(<?=json_encode(get_data('assessmentTestContext'), JSON_HEX_QUOT | JSON_HEX_APOS)?>);
                        });
                    });
                }());
                </script>
	</head>
	<body>
		<div id="runner" class="tao-scope">
			<div id="qti-actions">
				<div class="col-4" id="qti-test-context">
					<div id="qti-test-title"></div>
					<div id="qti-test-position"></div>
				</div>
				<div class="col-4" id="qti-test-time"></div>
				<div class="col-4" id="qti-test-progress">
					<div id="qti-progress-label"></div>
					<div id="qti-progressbar"></div>
				</div>
			</div>
			<div id="qti-content"></div>
			<div id="qti-navigation" class="grid-row">
				<div class="col-4" id="qti-tools"><button id="comment" class="btn-info"><span class="icon-document"></span><?= __("Comment"); ?></button></div>
				<div class="col-4" id="qti-flow"><button id="move-forward" class="btn-info qti-navigation" title="<?= __("Submit your responses and head for the next item."); ?>"><?= __("Next"); ?><span class="icon-forward r"></span><button id="move-end" class="btn-error qti-navigation" title="<?= __("Submit your responses and head for the end of the test."); ?>"><?= __("End Test"); ?><span class="icon-fast-forward r"></span></button><button id="move-backward" class="btn-info qti-navigation" title="<?= __("Submit your responses and head for the previous item."); ?>"><span class="icon-backward"></span><?= __("Previous"); ?></button><button id="skip" class="btn-warning qti-navigation" title="<?= __("Skip the current task and give an empty response."); ?>"><span class="icon-external"></span><?= __("Skip"); ?></button><button id="skip-end" class="btn-error qti-navigation" title="<?= __("Skip the current task, give an empty response and head for the end of the test."); ?>"><span class="icon-external"></span><?= __("Skip & End Test"); ?></button></div>
			</div>
		</div>
		<div id="qti-comment">
			<textarea></textarea>
			<button id="qti-comment-cancel" class="btn-info"><span class="icon-close"></span><?= __("Cancel"); ?></button>
			<button id="qti-comment-send" class="btn-info"><span class="icon-success"></span><?= __("Send"); ?></button>
		</div>
	</body>
</html>
