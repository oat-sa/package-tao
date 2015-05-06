<div id="inferenceRule-form">
	
	<?=get_data("formInferenceRule")?>
	<input type="button" name="submit-inferenceRule" id="submit-inferenceRule-<?=get_data("formId")?>" value="save"/>
</div>

<script type="text/javascript">

$(function(){
		
	$("#submit-inferenceRule-<?=get_data("formId")?>").click(function(){
		$.ajax({
			url: authoringControllerPath+'saveInferenceRule',
			type: "POST",
			data: $("#<?=get_data("formId")?>").serialize(),
			dataType: 'json',
			success: function(response){
				if(response.saved){
					$("#inferenceRule-form").html("inference rule saved");
					refreshActivityTree();
				}else{
					$("#inferenceRule-form").html("inference rule failed to save:" + response);
				}
			}
		});
	});
	
	switchElseType();
	$("input:radio[name=else_choice]").change(switchElseType);
	
});

function switchElseType(){
	if($("input:radio[name=else_choice]:checked").val() == 'assignment'){
		enable($("#else_assignment"));
	}else{
		disable($("#else_assignment"));
	}
}

function disable(object){
	object.parent().attr("disabled","disabled");
	object.parent().hide();
}

function enable(object){
	object.parent().removeAttr("disabled");
	object.parent().show();
}
</script>