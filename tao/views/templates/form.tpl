<? include(TAO_TPL_PATH . 'form_context.tpl') ?>
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
<? include(TAO_TPL_PATH . 'footer.tpl');