<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao');
?>

<?=get_data('deliveryTree')?>

<div class="main-container large">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>
<?php
Template::inc('footer.tpl', 'tao')
?>