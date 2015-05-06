<?php

use oat\tao\helpers\Template;
?>
<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
<?=get_data('formTitle')?>
</div>
    <?if(has_data('myForm')):?>
    <div class="ui-widget-content ui-corner-bottom">
    <?=get_data('myForm')?>
    </div>
<?php endif;?>
<br />

<div id="iframe-container"></div>

<script type="text/javascript">
    require(['jquery', 'lodash', 'helpers', 'uiForm'], function($, _, helpers, uiForm){

        var $form = $('#exportChooser'),
            $submitter = $form.find('.form-submiter'),
            $sent = $form.find(":input[name='" + $form.attr('name') + "_sent']");

        //by changing the format, the form is sent
        $form.on('change', ':radio[name=exportHandler]', function(){
            $sent.val(0).remove();//ensure that the export is not triggered
            uiForm.submitForm($form);
        });

        //overwrite the submit behaviour
        $submitter.off('click').on('click', function(e){

            if(parseInt($sent.val())){
                
                //prepare download params
                var $iframeContainer = $('#iframe-container'),
                    params = {},
                    instances = [];

                _.each($form.serializeArray(), function(param){
                    if(param.name.indexOf('instances_') === 0){
                        instances.push(param.value);
                    }else{
                        params[param.name] = param.value;
                    }
                });
                params.instances = instances;
                
                
                //build download url
                var url = helpers._url('<?=get_data('export_action')?>', '<?=get_data('export_module')?>', '<?=get_data('export_extension')?>', params);
                
                //use the iframe to embed download in the page
                var $iframe = $('<iframe>', {src : url}).hide();
                $iframeContainer.empty().append($iframe);

            }

        });

    });
</script>

<?php
Template::inc('footer.tpl', 'tao')
?>