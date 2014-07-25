<script type="text/javascript">
	$(function(){
		require(['require', 'jquery'], function (req, $) {
			var authoringIndex = helpers.getTabIndexByName('items_authoring');
			var previewIndex = helpers.getTabIndexByName('items_preview');
			
			function setAuthoringItemLabel(label){
				var authoringLabel = (label)?__('Authoring')+': '+label:__('Authoring');
				var previewLabel = (label)?__('Preview')+': '+label:__('Preview');
				$('a#items_authoring').html(authoringLabel).attr('title', authoringLabel);
				$('a#items_preview').html(previewLabel).attr('title', previewLabel);
			}
	
	<?if(get_data('uri') && get_data('classUri')):?>
		<?if(get_data('isAuthoringEnabled')):?>
			if(ctx_action != 'authoring'){
				uiBootstrap.tabs.tabs('url', authoringIndex, "<?=_url('authoring', 'Items', 'taoItems', array('uri' => get_data('uri'), 'classUri' => get_data('classUri')))?>");
				uiBootstrap.tabs.tabs('enable', authoringIndex);
			}
		<?endif;?>
        <?if(get_data('isPreviewEnabled')):?>
			if(ctx_action != 'preview'){
				uiBootstrap.tabs.tabs('url', previewIndex, 
				    <?= tao_helpers_Javascript::buildObject(
				        taoItems_models_classes_ItemsService::singleton()->getPreviewUrl(new core_kernel_classes_Resource(
                            tao_helpers_Uri::decode(get_data('uri'))
                        ))
			        )?>
			    );
				uiBootstrap.tabs.tabs('enable', previewIndex);
			}
        <?endif;?>    
		<?if(get_data('label')):?>
			setAuthoringItemLabel("<?=get_data('label')?>");
		<?endif;?>	
	<?else:?>
		setAuthoringItemLabel();
		if(ctx_action != 'authoring'){
			uiBootstrap.tabs.tabs('disable', authoringIndex);
		}
		if(ctx_action != 'preview'){
			uiBootstrap.tabs.tabs('disable', previewIndex);
		}
	<?endif?>
	<?if(get_data('reload')):?>
		uiBootstrap.initTrees();
	<?endif?>
	<?if(has_data('message')):?>
		helpers.createMessage("<?=get_data('message')?>");
	<?endif?>
		});
	});
</script>
