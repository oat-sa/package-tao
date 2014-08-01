<div class="data-container" style="width: 100%">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('LTI Link configuration')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<?=get_data('formContent')?>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-<?=get_data('formName')?>" type="button" value="<?=__('Save')?>" />
	</div>	
</div>
<script type="text/javascript">

require(['jquery', 'i18n', 'helpers', 'generis.tree.select'], function($, __, helpers) {
        $('#saver-action-<?=get_data('formName')?>').click(function(){
            var toSend = $('#<?=get_data('formName')?>').serialize();
            $.ajax({
                    url: "<?=get_data('saveUrl')?>",
                    type: "POST",
                    data: toSend,
                    dataType: 'json',
                    success: function(response) {
                        if (response.saved) {
                            helpers.createInfoMessage(__('Selection saved successfully'));
                        }
                    }
             });
        });
});
</script>