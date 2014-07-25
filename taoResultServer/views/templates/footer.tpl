<script type="text/javascript">
require(['jquery', 'uiBootstrap', 'helpers'], function($, uiBootstrap, helpers){

        uiBootstrap.tabs.bind('tabsshow', function(event, ui) {
                if(ui.index>0){
                        $("form[name=form_1]").html('');
                }
        });

        <?if(get_data('reload')):?>
                uiBootstrap.initTrees();
        <?endif;?>


        <?if(has_data('message')):?>
                helpers.createMessage(<?=json_encode(get_data('message'))?>);
        <?endif?>

	function setAuthoringModeButtons(){
		var $advContainer = $('#action_advanced_mode');
		var $simpleContainer = $('#action_simple_mode');
		if($advContainer.length && $simpleContainer.length){
			$advContainer.hide();
			$simpleContainer.hide();
			<?if(get_data('uri') && get_data('classUri')):?>
				<?if(get_data('authoringMode')=='advanced'):?>
					$simpleContainer.insertAfter($advContainer);
					$simpleContainer.show().off('click.taoDelivery').on('click.taoDelivery', function(e){
						if(!confirm('Are you sure to switch back to the simple mode? \n The delivery process will be linearized.')){
							return false;
						}else{
							return true;
						}
					});
				<?else:?>
					$advContainer.show();
				<?endif;?>
			<?else:?>
				$advContainer.show();
			<?endif;?>
		}
	}
});
</script>