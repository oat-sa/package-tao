/**
 * Mother class of Generis Trees
 *
 * @require jquery >= 1.4.2 [http://jquery.com/]
 * @require jstree = 0.9.9a2 [http://jstree.com/]
 *
 * @author Jehan Bihini
 * @deprecated use layout/tree instead
 */
define(['jquery', 'i18n', 'class'], function($, __, Class) {
	var GenerisTreeClass = Class.extend({
		/**
		 * Constructor
		 * @param {String} selector the jquery selector of the tree container
		 * @param {String} dataUrl the url to call, it must provide the json data to populate the tree
		 * @param {Object} options
		 * @param {Object} treeOptions
		 */
		init: function(selector, dataUrl, options, treeOptions) {
			var self = this;

			this.STATE_BROWSING = 0;
			this.STATE_FILTERING = 1;

			this.selector = selector;	        //jsquery selector of the tree
			this.options = options;		        //options
			this.dataUrl = dataUrl;		        //Url used to get tree data
			this.metaClasses = [];	  //Store meta data of opened classes
			this.lastOpened = null;		        //Keep a reference of the last opened node
			this.state = this.STATE_BROWSING; //Generis tree class state, by default browsing

			//Paginate the tree or not
			this.paginate = typeof options.paginate !== 'undefined' ? options.paginate : 0;
			//Options to pass to the server
			this.serverParameters = (typeof options.serverParameters !== "undefined") ? options.serverParameters : new Array ();
			//Default server parameters
			this.defaultServerParameters = {
				hideInstances:  this.options.hideInstances | false,
				filter: 		$("#filter-content-" + options.actionId).val(),
				offset:			0,
				limit:			this.options.paginate
			};

			this.treeOptions = {
				data: {
					type: "json",
					async : true,
					opts: {
						method : "POST",
						url: this.dataUrl
					}
				},
				types: {
					"default" : {
						renameable	: false,
						deletable	: true,
						creatable	: true
					}
				},
				callback: {
					beforeopen: function(NODE, TREE_OBJ) {
						self.lastOpened = NODE;
					}
				}
			};

		    $.extend(true, this.treeOptions, treeOptions);

			// workaround to fix dublicate tree bindings on multiple page loads
			var classes = $(selector).attr('class');
			if (typeof classes != 'string' || classes.match('tree') == null) {
				$(selector).tree(this.treeOptions);
			}
		},

		/**
		 * Create meta from class node
		 * @private
		 */
		createMeta: function(meta) {
			this.metaClasses[meta.id] = {
				displayed: meta.displayed ? meta.displayed : 0 // Total of elements displayed
				, count: meta.count ? meta.count : 0           // Total of elements in the class
				, position: meta.position ? meta.position : 0	 // Position of the last element displayed
			};
		},

		/**
		 * Extract meta data from received data
		 */
		extractMeta: function(DATA) {
			var nodes = new Array();
			var nodeId = null;

			if (!(DATA instanceof Array)) {
				//An object is received
				nodeId = DATA.attributes.id;
				if (typeof DATA.children != 'undefined') {
					nodes = DATA.children;
				}
				this.createMeta({id:DATA.attributes.id, count:DATA.count});
			} else {
				//An array of nodes is received
				// Get the last opened node
				if (this.lastOpened && this.state != this.STATE_FILTERING) {
					nodeId = this.lastOpened.id;
				} else {
					nodeId = "DEFAULT_ROOT";
					this.createMeta({id:nodeId, count:0});
				}
				nodes = DATA;
			}

			//Extract meta from children
			if (nodes) {
				//Number of classes found
				var countClass =0;
				for (var i=0; i < nodes.length; i++) {
					// if the children is a class, create meta for this class
					if (nodes[i].type == 'class') {
						this.extractMeta (nodes[i]);
						countClass++;
					}
				}
				var countInstances = nodes.length - countClass;
				this.setMeta(nodeId, 'position', countInstances); // Position of the last element displayed
				this.setMeta(nodeId, 'displayed',countInstances); // Total of elements displayed

				if (!(DATA instanceof Array) && DATA.state && DATA.state != 'closed') {
					if (this.getMeta(nodeId, 'displayed') < this.getMeta(nodeId, 'count')) {
						nodes.push(this.getPaginateActionNodes());
					}
				} else if ((DATA instanceof Array) && this.getMeta(nodeId, 'displayed') < this.getMeta(nodeId, 'count')) {
					nodes.push(this.getPaginateActionNodes());
				}
			}
		},

		/**
		 * Set a server parameter
		 * @param {string} key
		 * @param {string} value
		 * @param {boolean} reload Reload the tree after parameter updated
		 */
		setServerParameter: function(key, value, reload) {
			this.serverParameters[key] = value;
			if (typeof(reload)!='undefined' && reload){
				this.isRefreshing = true;
				this.getTree().refresh();
			}
		},

		/**
		 * @return {Object} the tree instance
		 */
		getTree: function() {
			return $.tree.reference(this.selector);
		},

		/**
		 * Get node's meta data
		 */
		getMeta: function(classId, metaName, value) {
			return this.metaClasses[classId][metaName];
		},

		/**
		 * Set node's meta data
		 */
		setMeta: function(classId, metaName, value) {
			this.metaClasses[classId][metaName] = value;
		},

		/**
		 * Get paginate nodes
		 * @return {array}
		 */
		getPaginateActionNodes: function() {
			returnValue = [{
				'data' : __('all')
					, 'attributes' : { 'class':'paginate paginate-all' }
				},{
					'data' : this.paginate+__(' next')
					, 'attributes' : { 'class':'paginate paginate-more' }
				}];
			return returnValue;
		},

		/**
		 * Show paginate options
		 * @param NODE
		 * @param TREE_OBJ
		 * @private
		 */
		showPaginate: function(NODE, TREE_OBJ) {
			var DATA = this.getPaginateActionNodes();
			for (var i = 0; i < DATA.length; i++) {
				TREE_OBJ.create(DATA[i], TREE_OBJ.get_node(NODE[0]));
			}
		},

		/**
		 * Hide paginate options
		 * @param NODE
		 * @param TREE_OBJ
		 * @private
		 */
		hidePaginate: function(NODE, TREE_OBJ) {
			$(NODE).find('.paginate').each(function(){
				$(this).remove();
			});
		},

		/**
		 * Refresh pagination, hide and show if required
		 * @param NODE
		 * @param TREE_OBJ
		 * @private
		 */
		refreshPaginate: function(NODE, TREE_OBJ) {
			var nodeId = $(NODE)[0].id;
			this.hidePaginate (NODE, TREE_OBJ);
			if (this.getMeta(nodeId, "displayed") < this.getMeta(nodeId, "count")) {
				this.showPaginate(NODE, TREE_OBJ);
			}
		},

		/**
		 * Get the Class URI from the tree
		 */
		getClassUri: function(NODE) {
			var parents = $(NODE).parents('li');
			var classUri = null;
			if (parents.length == 0) classUri = $(NODE).prop('id');
			else classUri = $(parents[0]).prop('id');
			return classUri;
		},

		callGetSectionActions: function(NODE, TREE_OBJ) {
			var uri = undefined;
			var classUri = undefined;

			if (NODE != undefined) {
				if ($(NODE).hasClass('node-class')) {
					classUri = $(NODE).prop('id');
				} else {
					uri = $(NODE).prop('id');
					classUri = this.getClassUri(NODE);
				}
			}
		}
	});

	return GenerisTreeClass;
});
