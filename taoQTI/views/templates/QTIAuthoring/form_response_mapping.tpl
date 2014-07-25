<div id="qtiAuthoring_responseOptionsEditor" class="qti-authoring-form-container">

		<?=get_data('form')?>

</div>

<script type="text/javascript">
$(document).ready(function(){
	$('#qtiAuthoring_responseOptionsEditor').find('.form-submiter').click(function(){
		var $form = $('#qtiAuthoring_responseOptionsEditor').find('form');
		if($form.length){
			myInteraction.saveResponseOptions($form);
		}
	});

});
</script>