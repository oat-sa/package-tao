<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?=PRODUCT_NAME?> <?=TAO_VERSION?> Service</title>
	<link rel="shortcut icon" href="<?= Template::img('favicon.ico')?>" type="image/x-icon" />

    <?=tao_helpers_Scriptloader::render()?>

    <script id='amd-loader' 
        type="text/javascript" 
        src="<?=Template::js('lib/require.js', 'tao')?>">
    </script>
	<script type='text/javascript'>
    require(['<?=get_data('client_config_url')?>'], function(){
         'use strict';
         require(['main', 'module', 'context', 'helpers', 'uiForm'], function(main, module, context, helpers, uiForm){	
            var config = module.config();

            if(/edit|Edit|add/.test(context.action)){
                uiForm.initElements();
                uiForm.initOntoForms();
            } else if(/translate/.test(context.action)){
                uiForm.initElements();
                uiForm.initTranslationForm();
            } else {
                uiForm.initElements();
            }
            helpers._autoFx();

            <?php if(get_data('errorMessage')):?>
                helpers.createErrorMessage("<?=get_data('errorMessage')?>");
            <?php endif?>
        });
    });
	</script>

</head>
<body>
<?php if(get_data('message')):?>
	<div id="info-box" class="ui-widget-header ui-corner-all auto-slide">
		<span><?=get_data('message')?></span>
	</div>

<?php endif?>

<?php Template::inc(get_data('includeTemplate'), get_data('includeExtension')); ?>
	
</body>
</html>
