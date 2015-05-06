<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao');
?>
<div class="main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>
<script type="text/javascript">
    require(['jquery', 'helpers', 'uiBootstrap'], function($, helpers, uiBootstrap){
        var $tabs = uiBootstrap.tabs;
		
    	<?if(get_data('isPreviewEnabled') !== true):?>
            $tabs.tabs('disable', helpers.getTabIndexByName('items_preview'));
        <?endif?>
		
	<?if(!get_data('isAuthoringEnabled')):?>
    		var $authoringButton = $('input[name="<?=tao_helpers_Uri::encode(TAO_ITEM_CONTENT_PROPERTY)?>"]');
    		$authoringButton.hide();
		$tabs.tabs('disable', helpers.getTabIndexByName('items_authoring'));
	<?endif;?>

    });
</script>
<?php
if(!get_data('isDeprecated')) {
    Template::inc('footer.tpl');
}