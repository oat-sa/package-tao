<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao')
?>
<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
	<?=get_data('formTitle')?>
</div>
<div id="form-container" class="ui-widget-content ui-corner-bottom">

	<?if(has_data('errorMessage')):?>
		<fieldset class='ui-state-error'>
			<legend><strong><?=__('Error')?></strong></legend>
			<?=get_data('errorMessage')?>
		</fieldset>
	<?endif?>

	<?=get_data('myForm')?>
</div>
<?php
Template::inc('footer.tpl', 'tao')
?>