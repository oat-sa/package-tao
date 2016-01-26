<?php
use oat\tao\helpers\Template;
?>
<link rel="stylesheet" href="<?= Template::css('preview.css', 'taoItems') ?>" />
<style>


    iframe {
        border: none;
        min-width: 100% !important;
        min-height: 100% !important;

    }
</style>

<?php if(has_data('previewUrl')): ?>
    <script>
        requirejs.config({
            config: {
                'taoItems/controller/preview/itemRunner': {
                    <?php if(has_data('resultServer')):?>
                    resultServer: <?=json_encode(get_data('resultServer'))?>,
                    <?php endif?>
                    previewUrl: <?=json_encode(get_data('previewUrl'))?>,
                    userInfoServiceRequestUrl: <?=json_encode(_url('getUserPropertyValues', 'ServiceModule', 'tao'))?>,
                    clientConfigUrl: '<?=get_data('client_config_url')?>',
                    timeout : '<?=get_data('client_timeout')?>',
                    context: 'preview26'
                }
            }
        });
        require(['jquery'], function($){
            if ("<?= get_data('previewUrl')?>".indexOf('Qti') != -1) {

                $('#preview-submit-button')
                    .css('display', 'inline')
                    .off('click')
                    .on('click', function () {
                        $('#preview-container')[0].contentWindow.qtiRunner.validate();
                    });

            }
        });
    </script>
<?php endif ?>
