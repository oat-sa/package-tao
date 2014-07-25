<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao');
?>
<link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/form_test.css" />
<?if(has_data('authoring')):?>
<div id="test-left-container">
	<?= get_data('authoring')?>
	<div class="breaker"></div>
</div>
<?endif;?>
<div class="main-container<?if(has_data('authoring')):?> medium<?endif;?>" id="test-main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>
<?php 
Template::inc('footer.tpl', 'tao');
?>
