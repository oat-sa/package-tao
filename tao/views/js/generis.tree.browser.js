/**
 * GenerisTreeBrowserClass is a easy to use container for the tree widget,
 * it provides the common behavior for a Class/Instance Rdf resource tree
 *
 * @example new GenerisTreeBrowserClass('#tree-container', 'myData.php', {});
 * @see GenerisTreeBrowserClass.defaultOptions for options example
 *
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * @require jstree = 0.9.9 [http://jstree.com/]
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @author Jehan Bihin (class, remove multi-instances)
 */

define(['jquery', 'i18n', 'generis.tree', 'helpers', 'context', 'jquery.tree', 'lib/jsTree/plugins/jquery.tree.contextmenu'], function($, __, GenerisTreeClass, helpers, context) {

    console.warn('Hello I am the GenerisTreeBrowserClass  and I am deprecated. I am there from a long but now I am tired, I need to retire. Please talk to my son layout/tree.');

    /*
     * DEPRECATED
     */
	var GenerisTreeBrowserClass = GenerisTreeClass.extend({
		/**
		 * The GenerisTreeBrowserClass constructor
		 * @param {String} selector the jquery selector of the tree container
		 * @param {String} dataUrl the url to call, it must provide the json data to populate the tree
		 * @param {Object} options {formContainer, actionId, instanceClass, instanceName, selectNode,
		 * 							editClassAction, editInstanceAction, createInstanceAction,
		 * 							moveInstanceAction, subClassAction, deleteAction, duplicateAction}
		 */
		init: function(selector, dataUrl, options) {
			var instance = this;

			this.defaultOptions = {
				formContainer: '#form-container'
			};

			if (!options) {
				options = this.defaultOptions;
			}

			this.filter = '*'; //filter to apply to the result (by default get all)

			if (!options.instanceName) {
				options.instanceName = 'instance';
			}

			/**
			 * @var {Object} jsTree options
			 * @see http://www.jstree.com/documentation for the options documentation
			 */
			var treeOptions = {
				types: {
				 "default" : {
						draggable	: function(NODE) {
							if ($(NODE).hasClass('node-instance') && instance.options.moveInstanceAction) {
								return true;
							}
							return false;
						}
					}
				},
				ui: {
					theme_name : "custom",
                                        theme_path : context.taobase_www + 'js/lib/jsTree/themes/css/style.css'
				},
				callback: {
					//Before receive data from server, return the POST parameters
					beforedata: function(NODE, TREE_OBJ) {
						var returnValue = instance.defaultServerParameters;
						// If a NODE is given, send its identifier to the server
						// and the user does not want to filter the data
						if (NODE && instance.filter=='*') {
							returnValue['classUri'] = $(NODE).prop('id');
						} else if (typeof returnValue['classUri'] != 'undefined') {
							// else destroy the class uri field to get filter on the whole instances
							delete returnValue['classUri'];
						}
						// Augment with the serverParameters
						for (var key in instance.serverParameters){
							returnValue[key] = instance.serverParameters[key];
						}
						// Add selected nodes
						returnValue['selected'] = instance.options.selectNode;
						// Add the filter parameter
						returnValue['filter'] = instance.filter;

						return returnValue;
					},
					//once the tree is loaded
					onload: function(TREE_OBJ){
						if (instance.options.selectNode) {
							TREE_OBJ.select_branch($("li[id='"+instance.options.selectNode+"']"));
							instance.options.selectNode = false;
						} else {
							TREE_OBJ.open_branch($("li.node-class:first"));
                            // always select the first item once the tree is open
                            $("li.node-class:first a:first").trigger('click');
						}
					},
					//when we receive the data
					ondata: function(DATA, TREE_OBJ) {
						//automatically open the children of the received node
						if (DATA.children) {
							DATA.state = 'open';
						}
						//extract meta data from children
						instance.extractMeta(DATA);
						//add specific classes to nodes
						if (instance.options.instanceClass) {
							function addClassToNodes(nodes, clazz) {
								$.each(nodes, function(i, node){
									if (node.attributes
											&& node.attributes['class']
											&& /node\-instance/.test(node.attributes['class'])) {
										node.attributes['class'] = node.attributes['class'] + ' ' + clazz;
									}

									if (node.children) {
										addClassToNodes(node.children, clazz);
									}
								});
							}

							if (DATA.children) {
								addClassToNodes(DATA.children, instance.options.instanceClass);
								if (instance.options.moveInstanceAction) {
									addClassToNodes(DATA.children, 'node-draggable');
								}
							} else if(DATA.length > 0) {
								addClassToNodes(DATA, instance.options.instanceClass);
								if (instance.options.moveInstanceAction) {
									addClassToNodes(DATA, 'node-draggable');
								}
							}
						}

						return DATA;
					},
					//when a node is selected
					onselect: function(NODE, TREE_OBJ) {
						var nodeId = $(NODE).prop('id');
						var parentNodeId = $(NODE).parent().parent().prop('id');
						$("a.clicked").each(function() {
							if ($(this).parent('li').prop('id') != nodeId) {
								$(this).removeClass('clicked');
							}
						});

                        //already selected
						if (($("input:hidden[name='uri']").val() == nodeId || $("input:hidden[name='classUri']").val() == nodeId) && nodeId == instance.options.selectNode) {
							return false;
						}

						if ($(NODE).hasClass('node-class') && instance.options.editClassAction) {
							if ($(NODE).hasClass('closed')) {
								TREE_OBJ.open_branch(NODE);
							}

							//load the editClassAction into the formContainer
							helpers._load(instance.options.formContainer, instance.options.editClassAction, instance.data(null, nodeId));
						}

						if ($(NODE).hasClass('node-instance') && instance.options.editInstanceAction) {
							//load the editInstanceAction into the formContainer
							var PNODE = TREE_OBJ.parent(NODE);
							helpers._load(instance.options.formContainer, instance.options.editInstanceAction, instance.data(nodeId, $(PNODE).prop('id')));
						}

						if ($(NODE).hasClass('paginate-more')) {
							instance.paginateInstances ($(NODE).parent().parent(), TREE_OBJ);
						}
						if ($(NODE).hasClass('paginate-all')) {
							var limit = instance.getMeta(parentNodeId, 'count') - instance.getMeta (parentNodeId, 'displayed');
							instance.paginateInstances($(NODE).parent().parent(), TREE_OBJ, {'limit':limit});
						}

						instance.callGetSectionActions(NODE, TREE_OBJ);

						return false;
					},
					//when a node is move by drag n'drop
					onmove: function(NODE, REF_NODE, TYPE, TREE_OBJ, RB) {
						if (!instance.options.moveInstanceAction) {
							return false;
						}
						if ($(REF_NODE).hasClass('node-instance') && TYPE == 'inside') {
							$.tree.rollback(RB);
							return false;
						} else {
							if (TYPE == 'after' || TYPE == 'before') {
								REF_NODE = TREE_OBJ.parent(REF_NODE);
							}
							//call the server with the new node position to save the new position
							function moveNode(url, data) {
								var NODE 		= data.NODE;
								var REF_NODE	= data.REF_NODE;
								var RB 			= data.RB;
								var TREE_OBJ 	= data.TREE_OBJ;
								var confirmed = (data.confirmed === true);

								$.postJson(url, {
									'uri': data.uri,
									'destinationClassUri':  data.destinationClassUri,
									'confirmed' : confirmed
									},
									function(response) {
										if (response == null) {
											$.tree.rollback(RB);
											return;
										}
										if (response.status == 'diff') {
											var message = __("Moving this element will replace the properties of the previous class by those of the destination class:");
											message += "\n";
											for (var i = 0; i < response.data.length; i++) {
												if (response.data[i].label) {
													message += "- " + response.data[i].label + "\n";
												}
											}
											message += "Please confirm this operation.\n";
											if (confirm(message)) {
												data.confirmed = true;
												moveNode(url, data);
											} else {
												$.tree.rollback(RB);
											}
										} else if (response.status == true) {
											$('li a').removeClass('clicked');
											TREE_OBJ.open_branch(NODE);
										} else {
											$.tree.rollback(RB);
										}
								});
							}
							moveNode(instance.options.moveInstanceAction, {
									'uri': $(NODE).prop('id'),
									'destinationClassUri': $(REF_NODE).prop('id'),
									'NODE'		: NODE,
									'REF_NODE'	: REF_NODE,
									'RB'		: RB,
									'TREE_OBJ'	: TREE_OBJ
								});
						}

						instance.callGetSectionActions(NODE, TREE_OBJ);
					},

					oninit: function(TREE_OBJ) {
						instance.callGetSectionActions(undefined, TREE_OBJ);
					}
				}
			};

			if (options.selectNode) {
				treeOptions.selected = options.selectNode;
			}

            if($(selector).length){
                var tmpTree = $.tree.reference(selector);
                if (tmpTree != null) {
                    tmpTree.destroy();
                }
                tmpTree = null;
            }
			/*
			 * Create and initialize the tree here
			 */
			this._super(selector, dataUrl, options, treeOptions);

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
				instance.enableFilter();
			});
			$("#filter-content-" + options.actionId).bind('keypress', function(e) {
				if (e.keyCode==13 && this.value.length > 0) {
					instance.enableFilter();
				}
			});
		},

		/**
		 * Formats sendable data with the defined options
		 * ONLY USED FOR SELECT ACTION
		 * @param {String} uri
		 * @param {String} classUri
		 * @return {Object}
		 */
		data: function(uri, classUri) {
			var data = {};

			var instanceKey = (this.options.instanceKey) ?  this.options.instanceKey :  'uri';
			var classKey =  (this.options.classKey) ? this.options.classKey : 'classUri';

			if (uri) {
				data[instanceKey] = uri;
			}
			// If the tree is filtering it does not know the class of each instance
			if (this.state != GenerisTreeClass.STATE_FILTERING) {
				if (classUri) {
					data[classKey] = classUri;
				}
			}

			return data;
		},

		/**
		 * Enable the filter
		 */
		enableFilter: function() {
			this.state = this.STATE_FILTERING;
			this.filter = $.trim($("#filter-content-" + this.options.actionId).val());
			var instance = this;

			if (this.filter != '*') {
				var $cancelBtn = $("#filter-cancel-" + this.options.actionId);
				if ($cancelBtn.hasClass("ui-helper-hidden")) {
					$($cancelBtn).
						removeClass("ui-helper-hidden").
						on('click', {instance: this}, function(){
							instance.disableFilter();
						});
				}
				$.tree.reference(this.selector).refresh();
			} else {
				this.disableFilter();
			}
		},

		/**
		 * Disable the filter
		 */
		disableFilter: function() {
			this.state = this.STATE_BROWSING;
			var $cancelBtn = $("#filter-cancel-" + this.options.actionId);
			this.filter = "*";
			$("#filter-content-" + this.options.actionId).val(this.filter);
			$cancelBtn.addClass("ui-helper-hidden");
			$.tree.reference(this.selector).refresh();
		},

		/**
		 * Paginate function, display more instances
		 */
		paginateInstances: function(NODE, TREE_OBJ, pOptions) {
			var nodeId = $(NODE).prop('id');
			var instancesLeft = this.getMeta(nodeId, "count") - this.getMeta(nodeId, "displayed");
			var options = {
				"classUri": nodeId,
				"subclasses": 0,
				"offset": this.getMeta(nodeId, "position"),
				"limit": instancesLeft < this.paginate ? instancesLeft : this.paginate
			};
			options = $.extend(options, pOptions);
			if(this.filter && this.filter != '*'){
				options.filter = this.filter;
			}

			$.post(this.dataUrl, options, (function(instance) { return function(DATA) {
				//Display instances
				for (var i=0; i < DATA.length; i++){
					DATA[i].attributes['class'] = instance.options.instanceClass+" node-instance node-draggable";
					if (!$('#'+DATA[i].attributes['id'], $(TREE_OBJ.container)).length) TREE_OBJ.create(DATA[i], TREE_OBJ.get_node(NODE[0]));
				}
				instance.setMeta(nodeId, "displayed", instance.getMeta(nodeId, "displayed")+DATA.length);
				instance.setMeta(nodeId, "position", instance.getMeta(nodeId, "position")+DATA.length);
				//refresh pagination options
				instance.refreshPaginate(NODE, TREE_OBJ);
			};})(this), "json");
		},

		/**
		 * select a node in the current tree
		 * @param {String} id
		 * @return {Boolean}
		 */
		selectTreeNode: function(id) {
			this.getTree().select_branch($("li[id='"+id+"']"));
			return false;
		},

		/**
		 * Enable you to retrieve the right tree instance and node instance from an Uri
		 * @param {String} uri is the id of the tree node
		 * @return {Object}
		 */
		getTreeOptions: function(uri) {
			if (uri) {
				/*var i = 0;
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
				}*/
				return {
					//instance: instance,
					NODE: this.getTree().get_node($("li[id='" + uri + "']")),
					TREE_OBJ: this.getTree(),
					cssClass: this.options.instanceClass
				};
			}
			return false;
		},

		/**
		 * Sub class action
		 * @param {Node} NODE Target node
		 * @param {Tree} TREE_OBJ Target Tree object
		 * @param {Object} options
		 */
		addClass: function(NODE, TREE_OBJ, options) {
			$.ajax({
				url: options.url,
				type: "POST",
				data: {classUri: options.id, type: 'class'},
				dataType: 'json',
				success: function(response){
					if (response.uri) {
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
		},

		/**
		 * add an instance
		 * @param {Node} NODE Target node
		 * @param {Tree} TREE_OBJ Target Tree object
		 * @param {Object} options
		 */
		addInstance: function(NODE, TREE_OBJ, options) {
			var cssClass = 'node-instance node-draggable';
			var instance = this;
			if (options.cssClass) {
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
						var classId = $(NODE).prop('id');
						instance.setMeta (classId, 'displayed', instance.getMeta(classId, 'displayed') + 1);
						instance.setMeta (classId, 'count', instance.getMeta(classId, 'count') + 1);
						instance.refreshPaginate(NODE, instance.getTree());
					}
				}
			});

			return true;
		},

		/**
		 * remove a resource
		 * @param {Node} NODE Target node
		 * @param {Tree} TREE_OBJ Target Tree object
		 * @param {Object} options
		 */
		removeNode: function(NODE, TREE_OBJ, options) {
			var instance = this;

			if (confirm(__("Please confirm deletion"))) {
				$.each(NODE, function() {
					var data = false;
					var selectedNode = this;
					if ($(selectedNode).hasClass('node-class')) {
						data =  {classUri: $(selectedNode).prop('id')};
					}
					if ($(selectedNode).hasClass('node-instance')) {
						var PNODE = TREE_OBJ.parent(selectedNode);
						data =  {uri: $(selectedNode).prop('id'), classUri: $(PNODE).prop('id')};
					}
					if (data) {
						$.ajax({
							url: options.url,
							type: "POST",
							data: data,
							dataType: 'json',
							success: function(response){
								if (response.deleted) {
									// refresh pagination if required
									var classId = $(NODE).parent().parent().prop('id');
									instance.setMeta(classId, 'displayed', instance.getMeta(classId, 'displayed') -1);
									instance.setMeta(classId, 'count', instance.getMeta(classId, 'count') -1);
									instance.setMeta(classId, 'position', instance.getMeta(classId, 'position') -1);

									TREE_OBJ.remove(selectedNode);
								}
							}
						});
					}
				});
			}
		},

		/**
		 * clone a resource
		 * @param {Node} NODE Target node
		 * @param {Tree} TREE_OBJ Target Tree object
		 * @param {Object} options
		 */
		cloneNode: function(NODE, TREE_OBJ, options) {
			var instance = this;
			var PNODE = TREE_OBJ.parent(NODE);
			$.ajax({
				url: options.url,
				type: "POST",
				data: {classUri: $(PNODE).prop('id'), uri: $(NODE).prop('id')},
				dataType: 'json',
				success: function(response){
					if (response.uri) {
						TREE_OBJ.select_branch(
							TREE_OBJ.create({
								data: response.label,
								attributes: {
									id: response.uri,
									'class': $(NODE).prop('class')
								}
							},
							TREE_OBJ.get_node(PNODE)
							)
						);

						// refresh pagination if required
						var classId = $(PNODE).prop('id');
						instance.setMeta(classId, 'displayed', instance.getMeta(classId, 'displayed') + 1);
						instance.setMeta(classId, 'count', instance.getMeta(classId, 'count') + 1);
						instance.refreshPaginate(PNODE, instance.getTree());
					}
				}
			});
		},

		/**
		 * Rename a node
		 * @param {Object} options
		 */
		renameNode: function(options) {
			var TREE_OBJ = options.TREE_OBJ;
			var NODE = options.NODE;
			var data = {
				uri: $(NODE).prop('id'),
				newName: TREE_OBJ.get_text(NODE)
			};
			if (options.classUri) {
				data.classUri = options.classUri;
			}
			$.ajax({
				url: options.url,
				type: "POST",
				data: data,
				dataType: 'json',
				success: function(response){
					if (!response.renamed) {
						TREE_OBJ.rename(NODE, response.oldName);
					}
				}
			});
		},

		/**
		 * Move an instance nf(options.actions.move){
ode
		 * @param {Node} NODE Target node
		 * @param {Tree} TREE_OBJ Target Tree object
		 */
		moveInstance: function(myNODE, myTREE_OBJ) {
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
						clickable: function(NODE) {
								if ($(NODE).hasClass('node-class')) {
									return true;
								}
								return false;
							},
							renameable: false,
							deletable: false,
							creatable: false,
							draggable: false
						}
					},
					ui: {
						theme_name : "custom",
                                                 theme_path : context.taobase_www + 'js/lib/jsTree/themes/css/style.css'
					},
					callback: {
						//add the type param to the server request to get only the classes
						beforedata: function(NODE, TREE_OBJ) {
							if (NODE) {
								return {
									hideInstances : true,
									subclasses: true,
									classUri: $(NODE).prop('id')
								};
							}
							return {
								hideInstances :true,
								subclasses: true
							};
						},
						//expand the tree on load
						onload: function(TREE_OBJ) {
							TREE_OBJ.open_branch($("li.node-class:first", $("#tmp-moving-tree")));//TREE_OBJ.open_branch($("li.node-class:first"));
						},
						ondata: function(DATA, TREE_OBJ) {
							return DATA;
						},
						//call the tree onmove callback by selecting a class
						onselect: function(NODE, TREE_OBJ) {
							var myREF_NODE = $(instance.selector).find('li[id="'+$(NODE).prop('id')+'"]');
							// copy / past seems to reproduce the tree behavior -> more efficient
							myTREE_OBJ.cut (myNODE);
							myTREE_OBJ.paste (myREF_NODE, "inside");
							//var rollback = {};
							//rollback[$(myTREE_OBJ.container).prop('id')] = myTREE_OBJ.get_rollback();
							//myTREE_OBJ.settings.callback.onmove(myNODE, NODE, 'inside', myTREE_OBJ, rollback);
							//myTREE_OBJ.refresh();
							$("#tmp-moving").dialog('close');
						}
					}
			};

			//create a dialog window to embed the tree
			var position = $(helpers.getMainContainerSelector()).offset();
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
		}
	});

	return GenerisTreeBrowserClass;
});
