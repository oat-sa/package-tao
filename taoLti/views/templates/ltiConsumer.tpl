<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>QTI LTI Launch Form</title>
        <script type="text/javascript" src="<?= Template::js('lib/require.js', 'tao')?>"></script>
        <script type="text/javascript">
        (function(){
            require(['<?=get_data('client_config_url')?>'], function(){
                require(['taoLti/controller/ltiConsumer'], function(controller){
                    controller.start();
                });
            });
        }());
        </script>
    </head>
    <body> 
        <div id="ltiLaunchFormSubmitArea">
            <form action="<?=get_data('launchUrl')?>"
                name="ltiLaunchForm" id="ltiLaunchForm" method="post"
                encType="application/x-www-form-urlencoded">
        <?php foreach (get_data('ltiData') as $key => $value) : ?>
            <input type="hidden" name="<?=$key?>" value="<?=$value?>"/>
        <?php endforeach; ?>    
            <input type="submit" value="Press to continue to external tool"/>
        </form>
        </div>
    </body>
</html>