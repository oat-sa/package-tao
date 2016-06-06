<?php
use oat\tao\helpers\Template;
?>
<script src="<?= Template::js('lib/require.js', 'tao') ?>"></script>
<script>
    (function() {
        requirejs.config({waitSeconds: <?=get_data('client_timeout')?>});
        require(['<?=get_data('client_config_url')?>'], function () {
            require([
                'taoDelivery/controller/runtime/service/deliveryExecution',
                'serviceApi/ServiceApi',
                'serviceApi/StateStorage',
                'serviceApi/UserInfoService'
            ],
            function(deliveryExecution, ServiceApi, StateStorage, UserInfoService, ui) {
                deliveryExecution.start({
                    serviceApi: <?=get_data('serviceApi')?>,
                    exitDeliveryExecution: '<?=get_data('returnUrl')?>',
                    finishDeliveryExecution: '<?=get_data('finishUrl')?>',
                    deliveryExecution: '<?=get_data('deliveryExecution')?>',
                    deliveryServerConfig: <?= json_encode(get_data('deliveryServerConfig')) ?>
                });
            });
        });
    }());
</script>
