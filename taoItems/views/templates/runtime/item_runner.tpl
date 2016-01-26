<?php
use oat\tao\helpers\Template;
?><!doctype html>
<html>
	<head>
            <link src="<?=Template::css('normalize.css', 'tao')?>"/>
            <script src="<?= Template::js('lib/require.js', 'tao')?>"></script>
            <script>
            (function(){
                var clientConfigUrl = '<?=get_data('client_config_url')?>';
                requirejs.config({waitSeconds : <?=get_data('client_timeout')?>});
                require([clientConfigUrl], function(){
                    require(['taoItems/controller/runtime/itemRunner'], function(itemRunner){
                        itemRunner.start({
                            resultServer : {
                                endpoint : <?=json_encode(get_data('resultServerEndpoint'));?>
                            },
                            itemService : {
                                module : 'taoItems/runtime/ItemServiceImpl'
                            },
                            itemId : <?=json_encode(get_data('itemId'));?>,
                            itemPath : <?=json_encode(get_data('itemPath'))?>,
                            clientConfigUrl : clientConfigUrl,
                            timeout : <?=get_data('client_timeout')?>
                        });
                    });
                });
            }());
            </script>
	</head>
	<body class="tao-item-scope">
        
    </body>
</html>
