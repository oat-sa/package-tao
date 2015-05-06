/**
 * GenerisTreeClass is a easy to use container for the tree widget,
 * it provides the common behavior for a Class/Instance Rdf resource tree
 *
 * @example new GenerisTreeClass('#tree-container', 'myData.php', {});
 * @see GenerisTreeClass.defaultOptions for options example
 *
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * @require jstree = 0.9.9 [http://jstree.com/]
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */

/**
 * @var {Array} instances the list of tree instances
 */
GenerisTreeClass.instances = [];

/**
 * The GenerisTreeClass constructor
 * @param {String} selector the jquery selector of the tree container
 * @param {String} dataUrl the url to call, it must provide the json data to populate the tree
 * @param {Object} options {formContainer, actionId, instanceClass, instanceName, selectNode,
 * 							editClassAction, editInstanceAction, createInstanceAction,
 * 							moveInstanceAction, subClassAction, deleteAction, duplicateAction}
 */
function GenerisTreeClass(selector, dataUrl, options){
	try{
		if(!options){
			options = GenerisTreeClass.defaultOptions;
		}
		this.selector = selector;
		//Generis Tree options
		this.options = options;
		//Default server parameters
		this.defaultServerParameters = new Array ();
		//Url used to get tree data
		this.dataUrl = dataUrl;
		//Store meta data of opened classes
		this.metaClasses = new Array();
		//Keep a reference of the last opened node
		this.lastOpened = null;
		//
		if(!options.instanceName){
			options.instanceName = 'instance';
		}
		//Paginate the tree or not
		this.paginate = typeof options.paginate != 'undefined' ? options.paginate : 0;
		//Options to pass to the server
		this.serverParameters = (typeof options.serverParameters != "undefined") ? options.serverParameters : new Array ();
		//Default server parameters
		this.defaultServerParameters = {
			hideInstances:  this.options.hideInstances | false,
			filter: 		$("#filter-content-" + options.actionId).val(),
			offset:			0,
			limit:			this.options.paginate
		};

		// Global access of the instance in the sub scopes
		var instance = this;

		// Add the instance to the global storage of instances
		GenerisTreeClass.instances[GenerisTreeClass.instances.length + 1] = instance;

		/**
		 * @var {Object} jsTree options
		 * @see http://www.jstree.com/documentation for the options documentation
		 */
		this.treeOptions = {
			//how to retrieve the data
			data: {
				type: "json",
				async : true,
				opts: {
					method : "POST",
					url: instance.dataUrl
				}
			},
			//what we can do with the elements
			types: {
			 "default" : {
					renameable	: false,
					deletable	: true,
					creatable	: true,
					draggable	: function(NODE){
						if($(NODE).hasClass('node-instance') && instance.options.moveInstanceAction){
							return true;
						}
						return false;
					}
				}
			},
			ui: {
				theme_name : "custom"
			},
			callback : {
				//Before receive data from server, return the POST parameters
				beforedata:function(NODE, TREE_OBJ) {
					var returnValue = instance.defaultServerParameters;
					// If a NODE is given, send its identifier to the server
					if(NODE){
						returnValue['classUri'] = $(NODE).attr('id');
					}
					// Augment with the serverParameters
					for (var key in instance.serverParameters){
						returnValue[key] = instance.serverParameters[key];
					}
					// Add selected nodes
					returnValue['selected'] = instance.options.checkedNodes;

					return returnValue;
				},
				//once the tree is loaded
				onload: function(TREE_OBJ){
					if (instance.options.selectNode) {
						TREE_OBJ.select_branch($("li[id='"+instance.options.selectNode+"']"));
						instance.options.selectNode = false;
					}
					else{
						TREE_OBJ.open_branch($("li.node-class:first"));
					}
				},
				//before open a branch
				beforeopen:function(NODE, TREE_OBJ){
					instance.lastOpened = NODE;
				},
				//when we receive the data
				ondata: function(DATA, TREE_OBJ){
					//automatically open the children of the received node
					if(DATA.children){
						DATA.state = 'open';
					}
					//extract meta data from children
					instance.extractMeta (DATA);
					//add specific classes to nodes
					if(instance.options.instanceClass){
						function addClassToNodes(nodes, clazz){
							$.each(nodes, function(i, node){
								if(node.attributes
										&& node.attributes['class']
										&& /node\-instance/.test(node.attributes['class'])){
									node.attributes['class'] = node.attributes['class'] + ' ' + clazz;
								}
								if(node.children){
									addClassToNodes(node.children, clazz);
								}
							});
						}
						if(DATA.children){
							addClassToNodes(DATA.children, instance.options.instanceClass);
							if(instance.options.moveInstanceAction){
								addClassToNodes(DATA.children, 'node-draggable');
							}
						}
						else if(DATA.length > 0){
							addClassToNodes(DATA, instance.options.instanceClass);
							if(instance.options.moveInstanceAction){
								addClassToNodes(DATA, 'node-draggable');
							}
						}
					}

					return DATA;
				},
				//when a node is selected
				onselect: function(NODE, TREE_OBJ){
					var nodeId = $(NODE).attr('id');
					var parentNodeId = $(NODE).parent().parent().attr('id');
					$("a.clicked").each(function(){
						if($(this).parent('li').attr('id') != nodeId){
							$(this).removeClass('clicked');
						}
					});

					if( ($("input:hidden[name='uri']").val() == nodeId || $("input:hidden[name='classUri']").val() == nodeId) && nodeId == instance.options.selectNode){
						return false;
					}

					if($(NODE).hasClass('node-class') && instance.options.editClassAction){

						if($(NODE).hasClass('closed')){
							TREE_OBJ.open_branch(NODE);
						}

						//load the editClassAction into the formContainer
						helpers._load(instance.options.formContainer,
							instance.options.editClassAction,
							instance.data(null, nodeId)
						);
					}
					if($(NODE).hasClass('node-instance') && instance.options.editInstanceAction){
						//load the editInstanceAction into the formContainer
						PNODE = TREE_OBJ.parent(NODE);
						helpers._load(instance.options.formContainer,
							instance.options.editInstanceAction,
							instance.data(nodeId, $(PNODE).attr('id'))
						);
					}
					if($(NODE).hasClass('paginate-more')) {
						instance.paginateInstances ($(NODE).parent().parent(), TREE_OBJ);
					}
					if($(NODE).hasClass('paginate-all')) {
						var limit = instance.getMeta (parentNodeId, 'count') - instance.getMeta (parentNodeId, 'displayed');
						instance.paginateInstances ($(NODE).parent().parent(), TREE_OBJ, {'limit':limit});
					}
					return false;
				},
				//when a node is move by drag n'drop
				onmove: function(NODE, REF_NODE, TYPE, TREE_OBJ, RB){

					if(!instance.options.moveInstanceAction){
						return false;
					}
					if($(REF_NODE).hasClass('node-instance') && TYPE == 'inside'){
						$.tree.rollback(RB);
						return false;
					}
					else{
						if(TYPE == 'after' || TYPE == 'before'){
							REF_NODE = TREE_OBJ.parent(REF_NODE);
						}
						//call the server with the new node position to save the new position
						function moveNode(url, data){

							var NODE 		= data.NODE;
							var REF_NODE	= data.REF_NODE;
							var RB 			= data.RB;
							var TREE_OBJ 	= data.TREE_OBJ;
							(data.confirmed == true) ? confirmed = true :  confirmed = false;

							$.postJson(url, {
								'uri': data.uri,
								'destinationClassUri':  data.destinationClassUri,
								'confirmed' : confirmed
								},
								function(response){

									if(response == null){
										$.tree.rollback(RB);
										return;
									}
									if(response.status == 'diff'){
										message = __("Moving this element will remove the following properties:");
										message += "\n";
										for(i = 0; i < response.data.length; i++){
											if(response.data[i].label){
												message += "- " + response.data[i].label + "\n";
											}
										}
										message += "Please confirm this operation.\n";
										if(confirm(message)){
											data.confirmed = true;
											moveNode(url, data);
										}
										else{
											$.tree.rollback(RB);
										}
									}
									else if(response.status == true){
										$('li a').removeClass('clicked');
										TREE_OBJ.open_branch(NODE);
									}
									else{
										$.tree.rollback(RB);
									}
							});
						}
						moveNode(instance.options.moveInstanceAction, {
								'uri': $(NODE).attr('id'),
								'destinationClassUri': $(REF_NODE).attr('id'),
								'NODE'		: NODE,
								'REF_NODE'	: REF_NODE,
								'RB'		: RB,
								'TREE_OBJ'	: TREE_OBJ
							});
					}
				}
			},
			plugins: {

				//the right click menu
				contextmenu : {
					items : {
						//edit action
						select: {
							label: __("edit"),
							icon: taobase_www +"img/pencil.png",
							visible : function (NODE, TREE_OBJ) {
								if( ($(NODE).hasClass('node-instance') &&  instance.options.editInstanceAction)  ||
									($(NODE).hasClass('node-class') &&  instance.options.editClassAction) ){
									return true;
								}
								return false;
							},
							action  : function(NODE, TREE_OBJ){
								TREE_OBJ.select_branch(NODE);		//call the onselect callback
							},
		                    separator_before : true
						},
						//new class action
						subclass: {
							label: __("new class"),
							icon	: taobase_www + "img/class_add.png",
							visible: function (NODE, TREE_OBJ) {
								if(NODE.length != 1) {
									return false;
								}
								if(!$(NODE).hasClass('node-class') || !instance.options.subClassAction){
									return false;
								}
								return TREE_OBJ.check("creatable", NODE);
							},
							action  : function(NODE, TREE_OBJ){

								//specialize the selected class
								instance.addClass(NODE, TREE_OBJ, {
									id: $(NODE).attr('id'),
									url: instance.options.subClassAction
								});
							},
		                    separator_before : true
						},
						//new instance action
						instance:{
							label: __("new") + ' ' +  __(instance.options.instanceName),
							icon	: taobase_www + "img/instance_add.png",
							visible: function (NODE, TREE_OBJ) {
								if(NODE.length != 1) {
									return false;
								}
								if(!$(NODE).hasClass('node-class') || !instance.options.createInstanceAction){
									return false;
								}
								return TREE_OBJ.check("creatable", NODE);
							},
							action: function (NODE, TREE_OBJ) {

								//add a new instance of the selected class
								instance.addInstance(NODE, TREE_OBJ, {
									url: instance.options.createInstanceAction,
									id: $(NODE).attr('id'),
									cssClass: instance.options.instanceClass
								});
							}
						},
						//move action
						move:{
							label	: __("move"),
							icon	: taobase_www + "img/move.png",
							visible	: function (NODE, TREE_OBJ) {
									if($(NODE).hasClass('node-instance')  && instance.options.moveInstanceAction){
										return true;
									}
									return false;
								},
							action	: function (NODE, TREE_OBJ) {

								//move the node
								instance.moveInstance(NODE, TREE_OBJ);
							},
		                    separator_before : true
						},
						//clone action
						duplicate:{
							label	: __("duplicate"),
							icon	: taobase_www + "img/duplicate.png",
							visible	: function (NODE, TREE_OBJ) {
									if($(NODE).hasClass('node-instance')  && instance.options.duplicateAction){
										return true;
									}
									return false;
								},
							action	: function (NODE, TREE_OBJ) {

								//clone the node
								instance.cloneNode(NODE, TREE_OBJ, {
									url: instance.options.duplicateAction
								});
							}
						},
						//delete action
						del:{
							label	: __("delete"),
							icon	: taobase_www + "img/delete.png",
							visible	: function (NODE, TREE_OBJ) {
								var ok = true;
								$.each(NODE, function () {
									if(TREE_OBJ.check("deletable", this) == false || !instance.options.deleteAction)
										ok = false;
										return false;
									});
									return ok;
								},
							action	: function (NODE, TREE_OBJ) {

								//remove the node
								instance.removeNode(NODE, TREE_OBJ, {
									url: instance.options.deleteAction
								});
								return false;
							}
						},
						//unset the default entries
						remove: false,
						create: false,
						rename: false
					}
				}
			}
		};

		if(this.options.selectNode){
			this.treeOptions.selected = this.options.selectNode;
		}

		tmpTree = $.tree.reference(selector);
		if(tmpTree != null){
			tmpTree.destroy();
		}
		tmpTree = null;

		/*
		 * Create and initialize the tree here
		 */
		$(selector).tree(this.treeOptions);

		//open all action
		$("#open-action-" + options.actionId).click(function(){
			$.tree.reference(instance.selector).open_all();
		});

		//close all action
		$("#close-action-" + options.actionId).click(function(){
			$.tree.reference(instance.selector).close_all();
		});

		//filter action
		$("#filter-action-" + options.actionId).click(function(){
			$.tree.reference(instance.selector).refresh();
		});
		$("#filter-content-" + options.actionId).bind('keypress', function(e) {
	        if(e.keyCode==13 && this.value.length > 0){
				$.tree.reference(instance.selector).refresh();
	        }
		});

	}
	catch(exp){
		//console.log(exp);
	}

	/**
	 * Formats sendable data with the defined options
	 * ONLY USED FOR SELECT ACTION
	 * @param {String} uri
	 * @param {String} classUri
	 * @return {Object}
	 */
	this.data = function(uri, classUri){
		data = {};

		(this.options.instanceKey) 	? instanceKey = this.options.instanceKey :  instanceKey = 'uri';
		(this.options.classKey) 	? classKey = this.options.classKey :  classKey = 'classUri';

		if(uri){
			data[instanceKey] = uri;
		}
		if(classUri){
			data[classKey] = classUri;
		}

		return data;
	};
}

/**
 * Extract meta data from received data
 */
GenerisTreeClass.prototype.extractMeta = function(DATA) {
	var nodes = new Array ();
	var nodeId = null;
	var instance = this;

	/**
	 * Create meta from class node
	 * @private
	 */
	function createMeta (meta) {
		instance.metaClasses[meta.id] = {
			displayed :  meta.displayed ? meta.displayed :0			// Total of elements displayed
			, count :     meta.count ? meta.count :0				// Total of elements in the class
			, position :  meta.position ? meta.position :0			// Position of the last element displayed
		};
	}

	//An object is received
	if ( !(DATA instanceof Array) ){
		nodeId = DATA.attributes.id;
		if (typeof DATA.children != 'undefined'){
			nodes = DATA.children;
		}
		createMeta ({id:DATA.attributes.id, count:DATA.count});
	}
	//An array of nodes is received
	else {
		// Get the last opened node
		if (this.lastOpened){
			nodeId = this.lastOpened.id;
		} else {
			nodeId = "DEFAULT_ROOT";
			createMeta ({id:nodeId, count:0});
		}
		nodes = DATA;
	}

	//Extract meta from children
	if (nodes) {
		//Number of classes found
		var countClass =0;
		for (var i=0; i<nodes.length; i++) {
			// if the children is a class, create meta for this class
			if (nodes[i].type == 'class'){
				this.extractMeta (nodes[i]);
				countClass++;
			}
		}
		var countInstances = nodes.length - countClass;
		this.setMeta (nodeId, 'position', countInstances); // Position of the last element displayed
		this.setMeta (nodeId, 'displayed',countInstances); // Total of elements displayed

		if (!(DATA instanceof Array) && DATA.state && DATA.state != 'closed'){
			if (this.getMeta(nodeId, 'displayed') < this.getMeta(nodeId, 'count')){
				nodes.push(instance.getPaginateActionNodes());
			}
		} else if ((DATA instanceof Array) && this.getMeta(nodeId, 'displayed') < this.getMeta(nodeId, 'count')){
			nodes.push(instance.getPaginateActionNodes());
		}
	}
}

/**
 * Set a variable to send to the server
 */
GenerisTreeClass.prototype.setServerParameter = function (key, value, reload){
	this.serverParameters[key] = value;
	if (typeof (reload)!='undefined' && reload){
		this.getTree().refresh();
	}
}

/**
 * @return {Object} the tree instance
 */
GenerisTreeClass.prototype.getTree = function(){
	return $.tree.reference(this.selector);
};

/**
 * Get node's meta data
 */
GenerisTreeClass.prototype.getMeta = function (classId, metaName, value) {
	return this.metaClasses[classId][metaName];
}

/**
 * Set node's meta data
 */
GenerisTreeClass.prototype.setMeta = function (classId, metaName, value) {
	this.metaClasses[classId][metaName] = value;
}

/**
 * Get paginate nodes
 * @return {array}
 */
GenerisTreeClass.prototype.getPaginateActionNodes = function () {
	returnValue = [{
		'data' : __('all')
			, 'attributes' : { 'class':'paginate paginate-all' }
		},{
			'data' : this.paginate+__(' next')
			, 'attributes' : { 'class':'paginate paginate-more' }
		}];
	return returnValue;
}

/**
 * Show paginate options
 * @param NODE
 * @param TREE_OBJ
 * @private
 */
GenerisTreeClass.prototype.showPaginate = function (NODE, TREE_OBJ){
	var DATA = this.getPaginateActionNodes();
	for (var i=0; i<DATA.length; i++){
		TREE_OBJ.create(DATA[i], TREE_OBJ.get_node(NODE[0]));
	}
}

/**
 * Hide paginate options
 * @param NODE
 * @param TREE_OBJ
 * @private
 */
GenerisTreeClass.prototype.hidePaginate  = function (NODE, TREE_OBJ){
	$(NODE).find('.paginate').each(function(){
		$(this).remove();
	});
}

/**
 * Refresh pagination, hide and show if required
 * @param NODE
 * @param TREE_OBJ
 * @private
 */
GenerisTreeClass.prototype.refreshPaginate  = function (NODE, TREE_OBJ){
	var nodeId = $(NODE)[0].id;
	this.hidePaginate (NODE, TREE_OBJ);
	if (this.getMeta(nodeId, "displayed") < this.getMeta(nodeId, "count")){
		this.showPaginate (NODE, TREE_OBJ);
	}
}

/**
 * Paginate function, display more instances
 */
GenerisTreeClass.prototype.paginateInstances = function(NODE, TREE_OBJ, pOptions){
	var instance = this;

	var nodeId = $(NODE).attr('id');
	var instancesLeft = instance.getMeta(nodeId, "count") - instance.getMeta(nodeId, "displayed");
	var options = {
		"classUri":		nodeId,
		"subclasses": 	0,
		"offset": 		instance.getMeta(nodeId, "position"),
		"limit":		instancesLeft < this.paginate ? instancesLeft : this.paginate
	};
	options = $.extend(options, pOptions);
	$.post(this.dataUrl, options, function(DATA){
		//Display instances
		for (var i=0; i<DATA.length; i++){
			DATA[i].attributes['class'] = instance.options.instanceClass+" node-instance node-draggable";
			TREE_OBJ.create(DATA[i], TREE_OBJ.get_node(NODE[0]));
		}
		instance.setMeta(nodeId, "displayed", instance.getMeta(nodeId, "displayed")+DATA.length);
		instance.setMeta(nodeId, "position", instance.getMeta(nodeId, "position")+DATA.length);
		//refresh pagination options
		instance.refreshPaginate(NODE, TREE_OBJ);
	}, "json");
};

/**
 * @var GenerisTreeClass.defaultOptions is an example of options to provide to the tree
 */
GenerisTreeClass.defaultOptions = {
	formContainer: '#form-container'
};

/**
 * select a node in the current tree
 * @param {String} id
 * @return {Boolean}
 */
GenerisTreeClass.selectTreeNode = function(id){
	var i=0;
	while(i < GenerisTreeClass.instances.length){
		var aGenerisTree = GenerisTreeClass.instances[i];
		if(aGenerisTree){
			var aJsTree = aGenerisTree.getTree();
			if(aJsTree){
				if(aJsTree.select_branch($("li[id='"+id+"']"))){
					return true;
				}
			}
		}
		i++;
	}
	return false;
};

/**
 * Enable you to retrieve the right tree instance and node instance from an Uri
 * @param {String} uri is the id of the tree node
 * @return {Object}
 */
function getTreeOptions(uri){
	if (uri) {
		var i = 0;
		while (i < GenerisTreeClass.instances.length) {
			var aGenerisTree = GenerisTreeClass.instances[i];
			if (aGenerisTree) {
				var aJsTree = aGenerisTree.getTree();
				if (aJsTree) {
					if (aJsTree.get_node($("li[id='" + uri + "']"))) {
						return {
							instance: 	aGenerisTree,
							NODE: 		aJsTree.get_node($("li[id='" + uri + "']")),
							TREE_OBJ: 	aJsTree,
							cssClass: 	aGenerisTree.options.instanceClass
						};
					}
				}
			}
			i++;
		}
	}
	return false;
}

/**
 * Sub class action
 * @param {Node} NODE Target node
 * @param {Tree} TREE_OBJ Target Tree object
 * @param {Object} options
 */
GenerisTreeClass.prototype.addClass = function(NODE, TREE_OBJ, options){
	$.ajax({
		url: options.url,
		type: "POST",
		data: {classUri: options.id, type: 'class'},
		dataType: 'json',
		success: function(response){
			if(response.uri){
				TREE_OBJ.select_branch(
					TREE_OBJ.create({
						data: response.label,
						attributes: {
							id: response.uri,
							'class': 'node-class'
						}
					}, TREE_OBJ.get_node(NODE[0])));
			}
		}
	});
};

/**
 * add an instance
 * @param {Node} NODE Target node
 * @param {Tree} TREE_OBJ Target Tree object
 * @param {Object} options
 */
GenerisTreeClass.prototype.addInstance = function(NODE, TREE_OBJ, options){
	var cssClass = 'node-instance node-draggable';
	var instance = this;
	if(options.cssClass){
		 cssClass += ' ' + options.cssClass;
	}

	$.ajax({
		url: options.url,
		type: "POST",
		data: {classUri: options.id, type: 'instance'},
		dataType: 'json',
		success: function(response){
			if (response.uri) {
				TREE_OBJ.select_branch(TREE_OBJ.create({
					data: response.label,
					attributes: {
						id: response.uri,
						'class': cssClass
					}
				}, TREE_OBJ.get_node(NODE[0])));

				// refresh pagination if required
				var classId = $(NODE).attr('id');
				instance.setMeta (classId, 'displayed', instance.getMeta(classId, 'displayed') + 1);
				instance.setMeta (classId, 'count', instance.getMeta(classId, 'count') + 1);
				instance.refreshPaginate(NODE, instance.getTree());
			}
		}
	});

	return true;
};


/**
 * remove a resource
 * @param {Node} NODE Target node
 * @param {Tree} TREE_OBJ Target Tree object
 * @param {Object} options
 */
GenerisTreeClass.prototype.removeNode = function(NODE, TREE_OBJ, options){
	var instance = this;

	if(confirm(__("Please confirm deletion"))){
		$.each(NODE, function () {
			data = false;
			var selectedNode = this;
			if($(selectedNode).hasClass('node-class')){
				data =  {classUri: $(selectedNode).attr('id')};
			}
			if($(selectedNode).hasClass('node-instance')){
				PNODE = TREE_OBJ.parent(selectedNode);
				data =  {uri: $(selectedNode).attr('id'), classUri: $(PNODE).attr('id')};
			}
			if(data){
				$.ajax({
					url: options.url,
					type: "POST",
					data: data,
					dataType: 'json',
					success: function(response){
						if(response.deleted){
							// refresh pagination if required
							var classId = $(NODE).parent().parent().attr('id');
							instance.setMeta (classId, 'displayed', instance.getMeta(classId, 'displayed') -1);
							instance.setMeta (classId, 'count', instance.getMeta(classId, 'count') -1);
							instance.setMeta (classId, 'position', instance.getMeta(classId, 'position') -1);

							TREE_OBJ.remove(selectedNode);
						}
					}
				});
			}
		});
	}
};

/**
 * clone a resource
 * @param {Node} NODE Target node
 * @param {Tree} TREE_OBJ Target Tree object
 * @param {Object} options
 */
GenerisTreeClass.prototype.cloneNode = function(NODE, TREE_OBJ, options){
	var instance = this;
	var PNODE = TREE_OBJ.parent(NODE);
	$.ajax({
		url: options.url,
		type: "POST",
		data: {classUri: $(PNODE).attr('id'), uri: $(NODE).attr('id')},
		dataType: 'json',
		success: function(response){
			if(response.uri){
				TREE_OBJ.select_branch(
					TREE_OBJ.create({
						data: response.label,
						attributes: {
							id: response.uri,
							'class': $(NODE).attr('class')
						}
					},
					TREE_OBJ.get_node(PNODE)
					)
				);

				// refresh pagination if required
				var classId = $(PNODE).attr('id');
				instance.setMeta (classId, 'displayed', instance.getMeta(classId, 'displayed') + 1);
				instance.setMeta (classId, 'count', instance.getMeta(classId, 'count') + 1);
				instance.refreshPaginate(PNODE, instance.getTree());
			}
		}
	});
};

/**
 * Rename a node
 * @param {Object} options
 */
GenerisTreeClass.renameNode = function(options){
	var TREE_OBJ = options.TREE_OBJ;
	var NODE = options.NODE;
	var data = {
			uri: $(NODE).attr('id'),
			newName: TREE_OBJ.get_text(NODE)
		};
	if(options.classUri){
		data.classUri = options.classUri;
	}
	$.ajax({
		url: options.url,
		type: "POST",
		data: data,
		dataType: 'json',
		success: function(response){
			if(!response.renamed){
				TREE_OBJ.rename(NODE, response.oldName);
			}
		}
	});
};

/**
 * Move an instance node
 * @param {Node} NODE Target node
 * @param {Tree} TREE_OBJ Target Tree object
 */
GenerisTreeClass.prototype.moveInstance = function(myNODE, myTREE_OBJ){

	//to prevent scope crossing
	var instance = this;

	//create the dialog content
	$('body').append(
		$("<div id='tmp-moving' style='display:none;'>" +
				"<span class='ui-state-highlight' style='margin:15px;'>" + __('Select the element destination') + "</span><br />" +
				"<div id='tmp-moving-tree'></div>" +
				"<div style='text-align:center;margin-top:30px;'> " +
					"<a id='tmp-moving-closer' class='ui-state-default ui-corner-all' href='#'>" + __('Cancel') + "</a> " +
				"</div> " +
			"</div>")
	);

	//create a new tree
	var TMP_TREE = {

			//with the same data than the parent tree
			data: myTREE_OBJ.settings.data,

			//but only the ability to click on a node
			types: {
			 "default" : {
				clickable: function(NODE){
						if($(NODE).hasClass('node-class')){
							return true;
						}
						return false;
					},
					renameable	: false,
					deletable	: false,
					creatable	: false,
					draggable	: false
				}
			},
			ui: {
				theme_name : "custom"
			},
			callback: {
				//add the type param to the server request to get only the classes
				beforedata:function(NODE, TREE_OBJ) {
					if(NODE){
						return {
							hideInstances : true,
							subclasses: true,
							classUri: $(NODE).attr('id')
						};
					}
					return {
						hideInstances :true,
						subclasses: true
					};
				},

				//expand the tree on load
				onload: function(TREE_OBJ){
					TREE_OBJ.open_branch($("li.node-class:first", $("#tmp-moving-tree")));//TREE_OBJ.open_branch($("li.node-class:first"));
				},
				ondata: function(DATA, TREE_OBJ){
					return DATA;
				},

				//call the tree onmove callback by selecting a class
				onselect: function(NODE, TREE_OBJ){
					var myREF_NODE = $(instance.selector).find('li[id="'+$(NODE).attr('id')+'"]');
					// copy / past seems to reproduce the tree behavior -> more efficient
					myTREE_OBJ.cut (myNODE);
					myTREE_OBJ.paste (myREF_NODE, "inside");
					//var rollback = {};
					//rollback[$(myTREE_OBJ.container).attr('id')] = myTREE_OBJ.get_rollback();
					//myTREE_OBJ.settings.callback.onmove(myNODE, NODE, 'inside', myTREE_OBJ, rollback);
					//myTREE_OBJ.refresh();
					$("#tmp-moving").dialog('close');
				}
			}
	};

	//create a dialog window to embed the tree
	position = $(helpers.getMainContainerSelector()).offset();
	$("#tmp-moving-tree").tree(TMP_TREE);
	$("#tmp-moving").dialog({
		width: 350,
		height: 400,
		position: [position.left, position.top],
		autoOpen: false,
		title: __('Move to'),
		modal: true
	});
	$("#tmp-moving").bind('dialogclose', function(event, ui){
		$.tree.reference("#tmp-moving-tree").destroy();
		$("#tmp-moving").dialog('destroy');
		$("#tmp-moving").remove();
	});
	$("#tmp-moving-closer").click(function(){
		$("#tmp-moving").dialog('close');
	});
	//open the dialog
	$("#tmp-moving").dialog('open');
};

