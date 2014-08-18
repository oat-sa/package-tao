<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>taoItems/views/css/preview.css"/>
<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>taoItems/views/css/quick-preview.css"/>
<style>


    iframe {
        border: none;
        min-width: 100% !important;
        min-height: 100% !important;

    }
</style>

<? if(has_data('previewUrl')): ?>
    <script>
        requirejs.config({
            config: {
                'taoItems/controller/preview/itemRunner': {
                    <?if(has_data('resultServer')):?>
                    resultServer: <?=json_encode(get_data('resultServer'))?>,
                    <?endif?>
                    previewUrl: <?=json_encode(get_data('previewUrl'))?>,
                    userInfoServiceRequestUrl: <?=json_encode(_url('getUserPropertyValues', 'ServiceModule', 'tao'))?>,
                    clientConfigUrl: '<?=get_data('client_config_url')?>',
                    context: 'preview26'
                }
            }
        });

        $(document).ready(function () {
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
<? endif ?>


<iframe id="preview-container" name="preview-container"></iframe>