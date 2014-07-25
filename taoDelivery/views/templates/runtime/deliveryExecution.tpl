<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=tao_helpers_I18n::getLangCode()?>" lang="<?=tao_helpers_I18n::getLangCode()?>">
    <head>
        <title><?php echo __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></title>
        <link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/tao-main-style.css"/>
        <link rel="stylesheet" type="text/css" href="<?=ROOT_URL?>taoDelivery/views/css/runtime/deliveryExecution.css"/>
        <link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/custom-theme/jquery-ui-1.8.22.custom.css" />
        <script src="<?=TAOBASE_WWW?>js/lib/require.js"></script>
        <script type="text/javascript">
        (function(){
            require(['<?=get_data('client_config_url')?>'], function(){

                require(['taoDelivery/controller/runtime/deliveryExecution', 'serviceApi/ServiceApi', 'serviceApi/StateStorage', 'serviceApi/UserInfoService',], 
                    function(deliveryExecution, ServiceApi, StateStorage, UserInfoService){
                    
                    deliveryExecution.start({
                        serviceApi : <?=get_data('serviceApi')?>,
                        finishDeliveryExecution : '<?=_url('finishDeliveryExecution')?>',
                        deliveryExecution : '<?=get_data('deliveryExecution')?>'
                    });
                    
                });
            });
        }());
        </script>
</head>
<body class="tao-scope">
<?php if (get_data('showControls')) :?>
     <ul id="control" >
         
         <li class="actionControl">
                <a id="home" href="<?=_url('index', 'DeliveryServer')?>">
                    <span class="icon-delivery"></span>
                    <?php echo __("My Tests"); ?></a>
         </li>
            
         <li class="separator">|</li>
         <li class="infoControl">
                <span class="icon-test-taker"></span>
                <?php echo get_data('userLabel'); ?>
            </li>   
            
                     
            <li class="actionControl">
                <a id="logout" class="" href="<?=_url('logout', 'DeliveryServer')?>">
                    <span class="icon-logout"></span>
                    <?php echo __("Logout"); ?>
                </a>
            </li>
      </ul>
<?php endif; ?>
    <div id="content" class='ui-corner-bottom'>
        <div id="tools">
            <iframe id="iframeDeliveryExec" class="toolframe" frameborder="0" scrolling="no"></iframe>
        </div>
    </div>
    <div id="overlay"></div>
    <div id="loading"><div></div></div>
        <!-- End of content -->
</body>
</html>