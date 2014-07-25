<div id="qtiAuthoring_processingEditor_formContainer" class="ext-home-container">
	<p><?=get_data('warningMessage')?></p>
	<?=get_data('form')?>
</div>

<script type="text/javascript">
	var warningMessage = "<?=get_data('warningMessage')?>"
	if (warningMessage.length) {
		alert(warningMessage);
	}

	$(document).ready(function(){
		$("select#responseProcessingType").change(function(){
			myItem.saveItemResponseProcessing($('#ResponseProcessingForm'));
		});
	});
</script>