/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'context',
    'core/store',
    'layout/actions',
    'ui/feedback',
    'uri',
    'jquery.tree',
    'lib/jsTree/plugins/jquery.tree.contextmenu'
], function($, _, __, context, store, actionManager, feedback, uri){
    'use strict';

    var pageRange = 30;

    /**
     * The tree factory helps you to intantiate a new tree from the TAO ontology
     * @exports layout/tree
     *
     * @param {jQueryElement} $elt - that will contain the tree
     * @param {String} url - the endpoint to load data
     * @param {Object} [options] - additional configuration options
     * @param {Object} [options.serverParameters] - add parameters to send to the endpoint (defaults are hideInstance, filter, offset and limit)
     * @param {Object} [options.actions] - which actions to perform from the tree
     * @param {String} [options.actions.moveInstance] - the id of the action bound (using actionManager.register) on move
     * @param {String} [options.actions.selectInstance] - the id of the action bound (using actionManager.register) on item selection
     * @param {String} [options.actions.selectClass] - the id of the action bound (using actionManager.register) on class selection
     * @param {String} [options.actions.deleteInstance] - the id of the action bound (using actionManager.register) on delete
     * @param {String} [options.selectNode] - the URI of the node to be selected by default, the node must be loaded.
     * @param {String} [options.loadNode] - the URI of a node to be loaded from the server side and selected.
     *
     */
    var treeFactory = function($elt, url, options){

        options = options || {};

        var lastOpened;
        var permissions = {};

        var moreNode = {
            data : __('More'),
            type : 'more',
            attributes : {
                class : 'more'
            }
        };

        //these are the parameters added to the server call to load data
        var serverParams = _.defaults(options.serverParameters || {}, {
            hideInstances   :  options.hideInstances || 0,
            filter          : '*',
            offset          : 0,
            limit           : pageRange
        });

        var treeStore = store('taotree');
        var lastSelected;

        /**
         * Set up the tree using the defined options
         * @private
         */
        var setUpTree  = function setUpTree(){

            //try to load the action instance from the options
            options.actions = _.transform(options.actions, function(result, value, key){
                if(value && value.length){
                    result[key] = actionManager.getBy(value);
                }
            });

            //bind events from the definition below
            _.forEach(events, function(callback, name){
                $elt.off(name + '.taotree')
                    .on(name + '.taotree', function(e){
                    callback.apply(this, Array.prototype.slice.call(arguments, 1));
                });
            });

            // workaround to fix dublicate tree bindings on multiple page loads
            if (!$elt.hasClass('tree')) {

                treeStore.getItem(context.section).then(function(node){
                    if(node){
                        lastSelected = node;
                    }
                    //create the tree
                    $elt.tree(treeOptions);
                });
            }
        };

        /**
         * Options given to the jsTree plugin
         */
        var treeOptions = {

            //data call
            data: {
                type: "json",
                async : true,
                opts: {
                    method : "GET",
                    url: url
                }
            },

            //theme
            ui: {
                "theme_name" : "css",
                "theme_path" : context.taobase_www + 'js/lib/jsTree/themes/css/style.css'
            },

            //nodes types
            types: {
                "default" : {
                    renameable	: false,
                    deletable	: true,
                    creatable	: true,
                    draggable	: function($node) {
                        return $node.hasClass('node-instance') && !$node.hasClass('node-undraggable') && options.actions && options.actions.moveInstance;
                    }
                }
            },

            //lifecycle callbacks
            callback: {
                /**
                 * Delete node callback.
                 * @fires layout/tree#delete.taotree
                 * @returns {undefined}
                 */
                ondelete: function ondelete() {
                    $elt.trigger('delete.taotree', Array.prototype.slice.call(arguments));
                },
                /**
                 * Additional parameters to send to the server to retrieve data.
                 * It uses the serverParams object previously defined
                 * @param {jQueryElement} [$node] - the node that represents a class. Used to add the classUri to the call
                 * @returns {Object} params
                 */
                beforedata: function($node) {
                    var treeData = $elt.data('tree-state');
                    var params = _.clone(serverParams);
                    if($node && $node.length){
                        params.classUri = $node.data('uri');
                    }
                    if(lastSelected){
                    	params.selected = lastSelected;
                    }

                    //check for additionnal parameters in tree state
                    if(treeData){

                        //the tree has been loaded/refreshed with the filtering
                        if(_.isString(treeData.filter) && treeData.filter.length){
                            params.filter = treeData.filter;
                            treeData = _.omit(treeData, 'filter');
                        }

                        //the tree has been loaded/refreshed with the loadNode parameter, so it has to be selected
                        if(_.isString(treeData.loadNode) && treeData.loadNode.length){
                            params.selected = treeData.loadNode;
                            treeData.selectNode = uri.encode(treeData.loadNode);
                            treeData = _.omit(treeData, 'loadNode');
                        }

                        $elt.data('tree-state', treeData);
                    }
                    return params;
                },

                /**
                 * Called back once the data are received.
                 * Used to modify them before building the tree.
                 *
                 * @param {Object} data - the received data
                 * @param {Object} tree - the tree instance
                 * @returns {Object} data the modified data
                 */
                ondata: function(data, tree) {

                    if(data.error){
                        feedback().error(data.error);
                        return [];
                    }

                    //automatically open the children of the received node
                    if (data.children) {
                        data.state = 'open';
                    }

                    computeSelectionAccess(data);

                    flattenPermissions(data);

                    needMore(data);

                    addTitle(data);

                    return data;
                },

                /**
                 * Once the data are loaded and the tree is ready
                 * Used to modify them before building the tree.
                 *
                 * @param {Object} tree - the tree instance
                 *
                 * @fires layout/tree#ready.taotree
                 */
                onload: function(tree){

                    var $lastSelected, $selectNode;
                    var $firstClass     = $(".node-class:not(.private):first", $elt);
                    var $firstInstance  = $(".node-instance:not(.private):first", $elt);
                    var treeState       = $elt.data('tree-state') || {};
                    var selectNode      = treeState.selectNode || options.selectNode;
                    var nodeSelection   = function nodeSelection(){
                        //the node to select is given
                        if(selectNode){
                             $selectNode = $('#' + selectNode, $elt);
                             if($selectNode.length && !$selectNode.hasClass('private')){
                                return tree.select_branch($selectNode);
                             }
                        }

                        //try to select the last one
                        if(lastSelected){
                            $lastSelected = $('#' +  lastSelected, $elt);
                            if($lastSelected.length && !$lastSelected.hasClass('private')){
                                lastSelected = undefined;
                                return tree.select_branch($lastSelected);
                            }
                        }

                        //or the 1st instance
                        if ($firstInstance.length) {
                            return tree.select_branch($firstInstance);
                        }
                        //or something
                        return tree.select_branch($('.node-class,.node-instance', $elt).get(0));
                    };

                    if($firstClass.hasClass('leaf')){
                        tree.select_branch($firstClass);
                    } else {
                        //open the first class
                        tree.open_branch($firstClass, false, function(){
                            _.delay(nodeSelection, 10); //delay needed as jstree seems to doesn't know the callbacks right now...,
                        });
                    }

                    /**
                     * The tree is now ready
                     * @event layout/tree#ready.taotree
                     * @param {Object} [context] - the tree context (uri, classUri)
                     */
                    $elt.trigger('ready.taotree');
                },

                /**
                 * After a branch is initialized
                 */
                oninit : function () {
                    //execute initTree action
                    if (options.actions && options.actions.init) {
                        actionManager.exec(options.actions.init, {
                            uri: $elt.data('rootnode')
                        });
                    }
                },

                /**
                 * Before a branch is opened
                 * @param {HTMLElement} node - the opened node
                 */
                beforeopen: function(node) {
                    lastOpened = $(node);
                },

                /**
                 * A node is selected.
                 *
                 * @param {HTMLElement} node - the opened node
                 * @param {Object} tree - the tree instance
                 *
                 * @fires layout/tree#change.taotree
                 * @fires layout/tree#select.taotree
                 */
                onselect: function(node, tree) {

                    var action;
                    var $node           = $(node);
                    var classActions = [];
                    var nodeId          = $node.attr('id');
                    var $parentNode     = tree.parent($node);
                    var nodeContext     = permissions[nodeId] ? {
                        permissions : permissions[nodeId]
                    } : {};

                    //mark all unselected
                    $('a.clicked', $elt)
                        .parent('li')
                        .not('[id="' + nodeId + '"]')
                        .removeClass('clicked');

                    //the more node makes you load more resources
                    if($node.hasClass('more')){
                        loadMore($node, $parentNode, tree);
                        return false;
                    }

                    //exec the  selectClass action
                    if ($node.hasClass('node-class')) {
                        if ($node.hasClass('closed')) {
                            tree.open_branch($node);
                        }
                        nodeContext.classUri = nodeId;
                        nodeContext.permissions = permissions[nodeId];
                        nodeContext.id = $node.data('uri');
                        nodeContext.context = ['class', 'resource'];

                        //Check if any class-level action is defined in the structures.xml file
                        classActions = _.intersection(_.pluck(options.actions, 'context'), ['class', 'resource', '*']);
                        if (classActions.length > 0) {
                            executePossibleAction(options.actions, nodeContext, ['delete']);
                        }
                    }

                    //exec the  selectInstance action
                    if ($node.hasClass('node-instance')){
                        nodeContext.uri = nodeId;
                        nodeContext.classUri = $parentNode.attr('id');
                        nodeContext.id = $node.data('uri');
                        nodeContext.context = ['instance', 'resource'];

                        //the last selected node is stored
                        treeStore.setItem(context.section, nodeId).then(function(){
                            executePossibleAction(options.actions, nodeContext, ['moveInstance', 'delete']);
                        });
                    }

                    /**
                     * A node has been selected
                     * @event layout/tree#select.taotree
                     * @param {Object} [context] - the tree context (uri, classUri)
                     */
                    $elt
                      .trigger('select.taotree', [nodeContext])
                      .trigger('change.taotree', [nodeContext]);

                    return false;
                },

                //when a node is move by drag n'drop
                onmove: function(node, refNode, type, tree, rollback) {
                    if (!options.actions.moveInstance) {
                        return false;
                    }

                    //do not move an instance into an instance...
                    if ($(refNode).hasClass('node-instance') && type === 'inside') {
                        $.tree.rollback(rollback);
                        return false;
                    }

                    if (type === 'after' || type === 'before') {
                        refNode = tree.parent(refNode);
                    }

                    //set the rollback data
                    $elt.data('tree-state', _.merge($elt.data('tree-state'), {rollback : rollback}));

                    //execute the selectInstance action
                    actionManager.exec(options.actions.moveInstance, {
                        uri: $(node).data('uri'),
                        destinationClassUri: $(refNode).data('uri')
                    });

                    $elt.trigger('change.taotree');
                }
            }
        };

        //list of events callbacks to be bound to the tree
        var events = {

            /**
             * Refresh the tree
             *
             * @event layout/tree#refresh.taotree
             * @param {Object} [data] - some data to bind to the tree
             * @param {String} [data.filter] - reload the tree in filtering mode
             * @param {String} [data.selectNode] - reload the tree and select the given node (by URI) if it is already loaded.
             * @param {String} [data.loadNode] - the URI of a node to display in filtering mode (it will load only this node)
             */
            'refresh' : function(data){
                var treeState, node;
                var tree =  $.tree.reference($elt);
                if(tree){

                    // try to select the node within the current loaded tree
                    if (data && data.loadNode) {
                        node = $elt.find('[data-uri="' + data.loadNode + '"]');
                        if (node.length) {
                            tree.select_branch(node);
                            return;
                        }
                    }

                    //update the state with data to be used later (ie. filter value, etc.)
                    treeState = _.merge($elt.data('tree-state') || {}, data);




                    if (data && data.loadNode) {
                        tree.deselect_branch(tree.selected);
                        tree.settings.selected = false;
                        treeState.selectNode = data.loadNode;
                    }
                    $elt.data('tree-state', treeState);
                    tree.refresh();
                }
            },

            /**
             * Rollback the tree.
             * The rollback state must have been set in the state previously, otherwise runs a refresh.
             *
             * @event layout/tree#rollback.taotree
             */
            'rollback' : function(){
                var treeState;
                var tree =  $.tree.reference($elt);
                if(tree){

                    treeState = $elt.data('tree-state');
                    if(treeState.rollback){
                        tree.rollback(treeState.rollback);

                        //remove the rollback infos.
                        $elt.data('tree-state', _.omit(treeState, 'rollback'));
                    } else {
                        //trigger a full refresh
                        $elt.trigger('refresh.taotree');
                    }
                }
            },

            /**
             * Add a node to the tree.
             *
             * @event layout/tree#addnode.taotree
             * @param {Object} data - the data about the node to add
             * @param {String} data.parent - the id/uri of the node that will contain the new node
             * @param {String} data.id - the id of the new node
             * @param {String} data.cssClass - the css class for the new node (node-instance or node-class at least).
             */
            'addnode' : function (data) {

                var tree =  $.tree.reference($elt);
            	var parentNode = tree.get_node($('#' + uri.encode(data.parent), $elt).get(0));

                var params = _.clone(serverParams);

                params.classUri = data.parent;
                if (data.cssClass === 'node-class') {
                    params.hideInstances = 1; //load only class nodes
                } else {
                    params.loadNode = data.uri; //load particular instance
                }
                //load tree branch with new node to get new node permissions
                $.ajax(tree.settings.data.opts.url, {
                    type        : tree.settings.data.opts.method,
                    dataType    : tree.settings.data.type,
                    async       : tree.settings.data.async,
                    data        : params,
                    success     : function (response) {
                        var items = response.children ? response.children : response;
                        var node = _.filter(items, function (child) {
                            return child.attributes && child.attributes['data-uri'] == data.uri;
                        });
                        if (node.length) {
                            tree.select_branch(
                                tree.create(node[0], parentNode)
                            );
                        }
                    }
                });
            },

            /**
             * Remove a node from the tree.
             *
             * @event layout/tree#removenode.taotree
             * @param {Object} data - the data about the node to remove
             * @param {String} data.id - the id of the node to remove
             */
            'removenode' : function(data){
                var tree =  $.tree.reference($elt);
                var node = tree.get_node($('#' + data.id, $elt).get(0));
                tree.remove(node);
           },

            /**
             * Select a node
             *
             * @event layout/tree#selectnode.taotree
             * @param {Object} data - the data about the node to select
             * @param {String} data.id - the id of the node to select
             */
            'selectnode' : function(data){
                var tree =  $.tree.reference($elt);
                var node = tree.get_node($('#' + data.id, $elt).get(0));
                $('li a', $elt).removeClass('clicked');
                tree.select_branch(node);
            },

            /**
             * Opens a tree branch
             *
             * @event layout/tree#openbranch.taotree
             * @param {Object} data - the data about the node to remove
             * @param {String} data.id - the id of the node to remove
             */
            'openbranch' : function(data){
                var tree =  $.tree.reference($elt);
                var node = tree.get_node($('#' + data.id, $elt).get(0));
                $('li a', $elt).removeClass('clicked');
                tree.open_branch(node);
            }
        };


        /**
         * Check if a node has access to a type of action regarding it's permissions
         * @private
         * @param {String} actionType - in selectClass, selectInstance, moveInstance and delete
         * @param {Object} node       - the node data as recevied from the server
         * @returns {Boolean} true if the action is allowed
         */
        var hasAccessTo = function hasAccessTo(actionType, node){
            var action = options.actions[actionType];
            if(node && action && node.permissions && node.permissions[action.id] !== undefined){
                return !!node.permissions[action.id];
            }
            return true;
        };

        /**
         * Check whether the nodes in a tree are selectable. If not, we add the <strong>private</strong> class.
         * @private
         * @param {Object} node - the tree node as recevied from the server
         */
        var computeSelectionAccess = function(node){

            if(_.isArray(node)){
                _.forEach(node, computeSelectionAccess);
                return;
            }
            if(node.type && node.permissions){
                addClassToNode(node, getPermissionClass(node));
             }
             if(node.type){
                if (!hasAccessTo('moveInstance', node)) {
                    addClassToNode(node, 'node-undraggable');
                }
            }
            if(node.children){
                _.forEach(node.children, computeSelectionAccess);
            }
        };

		/**
		 * Get the CSS class to apply to the node regarding the computed permissions
		 * @private
		 * @param {Object} node - the tree node
		 * @returns {String} the CSS class
		 */
		function getPermissionClass(node){
			var actions = _.pluck(_.filter(options.actions, function (val) {
				return val.context === node.type || val.context === 'resource';
			}), 'id');
			var keys = _.intersection(_.keys(node.permissions), actions);
			var values = _.filter(node.permissions, function (val, key) {
				return _.contains(keys, key);
			});
			var containsTrue = _.contains(values, true),
				containsFalse = _.contains(values, false);

			if (containsTrue && !containsFalse) {
			   return 'permissions-full';
			} else if (containsTrue && containsFalse) {
			   return 'permissions-partial';
			}
			return 'permissions-none';
		}

        /**
         * Add a title attribute to the nodes
         * @private
         * @param {Object} node - the tree node as recevied from the server
         */
        var addTitle = function addTitle(node){
            if(_.isArray(node)){
                _.forEach(node, addTitle);
                return;
            }
            if(node.attributes && node.data){
                node.attributes.title = node.data;
            }
            if(node.children){
                _.forEach(node.children, addTitle);
            }
        };

        /**
         * Reads the permissions from tree data to put them into a flat Map as <pre>nodeId : nodePermissions</pre>
         * @private
         * @param {Object} node - the tree node as recevied from the server
         */
        var flattenPermissions = function flattenPermissions(node){
            if(_.isArray(node)){
                _.forEach(node, flattenPermissions);
                return;
            }
            if(node.attributes && node.attributes.id){
                permissions[node.attributes.id] = node.permissions;
            }
            if(node.children){
                _.forEach(node.children, flattenPermissions);
            }
        };



        var needMore = function needMore(node){
           if(_.isArray(node) && lastOpened && lastOpened.length && lastOpened.data('count') > pageRange){
               node.push(moreNode);
           } else {
                if(node.count){
                    node.attributes['data-count'] = node.count;

                    if (node.children && node.count > node.children.length) {
                        node.children.push(moreNode);
                    }
                }
                if(node.children){
                    _.forEach(node.children, needMore);
                }
                if(_.isArray(node)){
                   _.forEach(node, needMore);
                }
           }
        };

        var loadMore = function loadMore($node, $parentNode, tree){
            var current     = $parentNode.children('ul').children('li.node-instance').length;
            var count       = $parentNode.data('count');
            var left        = count - current;
			var params      = _.defaults({
				'classUri'      : $parentNode.attr('id'),
				'subclasses'    : 0,
				'offset'        : current,
				'limit'         : left < pageRange ? left : pageRange
			}, serverParams);

            $.ajax(tree.settings.data.opts.url, {
                type        : tree.settings.data.opts.method,
                dataType    : tree.settings.data.type,
                async       : tree.settings.data.async,
                data        : params
            }).done(function(response){
                if(response && _.isArray(response.children)){
                    response = response.children;
                }
                if(_.isArray(response)){
                   _.forEach(response, function(newNode){
                        if(newNode.type === 'instance'){   //yes the server send also the class, even though I ask him gently...
                            tree.create(newNode, $parentNode);
                        }
                   });
                   tree.deselect_branch($node);
                   tree.remove($node);
                   if(left - response.length > 0){
                        tree.create(moreNode, $parentNode);
                   }
                }
            });
        };

        var permissionErrorMessage;

        /**
         * Function executes first found allowed action for tree node.
         * @param {object} actions - All tree actions
         * @param {object} [context] - Node context
         * @param {object} [context.permissions] - Node permissions
         * @param {object} [context.context] - The context of the action: (class|instance|resource|*)
         * @param {array} exclude - list of actions to be excluded.
         * @returns {undefined}
         */
        var executePossibleAction = function executePossibleAction(actions, context, exclude) {
            var possibleActions;
            if (!_.isArray(exclude)) {
                exclude = [];
            }
            possibleActions = _.filter(actions, function (action, name) {
                var possible = _.contains(context.context, action.context);
                if (context.permissions) {
                    possible = possible && context.permissions[action.id];
                }
                possible = possible && !_.contains(exclude, name);
                return possible;
            });
            //execute the first allowed action
            if(possibleActions.length > 0){
                //hide shown earlier message
                if (permissionErrorMessage) {
                    permissionErrorMessage.close();
                }
                actionManager.exec(possibleActions[0], context);
            } else {
                permissionErrorMessage = feedback().error(__("You don't have sufficient permissions to access"));
            }
        };

        return setUpTree();
    };

    /**
     * Add a css class to a list of nodes and their children, recursilvely.
     * @param {Array} nodes - the nodes to add the class to
     * @param {String} clazz - the css class
     */
    function addClassToNodes(nodes, clazz) {
        if(nodes.length){
           _.forEach(nodes, function(node){
                addClassToNode(node, clazz);
                if (node.children) {
                    addClassToNodes(node.children, clazz);
                }
            });
        }
    }

    function addClassToNode(node, clazz){
        if(node && node.attributes){

            node.attributes['class'] = node.attributes['class'] || '';

            if(node.attributes['class'].length) {
                node.attributes['class'] = node.attributes['class'] + ' ' + clazz;
            } else {
                node.attributes['class'] = clazz;
            }
        }
    }

    return treeFactory;
});
