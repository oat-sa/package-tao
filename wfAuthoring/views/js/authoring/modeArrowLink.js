// alert('ModeArrowLink loaded');


ModeArrowLink = new Object();
ModeArrowLink.targetObject = null;
ModeArrowLink.targetElement = null;
ModeArrowLink.arrowTipPosition = null;
ModeArrowLink.arrowType = null;
ModeArrowLink.connector = null;
ModeArrowLink.tempId = "defaultConnectorId";
ModeArrowLink.dropped = false;

ModeArrowLink.on = function(options){
	
	var connectorId = options.connectorId;
	var port = options.port;
	ModeArrowLink.connector = null;
	if(!connectorId||!port){
		throw 'no connector id or port found';
	}
	
	ModeArrowLink.connector = {
		"id": connectorId,
		"port": port
	};
	
	// var position = options.position;
	var arrowOriginEltId = ActivityDiagramClass.getActivityId('connector', connectorId, 'bottom', port);
	
	ModeArrowLink.tempId = arrowOriginEltId;
	
	//reset temp arrow array:
	ArrowClass.tempArrows = [];
	
	//remove original arrow from diagram, but do not delete it from the data!
	ArrowClass.removeArrow(arrowOriginEltId, false);
	
	//create a temporary arrow
	var tempArrow = ModeArrowLink.createDraggableTempArrow(arrowOriginEltId, options.position);
	
	//set droppable points:
	ModeArrowLink.activateAllDroppablePoints(connectorId);
	ModeArrowLink.dropped = false;
	
	return true;
}

ModeArrowLink.activateAllDroppablePoints = function(excludedConnectorId){

	for(connectorId in ActivityDiagramClass.connectors){
		if(excludedConnectorId == connectorId){
			continue;
		}
		ModeArrowLink.activateActivityDroppablePoints('connector', connectorId);
	}
	
	for(activityId in ActivityDiagramClass.activities){
		ModeArrowLink.activateActivityDroppablePoints('activity', activityId);
	}
	
}

ModeArrowLink.activateActivityDroppablePoints = function(type, id){
	if(type == 'activity' || type == 'connector'){
		ModeArrowLink.activateDroppablePoint(ActivityDiagramClass.getActivityId(type, id, 'top'));
		ModeArrowLink.activateDroppablePoint(ActivityDiagramClass.getActivityId(type, id, 'left'));
		ModeArrowLink.activateDroppablePoint(ActivityDiagramClass.getActivityId(type, id, 'right'));
	}
}

ModeArrowLink.deactivateAllDroppablePoints = function(){

	for(connectorId in ActivityDiagramClass.connectors){
		ModeArrowLink.deactivateActivityDroppablePoints('connector', connectorId);
	}
	
	for(activityId in ActivityDiagramClass.activities){
		ModeArrowLink.deactivateActivityDroppablePoints('activity', activityId);
	}
	
}

//TODO: put it in the arrowclass:
ModeArrowLink.createDraggableTempArrow = function(originId, position, options){
	
	//delete old one if exists
	// ArrowClass.tempArrows[originId] = {
		// 'targetObject': targetObjectId,
		// 'target': 'freeArrowTip',
		// 'type': 'top'
	// }
	
	//initialize the arrow tip position:
	var left = 0;
	var top = 0;
	if(position){
		if(position.left){
			left = position.left;
		}
		if(position.top){
			top = position.top;
		}
	}
	
	//add the arrow tip element
	var tipId = originId + '_tip';
	var left = Math.round(left)+'px';
	var top = Math.round(top)+'px';
	var elementTip = $('<div id="'+tipId+'"></div>');//put connector id here instead
	elementTip.addClass('diagram_arrow_tip');
	elementTip.css('position', 'absolute');
	elementTip.css('left', left);
	elementTip.css('top', top);
	elementTip.appendTo(ActivityDiagramClass.canvas);
	
	//save the initial position to allow reverting
	ModeArrowLink.arrowTipPosition = {left:left, top:top};
	
	//calculate the initial position & draw it
	var arrowType = 'top';
	var flex = null;
	var actualTarget = null;
	var targetObject = null;
	if(options){
		if(options.arrowType){
			arrowType = options.arrowType;
		}
		if(options.flex){
			flex = options.flex;
		}
		if(options.actualTarget){
			actualTarget = options.actualTarget;
		}
		if(options.targetObject){
			targetObject = options.targetObject;
		}
	}
	ArrowClass.tempArrows[originId] = ArrowClass.calculateArrow($('#'+originId),$('#'+tipId), arrowType, flex, true);
	
	ArrowClass.tempArrows[originId].actualTarget = actualTarget;
	ArrowClass.tempArrows[originId].targetObject = targetObject;
	
	ArrowClass.drawArrow(originId, {
		container: ActivityDiagramClass.canvas,
		arrowWidth: 2,
		temp: true
	});
	ArrowClass.getDraggableFlexPoints(originId);
	
	//transform to draggable
	$('#'+elementTip.attr('id')).draggable({
		snap: '.diagram_activity_border_point',
		snapMode: 'inner',
		snapTolerance: 30,
		drag: function(event, ui){
			
			// var position = $(this).position();
			// $("#message").html("<p> left: "+position.left+", top: "+position.top+"</p>");
			var id = $(this).attr('id');
			var arrowName = id.substring(0,id.indexOf('_tip'));
			
			//retrieve the arrow object in the temp arrows global array:
			var arrow = ArrowClass.tempArrows[arrowName];
			var actualTarget = arrow.actualTarget;
			
			//TODO edit 'type' at the same time:
			
			ArrowClass.tempArrows[arrowName] = ArrowClass.calculateArrow($('#'+arrowName), $(this), arrow.type, null, true);
			ArrowClass.tempArrows[arrowName].actualTarget = actualTarget;
			ArrowClass.redrawArrow(arrowName, true);
			
			//always reinitialize the dropped value to false
			ModeArrowLink.dropped = false;
		},
		containment: ActivityDiagramClass.canvas,
		stop: function(event, ui){
			var id = $(this).attr('id');
			var arrowName = id.substring(0,id.indexOf('_tip'));
			// getDraggableFlexPoints(arrowName);
			ArrowClass.redrawArrow(arrowName, true);
			ArrowClass.getDraggableFlexPoints(arrowName);
		}
	});
	
	if(options){
		if(options.revert){
			$('#'+elementTip.attr('id')).draggable( "option", "revert", function(socketObj){
				 //if false then no socket object drop occurred.
				 if(socketObj === false){
					//revert the position and redraw the arrow:
					var id = $(this).attr('id');
					var arrowName = id.substring(0,id.indexOf('_tip'));
					if(ModeArrowLink.arrowTipPosition){
						$(this).css('left', ModeArrowLink.arrowTipPosition.left);
						$(this).css('top', ModeArrowLink.arrowTipPosition.top);
					}
					ArrowClass.tempArrows[arrowName] = ArrowClass.calculateArrow($('#'+arrowName), $(this), null, null, true);
					ArrowClass.redrawArrow(arrowName, true);
					return true;
					
				 }else{
					//socket object was returned,
					//alert(socketObj.attr('id')); would work fine
					
					//
					ModeArrowLink.arrowTipPosition = {left: $(this).css('left'), top:$(this).css('top')};
					
					//return false so that the arrow does not revert
					return false;
				 }
			});
			$('#'+elementTip.attr('id')).draggable( "option", "revertDuration", 10 );
		}
	}
	
	return true;
}

ModeArrowLink.deactivateActivityDroppablePoints = function(type, id){
	if(type == 'activity' || type == 'connector'){
		ModeArrowLink.deactivateDroppablePoint(ActivityDiagramClass.getActivityId(type, id, 'top'));
		ModeArrowLink.deactivateDroppablePoint(ActivityDiagramClass.getActivityId(type, id, 'left'));
		ModeArrowLink.deactivateDroppablePoint(ActivityDiagramClass.getActivityId(type, id, 'right'));
	}
}

ModeArrowLink.deactivateDroppablePoint = function(DOMElementId){
	var elt = $('#'+DOMElementId);
	if(!elt.length){
		return null;
	}
	
	elt.droppable("destroy");
	elt.css('display','none');//TODO: css to be changed instead
}

ModeArrowLink.activateDroppablePoint = function(DOMElementId){

	var elt = $('#'+DOMElementId);
	if(!elt.length){
		return null;
	}
	
	elt.css('display','block');
	return elt.droppable({
		over: function(event, ui) {
			var id = $(this).attr('id');
			
			var startIndex = id.indexOf('_pos_');
			var newType = id.substr(startIndex+5); 
			var draggableId = ui.draggable.attr('id');
			var arrowName = draggableId.substring(0,draggableId.indexOf('_tip'));
			
			ArrowClass.tempArrows[arrowName].type = newType;
			ArrowClass.tempArrows[arrowName] = ArrowClass.calculateArrow($('#'+arrowName), $('#'+draggableId), newType, new Array(), true);
							
			//draw new arrow
			ArrowClass.removeArrow(arrowName, false, true);
			ArrowClass.drawArrow(arrowName, {
				container: ActivityDiagramClass.canvas,
				arrowWidth: 2,
				temp: true
			});
		},
		drop: function(event, ui) {
			//edit the arrow's 'end' property value and set it to this draggable, so moving the activity will make the update in position of the connected arrows easier
			var id = $(this).attr('id');
			var startIndex = id.indexOf('_pos_');
			var draggableId = ui.draggable.attr('id');
			var arrowName = draggableId.substring(0,draggableId.indexOf('_tip'));
			ArrowClass.tempArrows[arrowName].target = id;
			ArrowClass.tempArrows[arrowName].targetObject = ArrowClass.getTargetFromId(id);
			ArrowClass.tempArrows[arrowName].actualTarget = id;
			
			ModeArrowLink.targetObject = ArrowClass.getTargetFromId(id);
			ModeArrowLink.dropped = true;
		}
	});
	
}



ModeArrowLink.save = function(){
	if(ModeArrowLink.tempId){
		var connectorId = ModeArrowLink.tempId;
		
		// save the temporay arrow data into the actual arrows array:
		if(ArrowClass.tempArrows[connectorId]){
			if(!processUtil.isset(ModeArrowLink.targetObject) || !ModeArrowLink.dropped){
				// throw 'no arrow dropped';
				ModeArrowLink.cancel();
				return false;
			}else{
				ArrowClass.saveTemporaryArrowToReal(connectorId);
				
				//save the connection information in the client side model:
				var connectorId = ModeArrowLink.connector.id;
				ActivityDiagramClass.connectors[connectorId].port[ModeArrowLink.connector.port] = {
					"targetId":ModeArrowLink.targetObject,
					"multiplicity":1,
					"label": 'newly added'
				};
				
				//save the connector
				ActivityDiagramClass.saveConnector(connectorId);
				
				//save the diagram, so the position of the linked arrow are saved
				ActivityDiagramClass.saveDiagram();
			}
		}
	}
	
	//return to initial mode:
	ModeArrowLink.tempId = 'empty';
	ModeController.setMode('ModeInitial');
	return true;
}

ModeArrowLink.cancel = function(){
	if(ModeArrowLink.tempId){
		var connectorId = ModeArrowLink.tempId;
		
		if(ArrowClass.tempArrows[connectorId]){
			//delete the temp arrows and draw the actual one:
			ArrowClass.removeTempArrow(connectorId);
		}
				
		if(ArrowClass.arrows[connectorId]){
			//redraw the original arrow anyway
			ArrowClass.drawArrow(connectorId, {
				container: ActivityDiagramClass.canvas,
				arrowWidth: 2
			});
			
		}
	}
	
	ModeArrowLink.tempId = 'empty';
	return true;
}