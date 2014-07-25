//alert('ModeActivityMove loaded');

ModeActivityMove = [];
ModeActivityMove.tempId = '';
ModeActivityMove.originalPosition = [];
ModeActivityMove.originalArrows = [];

ModeActivityMove.on = function(options){
	
	var activityId = options.activityId;
	if(!activityId){
		throw 'no activity Id found';
		return false;
	}
	ModeActivityMove.originalArrows = [];

	//desactivate 'add activity' button(??)
	
	//save a temporary object the initial position of the activity in case of cancellation:
	var activity = ActivityDiagramClass.activities[activityId];
	ModeActivityMove.tempId = activityId;
	ModeActivityMove.originalPosition = activity.position;
	//then, set the inital arrows, in case of cancellation:
	var arrows = ModeActivityMove.getArrowsConnectedToActivity(activityId);
	for(arrowId in arrows){
		ModeActivityMove.originalArrows[arrowId] = arrows[arrowId];
	}
	
	//destroy click event handler, to prevent activity menu from poping up:
	ActivityDiagramClass.unsetActivityMenuHandler(activityId);
	
	//transform the activity to draggable (with itself as a helper opacity .7)
	var containerId = ActivityDiagramClass.getActivityId('container', activityId);
	if(!$('#'+containerId).length){
		throw 'The activity container '+containerId+' do not exists.';
	}
	$('#'+containerId).draggable({
		containment: ActivityDiagramClass.canvas,
		handle: '#'+ActivityDiagramClass.getActivityId('activity', activityId),
		scroll: true,
		drag: function(event, ui){
			var activityId = ModeActivityMove.tempId;
			//add transparent class:
			$('#'+ActivityDiagramClass.getActivityId('activity', activityId)).addClass('diagram_activity_drag');
			ModeActivityMove.updateConnectedArrows(activityId);
		},
		stop: function(event, ui){
			var activityId = ModeActivityMove.tempId;
			ModeActivityMove.updateConnectedArrows(activityId);
			$('#'+ActivityDiagramClass.getActivityId('activity', activityId)).removeClass('diagram_activity_drag');
		}
	});
	
	return true;
}

ModeActivityMove.updateConnectedArrows = function(activityId){
	if(ActivityDiagramClass.isActivity(activityId)){
		var arrows = ModeActivityMove.getArrowsConnectedToActivity(activityId);
		for(arrowId in arrows){
			var arrow = ArrowClass.arrows[arrowId];
			ArrowClass.arrows[arrowId] = ArrowClass.calculateArrow($('#'+arrowId), $('#'+arrow.target), arrow.type, new Array(), false);
			ArrowClass.redrawArrow(arrowId);
		}
	}else{
		throw 'no valid activity id to update arrows';
	}
}

ModeActivityMove.getArrowsConnectedToActivity = function(activityId){
	var arrows = [];
	var activityBottomBorderPointId = ActivityDiagramClass.getActivityId('activity',activityId,'bottom');
	for(var arrowId in ArrowClass.arrows){
		var arrow = ArrowClass.arrows[arrowId];
		if(arrow.targetObject == activityId || arrowId == activityBottomBorderPointId){
			arrows[arrowId] = arrow;
		}
	}
	
	return arrows;
}

ModeActivityMove.save = function(){
	
	if(ModeActivityMove.tempId){
		var activityId = ModeActivityMove.tempId;
		
		//get id of the arrows that are connected to the current moving activity:
		for(arrowId in ModeActivityMove.originalArrows){
			//they are drawn properly, so just set their menu handler:
			ActivityDiagramClass.setArrowMenuHandler(arrowId);
		}
		
		//destroy draggable too:
		var containerId = ActivityDiagramClass.getActivityId('container', activityId);
		if(!$('#'+containerId).length){
			throw 'The activity container '+containerId+' do not exists.';
		}
		$('#'+containerId).draggable('destroy');
		
		//re-set the menu handler
		ActivityDiagramClass.setActivityMenuHandler(activityId);
		
		//send updated position data to the server and get  saving confirmation
		ActivityDiagramClass.activities[activityId].position = ActivityDiagramClass.getActualPosition($('#'+containerId));
		ActivityDiagramClass.saveDiagram();
	}
	
	ModeActivityMove.tempId = null;
	ModeActivityMove.originalArrows = [];
	ModeController.setMode('ModeInitial');
	
}

ModeActivityMove.cancel = function(){

	if(ModeActivityMove.tempId){
		var activityId = ModeActivityMove.tempId;
				
		//remove activity box
		ActivityDiagramClass.removeActivity(activityId);
		
		//update real model with initial position
		ActivityDiagramClass.activities[activityId].position = ModeActivityMove.originalPosition;
		
		//redraw activity+the connected arrows
		ActivityDiagramClass.drawActivity(activityId);
		
		//arrows that are connected to that activity:
		var activityBottomBorderPointId = ActivityDiagramClass.getActivityId('activity',activityId,'bottom');
		for(arrowId in ModeActivityMove.originalArrows){
			//replace the origine arrows in the array of actual arrows:
			ArrowClass.arrows[arrowId] = ModeActivityMove.originalArrows[arrowId];
			ArrowClass.redrawArrow(arrowId, false, {setMenuHandler: true});
		}
		
		//re-set the menu handler
		ActivityDiagramClass.setActivityMenuHandler(activityId);
		
		//reset original arrow and class tempId property:
		ModeActivityMove.originalArrows = [];
		ModeActivityMove.tempId = null;
	}
	
	//reset ActivityDiagramClass.setArrowMenuHandler(arrowId);//for each arrow that has been redrawn
	// console.log(ActivityDiagramClass.currentMode);
	// console.log(ModeActivityMove.tempId);
	// console.log(containerId);
}
