<div class="ui-widget-content ui-corner-all" style="text-align:center;margin:30px auto 30px auto;width: 250px;padding:10px;font-size:16px;">
	<?=__('Delete')?> : <strong><?=get_data('label')?></strong><br /><br />
	<input id="instance-deleter" type='button' value="<?=__('Confirm')?>" /> 
</div>
<script type="text/javascript">
$(document).ready(function(){
	$("#instance-deleter").click(function(){
		url = '';
		if(ctx_extension){
			url = root_url + ctx_extension + '/' + ctx_module + '/';
		}
		url += 'delete';
		$.ajax({
			url: url,
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
</script>

<?include(TAO_TPL_PATH .'footer.tpl');?>