<script type="text/javascript">
	var ctx_extension 	= "<?=get_data('extension')?>";
	var ctx_module 		= "<?=get_data('module')?>";
	var ctx_action 		= "<?=get_data('action')?>";

	$(function(){
		uiBootstrap.tabs.bind('tabsshow', function(event, ui) {
			if(ui.index>0){
				$("form[name=form_1]").html('');
			}
		});

		<?if(get_data('reload')):?>
			uiBootstrap.initTrees();
		<?endif;?>
		
		<?if(has_data('message')):?>
			helpers.createMessage("<?=get_data('message')?>");
		<?endif?>

	});

</script>