<div id="connector-form">

	<?=get_data("formConnector")?>
	<input type="button" name="submit-connector" id="submit-connector-<?=get_data("formId")?>" value="save"/>
</div>

<script type="text/javascript">

$(function(){

	//get the initial selected value, if exists:
	var selectElement = $("select[id=\'<?=tao_helpers_Uri::encode(PROPERTY_CONNECTORS_TYPE)?>\']");
	var initalSelectedValue = selectElement.val();

	if(initalSelectedValue != 'none' && selectElement.length){
		selectElement.change(function(e){
			if(confirm(__("Do you want to change the connector type? \n THe child connectors will be deleted."))){

				// $("#<?=get_data("formId")?> :INPUT :gt(3)").attr("disabled","disabled");
				$("select[id=\'<?=tao_helpers_Uri::encode(PROPERTY_CONNECTORS_TYPE)?>\']").removeAttr("disabled");
				$("#<?=get_data("formId")?>").append("<p>reloading form...</p>");

				//send the form
				$.ajax({
					url: authoringControllerPath+'saveConnector',
					type: "POST",
					data: $("#<?=get_data("formId")?>").serialize(),
					dataType: 'json',
					success: function(response){
						if(response.saved){
							var selectedNode = $("#connectorUri").val();
							$("#connector-form").html("connector saved");
							// initActivityTree();
							refreshActivityTree();
							ActivityTreeClass.selectTreeNode(selectedNode);

							ActivityDiagramClass.loadDiagram();
						}else{
							$("#connector-form").html("save failed:" + response);
						}
					}
				});
			}else{
				//reset the option:
				$("#<?=get_data("formId")?> option[value='"+initalSelectedValue+"']").attr("selected","selected");
			}
		});
	}



	if( $("#if").length ){
		//split connector:
		initActivitySwitch('then');
		initActivitySwitch('else');
	}else{
		initActivitySwitch('next');
		initActivitySwitch('join');
	}

	//add listener to check box (only if the type of connector is parallel):
	if(initalSelectedValue == '<?=tao_helpers_Uri::encode(INSTANCE_TYPEOFCONNECTORS_PARALLEL)?>'){

		checkboxeSelector = $("#<?=get_data('formId')?> input:checkbox[name^='parallel_activityUri']");
		checkboxeSelector.css({ 'position' : 'relative', 'top' : '3.5px'});
		checkboxeSelector.each(function(){

			var number = 9;
			var checked = $(this).attr('checked');
			var id = $(this).val();
			var input_id = id+'_num';
			var input_hidden_id = id+'_num_hidden';

			var selectNumElt = $('<select id="'+input_id+'"/>').insertAfter($(this));
			selectNumElt.css('margin',0);

			for(var i=1;i<number+1;i++){
				selectNumElt.append('<option value="'+i+'">'+i+'</option>');
			}

			var variables = eval(<?=get_data('variables')?>);
//			console.log(variables);
			for(var variablesUri in variables){
				selectNumElt.append('<option value="'+variablesUri+'">'+variables[variablesUri]+'</option>');
			}
			selectNumElt.val($("input[id='"+input_hidden_id+"']").val());

			selectNumElt.change(function(){
				$("input[id='"+$(this).attr('id')+"_hidden']").val( $(this).val() );
			});

			if(!checked){
				$("select[id='"+input_id+"']").hide();
			}

		});

		checkboxeSelector.click(function(){
			var checked = $(this).attr('checked');
			var id = $(this).val();
			var input_id = id+'_num';
			var input_hidden_id = id+'_num_hidden';

			if(checked){
				$("select[id='"+input_id+"']").val(1);
				$("select[id='"+input_id+"']").show();
				$("input[id='"+input_hidden_id+"']").val(1);
			}else{
				//hide the input:
				$("select[id='"+input_id+"']").hide();

				//set hidden input to 0:
				$("input[id='"+input_hidden_id+"']").val(0);
			}
		});

	}


	$("#submit-connector-<?=get_data('formId')?>").click(function(){
		$.ajax({
			url: authoringControllerPath+'saveConnector',
			type: "POST",
			data: $("#<?=get_data("formId")?>").serialize(),
			dataType: 'json',
			success: function(response){
				if(response.saved){
					var selectedNode = $("#connectorUri").val();
					$("#connector-form").html("connector saved");

					ActivityDiagramClass.loadDiagram();

					refreshActivityTree();

					ActivityTreeClass.selectTreeNode(selectedNode);

				}else{
					$("#connector-form").html("connector save failed:" + response);
				}
			}
		});

	});

	switchNotify();

});

function switchNotify(){

	if($("input.notify-element")){

		var notifyUserBlock = $("input.notify-user").parent('div');
		var notifyGroupBlock = $("input.notify-group").parent('div');

		notifyUserBlock.css({'display': 'none'});
		notifyGroupBlock.css({'display': 'none'});

		function checkNotifiedElement(elt){
			value = $(elt).val();
			if(value == "<?=get_data('notifyUserUri')?>"){
				if(elt.checked){
					notifyUserBlock.css({'display': 'block'});
				}
				else{
					notifyUserBlock.css({'display': 'none'});
				}
			}
			if(value == "<?=get_data('notifyRoleUri')?>"){
				if(elt.checked){
					notifyGroupBlock.css({'display': 'block'});
				}
				else{
					notifyGroupBlock.css({'display': 'none'});
				}
			}
		}
		$.each($("input.notify-element"), function(i, elt){
			checkNotifiedElement(elt);
		});
		$("input.notify-element").click(function(){
			checkNotifiedElement(this);
		});
	}

}


function initActivitySwitch(clazz){
	switchActivityType(clazz);
	if($("input:radio[name="+clazz+"_activityOrConnector]").length){
		$("input:radio[name="+clazz+"_activityOrConnector]").change(function(){switchActivityType(clazz);});
	}
	if($("#"+clazz+"_activityUri").length){
		$("#"+clazz+"_activityUri").change(function(){switchActivityType(clazz);});
	}
}

function switchActivityType(clazz){
	var value = $("input:radio[name="+clazz+"_activityOrConnector]:checked").val();
	if(value == 'connector'){
		disable($("#"+clazz+"_activityUri"));
		disable($("#"+clazz+"_activityLabel"));
		enable($("#"+clazz+"_connectorUri"));
	}else if(value == 'activity' || !$("input:radio[name="+clazz+"_activityOrConnector]").length){
		enable($("#"+clazz+"_activityUri"));
		disable($("#"+clazz+"_activityLabel"));
		if($("#"+clazz+"_activityUri").val() == 'newActivity'){
			enable($("#"+clazz+"_activityLabel"));
		}
		disable($("#"+clazz+"_connectorUri"));
	}else{
		disable($("#"+clazz+"_activityUri"));
		disable($("#"+clazz+"_activityLabel"));
		disable($("#"+clazz+"_connectorUri"));
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