// alert('ModeActivityMenu loaded');

ModeActivityMenu = [];
ModeActivityMenu.existingMenu = new Array();

ModeActivityMenu.on = function(options){
	if(options.target){
		switch(options.type){
			case 'activity':{
				ModeActivityMenu.createActivityMenu(options.target);
				break;
			}
			case 'connector':{
				ModeActivityMenu.createConnectorMenu(options.target);
				break;
			}
			case 'arrow':{
				ModeActivityMenu.createArrowMenu(options.target);
				break;
			}
			default:{
				throw 'wrong target type given';
			}
		}
	}
}

ModeActivityMenu.createActivityMenu = function(activityId){
	//create top menu for the activity: first, last, edit, delete
	var containerId = ActivityDiagramClass.getActivityId('activity', activityId, 'top');
	var actions = [];
	
	//if is not the first activity:
	// actions.push({
		// label: "Define as the first activity",
		// icon: img_url + "flag-green.png",
		// action: function(actId){
		// }
	// });
	// actions.push({
		// label: "Define as a last activity",
		// icon: img_url + "flag-red.png",
		// action: function(actId){
			// console.log('islast => ',actId);
		// }
	// });
	actions.push({
		label: "Move",
		icon: img_url + "move.png",
		action: function(actId){
			// ModeActivityMove.on(actId);
			ModeController.setMode('ModeActivityMove', {activityId: actId});
		}
	});
	actions.push({
		label: "Edit",
		icon: img_url + "pencil.png",
		action: function(actId){
			//select the tree node:
			ActivityTreeClass.selectTreeNode(ActivityDiagramClass.getActivityUri(actId));
		},
		autoclose: true
	});
	actions.push({
		label: "Delete",
		icon: img_url + "delete.png",
		action: function(actId){
			GatewayProcessAuthoring.deleteActivity(authoringControllerPath+"deleteActivity", ActivityDiagramClass.getActivityUri(actId));
		}
	});
	
	ModeActivityMenu.createMenu(
		activityId,
		containerId,
		'top',
		actions,
		{offset:10}
	);
	// ModeActivityMenu.existingMenu = new Array();
	ModeActivityMenu.existingMenu[containerId] = containerId;
	
	//depending if the activity had a connector or not, build the bottom menu:
	var connectorActions = [];
	
	//if the activity has a connector:
	var connectors = ActivityDiagramClass.getConnectorsByActivity(activityId);
	if(connectors.length>0){
		connectorActions.push({
			label: 'Delete connector and set as a final activity',
			icon: img_url + "flag-red.png",
			action: function(actId){
				//go deleting the activity's connector:
				var activityConnectorUri = ActivityDiagramClass.getActivityUri(connectors[0]);
				
				if(confirm(__('Set the activity as a final one will delete its following connector. \n Are you sure?'))){
					GatewayProcessAuthoring.deleteConnector(authoringControllerPath+"deleteConnector", activityConnectorUri);
				}
				
			}
		});
	}else{
		//adding a new connector linked connector:
		connectorActions.push({
			label: 'Add a connector',
			icon: img_url + "process_connector.png",
			action: function(actId, data){
				//go deleting the activity's connector:
				
				//TODO: make it global array, feedale by a call of server action:
				var typesOfConnectorSelectionActions = [];
				
				//create
				typesOfConnectorSelectionActions.push({
					label: 'sequence',
					icon: img_url + "process_connector.png",
					action: function(actId, data){
						GatewayProcessAuthoring.addConnector(
							authoringControllerPath+"addConnector",
							ActivityDiagramClass.getActivityUri(data.activityId),
							'sequence'
						);
					}
				});
				typesOfConnectorSelectionActions.push({
					label: 'conditional',
					icon: img_url + "process_connector.png",
					action: function(actId){
						GatewayProcessAuthoring.addConnector(
							authoringControllerPath+"addConnector",
							ActivityDiagramClass.getActivityUri(data.activityId),
							'conditional'
						);
					}
				});
				
				//parallel and join connector currently disabled
				/*
				typesOfConnectorSelectionActions.push({
					label: 'parallel',
					icon: img_url + "process_connector.png",
					action: function(actId){
						GatewayProcessAuthoring.addConnector(
							authoringControllerPath+"addConnector",
							ActivityDiagramClass.getActivityUri(data.activityId),
							'parallel'
						);
					}
				});
				typesOfConnectorSelectionActions.push({
					label: 'join',
					icon: img_url + "process_connector.png",
					action: function(actId){
						GatewayProcessAuthoring.addConnector(
							authoringControllerPath+"addConnector",
							ActivityDiagramClass.getActivityUri(data.activityId),
							'join'
						);
					}
				});
				*/
				
				ModeActivityMenu.createMenu(
					data.selfId,
					data.selfId,
					'bottom',
					typesOfConnectorSelectionActions,
					{"offset":10, "data":{"activityId":data.activityId}}
				);
				ModeActivityMenu.existingMenu[data.selfId] = data.selfId;
			},
			autoclose: false
		});
	}
	
	var pointId = ActivityDiagramClass.getActivityId('activity', activityId, 'bottom');
	ModeActivityMenu.createMenu(
		activityId,
		pointId,
		'bottom',
		connectorActions,
		{offset:10, "data":{"activityId": activityId}}
	);
	ModeActivityMenu.existingMenu[pointId] = activityId;
}

ModeActivityMenu.createConnectorMenu = function(connectorId){

	var topContainerId = ActivityDiagramClass.getActivityId('connector', connectorId, 'top');
	actions = [];
	actions.push({
		label: "Move",
		icon: img_url + "move.png",
		action: function(connectorId){
			ModeController.setMode('ModeConnectorMove', {"connectorId": connectorId});
		}
	});
	actions.push({
		label: "Edit",
		icon: img_url + "pencil.png",
		action: function(connectorId){
			ActivityTreeClass.selectTreeNode(ActivityDiagramClass.getActivityUri(connectorId));
		},
		autoclose: true
	});
	actions.push({
		label: "Delete",
		icon: img_url + "delete.png",
		action: function(connectorId){
			var connectorUri = ActivityDiagramClass.getActivityUri(connectorId);
			if(confirm(__('Please confirm the deletion of the connector: \n child connectors will be delete at the same time'))){
				GatewayProcessAuthoring.deleteConnector(authoringControllerPath+"deleteConnector", connectorUri);
			}
		}
	});
	ModeActivityMenu.createMenu(
		connectorId,
		topContainerId,
		'top',
		actions,
		{offset:10}
	);
	ModeActivityMenu.existingMenu[topContainerId] = connectorId;
	
	//get connector data:
	if(!ActivityDiagramClass.connectors[connectorId]){
		throw 'the connector does not exist: '+connectorId;
		return false;
	}
	var connector = ActivityDiagramClass.connectors[connectorId];
	
	//get the type of connector, and thus the name of all 'port'
	var connectorTypeDescription = ActivityDiagramClass.getConnectorTypeDescription(connector);
	if(connectorTypeDescription == null){
		throw 'wrong type of connector';
		return false;
	}
	
	
	//for each port i, get the id and create a menu with one single option (autoclose set to false)
	
	for(var i=0; i<connectorTypeDescription.portNumber; i++){
		var pointId = ActivityDiagramClass.getActivityId('connector', connectorId, 'bottom', i);
		ModeActivityMenu.createMenu(
			connectorId,
			pointId,
			'bottom',
			[{
				label: connectorTypeDescription.portNames[i],
				icon: img_url + "process_connector.png",
				action: function(connectId, data){
					//hightlight the current one:
					
					//check if an arrow (=connection) exists:
					if(ArrowClass.arrows[data.arrowId]){
						
						//the target and origin element of that arrow must exist!
						ModeController.setMode('ModeArrowEdit', {"arrowId": data.arrowId});
						
					}else{
					
						//remove top connector menu and other connector submenu:
						// ModeActivityMenu.removeMenu(data.topMenuId);//delete only the connectors's top menu
						if(ModeActivityMenu.existingMenu){
							for(containerId in ModeActivityMenu.existingMenu){
								if(containerId != data.menuContainerId){
									ModeActivityMenu.removeMenu(containerId);
									delete ModeActivityMenu.existingMenu[containerId];
								}
							}
						}
					
						//else, menu with 3 items: new activity, new connector, free connection
						var subActions = [];
						
						if(connectorTypeDescription.className != 'connector_parallel'){//if not parallel, allow new activity or connector to be created
							subActions.push({
								label: "New Activity",
								icon: img_url + "process_activity.png",
								action: function(id, data, e){
									ActivityDiagramClass.editConnectorPort(data.connectorId, data.port, 'newActivity');
									ActivityDiagramClass.saveConnector(data.connectorId);
								}
							});
							
						}
						
						if(connectorTypeDescription.className == 'connector_conditional'){
							//only conditionnal connector can have a connector of connector : then else if
							subActions.push({
								label: "New Connector",
								icon: img_url + "process_connector.png",
								action: function(id, data){
									ActivityDiagramClass.editConnectorPort(data.connectorId, data.port, 'newConnector');
									ActivityDiagramClass.saveConnector(data.connectorId);
								}
							});
						}
						
						subActions.push({
							label: "Link to",
							icon: img_url + "go-jump.png",
							action: function(id, data, e){
								// ModeArrowLink.on(data.connectorId, data.port);
								
								var canvasPosition = $(ActivityDiagramClass.canvas).offset();
								//real offset need to be calculated:
								var position = {
									left:e.pageX - canvasPosition.left + ActivityDiagramClass.scrollLeft,
									top:e.pageY - canvasPosition.top + ActivityDiagramClass.scrollTop
								};
								ModeController.setMode('ModeArrowLink', {"connectorId": connectorId, "port":data.port, "position":position});
							}
						});
						
						
						//get connectorId and port out of arrowId:
						var submenuConnectorId = '';
						var submenuPort = '';
						var indexConnector = data.arrowId.indexOf('connector_');
						var indexPort = data.arrowId.indexOf('_pos_bottom_port_');
						if(indexConnector==0 && indexPort){
							submenuConnectorId = data.arrowId.substring(10,indexPort);
							submenuPort = data.arrowId.substr(indexPort+17);
						}else{
							throw 'wrong format of arrow id';
							return false
						}
						
						ModeActivityMenu.createMenu(
							data.selfId,
							data.selfId,
							'bottom',
							subActions,
							{offset:10, data:{connectorId: submenuConnectorId, port:submenuPort}}
						);
						ModeActivityMenu.existingMenu[topContainerId] = connectId;
						
					}
					
				}
			}],
			{offset:10, autoclose:false, data:{arrowId:pointId, topMenuId:topContainerId, menuContainerId: pointId}}
		);
		ModeActivityMenu.existingMenu[pointId] = connectorId;
	}
	
}


ModeActivityMenu.createMenu = function(targetId, containerId, position, actions, options){
	
	//container = activity or connector:
	var container = $('#'+containerId);
	if(!container.length){
		throw 'no such container element in the DOM';
	}
	
	//think about destroying old menu:
	
	//set default options value:
	var offset = 20;
	var autoclose = true;
	var data = [];
	if(options){
		if(options.offset != null){
			offset = options.offset;
		}
		if(options.autoclose != null){
			autoclose = options.autoclose;
		}
		if(options.data != null){
			data = options.data;
		}
	}
	
	var menuId = containerId+'_menu';
	var menuContainerId = menuId+'_container';
	//record the id of newly created menu, useful to build submenu
	data.selfId = menuContainerId;
	
	var menuContainer = $('<div id="'+menuContainerId+'"/>');
	var calculatedWith = (10+5+16+5)*parseInt(actions.length);
	// var calculatedHeight = (3+16+3);
	var calculatedHeight = 26;
	menuContainer.width(calculatedWith+"px");
	menuContainer.height(calculatedHeight+"px");
	menuContainer.addClass('activity_menu_container');
	menuContainer.css('z-index',1001);//always on top
	menuContainer.css('position','absolute');
	menuContainer.appendTo(container);
	
	var $menu = $('<div id="'+menuId+'"/>').appendTo(menuContainer);
	$menu.addClass('activity_menu_horizontal');
	
	for(var i=0; i<actions.length; i++){
		var action = actions[i];
		
		if(targetId && action.label && action.icon && action.action){
			
			var $anchor = $('<div style="background-image: url(\''+action.icon+'\');">&nbsp;</span>').appendTo($menu);
			$anchor.addClass('ui-corner-all');
			$anchor.attr('title', action.label);
			$anchor.attr('rel', targetId);
			
			initialAutoclose = autoclose;
			if(action.autoclose!=null){
				autoclose = action.autoclose;//if the autoclose option is set, overwrite the value
			}
			$anchor.bind('click', {id:targetId, action:action.action, autoclose: autoclose, data:data}, function(event){
				event.preventDefault();
				event.stopPropagation();
				if(event.data.autoclose){
					ModeActivityMenu.cancel();
				}
				event.data.action(event.data.id, event.data.data, event);
			});
			autoclose = initialAutoclose;//restore intial value, useful only when action.autoclose is set
		}
	}
	
	//position the menu with respect to the container:
	//correct offset value, due to absolute positionning... TODO redo that;
	switch(position){
		case 'top':{
			menuContainer.position({
				my: "center bottom",
				at: "center top",
				of: '#'+containerId,
				offset: "0 -"+offset,
				collision: 'fit none'
			});
			break;
		}
		case 'bottom':{
			menuContainer.position({
				my: "center top",
				at: "center bottom",
				of: '#'+containerId,
				offset: "0 "+offset
			});
			break;
		}
		case 'left':{
			menuContainer.position({
				my: "right center",
				at: "left center",
				of: '#'+containerId,
				offset: "-"+offset+" 0"
			});
			break;
		}
		case 'right':{
			menuContainer.position({
				my: "left center",
				at: "right center",
				of: '#'+containerId,
				offset: offset+" 0"
			});
			break;
		}
		default:{
			//destroy all and return error:
			// menu.remove();
			return false
		}
	}
	
	return true;
}

ModeActivityMenu.removeMenu = function(containerId){
	if(containerId){
		var menuId = containerId+'_menu';
		var menuContainerId = menuId+'_container';
		if($('#'+menuContainerId).length){
			$('#'+menuContainerId).remove();
		}
		
	}
}

ModeActivityMenu.removeAllMenu = function(){
	if(ModeActivityMenu.existingMenu){
		for(containerId in ModeActivityMenu.existingMenu){
			ModeActivityMenu.removeMenu(containerId);
			delete ModeActivityMenu.existingMenu[containerId];
		}
	}
}

ModeActivityMenu.cancel = function(){
	//delete old menu
	ModeActivityMenu.removeAllMenu();
}
