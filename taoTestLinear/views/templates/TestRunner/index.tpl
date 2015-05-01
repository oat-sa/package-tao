<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Test</title>
				<link rel="stylesheet" href="<?= Template::css('tao-main-style.css', 'tao') ?>"/>
                <script type="text/javascript" src="<?= Template::js('lib/require.js', 'tao') ?>"></script>
                
                <script type="text/javascript">
                    (function() {
                        requirejs.config({waitSeconds : <?=get_data('client_timeout')?> });
                        require(['<?=get_data('client_config_url')?>'], function(){
                            require(['taoTestLinear/testrunner'], function(testRunner){
                                testRunner.start(<?=json_encode(get_data('itemServiceApi'))?>);
                            });
                        });
                    }());
                </script>
	</head>
	<body>
		<div id="navigation" class="tao-scope">
			<div class="grid-row">
				<div class="col-4 rgt">
                    <button id="previous" class="btn-info <?=get_data('previous')?'':'hidden';?>" title="<?= __("Submit your responses and head for the previous item."); ?>"><span class="icon-backward"></span><?= __("Previous"); ?></button>
    				<button id="next" class="btn-info" title="<?= __("Submit your responses and head for the next item."); ?>"><?= __("Next"); ?><span class="icon-forward r"></span></button>
				</div>
			</div>
		</div>
	</body>
</html>
