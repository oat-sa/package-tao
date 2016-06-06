<?php
use oat\tao\helpers\Template;
?>
<div class="ui-widget-content ui-corner-all" style="text-align:center;margin:30px auto 30px auto;width: 250px;padding:10px;font-size:16px;">
	<?=__('Delete')?> : <strong><?=get_data('label')?></strong><br /><br />
	<input id="instance-deleter" type='button' value="<?=__('Confirm')?>" /> 
</div>
<script type="text/javascript">
require(['<?=get_data('client_config_url')?>'], function(){

    require(['jquery', 'context', 'helpers', 'i18n'], function($, context, helpers, __) {
        $("#instance-deleter").click(function(){
            url = '';
            $.ajax({
                url: helpers._url('delete', context.module, context.extension),
                type: "POST",
                data: {
                    uri: "<?=get_data('uri')?>",
                    classUri: "<?=get_data('classUri')?>"
                },
                dataType: 'json',
                success: function(response){
                    if(response.deleted){
                        $("#instance-deleter").hide();
                        helpers.createInfoMessage("<?=get_data('label')?> "+__(' has been deleted successfully'));
                    }
                }
            });
        });
	});
});
</script>
<?php
Template::inc('footer.tpl', 'tao')
?>
