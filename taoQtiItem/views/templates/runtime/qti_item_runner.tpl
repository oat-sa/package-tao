<?php
use oat\tao\helpers\Template;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
            <script type="text/javascript" src="<?=Template::js('lib/require.js', 'tao')?>"></script>
            <script type="text/javascript">
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
	<body>
	</body>
</html>
