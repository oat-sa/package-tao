

ModeActivityLabel = new Object();
ModeActivityLabel.tempId = '';
ModeActivityLabel.on = function(options){
	ModeActivityLabel.tempId = '';
	if(options.activityId){
		var activityId = options.activityId;
		var elementTextbox = ModeActivityLabel.createLabelTextbox(activityId);
		ModeActivityLabel.tempId = activityId;
		elementTextbox.bind('keydown', {activityId: activityId}, function(e){
			if(e.keyCode==13){
				ModeActivityLabel.save(e.data.activityId);
			}
		});
	}
}

ModeActivityLabel.cancel = function(){
	var activityId = ModeActivityLabel.tempId;
	var returnValue = '';
	var targetId = ActivityDiagramClass.getActivityId('activity', activityId);
	var elementActivity = $('#'+targetId);//id of the activity
	
	if(elementActivity.length){
		//redraw actiivty:
		ActivityDiagramClass.removeActivity(activityId);
		ActivityDiagramClass.drawActivity(activityId);
		ActivityDiagramClass.setActivityMenuHandler(activityId);
		
		//return:
		returnValue = true;
	}
	
	ModeActivityLabel.tempId = '';
	return returnValue;
}

ModeActivityLabel.createLabelTextbox = function(activityId){

	
	var returnValue = null;
	var targetId = ActivityDiagramClass.getActivityId('activity', activityId);
	var elementActivity = $('#'+targetId);//id of the activity
	
	if(elementActivity.length){//TODO: check if activity exists in the global too?
	
		var elementLabelId = ActivityDiagramClass.getActivityId('activityLabel', activityId);
		var elementLabel = $('#'+elementLabelId);
		
		if(elementLabel.length){
			var currentLabel = elementLabel.text();
			//get from the model instead?:
			
			var elementTextbox = $('<input type="text" id="'+elementLabelId+'_input"/>');
			// elementTextbox.addClass('diagram_activity_label_input');
			elementTextbox.addClass(targetId);
			// elementLabel.empty();
			elementLabel.hide();
			elementTextbox.val(currentLabel);
			// elementTextbox.appendTo('#'+elementLabelId);
			elementTextbox.appendTo('#'+targetId);
			// elementTextbox.keyup(function(){
				// co
			
			
			if(currentLabel==ActivityDiagramClass.defaultActivityLabel || currentLabel==''){
				//focus
				elementTextbox.select();
			}
			elementTextbox.focus();
			
			returnValue = elementTextbox;
		}
	}
	
	return returnValue;
}

ModeActivityLabel.save = function(activityId){
	var returnValue = '';
	
	if(!activityId){
		if(ModeActivityLabel.tempId){
			var activityId = ModeActivityLabel.tempId;
		}else{
			throw 'no actiivty id set in the activity label editing mode';
		}
	}
	var targetId = ActivityDiagramClass.getActivityId('activity', activityId);
	var elementActivity = $('#'+targetId);//id of the activity
	
	if(elementActivity.length){
		var elementLabelId = ActivityDiagramClass.getActivityId('activityLabel', activityId);
		var elementLabel = $('#'+elementLabelId);
		if(elementLabel.length){
			//if the textbox exists:
			var elementTextbox = $('#'+elementLabelId+'_input');
			if(elementTextbox.length){
				var currentLabel = elementTextbox.val();
				if(currentLabel != ''){
					//set in the model:
					ActivityDiagramClass.activities[activityId].label = currentLabel;
					
					//redraw actiivty:
					ActivityDiagramClass.removeActivity(activityId);
					ActivityDiagramClass.drawActivity(activityId);
					ActivityDiagramClass.setActivityMenuHandler(activityId);
					
					var activityUri = ActivityDiagramClass.getActivityUri(activityId);
					var properties = {};
					properties[RDFS_LABEL] = currentLabel;
					properties.ajaxReturn = true;
					GatewayProcessAuthoring.saveActivityProperties(authoringControllerPath+"saveActivityProperty", activityUri, properties);
					
					returnValue = currentLabel;
				}
			}
		}
	}
	
	ModeActivityLabel.tempId = '';
	ModeController.setMode('ModeInitial');
	
	return returnValue;
}


ModeActivityLabel.destroyLabelTextbox = function(activityId){
	var returnValue = '';
	
	if(!activityId){
		if(ModeActivityLabel.tempId){
			var activityId = ModeActivityLabel.tempId;
		}else{
			throw 'no actiivty id set in the activity label editing mode';
		}
	}
	
	var targetId = ActivityDiagramClass.getActivityId('activity', activityId);
	var elementActivity = $('#'+targetId);//id of the activity
	
	if(elementActivity.length){
		var elementLabelId = ActivityDiagramClass.getActivityId('activityLabel', activityId);
		var elementLabel = $('#'+elementLabelId);
		if(elementLabel.length){
			//if the textbox exists:
			var elementTextbox = $('#'+elementLabelId+'_input');
			if(elementTextbox.length){
				var currentLabel = elementTextbox.val();
				if(currentLabel != ''){
					elementLabel.empty();
					elementLabel.html(currentLabel);
					// textCutter('#'+elementLabelId, 10);
					elementLabel.attr('title', currentLabel);
					
					elementLabel.removeClass('diagram_activity_label');
					elementLabel.addClass('diagram_activity_label');
					
					//set in the model:
					ActivityDiagramClass.activities[activityId].label = currentLabel;
					
					//return:
					returnValue = currentLabel;
				}
			}
		}
	}
	
	return returnValue;
}