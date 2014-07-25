<? include(TAO_TPL_PATH . 'form_context.tpl') ?>

<div class="main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		var deprecated = <?=get_data('deprecatedOptions')?>;
		$(':radio').each(function(){
			if($.inArray($(this).val(), deprecated) > -1){
				$(this).attr('disabled', 'true');
				$("label[for='"+$(this).attr('id')+"']").css('color', '#A0A0A0');
			}
		});
		
    	<?if(get_data('isPreviewEnabled') !== true):?>
            uiBootstrap.tabs.tabs('disable', helpers.getTabIndexByName('items_preview'));
        <?endif?>
		
		<?if(!get_data('isAuthoringEnabled')):?>
    		var $authoringButton = $('input[name="<?=tao_helpers_Uri::encode(TAO_ITEM_CONTENT_PROPERTY)?>"]');
    		$authoringButton.hide();
			uiBootstrap.tabs.tabs('disable', helpers.getTabIndexByName('items_authoring'));
		<?endif;?>

	});
</script>
<?if(!get_data('isDeprecated')):?>
	<? include('footer.tpl') ?>
<?endif?>