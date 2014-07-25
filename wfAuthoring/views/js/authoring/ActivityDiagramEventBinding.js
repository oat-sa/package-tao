if(eventMgr && ActivityDiagramClass && ArrowClass){

	eventMgr.bind('activityAdded', function(event, response){

		try{
			var activity = ActivityDiagramClass.feedActivity({
				"data": response.label,
				"attributes": {"id": response.uri}
			});

			//draw activity with the default positionning:
			ActivityDiagramClass.drawActivity(activity.id);
			ActivityDiagramClass.setActivityMenuHandler(activity.id);

			//draw arrow if need be (i.e. in case of adding an activity with a connector)
			if(response.previousConnectorUri && response.port>=0){
				//should be a connector:
				var previousObjectId = ActivityDiagramClass.getIdFromUri(response.previousConnectorUri);
				var originEltId = ActivityDiagramClass.getActivityId('connector', previousObjectId);
				var arrowId = ActivityDiagramClass.getActivityId('connector', previousObjectId, 'bottom', response.port);

				var activityId = ActivityDiagramClass.getActivityId('container', activity.id);
				ActivityDiagramClass.positionNewActivity($('#'+originEltId), $('#'+activityId));
				// ActivityDiagramClass.setActivityMenuHandler(activityId);

				//create and draw arrow:
				var activityTopId = ActivityDiagramClass.getActivityId('activity', activity.id, 'top');
				ArrowClass.arrows[arrowId] = ArrowClass.calculateArrow($('#'+arrowId), $('#'+activityTopId), 'top', new Array(), false);
				ArrowClass.drawArrow(arrowId, {
					container: ActivityDiagramClass.canvas,
					arrowWidth: 2
				});

				//update connector in connectors array:
				ActivityDiagramClass.editConnectorPort(previousObjectId, response.port, activity.id, response.multiplicity);//multiplicity to be defined

				//save diagram:
				ActivityDiagramClass.saveDiagram();
			}
		}catch(ex){
			CL('activityAdded exception:', ex);
		}
	});

	eventMgr.bind('connectorAdded', function(event, response){
		try{
			//a connector is always added throught the "linked mode"
			var previousObjectId = ActivityDiagramClass.getIdFromUri(response.previousActivityUri);
			if(response.previousIsActivity){
				var originEltId = ActivityDiagramClass.getActivityId('activity', previousObjectId);
				var arrowId = ActivityDiagramClass.getActivityId('activity', previousObjectId, 'bottom');

				var activityRefId = previousObjectId;
				ActivityDiagramClass.refreshRelatedTree();
			}else{
				//should be a connector:
				var originEltId = ActivityDiagramClass.getActivityId('connector', previousObjectId);
				var arrowId = ActivityDiagramClass.getActivityId('connector', previousObjectId, 'bottom', response.port);
				if(ActivityDiagramClass.connectors[previousObjectId]){
					var activityRefId = ActivityDiagramClass.connectors[previousObjectId].activityRef;

					//update the local datastore on the previous activity:
					ActivityDiagramClass.connectors[previousObjectId].port[response.port].targetId = ActivityDiagramClass.getIdFromUri(response.uri);
					//update multiplicity here?
				}else{
					throw 'the connector does not exist in the connectors array';
				}

			}

			var connector = ActivityDiagramClass.feedConnector(
				{
					"data": response.label,
					"attributes": {"id": response.uri},
					"type": response.type
				},
				null,
				previousObjectId,
				null,
				activityRefId
			);

			//draw connector and reposition it:
			var connectorId = ActivityDiagramClass.getActivityId('connector', connector.id);
			var connectorTopId = ActivityDiagramClass.getActivityId('connector', connector.id, 'top');

			ActivityDiagramClass.drawConnector(connector.id);
			ActivityDiagramClass.positionNewActivity($('#'+originEltId), $('#'+connectorId));
			ActivityDiagramClass.setConnectorMenuHandler(connector.id);

			//create and draw arrow:
			ArrowClass.arrows[arrowId] = ArrowClass.calculateArrow($('#'+arrowId), $('#'+connectorTopId), 'top', new Array(), false);
			ArrowClass.drawArrow(arrowId, {
				container: ActivityDiagramClass.canvas,
				arrowWidth: 2
			});

			//save diagram:
			ActivityDiagramClass.saveDiagram();
		}catch(ex){
			CL('connectorAdded exception:', ex);
			// CL('connector', connector);
			// CL('originEltId', originEltId);
			// CL('connectorId', connectorId);
			// CL('arrowId', arrowId);
		}

	});

	eventMgr.bind('connectorSaved', function(event, response){
		var added = false
		if(response.newActivities && response.previousConnectorUri){
			if(response.newActivities.length > 0){
				var activityAddedResponse = response.newActivities[0];//currently, the first one is enough
				activityAddedResponse.previousConnectorUri = response.previousConnectorUri;
				eventMgr.trigger('activityAdded', activityAddedResponse);
				added = true;
			}
		}

		if(response.newConnectors && response.previousConnectorUri){
			if(response.newConnectors.length > 0){
				var connectorAddedResponse = response.newConnectors[0];//currently, the first one is enough
				connectorAddedResponse.previousActivityUri = response.previousConnectorUri;
				connectorAddedResponse.previousIsActivity = false;//the previous activity is obviously a connector here
				eventMgr.trigger('connectorAdded', connectorAddedResponse);
				added = true;
			}
		}

		if(!added){
			//reload the tree:
			ActivityDiagramClass.refreshRelatedTree();
			ActivityDiagramClass.loadDiagram();
		}

	});


	eventMgr.bind('activityPropertiesSaved', function(event, response){
		//simply reload the tree:
		ActivityDiagramClass.refreshRelatedTree();
	});

	eventMgr.bind('activityDeleted', function(event, response){
		ActivityDiagramClass.reloadDiagram();
	});

	eventMgr.bind('connectorDeleted', function(event, response){
		ActivityDiagramClass.reloadDiagram();
	});

	eventMgr.bind('diagramLoaded', function(event, response){
		setTimeout(function(){
			$('#processAuthoring_loading').hide();
			$('#authoring-container').show();
		}, 1000);
	});

}