<?php
use oat\tao\helpers\Template;
use oat\tao\helpers\Layout;
use oat\tao\model\theme\Theme;

?><!doctype html>
<html class="no-js no-version-warning" lang="<?=tao_helpers_I18n::getLangCode()?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></title>
        <link rel="stylesheet" href="<?= Template::css('tao-main-style.css', 'tao')?>"/>
        <link rel="stylesheet" href="<?= Template::css('tao-3.css', 'tao')?>"/>
        <link rel="stylesheet" href="<?= Template::css('delivery.css', 'taoDelivery') ?>"/>
        <link rel="shortcut icon" href="<?= Template::img('favicon.ico', 'tao')?>"/>


        <link rel="stylesheet" href="<?= Layout::getThemeStylesheet(Theme::CONTEXT_FRONTOFFICE) ?>" />

        <?php if(get_data('jsBlock') === 'runtime') : ?>
            <script src="<?= Template::js('lib/require.js', 'tao')?>"></script>
            <script>
                (function(){
                    requirejs.config({waitSeconds : <?=get_data('client_timeout')?>});
                    require(['<?=get_data('client_config_url')?>'], function(){
                        require([
                            'taoDelivery/controller/runtime/deliveryExecution',
                            'serviceApi/ServiceApi',
                            'serviceApi/StateStorage',
                            'serviceApi/UserInfoService'
                        ],
                            function(deliveryExecution, ServiceApi, StateStorage, UserInfoService, ui){

                                deliveryExecution.start({
                                    serviceApi : <?=get_data('serviceApi')?>,
                                    exitDeliveryExecution : '<?=get_data('returnUrl')?>',
                                    finishDeliveryExecution : '<?=get_data('finishUrl')?>',
                                    deliveryExecution : '<?=get_data('deliveryExecution')?>',
                                    deliveryServerConfig : <?= json_encode(get_data('deliveryServerConfig')) ?>
                                });
                            });
                    });
                }());
            </script>
        <?php endif; ?>
    </head>
    <body class="delivery-scope">

        <div class="content-wrap<?php if (!get_data('showControls')) :?> no-controls<?php endif; ?>">

            <?php if (get_data('showControls')){
                Template::inc('DeliveryServer/blocks/header.tpl', 'taoDelivery');
            }?>

            <div id="feedback-box"></div>

            <?php /* actual content */
            Template::inc(get_data('content-template'), get_data('content-extension')); ?>
        </div>

        <?php if (get_data('showControls')){
            echo Layout::renderThemeTemplate(Theme::CONTEXT_FRONTOFFICE, 'footer');
        }?>
        <div class="loading-bar"></div>
    </body>
</html>
