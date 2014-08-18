//alert('ActivityTreeClass loaded 2');

ActivityTreeClass.instances = [];

/**
 * Constructor
 * @param {String} selector the jquery selector of the tree container
 * @param {String} dataUrl the url to call, it must provide the json data to populate the tree
 * @param {Object} options
 */
function ActivityTreeClass(selector, dataUrl, options){
	try{
		if(!options){
			options = ActivityTreeClass.defaultOptions;
		}
		this.selector = selector;
		this.options = options;
		this.dataUrl = dataUrl;
		var instance = this;


		if(!options.instanceName){
			options.instanceName = 'instance';
		}

		//check validity of the seletor:
		if(this.selector.substring(0,1) != '#'){
			//no good selection by id:
			throw 'no correct selector in the activity tree selector';
		}
		var treeId = this.selector.substr(1);
		this.treeId = treeId;
		ActivityTreeClass.instances[treeId] = instance;

		this.currentNode = null;
		this.treeObj = null;

		this.treeOptions = {
			data: {
				type: "json",
				async : true,
				opts: {
					method : "POST",
					url: instance.dataUrl
				}
			},
			types: {
			 "default" : {
					renameable	: false,
					deletable	: true,
					creatable	: true,
					draggable	: false
				}
			},
			ui: {
				theme_name : "custom"
			},
			callback : {
				beforedata:function(NODE, TREE_OBJ) {
					return {
						type : $(TREE_OBJ.container).attr('id'),
						processUri : instance.options.processUri
						// filter: $("#filter-content-" + options.actionId).val()
					}
				},
				oninit:function(TREE_OBJ){
					instance.treeObj = TREE_OBJ;
				},
				onload: function(TREE_OBJ){
					if (instance.options.selectNode && !instance.nodeSelected) {
						TREE_OBJ.select_branch($("li[id='"+instance.options.selectNode+"']"));
						instance.nodeSelected = true;	//select it only on first load
					}
					else{
						TREE_OBJ.open_branch($("li.node-process-root:first"));
						// TREE_OBJ.reselect(true);
					}

					//set the "default" current node as the root:
					instance.currentNode = ActivityTreeClass.getTreeNode('node-process-root', instance.treeId);
				},
				ondata: function(DATA, TREE_OBJ){

					return DATA;
				},
				onselect: function(NODE, TREE_OBJ){

					if( ($(NODE).hasClass('node-activity') || $(NODE).hasClass('node-property')) && instance.options.editActivityPropertyAction){
						var index = $(NODE).attr('id').indexOf("prop_");
						var activityUri = '';
						if(index == 0){
							//it is a property node
							activityUri = $(NODE).attr('id').substr(5);
						}else{
							activityUri = $(NODE).attr('id');
						}
						helpers._load(instance.options.formContainer,
							instance.options.editActivityPropertyAction,
							{ activityUri: activityUri}//put encoded uri as the id of the activity node
						);
					}else if( $(NODE).hasClass('node-activity-goto') && instance.options.editActivityPropertyAction){
						//hightlight the target node
						var activityUri = $(NODE).attr('rel');
						helpers._load(instance.options.formContainer,
							instance.options.editActivityPropertyAction,
							{ activityUri: activityUri}
						);

					}else if( $(NODE).hasClass('node-connector') && instance.options.editConnectorAction){
						var activityUri = false;
						var currentNode = TREE_OBJ.parent(NODE);
						do{
							if($(currentNode).hasClass('node-activity')){
								activityUri = $(currentNode).attr('id');
							}
							currentNode = TREE_OBJ.parent(currentNode);
						}while(!activityUri && currentNode);

						helpers._load(instance.options.formContainer,
							instance.options.editConnectorAction,
							{connectorUri:$(NODE).attr('id'), activityUri:activityUri}
						);

					}else if( ($(NODE).hasClass('node-connector-goto')||$(NODE).hasClass('node-connector-prev')) && instance.options.editConnectorAction){
						//hightlight the target node
						// TREE_OBJ.select_branch(NODE);
						var connectorUri = $(NODE).attr('rel');
						helpers._load(instance.options.formContainer,
							instance.options.editConnectorAction,
							{connectorUri: connectorUri}
						);
					}else if( $(NODE).hasClass('node-interactive-service') && instance.options.editInteractiveServiceAction){
						helpers._load(instance.options.formContainer,
							instance.options.editInteractiveServiceAction,
							{uri:$(NODE).attr('id')}
						);
					}else if( ($(NODE).hasClass('node-inferenceRule-onBefore')||$(NODE).hasClass('node-inferenceRule-onAfter')) && instance.options.editInferenceRuleAction){
						helpers._load(instance.options.formContainer,
							instance.options.editInferenceRuleAction,
							{inferenceRuleUri:$(NODE).attr('id')}
						);
					}else if( $(NODE).hasClass('node-consistencyRule') && instance.options.editConsistencyRuleAction){
						helpers._load(instance.options.formContainer,
							instance.options.editConsistencyRuleAction,
							{consistencyRuleUri:$(NODE).attr('id')}
						);
					}
					return false;
				}
			},
			plugins: {
				cookie:{

				},
				contextmenu : {
					items : {
						refreshTree: {
							label: "Refresh",
							icon: img_url + "view-refresh.png",
							visible : function (NODE, TREE_OBJ) {
								if( $(NODE).hasClass('node-process-root')){
									return 1;
								}
								return -1;
							},
							action  : function(NODE, TREE_OBJ){
								ActivityTreeClass.refreshTree({
									NODE: NODE,
									TREE_OBJ: TREE_OBJ
								});
							},
							separator_after : true
						},
						select: {
							label: "Edit",
							icon: img_url + "pencil.png",
							visible : function (NODE, TREE_OBJ) {
								if( $(NODE).hasClass('node-process-root') || $(NODE).hasClass('node-then') || $(NODE).hasClass('node-else')){
									return -1;
								}
								return 1;
							},
							action  : function(NODE, TREE_OBJ){
								TREE_OBJ.select_branch(NODE);
							},
							separator_after : true
						},
						addActivity: {
							label: "Add Activity",
							icon: img_url + "process_activity.png",
							visible : function (NODE, TREE_OBJ) {
								if(NODE.length != 1) {
									return -1;
								}
								if($(NODE).hasClass('node-process-root') && TREE_OBJ.check("creatable", NODE) && instance.options.createActivityAction){
									return 1;
								}
								return -1;
							},
							action  : function(NODE, TREE_OBJ){
								instance.currentNode = NODE;
								GatewayProcessAuthoring.addActivity(instance.options.createActivityAction, $(NODE).attr('rel'));
							}
						},
						isFirst:{
							label	: "Define as the first activity",
							icon	: img_url + "flag-green.png",
							visible	: function (NODE, TREE_OBJ) {
								if($(NODE).hasClass('node-activity') && !$(NODE).hasClass('node-activity-initial')){
									return 1;
								}
								return -1;
							},
							action	: function (NODE, TREE_OBJ) {
								ActivityTreeClass.setFirstActivity({
									url: instance.options.setFirstActivityAction,
									NODE: NODE,
									TREE_OBJ: TREE_OBJ
								});
								return false;
							}
						},
						isLast:{
							label	: "Define as a final activity",
							icon	: img_url + "flag-red.png",
							visible	: function (NODE, TREE_OBJ) {
								if($(NODE).hasClass('node-activity') && !$(NODE).hasClass('node-activity-last')){
									return 1;
								}
								return -1;
							},
							action	: function (NODE, TREE_OBJ) {
								//find the child connector node and delete it
								$.each(TREE_OBJ.children(NODE), function(){
									var selectedNode = this;
									if($(selectedNode).hasClass('node-connector') && instance.options.deleteConnectorAction){
										if(confirm(__('Set the activity as a final one will delete its following connector. \n Are you sure?'))){
											GatewayProcessAuthoring.deleteConnector(instance.options.deleteConnectorAction, $(selectedNode).attr('id'));
										}
									}
								});
								return false;
							}
						},
						addConnector:{
							label	: "Add connector",
							icon	: img_url + "process_connector.png",
							visible	: function (NODE, TREE_OBJ) {
								if(instance.options.createConnectorAction
								  && ($(NODE).hasClass('node-activity-last'))
								){
									return 1;
								}
								return -1;
							},
							action	: function (NODE, TREE_OBJ) {
								instance.currentNode = NODE;
								GatewayProcessAuthoring.addConnector(instance.options.createConnectorAction, $(NODE).attr('id'));
								return false;
							},
							separator_before : true
						},
						addInteractiveService: {
							label: "Add Interactive Service",
							icon: img_url + "process_service.png",
							visible : function (NODE, TREE_OBJ) {
								if(NODE.length != 1) {
									return -1;
								}
								if($(NODE).hasClass('node-activity') && TREE_OBJ.check("creatable", NODE) ){
									return 1;
								}
								return -1;
							},
							action  : function(NODE, TREE_OBJ){
								instance.currentNode = NODE;
								GatewayProcessAuthoring.addInteractiveService(instance.options.createInteractiveServiceAction, $(NODE).attr('id'));
								return false;
							}
						},
						deleteActivity:{
							label	: "Remove activity",
							icon	: img_url + "delete.png",
							visible	: function (NODE, TREE_OBJ){
								var ok = -1;
								$.each(NODE, function (){
									if( $(NODE).hasClass('node-activity')
									&& instance.options.deleteActivityAction
									&& (TREE_OBJ.check("deletable", this) == true)){
										ok = 1;
										return 1;
									}
								});
								return ok;
							},
							action	: function (NODE, TREE_OBJ){
								GatewayProcessAuthoring.deleteActivity(instance.options.deleteActivityAction, $(NODE).attr('id'));
								return false;
							},
							separator_before: true
						},
						deleteConnector:{
							label	: "Remove connector",
							icon	: img_url + "delete.png",
							visible	: function (NODE, TREE_OBJ){
								var ok = -1;
								$.each(NODE, function (){
									if( $(NODE).hasClass('node-connector')
									&& instance.options.deleteConnectorAction
									&& (TREE_OBJ.check("deletable", this) == true)){
										ok = 1;
										return 1;
									}
								});
								return ok;
							},
							action	: function (NODE, TREE_OBJ){
								if(confirm(__('Please confirm the deletion of the connector: \n child connectors will be delete at the same time'))){
									GatewayProcessAuthoring.deleteConnector(instance.options.deleteConnectorAction, $(NODE).attr('id'));
								}
								return false;
							},
							separator_before: true
						},
						deleteService:{
							label	: "Remove interactive service",
							icon	: img_url + "delete.png",
							visible	: function (NODE, TREE_OBJ){
								var ok = -1;
								$.each(NODE, function (){
									if( $(NODE).hasClass('node-interactive-service')
									&& instance.options.deleteInteractiveServiceAction
									&& (TREE_OBJ.check("deletable", this) == true)){
										ok = 1;
										return 1;
									}
								});
								return ok;
							},
							action	: function (NODE, TREE_OBJ){
								ActivityTreeClass.removeNode({
									url: instance.options.deleteInteractiveServiceAction,
									NODE: NODE,
									TREE_OBJ: TREE_OBJ
								});
								return false;
							},
							separator_before: true
						},
						gotonode:{
							label	: "Goto",
							icon	: img_url + "go-jump.png",
							visible	: function (NODE, TREE_OBJ) {
								if($(NODE).hasClass('node-activity-goto') || $(NODE).hasClass('node-connector-goto')){
									return 1;
								}
								return -1;
							},
							action	: function (NODE, TREE_OBJ) {
								//hightlight the target node
								var targetId = $(NODE).attr('rel');
								TREE_OBJ.select_branch($("li[id='"+targetId+"']"));
								return false;
							}
						},
						remove: false,
						create: false,
						rename: false
					}
				}
			}
		};

		//create the tree
		$(selector).tree(this.treeOptions);

		//bind listeners:
		this.bindListeners();

	}
	catch(exp){
		$.error('ActivityTreeClass exception : ' + exp);
	}
}


ActivityTreeClass.prototype.bindListeners = function(){

	//TODO: put treeId in evnt data object: data = {treeId: treeId}
	var _this = this;

	$(document).on('activityAdded.wfAuthoring', function(event, response){
		var response = _this.feedCurrentNode(response);
		if(response.NODE && response.TREE_OBJ){
			_this.addActivity(response);
		}
	});

	$(document).on('interactiveServiceAdded.wfAuthoring', function(event, response){
		var response = _this.feedCurrentNode(response);
		if(response.NODE && response.TREE_OBJ){
			_this.addInteractiveService(response);
		}
	});

	$(document).on('connectorAdded.wfAuthoring', function(event, response){
		var response = _this.feedCurrentNode(response);
		if(response.NODE && response.TREE_OBJ){
			_this.addConnector(response);
		}
	});

	$(document).on('activityDeleted.wfAuthoring', function(event, response){
		if(_this.treeObj){
			_this.treeObj.refresh();
		}
	});

	$(document).on('connectorDeleted.wfAuthoring', function(event, response){
		if(_this.treeObj){
			_this.treeObj.refresh();
		}
	});
}

ActivityTreeClass.prototype.feedCurrentNode = function(object){
	if(this.treeObj && this.currentNode){
		object.TREE_OBJ = this.treeObj;
		object.NODE = this.currentNode;
	}

	return object;
}

ActivityTreeClass.setCurrentNode = function(treeId, nodeId){
	var node = ActivityTreeClass.getTreeNode(nodeId, treeId);
	if(ActivityTreeClass.instances[treeId]){
		ActivityTreeClass.instances[treeId].currentNode = node;
	}else{
		throw 'no instance of activity tree has been found with the id '+treeId;
	}

}

ActivityTreeClass.prototype.addActivity = function(response){
	var TREE_OBJ = this.treeObj;
	var NODE = this.currentNode;
	if(!NODE){
		NODE = this.getTreeNode('node-process-root');//always add to the root, process node
	}


	if(NODE && TREE_OBJ){

		TREE_OBJ.select_branch(TREE_OBJ.create({
			data: response.label,
			attributes: {
				id: response.uri,
				'class': response.clazz
			}
		}, TREE_OBJ.get_node(NODE[0])));

	}

}

ActivityTreeClass.prototype.refreshTree = function(){
	ActivityTreeClass.refreshTree(this.treeObj);
}

ActivityTreeClass.refreshTree = function(options){
	var TREE_OBJ = options.TREE_OBJ;
	// var NODE = options.NODE;
	TREE_OBJ.refresh();
	// TREE_OBJ.reselect(true);
}


ActivityTreeClass.prototype.addInteractiveService = function(response){
	var TREE_OBJ = this.treeObj;
	var NODE = this.currentNode;
	var  cssClass = 'node-interactive-service';
	if(response.cssClass){
		 cssClass += ' ' + response.cssClass;
	}

	TREE_OBJ.select_branch(TREE_OBJ.create({
		data: response.label,
		attributes: {
			id: response.uri,
			'class': cssClass
		}
	}, TREE_OBJ.get_node(NODE[0])));
}

ActivityTreeClass.prototype.getTree = function(){
	return $.tree.reference(this.selector);
}

ActivityTreeClass.prototype.selectTreeNode = function(nodeId){
	return ActivityTreeClass.selectTreeNode(nodeId, this.treeId);
}

ActivityTreeClass.selectTreeNode = function(nodeId, treeId){

	if(treeId){
		if(ActivityTreeClass.instances[treeId]){
			var anActivityTree = ActivityTreeClass.instances[treeId];
			if(anActivityTree){
				var aJsTree = anActivityTree.getTree();
				if(aJsTree){
					if(aJsTree.select_branch($("li[id='"+nodeId+"']"))){
						aJsTree.open_branch($("li[id='"+nodeId+"']"));
						return true;
					}
				}
			}
		}
	}else{
		for(treeName in ActivityTreeClass.instances){
			var anActivityTree = null;
			anActivityTree = ActivityTreeClass.instances[treeName];
			if(anActivityTree){
				var aJsTree = anActivityTree.getTree();
				if(aJsTree){
					if(aJsTree.select_branch($("li[id='"+nodeId+"']"))){
						aJsTree.open_branch($("li[id='"+nodeId+"']"));
						return true;
					}
				}
			}
		}
	}

	return false;
}

ActivityTreeClass.prototype.getTreeNode = function(nodeId){
	return ActivityTreeClass.getTreeNode(nodeId, this.treeId);
}

ActivityTreeClass.getTreeNode = function(nodeId, treeId){

	if(treeId){
		if(ActivityTreeClass.instances[treeId]){
			var anActivityTree = ActivityTreeClass.instances[treeId];
			if(anActivityTree){
				var aJsTree = anActivityTree.getTree();
				if(aJsTree){
					if(aJsTree.get_node($("li[id='"+nodeId+"']"))){
						return aJsTree.get_node($("li[id='"+nodeId+"']"));
					}
				}
			}
		}
	}else{
		for(treeName in ActivityTreeClass.instances){
			var anActivityTree = null;
			anActivityTree = ActivityTreeClass.instances[treeName];
			if(anActivityTree){
				var aJsTree = anActivityTree.getTree();
				if(aJsTree){
					if(aJsTree.get_node($("li[id='"+nodeId+"']"))){
						return aJsTree.get_node($("li[id='"+nodeId+"']"));
					}
				}
			}
		}
	}

	return null;
}

ActivityTreeClass.removeNode = function(options){

	if(options.TREE_OBJ && options.NODE){

		var TREE_OBJ = options.TREE_OBJ;
		var NODE = options.NODE;
		if(confirm(__("Please confirm deletion.\n Warning: related resources might be affected."))){

			var data = false;
			// var selectedNode = this;
			if(NODE.hasClass('node-connector')){
				// PNODE = TREE_OBJ.parent(selectedNode);
				data =  {connectorUri: NODE.attr('id')};
			}
			else if(NODE.hasClass('node-activity')){
				// PNODE = TREE_OBJ.parent(selectedNode);
				data =  {activityUri: NODE.attr('id')};
			}
			else if(NODE.hasClass('node-interactive-service')){
				// PNODE = TREE_OBJ.parent(selectedNode);
				data =  {serviceUri: NODE.attr('id')};
			}
			else if(NODE.hasClass('node-inferenceRule-onBefore') || NODE.hasClass('node-inferenceRule-onAfter')){
				// PNODE = TREE_OBJ.parent(selectedNode);
				data =  {inferenceUri: NODE.attr('id')};
			}
			else if(NODE.hasClass('node-consistencyRule')){
				data =  {consistencyUri: NODE.attr('id')};
			}
			if(data){
				$.ajax({
					url: options.url,
					type: "POST",
					data: data,
					dataType: 'json',
					success: function(response){
						if(response.deleted){
							TREE_OBJ.refresh();
						}
					}
				});
			}

		}
	}


}

ActivityTreeClass.setFirstActivity = function(options){

	if(options.NODE && options.TREE_OBJ){
		var TREE_OBJ = options.TREE_OBJ;
		var NODE = options.NODE;
		var data = {processUri:TREE_OBJ.parent(NODE).attr('rel'), activityUri:NODE.attr('id')};

		if(data){
			$.ajax({
				url: options.url,
				type: "POST",
				data: data,
				dataType: 'json',
				success: function(response){
					if(response.set){
						TREE_OBJ.refresh();
					}
				}
			});
		}
	}

}

ActivityTreeClass.prototype.addConnector = function(response){

	var TREE_OBJ = this.treeObj;
	var NODE = this.currentNode;

	if(NODE && TREE_OBJ){

		TREE_OBJ.select_branch(TREE_OBJ.create({
			data: response.label,
			attributes: {
				id: response.uri,
				'class': 'node-connector'
			}
		}, TREE_OBJ.get_node(NODE[0])));

	}

}

/*
ActivityTreeClass.removeNode0 = function(options){
	if(options.TREE_OBJ && options.NODE){

	}


	var TREE_OBJ = options.TREE_OBJ;
	var NODE = options.NODE;
	if(confirm(__("Please confirm deletion"))){
		$.each(NODE, function () {
			var data = false;
			var selectedNode = this;
			if($(selectedNode).hasClass('node-activity')){
				data =  {activityUri: $(selectedNode).attr('id')}
			}
			if($(selectedNode).hasClass('node-interactive-service') || $(selectedNode).hasClass('node-consistency-rule')){
				PNODE = TREE_OBJ.parent(selectedNode);
				data =  {uri: $(selectedNode).attr('id'), activityUri: $(PNODE).attr('id')}
			}
			if(data){
				$.ajax({
					url: options.url,
					type: "POST",
					data: data,
					dataType: 'json',
					success: function(response){
						if(response.deleted){
							TREE_OBJ.remove(selectedNode);
						}
					}
				});
			}
		});
	}
}
*/
