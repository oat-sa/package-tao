<style type="text/css">
	.ui-slider .ui-slider-handle{
		width: 5px;
		height: 3px;
	}

	.service-box{
		position:absolute;
		border:1px solid green;
		overflow:hidden;
		background-color:#A6FA87;
		cursor:pointer;
	}

	.service-box-highlight {
		border:1px solid green;
		overflow:visible;
		z-index:20;
		background-color:#82FF54;
	}

	span.service-box-highlight {
		border:3px solid green;
	}
	.service-box-current {
		border:1px solid red;
		z-index:10;
		background-color:#FA8794;
	}

	#servicePositionningPreview{
		top:20px;
		width:256px;
		height:200px;
		border:1px solid black;
	}

	#slider_container{
		width: 300px;
	}
</style>

<div id="interactiveService-form">

	<!--the form here:-->
	<?=get_data("formInteractionService")?>

	<div id="servicePositionningEditor" style="display:none">
		<div id="slider_container">
			<div id="slider_top_container">
				<label id="slider_top_label">Top shifting</label>
				<div id="slider_top"/>
			</div>

			<div id="slider_left_container">
				<label id="slider_left_label">Left shifting</label>
				<div id="slider_left"/>
			</div>

			<div id="slider_height_container">
				<label id="slider_height_label">Height</label>
				<div id="slider_height"/>
			</div>

			<div id="slider_width_container">
				<label id="slider_width_label">Width</label>
				<div id="slider_width"/>
			</div>
		</div>

		<div id="servicePositionningPreview" style="position:relative">
			<!--<div id='preview_truc1' style="left: 25%; top: 59%; width: 70%; height: 100%;">truc</div>-->
		</div>
	</div>

	<br/><br/>
	<input type="button" name="submit-interactiveService" id="submit-interactiveService" value="save"/>

</div>




<script type="text/javascript">

function drawServiceBox(serviceId, serviceLabel, style, eltClass){
	var prefix = 'preview_';
	var eltId = prefix+serviceId;
	var eltLabelId = eltId+'_label';

	if($('#'+eltId).length){
		//if element exists, delete it
		$('#'+eltId).remove();
	}

	var elt = $('<div id="'+eltId+'"></div>');
	// elt.css('position', 'absolute');
	elt.css('left', style.left+'%');
	elt.css('top', style.top+'%');
	// elt.css('overflow', 'hidden');
	elt.width(style.width+'%');
	elt.height(style.height+'%');
	elt.addClass('service-box');
	if(eltClass){
		elt.addClass(eltClass);
	}
	elt.attr('title', serviceLabel);
	elt.appendTo($("#servicePositionningPreview"));

	eltLabel = $('<span id="'+eltLabelId+'"/>').appendTo('#'+eltId);
	eltLabel.html(serviceLabel).position({
		my: "center center",
		at: "center center",
		of: '#'+eltId
	});

	return $('#'+eltId);

}

$(function(){


	var services = <?=json_encode(get_data("servicesData"))?>;
	var eltHeight = $("input[id=\'<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_HEIGHT)?>\']");
	var eltWidth = $("input[id=\'<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_WIDTH)?>\']");
	var eltTop = $("input[id=\'<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_TOP)?>\']");
	var eltLeft = $("input[id=\'<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_LEFT)?>\']");


	//continue only if the four elements exists
	if(eltHeight.length && eltWidth.length && eltTop.length && eltLeft.length && services.other){
		eltHeight.parent().hide();
		eltWidth.parent().hide();
		eltTop.parent().hide();
		eltLeft.parent().hide();

		$('#servicePositionningEditor').show();

		$("#slider_height, #slider_width, #slider_top, #slider_left").slider({
			orientation: 'horizontal',
			range: "min",
			max: 100,
			min: 0,
			step: 1,
			slide: function(event, ui){
				refreshPositionPreview(ui.handle.parentNode.id);
			},
			stop:function(event, ui){
				// refreshPositionPreview(ui.handle.parentNode.id);
			}
		});
		$("#slider_height").slider("value", eltHeight.val());
		$("#slider_width").slider("value", eltWidth.val());
		$("#slider_top").slider("value", eltTop.val());
		$("#slider_left").slider("value", eltLeft.val());

		refreshPositionPreview();

		// draw other services:
		for(serviceId in services.other){
			elt = drawServiceBox(serviceId, services.other[serviceId].label, services.other[serviceId], 'service-box-others');

			// console.log('elt id: ', elt.attr('id'));

			elt.hover(function(){
				// console.log('added class for', $(this).attr('id'));
				$(this).addClass('service-box-highlight');
				$(this).find('span').addClass('service-box-highlight');
			},function(){
				// console.log('removed class for', $(this).attr('id'));
				$(this).removeClass('service-box-highlight');
				$(this).find('span').removeClass('service-box-highlight');
			});

			elt.click(function(){
				//TODO: goto to the other service:
				//get the uri of the the service:
				id = $(this).attr('id').replace('preview_','');
				// console.log(id);
				// console.dir(services);
				if(services.other[id].uri){

					ActivityTreeClass.selectTreeNode(services.other[id].uri);
				}
			});

		}

	}

	function refreshPositionPreview(currentHandleId){
		var height = parseInt($("#slider_height").slider("value"));
		var width = parseInt($("#slider_width").slider("value"));
		var top = parseInt($("#slider_top").slider("value"));
		var left = parseInt($("#slider_left").slider("value"));

		if(currentHandleId){
			if((height+top)>100) {
				// console.log('height',height);
				// console.log('top',top);
				// console.log('sum', height+top);
				if(currentHandleId == 'slider_height'){
					top=100-height;
					$("#slider_top").slider("value", top);
				}else{
					//handle should be "slider_top"....
					height=100-top;
					$("#slider_height").slider("value", height);
				}
			}
			if((left+width)>100){
				// console.log('left', left);
				// console.log('width', width);
				if(currentHandleId == 'slider_width'){
					left=100-width;
					$("#slider_left").slider("value", left);
				}else{
					width=100-left;
					$("#slider_width").slider("value", width);
				}
			}
		}

		eltHeight.val(height);
		eltWidth.val(width);
		eltTop.val(top);
		eltLeft.val(left);

		drawServiceBox(
			services.current.id,
			services.current.label,
			{
				width: eltWidth.val(),
				height: eltHeight.val(),
				top: eltTop.val(),
				left: eltLeft.val()
			},
			'service-box-current'
		);
	}

	//get the initial selected value, if exists:
	var selectElement = $("select[id=\'<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION)?>\']");
	var initalSelectedValue = selectElement.val();

	// alert($("select[id=\'<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION)?>\']").html());
	selectElement.change(function(e){
		if(confirm(__("Sure?"))){

			// $("#<?=get_data("formId")?> :INPUT :gt(3)").attr("disabled","disabled");
			// selectElement.removeAttr("disabled");
			$("#<?=get_data("formId")?>").append("<p>"+__('reloading form...')+"</p>");

			//send the form
			$.ajax({
//				url: authoringControllerPath+'saveCallOfService',
				url: helpers._url('setServiceDefinition','ServiceCall','wfAuthoring'),
				type: "POST",
				data: $("#<?=get_data("formId")?>").serialize(),
				dataType: 'json',
				success: function(response){
					if(response.saved){
						//call ajax function again to get the new form
						ActivityTreeClass.selectTreeNode($("#callOfServiceUri").val());
					}else{
						$("#interactiveService-form").html("save failed:" + response);//debug
					}
				}
			});
		}else{
			//reset the option:
			$("#<?=get_data("formId")?> option[value='"+initalSelectedValue+"']").attr("selected","selected");
		}

	});

	$("#submit-interactiveService").click(function(){
		$.ajax({
			url: authoringControllerPath+'saveCallOfService',
			type: "POST",
			data: $("#<?=get_data("formId")?>").serialize(),
			dataType: 'json',
			success: function(response){
				if(response.saved){
					$("#interactiveService-form").html(__("interactive service saved"));
					refreshActivityTree();
				}else{
					$("#interactiveService-form").html("interactive service save failed:" + response);//debug
				}
			}
		});
	});

	//init switches:
	$(":input").each(function(i){
		if ($(this).attr('id')) {
			var startIndex = $(this).attr('id').indexOf('_choice_0');
			if (startIndex > 0) {
				var clazz = $(this).attr('id').substring(0,startIndex);
				initParameterSwitch(clazz);
			}
		}
	});
});

function initParameterSwitch(clazz){

	switchParameterType(clazz);
	$("input:radio[name="+clazz+"_choice]").change(function(){switchParameterType(clazz);});
	$("#"+clazz+"_var").change(function(){switchParameterType(clazz);});
}

function switchParameterType(clazz){

	var value = $("input:radio[name="+clazz+"_choice]:checked").val();

	if(value == 'constant'){
		enable($("[id="+clazz+"_constant]"));
		disable($("[id="+clazz+"_var]"));
		// console.log(clazz+"_var");
		// console.log($("input[name="+clazz+"_var]"));
	}else if(value == 'processvariable'){
		disable($("[id="+clazz+"_constant]"));
		enable($("[id="+clazz+"_var]"));
		// console.log('var');
	}else{
		disable($("[id="+clazz+"_constant]"));
		disable($("[id="+clazz+"_var]"));
		// console.log('oth');
	}
}

function disable(object){
	object.parent().attr("disabled","disabled");
	object.parent().hide();
	// console.log(object.attr('id'));
}

function enable(object){
	object.parent().removeAttr("disabled");
	object.parent().show();
}

</script>
