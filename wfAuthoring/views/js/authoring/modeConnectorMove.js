// alert('ModeConnectorMove loaded');

ModeConnectorMove = [];
ModeConnectorMove.tempId = '';
ModeConnectorMove.originalPosition = [];
ModeConnectorMove.originalArrows = [];

ModeConnectorMove.on = function(options){
	
	var connectorId = options.connectorId;
	if(!connectorId){
		throw 'no connector id found';
		return false;
	}
	ModeConnectorMove.originalArrows = [];

	//save a temporary object the initial position of the connector in case of cancellation:
	var connector = ActivityDiagramClass.connectors[connectorId];
	ModeConnectorMove.tempId = connectorId;
	ModeConnectorMove.originalPosition = connector.position;
	//then, set the inital arrows, in case of cancellation:
	var arrows = ModeConnectorMove.getArrowsConnectedToConnector(connectorId);
	for(arrowId in arrows){
		ModeConnectorMove.originalArrows[arrowId] = arrows[arrowId];
	}
	
	//destroy click event handler, to prevent activity menu from poping up:
	ActivityDiagramClass.unsetConnectorMenuHandler(connectorId);
	
	var containerId = ActivityDiagramClass.getActivityId('connector', connectorId);
	if(!$('#'+containerId).length){
		throw 'The connector element '+containerId+' does not exist.';
	}
	$('#'+containerId).draggable({
		containment: ActivityDiagramClass.canvas,
		scroll: true,
		drag: function(event, ui){
			var connectorId = ModeConnectorMove.tempId;
			$('#'+ActivityDiagramClass.getActivityId('connector', connectorId)).addClass('diagram_activity_drag');
			ModeConnectorMove.updateConnectedArrows(connectorId);
		},
		stop: function(event, ui){
			var connectorId = ModeConnectorMove.tempId;
			ModeConnectorMove.updateConnectedArrows(ModeConnectorMove.tempId);
			$('#'+ActivityDiagramClass.getActivityId('connector', connectorId)).removeClass('diagram_activity_drag');
		}
	});
	
	return true;
}

ModeConnectorMove.updateConnectedArrows = function(connectorId){
	if(ActivityDiagramClass.isConnector(connectorId)){
		var arrows = ModeConnectorMove.getArrowsConnectedToConnector(connectorId);
		for(arrowId in arrows){
			var arrow = ArrowClass.arrows[arrowId];
			ArrowClass.arrows[arrowId] = ArrowClass.calculateArrow($('#'+arrowId), $('#'+arrow.target), arrow.type, new Array(), false);
			ArrowClass.redrawArrow(arrowId);
		}
	}else{
		throw 'no valid connector id to update arrows';
	}
}


ModeConnectorMove.getArrowsConnectedToConnector = function(connectorId){
	var arrows = [];
	var bottomBorderPointIdPart = ActivityDiagramClass.getActivityId('connector',connectorId,'bottom');
	
	for(var arrowId in ArrowClass.arrows){
		var arrow = ArrowClass.arrows[arrowId];
		if(arrow.targetObject == connectorId || (arrowId.indexOf(bottomBorderPointIdPart))==0){
			arrows[arrowId] = arrow;
		}
	}
	
	return arrows;
}

ModeConnectorMove.save = function(){
	
	if(ModeConnectorMove.tempId){
		var connectorId = ModeConnectorMove.tempId;
		
		//get id of the arrows that are connected to the current moving activity:
		for(arrowId in ModeConnectorMove.originalArrows){
			//they are drawn properly, so just set their menu handler:
			ActivityDiagramClass.setArrowMenuHandler(arrowId);
		}
		
		//destroy draggable too:
		var containerId = ActivityDiagramClass.getActivityId('connector', connectorId);
		if(!$('#'+containerId).length){
			throw 'The connector element'+containerId+' do not exists.';
		}
		$('#'+containerId).draggable('destroy');
		
		//re-set the menu handler
		ActivityDiagramClass.setConnectorMenuHandler(connectorId);
		
		//send updated position data to the server and get  saving confirmation
		
		ActivityDiagramClass.connectors[connectorId].position = ActivityDiagramClass.getActualPosition($('#'+containerId));
		
		
		ActivityDiagramClass.saveDiagram();
	}
	
	ModeConnectorMove.tempId = null;
	ModeConnectorMove.originalArrows = [];
	ModeController.setMode('ModeInitial');
	
}

ModeConnectorMove.cancel = function(){

	if(ModeConnectorMove.tempId){
		var connectorId = ModeConnectorMove.tempId;
				
		//remove activity box
		ActivityDiagramClass.removeConnector(connectorId);
		
		//update real model with initial position
		ActivityDiagramClass.connectors[connectorId].position = ModeConnectorMove.originalPosition;
		
		//redraw activity+the connected arrows
		ActivityDiagramClass.drawConnector(connectorId);
		
		//arrows that are connected to that activity:
		for(arrowId in ModeConnectorMove.originalArrows){
			//replace the origine arrows in the array of actual arrows:
			ArrowClass.arrows[arrowId] = ModeConnectorMove.originalArrows[arrowId];
			ArrowClass.redrawArrow(arrowId, false, {setMenuHandler: true});
		}
		
		//re-set the menu handler
		ActivityDiagramClass.setConnectorMenuHandler(connectorId);
		
		//reset original arrow and class tempId property:
		ModeConnectorMove.originalArrows = [];
		ModeConnectorMove.tempId = null;
	}
	
	//reset ActivityDiagramClass.setArrowMenuHandler(arrowId);//for each arrow that has been redrawn
	// console.log(ActivityDiagramClass.currentMode);
	// console.log(ModeConnectorMove.tempId);
	// console.log(containerId);
}
