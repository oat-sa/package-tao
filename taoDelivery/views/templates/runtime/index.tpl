<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
      xml:lang="<?=tao_helpers_I18n::getLangCode()?>"
      lang="<?=tao_helpers_I18n::getLangCode()?>">
    <head>
        <title><?php echo __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></title>
        <link rel="stylesheet" type="text/css" href="<?= Template::css('tao-main-style.css', 'tao')?>"/>
        <link rel="stylesheet" type="text/css" href="<?= Template::css('tao-3.css', 'tao')?>"/>
        <link rel="stylesheet" type="text/css" href="<?= Template::css('runtime/index.css', 'taoDelivery') ?>"/>
        <link rel="stylesheet" type="text/css" href="<?= Template::css('custom-theme/jquery-ui-1.8.22.custom.css', 'tao') ?>"/>
        <script id='amd-loader' 
                type="text/javascript" 
                src="<?= Template::js('lib/require.js', 'tao')?>" 
                data-main="<?= Template::js('controller/runtime/index')?>"
        data-config="<?=get_data('client_config_url')?>"></script>

    </head>
    <body class="tao-scope">

  <div class="content-wrap">
        <ul id="control" class="dark-bar">
            <li class="infoControl">
                <span class="icon-test-taker"></span><?php echo get_data('login'); ?>
            </li>         
            <li class="actionControl">
                <a id="logout" class="" href="<?=_url('logout', 'DeliveryServer')?>"><span class="icon-logout"></span><?php echo __("Logout"); ?></a>
            </li>
        </ul>


        <div id="content">
            <h1>
                 <span class="icon-delivery"></span>
                <?=__("My Tests");?>
            </h1>
             <?php if(count(get_data('startedDeliveries')) > 0) : ?>
            <div class="header">
                <?php echo __("Paused Tests"); ?> 
                <span class="counter">(<?php echo count($startedDeliveries); ?>)</span>
            </div>
            <div class="deliveries resume">

                <?php foreach ($startedDeliveries as $deliveryExecution): ?>

                <div class="tile clearfix">
                    
                    <div class="tileLabel">
                        <?= _dh($deliveryExecution->getLabel()) ?>
                    </div>
                    <div class="tileDetail">
                        <?php echo __("Started at "); ?><?php echo tao_helpers_Date::displayeDate($deliveryExecution->getStartTime()); ?>
                    </div>
          
                    <a class="btn-info small rgt" href="<?=_url('runDeliveryExecution', 'DeliveryServer', null, array('deliveryExecution' => $deliveryExecution->getIdentifier()))?>">
                            <?php echo __("Resume"); ?><span class="icon-continue r"></span>
                  
                    </a>
                </div>

                <?php endforeach;  ?>
            </div>
            <?php endif; ?>
       
        <?php if(count(get_data('availableDeliveries')) > 0) : ?>
            <div class="header">
                <?php echo __("Assigned Tests"); ?> <span class="counter">(<?php echo count($availableDeliveries); ?>)</span>
            </div>
            <div class="deliveries start">
                <?php foreach($availableDeliveries as $delivery) : ?>
                
                        
                
                    <div class="tile clearfix">
                        <div class="tileLabel">
                            <?= _dh($delivery["compiledDelivery"]->getLabel()) ?>
                        </div>
                         <div class="tileDetail">
                        <?php if ($delivery["settingsDelivery"][TAO_DELIVERY_START_PROP] != "") {?>
                            Available from <?php echo tao_helpers_Date::displayeDate(@$delivery["settingsDelivery"][TAO_DELIVERY_START_PROP]); ?>
                        <?php }?>
                        <?php if ($delivery["settingsDelivery"][TAO_DELIVERY_END_PROP] != "") {?>
                            <br/>until <?php echo tao_helpers_Date::displayeDate($delivery["settingsDelivery"][TAO_DELIVERY_END_PROP]); ?>
                        <?php }?>
                          </div>

                         <div class="tileDetail">
                            <?php echo __('Attempt(s)');?> [ <?php echo $delivery["settingsDelivery"]["TAO_DELIVERY_USED_TOKENS"]; ?> / <?php echo ($delivery["settingsDelivery"][TAO_DELIVERY_MAXEXEC_PROP]!=0) ? $delivery["settingsDelivery"][TAO_DELIVERY_MAXEXEC_PROP] : __('Unlimited'); ?> ]
                         </div>
                          
                          <a accesskey="" class="btn-info small rgt <?= ($delivery["settingsDelivery"]["TAO_DELIVERY_TAKABLE"]) ? "" : "disabled" ?>"
                                   href="<?=($delivery["settingsDelivery"]["TAO_DELIVERY_TAKABLE"]) ? _url('initDeliveryExecution', 'DeliveryServer', null, array('uri' => $delivery["compiledDelivery"]->getUri())) : '#'?>" >
                                   <?php echo __("Start"); ?><span class="icon-play r" ></span> 
                          </a>
                     </div>
                 
            <?php endforeach;  ?>

        </div>
        <?php endif; ?>
        
        
        
        
        
        <!-- End of New Processes -->
        <!--
        <?php if(count(get_data('finishedDeliveries')) > 0) : ?>
                 <div class="header">
                <?php echo __("Completed Tests"); ?> <span class="counter">(<?php echo count($finishedDeliveries); ?>)</span>
                 </div>
                <div id="old_process" class="deliveries finished">
                    <ul>
                    <?php foreach($finishedDeliveries as $delivery) : ?>
                    <li>
                            <?= _dh($delivery->getLabel()) ?>
                    </li>
                    <?php endforeach;  ?>
                    </ul>
                </div>
        <?php endif; ?>
        !-->
    </div>
                           <div id="footer" style="clear: both; height: 30px;">
    </div>
    <!-- End of content -->
    </div>
<!-- /content-wrap -->
<?php
Template::inc('footer.tpl', 'tao')
?>
</body>
</html>
