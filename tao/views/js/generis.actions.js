/**
 * Actions component
 *
 * @require jquery >= 1.4.2 [http://jquery.com/]
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @author Jehan Bihin
 */
define([
    'jquery',
    'helpers'
], function($, helpers) {
    
    console.warn('Hello I am the GenerisActions  and I am deprecated. I am there from a long but now I am tired, I need to retire. Please talk to my son layout/action.');

    /*
     * DEPRECATED
     */
        var mainTree;
    
	var GenerisActions = {
		init: function() {
                    this._listenForActions();    
		},
                
		setMainTree: function(tree) {
                    mainTree = tree;
		},
                
                /**
                 * Listen for click on actions that contains the attr data-action
                 * @private
                 */
                _listenForActions : function(){
                    var self = this;
                    //$(document).on('click', '#section-actions [data-action]', function(e){
                        //e.preventDefault();
                        //var $elt = $(this);
                        //var action = $elt.data('action');
                        //var url = $elt.attr('href');
                        //var uri = $elt.data('uri');
                        //var classUri = $elt.data('class-uri');
                        
                        //if(self[action] && typeof self[action] === 'function'){
                            //self[action](uri, classUri, url);
                        //}
                    //});
                },
                
		/**
		 * convenience method to select a resource
		 * @param {String} uri
		 */
		select: function(uri) {
                    mainTree.selectTreeNode(uri);
		},
                
		/**
		 * convenience method to subclass
		 * @param {String} uri
		 * @param {String} classUri
		 * @param {String} url
		 */
		subClass: function(uri, classUri, url) {
			var options = mainTree.getTreeOptions(classUri);
			if (options) {
                            options.id = classUri;
                            options.url = url;
                            mainTree.addClass(options.NODE, options.TREE_OBJ, options);
			}
		},
                
		/**
		 * convenience method to instanciate
		 * @param {String} uri
		 * @param {String} classUri
		 * @param {String} url
		 */
		instanciate: function (uri, classUri, url) {
			var options = mainTree.getTreeOptions(classUri);
			if (options) {
				options.id = classUri;
				options.url = url;
				mainTree.addInstance(options.NODE, options.TREE_OBJ, options);
			}
		},
		/**
		 * convenience method to instanciate
		 * @param {String} uri
		 * @param {String} classUri
		 * @param {String} url
		 */
		removeNode: function (uri, classUri, url) {
			var options = mainTree.getTreeOptions(uri);
			if (!options) {
				options = mainTree.getTreeOptions(classUri);
			}
			if (options) {
				options.url = url;
				mainTree.removeNode(options.NODE, options.TREE_OBJ, options);
			}
		},
		/**
		 * convenience method to clone
		 * @param {String} uri
		 * @param {String} classUri
		 * @param {String} url
		 */
		duplicateNode: function(uri, classUri, url) {
			var options = mainTree.getTreeOptions(uri);
			if (options) {
				options.url = url;
				mainTree.cloneNode(options.NODE, options.TREE_OBJ, options);
			}
		},
		/**
		 * move a selected node
		 * @param {String} uri
		 * @param {String} classUri
		 * @param {String} url
		 */
		moveNode: function(uri, classUri, url) {
			var options = mainTree.getTreeOptions(uri);
			if (options) {
				mainTree.moveInstance(options.NODE, options.TREE_OBJ);
			}
		},
		/**
		 * Open a popup
		 * @param {String} uri
		 * @param {String} classUri
		 * @param {String} url
		 */
		fullScreen: function(uri, classUri, url) {
                        if(url.indexOf('?') === -1){
                            url += '?';
                        } else{
                            url += '&';
                        }
			url += 'uri='+uri+'&classUri='+classUri;
                        
			var width = parseInt($(window).width());
			if(width < 800){
				width = 800;
			}
			var height = parseInt($(window).height()) + 50;
			if(height < 600){
				height = 600;
			}
			var windowOptions = {
				'width' 	: width,
				'height'	: height,
				'menubar'	: 'no',
				'resizable'	: 'yes',
				'status'	: 'no',
				'toolbar'	: 'no',
				'dependent' : 'yes',
				'scrollbar' : 'yes'
			};
			var params = '';
			for (var key in windowOptions) {
				params += key + '=' + windowOptions[key] + ',';
			}
			params = params.replace(/,$/, '');
			window.open(url, 'preview', params);
		},
		/**
		 * Load the result table with the tree instances in parameter
		 * @deprecated
		 * @param {String} uri
		 * @param {String} classUri
		 * @param {String} url
		 */
		resultTable: function(uri, classUri, url) {
			var options = mainTree.getTreeOptions(classUri);
			var TREE_OBJ = options.TREE_OBJ;
			var NODE = options.NODE;

			function getInstances(TREE_OBJ, NODE){
				var NODES = new Array();
				$.each(TREE_OBJ.children(NODE), function(i, CNODE){
					if ($(CNODE).hasClass('node-instance')) {
						NODES.push($(CNODE).prop('id'));
					}
					if ($(CNODE).hasClass('node-class')) {
						var subNodes = getInstances(TREE_OBJ, CNODE);
						NODES.concat(subNodes);
					}
				});
				return NODES;
			}
			var data = {};
			var instances = getInstances(TREE_OBJ, NODE);

			var i=0;
			while(i< instances.length){
				data['uri_'+i] = instances[i];
				i++;
			}
			data.classUri = classUri;
			helpers._load(helpers.getMainContainerSelector(), url, data);
		}
        };

	return GenerisActions;
});
