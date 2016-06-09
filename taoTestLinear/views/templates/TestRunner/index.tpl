<?php
use oat\tao\helpers\Template;
?><!doctype html>
<html class="no-js" lang="<?= tao_helpers_I18n::getLangCode() ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test</title>
    <link rel="stylesheet" href="<?= Template::css('tao-main-style.css', 'tao') ?>"/>
    <link rel="stylesheet" href="<?= Template::css('test-runner.css', 'taoQtiTest') ?>"/>

    <script src="<?= Template::js('lib/require.js', 'tao') ?>"></script>
    <script>
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

<body class="qti-test-scope">
<div class="section-container">
    <div class="plain action-bar content-action-bar horizontal-action-bar bottom-action-bar">
        <div class="control-box size-wrapper">
            <div class="rgt navi-box">
                <ul class="plain">
                    <li class="small btn-info action<?=get_data('previous')?'':' hidden';?>" title="<?= __("Submit your responses and head for the previous item."); ?>" data-control="previous">
                        <a class="li-inner" href="#">
                            <span class="icon-backward"></span>
                            <span class="text"><?= __("Previous"); ?></span>
                        </a>
                    </li>

                    <li class="small btn-info action" title="<?= __("Submit your responses and head for the next item."); ?>" data-control="next">
                        <a class="li-inner" href="#">
                            <span class="text"><?= __("Next") ?></span>
                            <span class="icon-forward r"></span>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</div>

</body>
</html>
