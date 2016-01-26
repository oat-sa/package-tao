<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=__('QTI Item Runner')?></title>
    <style>
        html, body{
            height: 100%;
            margin: 0;
            padding: 0;
            font-size: 0;
        }
    </style>
    <script src="<?= Template::js('lib/require.js', 'tao') ?>"></script>
        <script>
        (function(){
            var clientConfigUrl = '<?=get_data('client_config_url')?>';
            requirejs.config({waitSeconds : <?=get_data('client_timeout')?>});
            require([clientConfigUrl], function(){
                require(['taoItems/controller/runtime/itemRunner'], function(itemRunner){
                    itemRunner.start({
                        resultServer    : {
                            endpoint : <?=json_encode(get_data('resultServerEndpoint'));?>,
                            module   : 'taoQtiItem/QtiResultServerApi'
                        },
                        itemService     : {
                            module      : 'taoQtiItem/runtime/QtiItemServiceImpl',
                            params  : {
                                contentVariables: <?=json_encode(get_data('contentVariableElements'))?>,
                                itemDataPath: <?=json_encode(get_data('itemDataPath'))?>
                            }
                        },
                        itemId          : <?=json_encode(get_data('itemId'));?>,
                        itemPath        : <?=json_encode(get_data('itemPath'))?>,
                        clientConfigUrl : clientConfigUrl,
                        timeout         : <?=get_data('client_timeout')?>
                    });
                });
            });
        }());
    </script>
</head>
<body class="tao-item-scope"></body>
</html>
