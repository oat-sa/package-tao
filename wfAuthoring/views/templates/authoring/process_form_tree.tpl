<?php
use oat\tao\helpers\Template;
?>

<?$sectionName=get_data("section");?>

<div id="<?=$sectionName?>-form">
	<?=get_data("formPlus")?>
	<input type="button" name="submit-<?=$sectionName?>" id="submit-<?=$sectionName?>" value="save" />
</div>

<script type="text/javascript" src="<?=Template::js('processResourceSelector.js', 'wfAuthoring')?>"></script>
<script type="text/javascript">
$(function(){
	//bloc specific to delivery authoring tool:

	var supportServiceUrlInput = "input[id=\'<?=tao_helpers_Uri::encode(PROPERTY_SUPPORTSERVICES_URL)?>\']";
	if($(supportServiceUrlInput).length){
		textBoxControl = $('<a href="#">'+__("Browse Tests")+'</a>');
		textBoxControl.click(function(){
			resourceSelector(supportServiceUrlInput,"tests");
			return false;
		});
		textBoxControl.insertAfter($(supportServiceUrlInput));
	}
});
</script>


<script type="text/javascript">
$(function(){
	//bloc to check if an identical code already exists
	var processVarCodeInput = "input[id=\'<?=tao_helpers_Uri::encode(PROPERTY_PROCESSVARIABLES_CODE)?>\']";
	if($(processVarCodeInput).length){
		$("#submit-<?=$sectionName?>").attr('disabled','disabled');
		$(processVarCodeInput).parent("div").append('<a id="codeCheckTrigger" href="#">'+__('check code')+'</a><br/>');

		$("#codeCheckTrigger").click(function(){
			$(this).parent("div").append('<span id="codeCheckMsg"/>');
			$("#codeCheckMsg").html(__("checking the code..."));

			var code = $(processVarCodeInput).val();
			var varUri = $("input[name=uri]:last").val();
			$.ajax({
				type: "POST",
				url: authoringControllerPath+"checkCode",
				dataType: "json",
				data: {code : code, varUri:varUri},
				success: function(response){
					if(response.exist){
						var message = __('the chosen code has already been used for the process variable')+' "'+response.label+'"';
						message += ', ';
						message += __('please choose another one');
						$("#codeCheckMsg").html(message);
						$("#codeCheckMsg").addClass('ui-state-highlight');
					}else{
						//enable submit
						$("#codeCheckMsg").append(__("ok")).fadeOut(4000, function(){ $(this).remove(); });
						$("#submit-<?=$sectionName?>").removeAttr('disabled');
					}
				}
			});
		});

	}


	//change to submit event interception would be "cleaner" than adding a button
	$("#submit-<?=$sectionName?>").click(function(){

		$.ajax({
			url: authoringControllerPath+'editInstance',
			type: "POST",
			data: $("#<?=$sectionName?>-form :input").serialize(),
			dataType: 'html',
			success: function(response){
				$("#<?=$sectionName?>-form").html(response);
				//reload the tree
				loadSectionTree("<?=$sectionName?>");
			}
		});
	});
});

</script>