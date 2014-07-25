<div id="consistencyRule-form">
	
	<?=get_data("formConsistencyRule")?>
	<input type="button" name="submit-consistencyRule" id="submit-consistencyRule-<?=get_data("formId")?>" value="save"/>
</div>

<script type="text/javascript">

$(function(){
		
	$("#submit-consistencyRule-<?=get_data("formId")?>").click(function(){
		$.ajax({
			url: authoringControllerPath+'saveConsistencyRule',
			type: "POST",
			data: $("#<?=get_data("formId")?>").serialize(),
			dataType: 'json',
			success: function(response){
				if(response.saved){
					$("#consistencyRule-form").html("consistency rule saved");
					refreshActivityTree();
				}else{
					// $("#consistencyRule-form").html("inference rule failed to save:" + response);
				}
			}
		});
	});
	
});

</script>